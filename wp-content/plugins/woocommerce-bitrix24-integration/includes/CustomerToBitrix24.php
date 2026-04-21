<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\DataPreparers\Options\CustomerUserOption;

class CustomerToBitrix24
{
    private static $instance = false;

    protected function __construct()
    {
        if (!$this->isEnabled()) {
            return;
        }

        add_action('woocommerce_checkout_update_user_meta', [$this, 'customerSendCrm'], PHP_INT_MAX);
        add_action('woocommerce_created_customer', [$this, 'customerSendCrm']);
        add_action('woocommerce_save_account_details', [$this, 'customerSendCrm']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function customerSendCrm($customerID)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        $crmFields['contact'] = CrmFields::mixedContactAddressFields(get_option(Bootstrap::CONTACT_FIELDS_KEY, []));

        $contactData = $this->prepareContactData(get_user_meta($customerID), $customerID);

        $sendFields['contact'] = $this->prepareFields($settings['contact'], $contactData);

        if (empty($sendFields['contact']['NAME'])) {
            $sendFields['contact']['NAME'] = get_user_meta($customerID, 'first_name', true);
        }

        if (empty($sendFields['contact']['NAME'])) {
            $sendFields['contact']['NAME'] = get_user_meta($customerID, 'nickname', true);
        }

        /**
         * Filters a set of contact fields by created / updated customer.
         *
         * @since 1.48.0
         *
         * @param array $contactFields Current array of contact fields.
         * @param int   $customerID    Buyer's user ID.
         */
        $sendFields['contact'] = \apply_filters(
            'wc_bitrix24_plugin_contact_by_customer_fields_before_send',
            $sendFields['contact'],
            $customerID
        );

        // wrong create contact
        if (
            empty($sendFields['contact']['NAME'])
            && empty($sendFields['contact']['SECOND_NAME'])
            && empty($sendFields['contact']['LAST_NAME'])
        ) {
            return;
        }

        $sendFields['customer_id'] = $customerID;
        $sendFields['customer_send'] = true;

        $contact = Crm::send($sendFields, $crmFields, 'contact');
    }

    private function prepareContactData($data, $userID)
    {
        $returnData = [];

        $strposFunc = 'strpos';

        if (function_exists('mb_strpos')) {
            $strposFunc = 'mb_strpos';
        }

        foreach ($data as $key => $value) {
            if (!isset($value[0])) {
                continue;
            }

            if ($strposFunc($key, 'billing') === false && $strposFunc($key, 'shipping') === false) {
                continue;
            }

            $returnData[$key] = $value[0];
        }

        if (!empty($returnData['billing_country']) && !empty($returnData['billing_state'])) {
            $states = \WC()->countries->get_states($returnData['billing_country']);

            if (isset($states[$returnData['billing_state']])) {
                $returnData['billing_state'] = $states[$returnData['billing_state']];
            }
        }

        if (!empty($returnData['shipping_country']) && !empty($returnData['shipping_state'])) {
            $states = \WC()->countries->get_states($returnData['shipping_country']);

            if (isset($states[$returnData['shipping_state']])) {
                $returnData['shipping_state'] = $states[$returnData['shipping_state']];
            }
        }

        $customer = new \WC_Customer($userID);

        if (empty($returnData['billing_email'])) {
            $returnData['billing_email'] = $customer->get_email();
        }

        if (empty($returnData['billing_full_name'])) {
            $returnData['billing_full_name'] = $customer->get_billing_last_name()
                . ' '
                . $customer->get_billing_first_name();
        }

        if (empty($returnData['billing_full_name_backward'])) {
            $returnData['billing_full_name_backward'] = $customer->get_billing_first_name()
                . ' '
                . $customer->get_billing_last_name();
        }

        if (empty($returnData['shipping_full_name'])) {
            $returnData['shipping_full_name'] = $customer->get_shipping_last_name()
                . ' '
                . $customer->get_shipping_first_name();
        }

        if (empty($returnData['shipping_full_name_backward'])) {
            $returnData['shipping_full_name_backward'] = $customer->get_shipping_first_name()
                . ' '
                . $customer->get_shipping_last_name();
        }

        $returnData = CustomerUserOption::prepare($returnData, $userID);

        $utmFields = Helper::parseUtmCookie();

        $returnData['utm_source'] = isset($utmFields['utm_source'])
            ? rawurldecode(wp_unslash($utmFields['utm_source']))
            : '';
        $returnData['utm_medium'] = isset($utmFields['utm_medium'])
            ? rawurldecode(wp_unslash($utmFields['utm_medium']))
            : '';
        $returnData['utm_campaign'] = isset($utmFields['utm_campaign'])
            ? rawurldecode(wp_unslash($utmFields['utm_campaign']))
            : '';
        $returnData['utm_term'] = isset($utmFields['utm_term'])
            ? rawurldecode(wp_unslash($utmFields['utm_term']))
            : '';
        $returnData['utm_content'] = isset($utmFields['utm_content'])
            ? rawurldecode(wp_unslash($utmFields['utm_content']))
            : '';

        return $returnData;
    }

    private function prepareFields($fields, $orderData)
    {
        foreach ($orderData as $key => $value) {
            foreach ($fields as $keyField => $fieldValue) {
                if ($key == $fieldValue) {
                    $fields[$keyField] = $value;
                }
            }
        }

        // Resolving populate fields
        $temporaryFields = $fields;

        foreach ($fields as $keyField => $fieldValue) {
            if (isset($fields[$keyField . '-populate'])) {
                if (!empty($fields[$keyField . '-populate'])) {
                    $temporaryFields[$keyField] = $fields[$keyField . '-populate'];
                }

                unset($temporaryFields[$keyField . '-populate']);
            }
        }

        // clearing empty fields
        foreach ($fields as $keyField => $fieldValue) {
            if (strpos($fieldValue, 'billing_') !== false) {
                if (isset($keyField)) {
                    unset($temporaryFields[$keyField]);
                }
            }

            if (strpos($fieldValue, 'shipping_') !== false) {
                if (isset($keyField)) {
                    unset($temporaryFields[$keyField]);
                }
            }
        }

        return $temporaryFields;
    }

    private function isEnabled()
    {
        if (!Helper::isVerify()) {
            return false;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        return Helper::isEnabled()
            && isset($settings['enabled_contact'])
            && (int) $settings['enabled_contact'] === 1;
    }
}
