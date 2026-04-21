<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

class WcMembershipIntegration
{
    private static $instance = false;

    protected function __construct()
    {
        if (!$this->isEnabled()) {
            return;
        }

        add_action('wc_memberships_user_membership_created', [$this, 'membershipSendCrm'], 10, 2);
        add_action('wc_memberships_user_membership_status_changed', [$this, 'membershipSendCrm'], 10, 2);
        add_action('wc_memberships_user_membership_saved', [$this, 'membershipSendCrm'], 10, 2);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function membershipSendCrm($membershipPlan, $args)
    {
        if (empty($args['user_id'])) {
            return;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY);

        $wcMembershipField = $settings['wc_membership_field'];
        $email = get_user_meta($args['user_id'], 'billing_email', true);

        if (empty($email)) {
            return;
        }

        $contactFields = get_option(Bootstrap::CONTACT_FIELDS_KEY);

        if (empty($contactFields[$wcMembershipField])) {
            return;
        }

        $field = $contactFields[$wcMembershipField];

        if ($field['type'] !== 'enumeration' || empty($field['items'])) {
            return;
        }

        $contactID = Crm::findItemByField(
            [
                'contact' => [
                    'EMAIL' => $email,
                ],
            ],
            'contact',
            'EMAIL'
        );

        if (!$contactID) {
            return;
        }

        $plans = [];

        /** @psalm-suppress UndefinedFunction */
        foreach (\wc_memberships_get_user_active_memberships($args['user_id']) as $plan) {
            $plans[] = $plan->plan->name;
        }

        $selectedPlans = [];

        $ids = array_column($field['items'], 'ID');
        $values = array_column($field['items'], 'VALUE');

        foreach ($plans as $plan) {
            if (array_search($plan, $ids) !== false) {
                $selectedPlans[] = $plan;
            } elseif (array_search($plan, $values) !== false) {
                $selectedPlans[] = $ids[array_search($plan, $values)];
            }
        }

        if ($selectedPlans) {
            if (!$field['isMultiple']) {
                $selectedPlans = $selectedPlans[0];
            }
        } else {
            if (!$field['isMultiple']) {
                $selectedPlans = '';
            } else {
                $selectedPlans = [0];
            }
        }

        $result = Crm::sendApiRequest(
            'crm.contact.update',
            false,
            [
                'id' => $contactID,
                'fields' => [
                    $wcMembershipField => $selectedPlans,
                ],
            ]
        );
    }

    private function isEnabled()
    {
        if (!Helper::isVerify()) {
            return false;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY);

        return Helper::isEnabled()
            && !empty($settings['wc_membership_field'])
            && !empty($settings['enabled_wc_membership'])
            && (int) $settings['enabled_wc_membership'] === 1;
    }
}
