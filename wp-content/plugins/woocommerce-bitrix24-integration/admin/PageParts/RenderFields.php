<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\CrmFields;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class RenderFields
{
    public $fieldNameStart = '';

    public $withUpdate = false;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    private static $crmFields = [];

    public function __construct(string $type, $withUpdate = false)
    {
        $this->type = $type;
        $this->fieldNameStart .= $type . '[';
        $this->withUpdate = $withUpdate;

        // compatibility with `WP Crowdfunding`
        if (class_exists('Wpneo_Crowdfunding')) {
            remove_filter(
                'woocommerce_checkout_fields',
                [\Wpneo_Crowdfunding::instance(), 'wpneo_override_checkout_fields']
            );
        }

        // compatibility with `WC Fields Factory`
        global $wcff;

        if (is_object($wcff) && !empty($wcff->checkout) && is_object($wcff->checkout)) {
            add_filter(
                'woocommerce_checkout_fields',
                [$wcff->checkout, 'wcccf_filter_checkout_fields']
            );
        }

        // compatibility with `TcoWooCheckout WooCommerce Checkout Manager`
        if (class_exists('Tco_Woo_Hooks')) {
            remove_filter(
                'woocommerce_checkout_fields',
                ['Tco_Woo_Hooks', 'other_fields'],
                99,
                1
            );
        }

        // compatibility with `Checkout Field Editor for WooCommerce`
        if (class_exists('THWCFE_Public_Checkout')) {
            $plugin = new \THWCFE_Public_Checkout(1, 1);
            $plugin->define_public_hooks();

            add_filter('thwcfe_show_field', function () {
                return true;
            });
        }

        if (class_exists('WC_Customer')) {
            WC()->customer = new \WC_Customer();
        }

        // compatibility with `WooCommerce Shiptor`
        if (class_exists('WC_Session_Handler')) {
            WC()->session = new \WC_Session_Handler();
        }

        // compatibility with `Woo Checkout for Digital Goods`
        if (class_exists('WC_Cart')) {
            WC()->frontend_includes();
            WC()->cart = new \WC_Cart();
        }

        $this->billing = WC()->checkout()->get_checkout_fields('billing');
        $this->shipping = WC()->checkout()->get_checkout_fields('shipping');
        $this->rawMetaKeys = Helper::getMetaKeys();
    }

    public function startTable()
    {
        echo '<table class="form-table">';
    }

    public function endTable()
    {
        echo '</table>';
    }

    public function startFieldRow($title, $isRequired)
    {
        ?>
        <tr>
        <th>
            <?php echo esc_html($title); ?>
            <?php echo $isRequired ? '<span style="color:red;"> * </span>' : ''; ?>
        </th>
        <td>
        <?php
    }

    public function endFieldRow()
    {
        ?></td></tr><?php
    }

    public function selectField($list, $name, $title, $currentValue, $currentValuePopulate = '', $update = [])
    {
        ?>
        <table width="100%">
            <tr>
                <td style="width: 50%;">
                    <label><?php esc_html_e('Default value', 'wc-bitrix24-integration'); ?></label>
                    <br>
                    <select id="<?php echo esc_attr($this->type); ?>_<?php echo esc_attr($name); ?>"
                        title="<?php echo esc_attr($title); ?>"
                        name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]">
                        <option value=""><?php esc_html_e('Not chosen', 'wc-bitrix24-integration'); ?></option>
                        <?php
                        foreach ((array) $list as $value => $valueLabel) {
                            echo '<option value="'
                                . esc_attr($value)
                                . '"'
                                . ($currentValue == $value ? ' selected' : '')
                                . '>'
                                . esc_html($value . ' - ' . $valueLabel)
                                . '</option>';
                        } ?>
                    </select>
                </td>
                <td>
                    <label><?php esc_html_e('Populate value (optional)', 'wc-bitrix24-integration'); ?></label>
                    <br>
                    <select id="<?php echo esc_attr($this->type); ?>_populate_<?php echo esc_attr($name); ?>"
                        title="<?php echo esc_attr($title); ?>"
                        name="<?php echo esc_attr($this->fieldNameStart . $name); ?>-populate]">
                        <?php $this->generateOptions($currentValuePopulate); ?>
                    </select>
                </td>
                <td>
                    <?php if ($this->withUpdate) { ?>
                        <br>
                        <input id="<?php echo esc_attr($this->type); ?>_update_<?php echo esc_attr($name); ?>"
                            type="checkbox"
                            title="<?php echo esc_html__('Update when send status change event or order changed', 'wc-bitrix24-integration'); ?>"
                            name="<?php echo esc_attr($this->fieldNameStart . 'update][' . $name); ?>]"
                            value="true"
                            <?php echo in_array($name, array_keys($update)) ? 'checked' : ''; ?>>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <?php
        ?>

        <?php
    }

    public function statusField($type, $name, $title, $currentValue)
    {
        $list = $this->getStatusListByType($type); ?>
        <select id="<?php echo esc_attr($this->type); ?>_<?php echo esc_attr($name); ?>"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]">
            <option value=""><?php esc_html_e('Not chosen', 'wc-bitrix24-integration'); ?></option>
            <?php
            foreach ((array) $list as $value => $name) {
                echo '<option value="'
                    . esc_attr($value)
                    . '"'
                    . ($currentValue == $value ? ' selected' : '')
                    . '>'
                    . esc_html($value . ' - ' . $name)
                    . '</option>';
            } ?>
        </select>
        <?php
    }

    public function checkoutFieldsSelect($name, $title, $currentValue, $update = [])
    {
        if (in_array($name, ['ASSIGNED_BY_ID'])) {
            $this->inputTextField($name, $title, $currentValue);

            return;
        } ?>
        <select id="<?php echo esc_attr($this->type); ?>_<?php echo esc_attr($name); ?>"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]">
            <?php $this->generateOptions($currentValue); ?>
        </select>
        <?php if ($this->withUpdate) { ?>
            <input id="<?php echo esc_attr($this->type); ?>_update_<?php echo esc_attr($name); ?>"
                type="checkbox"
                title="<?php echo esc_html__('Update when send status change event or order changed', 'wc-bitrix24-integration'); ?>"
                name="<?php echo esc_attr($this->fieldNameStart . 'update][' . $name); ?>]"
                value="true"
                <?php echo in_array($name, array_keys($update)) ? 'checked' : ''; ?>>
        <?php } ?>
        <?php
    }

    public function inputTextField($name, $title, $currentValue)
    {
        ?>
        <input id="<?php echo esc_attr($this->type); ?>_<?php echo esc_attr($name); ?>"
            type="text"
            class="large-text code"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            value="<?php echo esc_attr($currentValue); ?>">
        <?php
    }

    public function inputCheckboxField($name, $title, $currentValue)
    {
        ?>
        <input type="hidden"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            value="N">
        <input id="<?php echo esc_attr($this->type); ?>_<?php echo esc_attr($name); ?>"
            type="checkbox"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            value="Y"
            <?php echo $currentValue === 'Y' ? 'checked' : ''; ?>>
        <?php
    }

    public function textareaField($name, $title, $currentValue)
    {
        ?>
        <textarea
            id="<?php echo esc_attr($this->type); ?>_<?php echo esc_attr($name); ?>"
            class="large-text code"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            rows="4"><?php echo esc_attr($currentValue); ?></textarea>
        <?php
    }

    /**
     * @param string $key
     * @param array  $field
     *
     * @return string
     */
    public static function resolveFieldTitle(string $key, array $field): string
    {
        if (!self::$crmFields) {
            $crmFields = new CrmFields();
            self::$crmFields = $crmFields->fields;
        }

        if (isset(self::$crmFields[$key])) {
            return self::$crmFields[$key];
        }

        if (isset($field['formLabel'])) {
            return $field['formLabel'];
        }

        if (!empty($field['title'])) {
            return $field['title'];
        }

        return $key;
    }

    private function getStatusListByType($type)
    {
        $statusList = get_option(Bootstrap::STATUS_LIST_KEY, []);

        if (empty($statusList)) {
            return [];
        }

        $returnList = [];

        if ($type == 'DEAL_STAGE') {
            // Default pipeline
            foreach ($statusList as $status) {
                if ($status['ENTITY_ID'] === $type) {
                    $returnList[$status['STATUS_ID']] = esc_html__('Default pipeline', 'wc-bitrix24-integration')
                        . ' - '
                        . $status['NAME'];
                }
            }

            $pipelines = get_option(Bootstrap::DEAL_CATEGORY_LIST_KEY, []);

            if ($pipelines) {
                foreach ($pipelines as $pipeline) {
                    foreach ($statusList as $status) {
                        if ($status['ENTITY_ID'] === $type . '_' . $pipeline['ID']) {
                            $returnList[$status['STATUS_ID']] = $pipeline['NAME'] . ' - ' . $status['NAME'];
                        }
                    }
                }
            }
        } else {
            foreach ($statusList as $status) {
                if ($status['ENTITY_ID'] === $type) {
                    $returnList[$status['STATUS_ID']] = $status['NAME'];
                }
            }
        }

        return $returnList;
    }

    private function generateOptions($currentValue)
    {
        ?>
        <option value=""><?php esc_html_e('Not chosen', 'wc-bitrix24-integration'); ?></option>
        <optgroup label="<?php esc_html_e('Prepared values (main fields)', 'wc-bitrix24-integration'); ?>">
            <?php
            foreach ($this->getMainPreparedOptions() as $value => $label) {
                echo '<option value="'
                    . esc_attr($value)
                    . '"'
                    . ($currentValue == $value ? ' selected' : '')
                    . '>'
                    . esc_html($label)
                    . '</option>';
            } ?>
        </optgroup>
        <optgroup label="<?php esc_html_e('Prepared values (additional)', 'wc-bitrix24-integration'); ?>">
            <?php
            foreach ($this->getAdditionalPreparedOptions() as $value => $label) {
                echo '<option value="'
                    . esc_attr($value)
                    . '"'
                    . ($currentValue == $value ? ' selected' : '')
                    . '>'
                    . esc_html($value . ' - ' . $label)
                    . '</option>';
            } ?>
        </optgroup>
        <optgroup label="<?php esc_html_e('Prepared values (buyer user data)', 'wc-bitrix24-integration'); ?>">
            <?php
            foreach ($this->getBuyerPreparedOptions() as $value => $label) {
                echo '<option value="'
                    . esc_attr($value)
                    . '"'
                    . ($currentValue == $value ? ' selected' : '')
                    . '>'
                    . esc_html($value . ' - ' . $label)
                    . '</option>';
            } ?>
        </optgroup>
        <?php
        $attributes = $this->getProductAttributesPreparedOptions();

        if ($attributes) {
            ?>
            <optgroup label="<?php esc_html_e('Prepared values (to pass values of product attributes)', 'wc-bitrix24-integration'); ?>">
                <?php
                foreach ($attributes as $value => $label) {
                    echo '<option value="'
                        . esc_attr($value)
                        . '"'
                        . ($currentValue == $value ? ' selected' : '')
                        . '>'
                        . esc_html($label)
                        . '</option>';
                } ?>
            </optgroup>
        <?php
        } ?>
        <optgroup label="<?php esc_html_e('Raw meta values', 'wc-bitrix24-integration'); ?>">
            <?php
            if ($this->rawMetaKeys) {
                foreach ($this->rawMetaKeys as $metaKey) {
                    ?>
                    <option value="<?php echo esc_attr($metaKey); ?>" <?php echo $currentValue == $metaKey ? 'selected' : ''; ?>>
                        <?php echo esc_html($metaKey); ?>
                    </option>
                    <?php
                }
            } ?>
        </optgroup>
        <?php
    }

    private function getMainPreparedOptions()
    {
        $optionList = [];

        $optionList['billing_full_name'] = 'billing_full_name - ' . esc_html__('Full name (last name + first name)', 'wc-bitrix24-integration');
        $optionList['billing_full_name_backward'] = 'billing_full_name_backward - ' . esc_html__('Full name (first name + last name)', 'wc-bitrix24-integration');

        foreach ((array) $this->billing as $value => $field) {
            $optionList[$value] = $value . (isset($field['label']) ? ' - ' . $field['label'] : '');
        }

        $optionList['shipping_full_name'] = 'shipping_full_name - ' . esc_html__('Full name (last name + first name)', 'wc-bitrix24-integration');
        $optionList['shipping_full_name_backward'] = 'shipping_full_name_backward - ' . esc_html__('Full name (first name + last name)', 'wc-bitrix24-integration');

        foreach ((array) $this->shipping as $value => $field) {
            $optionList[$value] = $value . (isset($field['label']) ? ' - ' . $field['label'] : '');
        }

        return $optionList;
    }

    private function getAdditionalPreparedOptions()
    {
        $optionList = [];

        $optionList['order_coupon_list'] = esc_html__('Order coupon list', 'wc-bitrix24-integration');
        $optionList['order_id'] = esc_html__('Order number', 'wc-bitrix24-integration');
        $optionList['order_total_weight'] = esc_html__('Total weight of items in the order', 'wc-bitrix24-integration');
        $optionList['shipping_method_title'] = esc_html__('Name of shipping method', 'wc-bitrix24-integration');
        $optionList['payment_method_title'] = esc_html__('Name of payment method', 'wc-bitrix24-integration');
        $optionList['order_edit_admin_link'] = esc_html__('Link to order in the admin panel', 'wc-bitrix24-integration');

        if (class_exists('\Dokan_Vendor')) {
            $optionList['dokan_vendor'] = esc_html__('Dokan Vendor', 'wc-bitrix24-integration');
        }

        if (class_exists('\WCMp')) {
            $optionList['wc_marketplace_vendor'] = esc_html__('WC Marketplace Vendor', 'wc-bitrix24-integration');
        }

        if (defined('WOO_VOU_META_PREFIX')) {
            $optionList['voucher_code'] = esc_html__('Voucher code', 'wc-bitrix24-integration');
        }

        if (class_exists('Atum\Inc\Helpers')) {
            $optionList['atum_items_purchase_amount'] = esc_html__('ATUM (order items purchase amount)', 'wc-bitrix24-integration');
        }

        $optionList['utm_source'] = esc_html__('Utm source', 'wc-bitrix24-integration');
        $optionList['utm_medium'] = esc_html__('Utm medium', 'wc-bitrix24-integration');
        $optionList['utm_campaign'] = esc_html__('Utm campaign', 'wc-bitrix24-integration');
        $optionList['utm_content'] = esc_html__('Utm content', 'wc-bitrix24-integration');
        $optionList['utm_term'] = esc_html__('Utm term', 'wc-bitrix24-integration');

        $optionList['gaClientID'] = esc_html__('Google client ID', 'wc-bitrix24-integration');
        $optionList['yandexClientID'] = esc_html__('Yandex client ID', 'wc-bitrix24-integration');
        $optionList['roistat_visit'] = esc_html__('Roistat visit cookie value', 'wc-bitrix24-integration');
        $optionList['_fbp'] = esc_html__('Facebook "_fbp" cookie value', 'wc-bitrix24-integration');
        $optionList['_fbc'] = esc_html__('Facebook "_fbc" cookie value', 'wc-bitrix24-integration');

        $optionList['first_product_title'] = esc_html__('Name of the first item in the order', 'wc-bitrix24-integration');
        $optionList['order_product_sku_list'] = esc_html__('List sku separated by commas', 'wc-bitrix24-integration');
        $optionList['order_product_titles_list'] = esc_html__('List title separated by commas', 'wc-bitrix24-integration');

        return $optionList;
    }

    private function getBuyerPreparedOptions()
    {
        return [
            'customer_user_id' => esc_html__('Customer User ID', 'wc-bitrix24-integration'),
            'customer_username' => esc_html__('Customer Username', 'wc-bitrix24-integration'),
            'customer_date_created' => esc_html__('Customer Date Created', 'wc-bitrix24-integration'),
            'customer_role' => esc_html__('Customer Role', 'wc-bitrix24-integration'),
            'customer_total_orders' => esc_html__('Customer Total Orders', 'wc-bitrix24-integration'),
            'customer_total_spent' => esc_html__('Customer Total Spent', 'wc-bitrix24-integration'),
            'customer_last_order_date' => esc_html__('Customer last order date', 'wc-bitrix24-integration'),
        ];
    }

    private function getProductAttributesPreparedOptions()
    {
        $optionList = [];

        $attributes = \wc_get_attribute_taxonomies();

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $optionList['wciwtb_' . $attribute->attribute_name] = $attribute->attribute_name
                    . ' - ' . $attribute->attribute_label;
            }
        }

        return $optionList;
    }
}
