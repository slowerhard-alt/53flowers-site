<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin;

use Itgalaxy\PluginCommon\ActionSchedulerHelper;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions\ItglxWcBitrix24AjaxBulkOrdersSent;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions\ItglxWcBitrix24AjaxClearLog;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions\ItglxWcBitrix24AjaxClearSendQueue;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions\ItglxWcBitrix24AjaxRemoveLinksWithOrders;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions\ItglxWcBitrix24AjaxSaveSettings;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions\ItglxWcBitrix24AjaxValidateWebhook;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions\LicenseAjaxAction;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts\AdditionalSection;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts\CompanySettings;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts\ContactSettings;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts\DealSettings;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts\LeadSettings;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts\LicenseSection;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Crm;

class WcSettingsPage
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addSubmenu'], 1000); // 1000 - fix priority for Admin Menu Editor
        add_action('admin_notices', [$this, 'notice']);

        if (isset($_GET['page']) && $_GET['page'] === Bootstrap::OPTIONS_KEY) {
            add_action('admin_enqueue_scripts', function () {
                wp_enqueue_style('jquery-ui-tabs-bx-style', Bootstrap::$common->assetsHelper->getPathAssetFile('/admin/css/app.css'), false, false);
                wp_enqueue_script('wc-bitrix24-admin-js', Bootstrap::$common->assetsHelper->getPathAssetFile('/admin/js/app.js'), false, false);
            });
        }

        new ItglxWcBitrix24AjaxValidateWebhook();
        new ItglxWcBitrix24AjaxSaveSettings();
        new ItglxWcBitrix24AjaxClearLog();
        new ItglxWcBitrix24AjaxRemoveLinksWithOrders();
        new ItglxWcBitrix24AjaxBulkOrdersSent();
        new ItglxWcBitrix24AjaxClearSendQueue();
        new LicenseAjaxAction();
    }

    public function notice()
    {
        if (\get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY)) {
            return;
        }

        echo sprintf(
            '<div class="notice notice-error" data-ui-component="itglx-license-notice"><p><strong>%1$s</strong>: %2$s <a href="%3$s">%4$s</a></p></div>',
            esc_html__('WooCommerce - Bitrix24 CRM - Integration', 'wc-bitrix24-integration'),
            esc_html__(
                'Please verify the license key on the plugin settings page - ',
                'wc-bitrix24-integration'
            ),
            esc_url(admin_url() . 'admin.php?page=wc-bitrix24-integration-settings#wcbx24-license-verify'),
            esc_html__('open', 'wc-bitrix24-integration')
        );
    }

    public function addSubmenu()
    {
        add_submenu_page(
            'woocommerce',
            esc_html__('Bitrix24', 'wc-bitrix24-integration'),
            esc_html__('Bitrix24', 'wc-bitrix24-integration'),
            'manage_woocommerce',
            Bootstrap::OPTIONS_KEY,
            [$this, 'settingsPage']
        );
    }

    public function settingsPage()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        if (
            \get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY)
            && !class_exists('\YahnisElsts\PluginUpdateChecker\v5\PucFactory')
        ) {
            echo sprintf(
                '<div class="notice notice-error"><p><strong>%1$s</strong>: %2$s</p></div>',
                esc_html__('WooCommerce - Bitrix24 CRM - Integration', 'wc-bitrix24-integration'),
                esc_html__(
                    'Not loaded `PucFactory`. Plugin updates not working.',
                    'wc-bitrix24-integration'
                )
            );
        }

        if (isset($_POST['wcBitrix24ReloadFieldsCache'])) {
            Crm::updateInformation();

            echo sprintf(
                '<div data-ui-component="wcbitrix24notice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                esc_html__('Fields cache updated successfully.', 'wc-bitrix24-integration')
            );
        }

        if (!empty($settings['type']) && !empty($settings['webhook'])) {
            $showNotice = false;

            if ($settings['type'] === 'lead') {
                if (empty($settings['lead_statuses']) || empty(array_filter($settings['lead_statuses']))) {
                    $showNotice = true;
                }
            } elseif (empty($settings['deal_statuses']) || empty(array_filter($settings['deal_statuses']))) {
                $showNotice = true;
            }

            if ($showNotice) {
                echo sprintf(
                    '<div class="error notice notice-error"><p>%1$s</p></div>',
                    esc_html__(
                        'Please note that `Status mapping` is not configured, orders will not be sent.',
                        'wc-bitrix24-integration'
                    )
                );
            }
        } ?>
        <div id="poststuff" class="woocommerce-reports-wrap halved">
            <h1><?php esc_html_e('Integration settings', 'wc-bitrix24-integration'); ?></h1>
            <p>
                <?php
                echo sprintf(
                    '%1$s <a href="%2$s" target="_blank">%3$s</a>.',
                    esc_html__('Plugin documentation: ', 'wc-bitrix24-integration'),
                    esc_html__('https://itgalaxy.company/en/software/woocommerce-bitrix24-crm-integration/woocommerce-bitrix24-crm-integration-instructions/', 'wc-bitrix24-integration'),
                    esc_html__('open', 'wc-bitrix24-integration')
                ); ?>
            </p>

            <form method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enabled">
                                <?php esc_html_e('Enable', 'wc-bitrix24-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="hidden" value="0" name="enabled">
                            <input type="checkbox"
                                value="1"
                                <?php echo isset($settings['enabled']) && $settings['enabled'] == '1' ? 'checked' : ''; ?>
                                id="enabled"
                                name="enabled">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="webhook">
                                <?php esc_html_e('Inbound web hook', 'wc-bitrix24-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                aria-required="true"
                                value="<?php echo isset($settings['webhook']) ? esc_attr($settings['webhook']) : ''; ?>"
                                id="webhook"
                                placeholder="https://your.bitrix24.ru/rest/*/**********/"
                                name="webhook"
                                class="large-text">
                            <p class="description">
                                <?php esc_html_e('The following permissions are required: CRM, Chat and Notifications', 'wc-bitrix24-integration'); ?>
                            </p>
                            <p class="submit">
                                <input type="submit"
                                    class="button button-primary"
                                    data-ui-component="validate-webhook"
                                    value="<?php esc_attr_e('Check webhook', 'wc-bitrix24-integration'); ?>"
                                    name="check">
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="type">
                                <?php esc_html_e('Type', 'wc-bitrix24-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <select name="type" id="type">
                                <option value="lead" <?php echo !isset($settings['type']) || $settings['type'] == 'lead' ? 'selected' : ''; ?>>
                                    <?php esc_html_e('Lead', 'wc-bitrix24-integration'); ?>
                                </option>
                                <option value="dealcontact" <?php echo isset($settings['type']) && $settings['type'] == 'dealcontact' ? 'selected' : ''; ?>>
                                    <?php esc_html_e('Deal + Contact', 'wc-bitrix24-integration'); ?>
                                </option>
                                <option value="dealcontact_alwayscreatenew" <?php echo isset($settings['type']) && $settings['type'] == 'dealcontact_alwayscreatenew' ? 'selected' : ''; ?>>
                                    <?php esc_html_e('Deal + Contact (without searching for an existing contact, always create a new one)', 'wc-bitrix24-integration'); ?>
                                </option>
                                <option value="dealcompany" <?php echo isset($settings['type']) && $settings['type'] == 'dealcompany' ? 'selected' : ''; ?>>
                                    <?php esc_html_e('Deal + Company', 'wc-bitrix24-integration'); ?>
                                </option>
                                <option value="deal" <?php echo isset($settings['type']) && $settings['type'] == 'deal' ? 'selected' : ''; ?>>
                                    <?php esc_html_e('Deal + Contact + Company', 'wc-bitrix24-integration'); ?>
                                </option>
                                <option value="contact_only" <?php echo isset($settings['type']) && $settings['type'] == 'contact_only' ? 'selected' : ''; ?>>
                                    <?php esc_html_e('Contact only', 'wc-bitrix24-integration'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="send_type">
                                <?php esc_html_e('Send type', 'wc-bitrix24-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <select name="send_type" id="send_type">
                                <option value="immediately" <?php echo empty($settings['send_type']) || $settings['send_type'] == 'immediately' ? 'selected' : ''; ?>>
                                    <?php esc_html_e('Immediately upon checkout', 'wc-bitrix24-integration'); ?>
                                </option>
                                <option value="wp_cron" <?php echo isset($settings['send_type']) && $settings['send_type'] == 'wp_cron' ? 'selected' : ''; ?>>
                                    <?php esc_html_e('WP Cron', 'wc-bitrix24-integration'); ?>
                                </option>
                            </select>
                            <?php if (!empty($settings['webhook']) && !empty($settings['send_type']) && $settings['send_type'] === 'wp_cron') { ?>
                                <p class="descripton">
                                    <?php esc_html_e('The number of registered order submit events pending', 'wc-bitrix24-integration'); ?>:
                                    <strong>
                                        <?php echo (int) ActionSchedulerHelper::getCountPendingActions(Bootstrap::CRON_TASK_SEND); ?>
                                    </strong>
                                </p>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enabled_logging">
                                <?php esc_html_e('Enable logging', 'wc-bitrix24-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="hidden" value="0" name="enabled_logging">
                            <input type="checkbox"
                                value="1"
                                <?php echo isset($settings['enabled_logging']) && $settings['enabled_logging'] == '1' ? 'checked' : ''; ?>
                                id="enabled_logging"
                                name="enabled_logging">
                            <br>
                            <small><?php echo esc_html(Bootstrap::$pluginLogFile); ?></small>
                            <hr>
                            <a href="<?php echo esc_url(admin_url() . '?' . Bootstrap::OPTIONS_KEY . '-logs-get'); ?>"
                                class="button"
                                target="_blank">
                                <?php echo esc_html__('Download log', 'wc-bitrix24-integration'); ?>
                            </a>
                            <a href="#" data-ui-component="itglx-wc-bx24-log-clear" class="button">
                                <?php echo esc_html__('Clear log', 'wc-bitrix24-integration'); ?>
                            </a>
                        </td>
                    </tr>
                </table>
                <?php if (!empty($settings['webhook'])) { ?>
                    <hr>
                    <input
                        type="submit"
                        class="button button-primary"
                        name="wcBitrix24ReloadFieldsCache"
                        value="<?php esc_html_e('Reload fields data from CRM', 'wc-bitrix24-integration'); ?>">
                    <hr>
                    <div id="tabs" data-ui-component="wc-bitrix24-setting-tabs">
                        <ul>
                            <li>
                                <a href="#lead-fields">
                                    <?php esc_html_e('Lead fields', 'wc-bitrix24-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#deal-fields">
                                    <?php esc_html_e('Deal fields', 'wc-bitrix24-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#contact-fields">
                                    <?php esc_html_e('Contact fields', 'wc-bitrix24-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#company-fields">
                                    <?php esc_html_e('Company fields', 'wc-bitrix24-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#status-mapping">
                                    <?php esc_html_e('Status mapping', 'wc-bitrix24-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#wc-bitrix24-additional">
                                    <?php esc_html_e('Additional', 'wc-bitrix24-integration'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#bitrix24-to-woocommerce">
                                    <?php esc_html_e('Bitrix24 -> WooCommerce', 'wc-bitrix24-integration'); ?>
                                </a>
                            </li>
                            <?php if (class_exists('\WC_Memberships_Loader')) { ?>
                            <li>
                                <a href="#woocommerce-memberships">
                                    <?php esc_html_e('WooCommerce Memberships', 'wc-bitrix24-integration'); ?>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                        <div id="lead-fields">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label for="add_admin_order_link_lead_comment">
                                                <?php $value = isset($settings['add_admin_order_link_lead_comment']) ? $settings['add_admin_order_link_lead_comment'] : ''; ?>
                                                <input type="hidden" name="add_admin_order_link_lead_comment" value="false">
                                                <input id="add_admin_order_link_lead_comment"
                                                    type="checkbox"
                                                    title="<?php esc_html_e('Add a link to the order on the site in the comment', 'wc-bitrix24-integration'); ?>"
                                                    <?php checked($value, 'true'); ?>
                                                    name="add_admin_order_link_lead_comment"
                                                    value="true">
                                                <?php esc_html_e('Add a link to the order on the site in the comment', 'wc-bitrix24-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="lead_not_send_product_list_only_summ">
                                                <?php $value = isset($settings['lead_not_send_product_list_only_summ']) ? $settings['lead_not_send_product_list_only_summ'] : ''; ?>
                                                <input type="hidden" name="lead_not_send_product_list_only_summ" value="false">
                                                <input id="lead_not_send_product_list_only_summ"
                                                       type="checkbox"
                                                       title="<?php esc_html_e('Do not fill out the list of products, only the lead amount', 'wc-bitrix24-integration'); ?>"
                                                    <?php checked($value, 'true'); ?>
                                                       name="lead_not_send_product_list_only_summ"
                                                       value="true">
                                                <?php esc_html_e('Do not fill out the list of products, only the lead amount', 'wc-bitrix24-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="lead_link_with_exists_contact">
                                                <?php $value = isset($settings['lead_link_with_exists_contact']) ? $settings['lead_link_with_exists_contact'] : ''; ?>
                                                <input id="lead_link_with_exists_contact"
                                                    type="checkbox"
                                                    <?php checked($value, 'true'); ?>
                                                    name="lead_link_with_exists_contact"
                                                    value="true">
                                                <?php esc_html_e('Link to existing contact (search by email/phone)', 'wc-bitrix24-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <?php LeadSettings::render($settings); ?>
                        </div>
                        <div id="deal-fields">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label for="add_admin_order_link_deal_comment">
                                                <?php $value = isset($settings['add_admin_order_link_deal_comment']) ? $settings['add_admin_order_link_deal_comment'] : ''; ?>
                                                <input type="hidden" name="add_admin_order_link_deal_comment" value="false">
                                                <input id="add_admin_order_link_deal_comment"
                                                    type="checkbox"
                                                    title="<?php esc_html_e('Add a link to the order on the site in the comment', 'wc-bitrix24-integration'); ?>"
                                                    <?php checked($value, 'true'); ?>
                                                    name="add_admin_order_link_deal_comment"
                                                    value="true">
                                                <?php esc_html_e('Add a link to the order on the site in the comment', 'wc-bitrix24-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="not_send_product_list_only_summ">
                                                <?php $value = isset($settings['not_send_product_list_only_summ']) ? $settings['not_send_product_list_only_summ'] : ''; ?>
                                                <input type="hidden" name="not_send_product_list_only_summ" value="false">
                                                <input id="not_send_product_list_only_summ"
                                                    type="checkbox"
                                                    title="<?php esc_html_e('Do not fill out the list of products, only the deal amount', 'wc-bitrix24-integration'); ?>"
                                                    <?php checked($value, 'true'); ?>
                                                    name="not_send_product_list_only_summ"
                                                    value="true">
                                                <?php esc_html_e('Do not fill out the list of products, only the deal amount', 'wc-bitrix24-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <?php DealSettings::render($settings); ?>
                        </div>
                        <div id="contact-fields">
                            <table>
                                <tr>
                                    <td>
                                        <label>
                                            <input type="checkbox"
                                                value="1"
                                                <?php echo isset($settings['enabled_contact']) && $settings['enabled_contact'] == '1' ? 'checked' : ''; ?>
                                                id="enabled_contact"
                                                name="enabled_contact">
                                                <?php esc_html_e('Create / update a contact when user registering / updates profile information.', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </td>
                                </tr>
                                <?php if (isset($settings['type']) && $settings['type'] !== 'dealcontact_alwayscreatenew') { ?>
                                    <tr>
                                        <td>
                                            <label>
                                                <input type="checkbox"
                                                    value="1"
                                                    <?php echo isset($settings['contact_update_exists']) && $settings['contact_update_exists'] == '1' ? 'checked' : ''; ?>
                                                    id="contact_update_exists"
                                                    name="contact_update_exists">
                                                    <?php esc_html_e('Update existing contact when submitting order data (search by phone and mail).', 'wc-bitrix24-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label>
                                                <input type="checkbox"
                                                       value="1"
                                                    <?php echo isset($settings['contact_responsible_by_deal']) && $settings['contact_responsible_by_deal'] == '1' ? 'checked' : ''; ?>
                                                       id="contact_responsible_by_deal"
                                                       name="contact_responsible_by_deal">
                                                <?php esc_html_e('When creating a contact, assign a responsible based on the deal (if specified).', 'wc-bitrix24-integration'); ?>
                                            </label>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <hr>
                            <?php ContactSettings::render($settings); ?>
                        </div>
                        <div id="company-fields">
                            <table>
                                <tr>
                                    <td>
                                        <label>
                                            <?php $value = isset($settings['company_update_exists']) ? $settings['company_update_exists'] : ''; ?>
                                            <input type="checkbox"
                                                id="company_update_exists"
                                                <?php checked($value, 'true'); ?>
                                                name="company_update_exists"
                                                value="true">
                                            <?php esc_html_e('Update existing company when submitting order data (search by phone / email).', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>
                                            <input type="checkbox"
                                                   value="1"
                                                <?php echo isset($settings['company_responsible_by_deal']) && $settings['company_responsible_by_deal'] == '1' ? 'checked' : ''; ?>
                                                   id="company_responsible_by_deal"
                                                   name="company_responsible_by_deal">
                                            <?php esc_html_e('When creating a company, assign a responsible based on the deal (if specified).', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                            <hr>
                            <?php CompanySettings::render($settings); ?>
                        </div>
                        <div id="status-mapping">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="resend_product_list">
                                            <?php esc_html_e('Resend product list', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="hidden" value="0" name="resend_product_list">
                                        <input type="checkbox"
                                            value="1"
                                            <?php echo isset($settings['resend_product_list']) && $settings['resend_product_list'] == '1' ? 'checked' : ''; ?>
                                            id="resend_product_list"
                                            name="resend_product_list">
                                        <small>
                                            <?php esc_html_e('Re-send products from the order to the Bitrix24 when the order status changes. It will be useful if you edit the order.', 'wc-bitrix24-integration'); ?>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="do_not_post_status_changes">
                                            <?php esc_html_e('Do not post status changes', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="hidden" value="0" name="do_not_post_status_changes">
                                        <input type="checkbox"
                                            value="1"
                                            <?php echo isset($settings['do_not_post_status_changes']) && $settings['do_not_post_status_changes'] == '1' ? 'checked' : ''; ?>
                                            id="do_not_post_status_changes"
                                            name="do_not_post_status_changes">
                                    </td>
                                </tr>
                            </table>
                            <hr>
                            <h3><?php esc_html_e('For lead', 'wc-bitrix24-integration'); ?></h3>
                            <table class="form-table">
                                <?php $leadStatuses = isset($settings['lead_statuses']) ? $settings['lead_statuses'] : [];

                    foreach (wc_get_order_statuses() as $status => $label) {
                        $value = str_replace('wc-', '', $status); ?>
                                    <tr>
                                    <td>
                                        <strong><?php echo esc_html($label); ?></strong>
                                        <br>
                                        (<?php echo esc_html($value); ?>)
                                    </td>
                                    <td>
                                    <?php $list = $this->getStatusListByType('STATUS'); ?>
                                    <select id="_lead_statuses_<?php echo esc_attr($value); ?>"
                                        name="lead_statuses[<?php echo esc_attr($value); ?>]">
                                    <option value=""><?php esc_html_e('Not chosen', 'wc-bitrix24-integration'); ?></option>
                                    <?php $currentValue = isset($leadStatuses[$value]) ? $leadStatuses[$value] : '';

                        foreach ((array) $list as $value => $name) {
                            echo '<option value="'
                                . esc_attr($value)
                                . '"'
                                . ($currentValue == $value || !isset($settings['lead_statuses']) && $name === reset($list) ? ' selected' : '')
                                . '>'
                                . esc_html($value . ' - ' . $name)
                                . '</option>';
                        } ?>
                                    </select>
                                    </td>
                                    </tr>
                                    <?php
                    }
                    ?>
                            </table>
                            <hr>
                            <h3><?php esc_html_e('For deal', 'wc-bitrix24-integration'); ?></h3>
                            <table class="form-table">
                                <?php $dealStatuses = isset($settings['deal_statuses']) ? $settings['deal_statuses'] : [];

                    foreach (wc_get_order_statuses() as $status => $label) {
                        $value = str_replace('wc-', '', $status); ?>
                                    <tr>
                                    <td>
                                        <strong><?php echo esc_html($label); ?></strong>
                                        <br>
                                        (<?php echo esc_html($value); ?>)
                                    </td>
                                    <td>
                                    <?php $list = $this->getStatusListByType('DEAL_STAGE'); ?>
                                    <select id="_deal_statuses_<?php echo esc_attr($value); ?>"
                                        name="deal_statuses[<?php echo esc_attr($value); ?>]">
                                    <option value=""><?php esc_html_e('Not chosen', 'wc-bitrix24-integration'); ?></option>
                                    <?php
                        $currentValue = isset($dealStatuses[$value]) ? $dealStatuses[$value] : '';

                        foreach ((array) $list as $pipelineName => $pipeline) {
                            $pipelineName = explode('||||', $pipelineName);
                            $pipelineName = $pipelineName[0];

                            echo '<optgroup label="' . esc_attr($pipelineName) . '">';

                            foreach ($pipeline as $statusID => $status) {
                                ?>
                                            <option value="<?php echo esc_attr($statusID); ?>"
                                                <?php
                                    echo !isset($settings['deal_statuses'])
                                    && $pipeline === reset($list)
                                    && $status === reset($pipeline)
                                        ? 'selected'
                                        : ''; ?>
                                                <?php selected($currentValue, $statusID); ?>>
                                                <?php echo esc_attr($status . ' (id - ' . $statusID . ')'); ?>
                                            </option>
                                            <?php
                            }

                            echo '</optgroup>';
                        } ?>
                                    </select>
                                    </td>
                                    </tr>
                                    <?php
                    }
                    ?>
                            </table>
                        </div>
                        <div id="wc-bitrix24-additional">
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <label for="do_not_add_sku_in_product_name">
                                            <?php $value = isset($settings['do_not_add_sku_in_product_name']) ? $settings['do_not_add_sku_in_product_name'] : ''; ?>
                                            <input type="hidden" name="do_not_add_sku_in_product_name" value="0">
                                            <input id="do_not_add_sku_in_product_name"
                                                   type="checkbox"
                                                   title="<?php esc_html_e('Don\'t add SKU to the product name', 'wc-bitrix24-integration'); ?>"
                                                <?php checked($value, '1'); ?>
                                                   name="do_not_add_sku_in_product_name"
                                                   value="1">
                                            <?php esc_html_e('Don\'t add SKU to the product name', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="product_create_send_image">
                                            <input id="product_create_send_image" type="checkbox"
                                                <?php checked($settings['product_create_send_image'] ?? '', '1'); ?>
                                                name="product_create_send_image"
                                                value="1">
                                            <?php esc_html_e('Send product image', 'wc-bitrix24-integration'); ?>
                                        </label>
                                        <br>
                                        <small>
                                            <?php esc_html_e('If a product on the site has a main image, then when creating a product in Bitrix24, this image will be added to it.', 'wc-bitrix24-integration'); ?>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="update_product_in_crm">
                                            <?php $value = isset($settings['update_product_in_crm']) ? $settings['update_product_in_crm'] : ''; ?>
                                            <input type="hidden" name="update_product_in_crm" value="0">
                                            <input id="update_product_in_crm"
                                                   type="checkbox"
                                                <?php checked($value, '1'); ?>
                                                   name="update_product_in_crm"
                                                   value="1">
                                            <?php esc_html_e('Update the product (name) in Bitrix24 when updating on the site', 'wc-bitrix24-integration'); ?>
                                        </label>
                                        <br>
                                        <small>
                                            <?php esc_html_e('If the product on the site has a link with the product in the CRM, then when it is updated, it will be updated in Bitrix24.', 'wc-bitrix24-integration'); ?>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="check_exists_product_if_has_link_by_id">
                                            <?php $value = isset($settings['check_exists_product_if_has_link_by_id']) ? $settings['check_exists_product_if_has_link_by_id'] : ''; ?>
                                            <input type="hidden" name="check_exists_product_if_has_link_by_id" value="0">
                                            <input id="check_exists_product_if_has_link_by_id"
                                                   type="checkbox"
                                                <?php checked($value, '1'); ?>
                                                   name="check_exists_product_if_has_link_by_id"
                                                   value="1">
                                            <?php esc_html_e('Checking the existence of a product, if there is already a link by ID', 'wc-bitrix24-integration'); ?>
                                        </label>
                                        <br>
                                        <small>
                                            <?php esc_html_e('It may be useful to re-form a link if you have deleted an item from CRM.', 'wc-bitrix24-integration'); ?>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="do_not_send_data_order_notes_to_crm_feed">
                                            <?php $value = isset($settings['do_not_send_data_order_notes_to_crm_feed']) ? $settings['do_not_send_data_order_notes_to_crm_feed'] : ''; ?>
                                            <input id="do_not_send_data_order_notes_to_crm_feed"
                                                   type="checkbox"
                                                <?php checked($value, '1'); ?>
                                                   name="do_not_send_data_order_notes_to_crm_feed"
                                                   value="1">
                                            <?php esc_html_e('Do not send data from `Order notes` to the deal / lead crm feed', 'wc-bitrix24-integration'); ?>
                                        </label>
                                        <br>
                                        <small>
                                            <?php esc_html_e('By default, entries that appear in block `Order notes` will be sent as comments in the deal / lead crm feed. If you do not need it, then you can disable it.', 'wc-bitrix24-integration'); ?>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="do_not_create_bitrix24_notify">
                                            <?php $value = isset($settings['do_not_create_bitrix24_notify']) ? $settings['do_not_create_bitrix24_notify'] : ''; ?>
                                            <input id="do_not_create_bitrix24_notify"
                                                   type="checkbox"
                                                <?php checked($value, '1'); ?>
                                                   name="do_not_create_bitrix24_notify"
                                                   value="1">
                                            <?php esc_html_e('Do not create an additional notification in Bitrix24 when creating a lead/deal', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="bitrix24-to-woocommerce">
                            <p class="description">
                                <?php esc_html_e('If you want the order status to change when a deal stage / lead status change occurs in CRM.', 'wc-bitrix24-integration'); ?>
                            </p>
                            <hr>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="outbound-webhook">
                                            <?php esc_html_e('Outbound webhook (Application token)', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text"
                                            aria-required="true"
                                            value="<?php echo isset($settings['outbound-webhook']) ? esc_attr($settings['outbound-webhook']) : ''; ?>"
                                            id="outbound-webhook"
                                            placeholder="gs4eckydp7ff4ird84a3r04z6r5qow4r5"
                                            name="outbound-webhook"
                                            class="large-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="handler_url">
                                            <?php esc_html_e('Handler URL: ', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input
                                            type="text"
                                            readonly
                                            class="large-text"
                                            id="handler_url"
                                            value="<?php echo esc_url(site_url() . '/?' . Bootstrap::BITRIX24_HANDLER_GET_KEY); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php esc_html_e('Changing the order status on the site', 'wc-bitrix24-integration'); ?>
                                    </th>
                                    <td>
                                        <p class="description">
                                            <strong>
                                                <?php esc_html_e('For lead', 'wc-bitrix24-integration'); ?>
                                            </strong>
                                            <br>
                                            <?php esc_html_e('Event: Lead updated (ONCRMLEADUPDATE)', 'wc-bitrix24-integration'); ?>
                                        </p>
                                        <br>
                                        <p class="description">
                                            <strong>
                                                <?php esc_html_e('For deal', 'wc-bitrix24-integration'); ?>
                                            </strong>
                                            <br>
                                            <?php esc_html_e('Event: Deal updated (ONCRMDEALUPDATE)', 'wc-bitrix24-integration'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php esc_html_e('Deleting an order on the site when deleting a lead / deal', 'wc-bitrix24-integration'); ?>
                                    </th>
                                    <td>
                                        <?php $deleteAction = isset($settings['outbound-webhook-remove-action']) ? $settings['outbound-webhook-remove-action'] : ''; ?>
                                        <label>
                                            <?php esc_html_e('Action', 'wc-bitrix24-integration'); ?>:
                                            <select name="outbound-webhook-remove-action" id="outbound-webhook-remove-action">
                                                <option value="" <?php echo empty($deleteAction) ? 'selected' : ''; ?>>
                                                    <?php esc_html_e('Not chosen', 'wc-bitrix24-integration'); ?>
                                                </option>
                                                <option value="trash" <?php echo $deleteAction == 'trash' ? 'selected' : ''; ?>>
                                                    <?php esc_html_e('Move to trash', 'wc-bitrix24-integration'); ?>
                                                </option>
                                                <option value="completely" <?php echo $deleteAction == 'completely' ? 'selected' : ''; ?>>
                                                    <?php esc_html_e('Delete completely', 'wc-bitrix24-integration'); ?>
                                                </option>
                                            </select>
                                        </label>
                                        <p class="description">
                                            <strong>
                                                <?php esc_html_e('For lead', 'wc-bitrix24-integration'); ?>
                                            </strong>
                                            <br>
                                            <?php esc_html_e('Event: Lead deleted (ONCRMLEADDELETE)', 'wc-bitrix24-integration'); ?>
                                        </p>
                                        <br>
                                        <p class="description">
                                            <strong>
                                                <?php esc_html_e('For deal', 'wc-bitrix24-integration'); ?>
                                            </strong>
                                            <br>
                                            <?php esc_html_e('Event: Deal deleted (ONCRMDEALDELETE)', 'wc-bitrix24-integration'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php if (class_exists('\WC_Memberships_Loader')) { ?>
                        <div id="woocommerce-memberships">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="enabled_wc_membership">
                                            <?php esc_html_e('Enable WooCommerce Memberships integration', 'wc-bitrix24-integration'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="hidden" value="0" name="enabled_wc_membership">
                                        <input type="checkbox"
                                            value="1"
                                            <?php echo isset($settings['enabled_wc_membership']) && $settings['enabled_wc_membership'] == '1' ? 'checked' : ''; ?>
                                            id="enabled_wc_membership"
                                            name="enabled_wc_membership">
                                        <br>
                                        <p class="description">
                                            <?php esc_html_e('When enabled, a set of tags will be added to contacts in CRM, in accordance with their plan. The tag will be added when the plan is added, and also deleted when the subscription ends or is deleted.', 'wc-bitrix24-integration'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php esc_html_e('Memberships list field in contact', 'wc-bitrix24-integration'); ?>
                                    </th>
                                    <td>
                                        <select id="__wc_membership_field"
                                            title="wc_membership_field"
                                            name="wc_membership_field">
                                            <option value="">
                                                <?php esc_html_e('Not chosen', 'wc-bitrix24-integration'); ?>
                                            </option>
                                            <?php
                            $contactFields = (array) get_option(Bootstrap::CONTACT_FIELDS_KEY);

                            $currentValue = isset($settings['wc_membership_field'])
                                ? $settings['wc_membership_field']
                                : '';

                            foreach ($contactFields as $key => $field) {
                                // Not show read only fields
                                if ($field['isReadOnly'] === true) {
                                    continue;
                                }

                                if ($field['type'] !== 'enumeration' || empty($field['items'])) {
                                    continue;
                                }

                                $title = isset($field['formLabel']) ? $field['formLabel'] : $key;

                                echo '<option value="'
                                    . esc_attr($key)
                                    . '"'
                                    . ($currentValue == $key ? ' selected' : '')
                                    . '>'
                                    . esc_html($key . ' - ' . $title)
                                    . '</option>';
                            }
                            ?>
                                            </select>
                                            <p class="description">
                                                <?php esc_html_e('Only list or multiList field.', 'wc-bitrix24-integration'); ?>
                                            </p>
                                        </td>
                                    </tr>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                    <p class="submit">
                        <input type="submit"
                            class="button button-primary"
                            data-ui-component="wc-bitrix24-save-settings"
                            value="<?php esc_attr_e('Save settings', 'wc-bitrix24-integration'); ?>"
                            name="submit">
                    </p>
                <?php } ?>
            </form>
            <?php AdditionalSection::render(); ?>
            <?php LicenseSection::render(); ?>
        </div>
        <?php
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
                    $returnList[esc_html__('Default pipeline', 'wc-bitrix24-integration')][$status['STATUS_ID']] = $status['NAME'];
                }
            }

            $pipelines = get_option(Bootstrap::DEAL_CATEGORY_LIST_KEY, []);

            if (!empty($pipelines)) {
                foreach ($pipelines as $pipeline) {
                    foreach ($statusList as $status) {
                        if ($status['ENTITY_ID'] === $type . '_' . $pipeline['ID']) {
                            $isPipelineStatus = explode(':', $status['STATUS_ID']);

                            if (count($isPipelineStatus) !== 2) {
                                $status['STATUS_ID'] = $pipeline['ID'] . '||||' . $status['STATUS_ID'];
                            }

                            $returnList[$pipeline['NAME'] . '||||' . $pipeline['ID']][$status['STATUS_ID']] = $status['NAME'];
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
}
