<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Crm;

class ItglxWcBitrix24AjaxValidateWebhook
{
    public function __construct()
    {
        add_action('wp_ajax_itglxWcBitrix24AjaxValidateWebhook', [$this, 'actionProcessing']);
    }

    public function actionProcessing()
    {
        if (!current_user_can('manage_options')) {
            exit;
        }

        $response = '';
        $webhook = isset($_POST['webhook']) ? trim(wp_unslash($_POST['webhook'])) : '';
        $webhook = trailingslashit($webhook);

        if (empty($webhook)) {
            $response = sprintf(
                '<div data-ui-component="wcbitrix24notice" class="error notice notice-error is-dismissible"><p><strong>%1$s</strong>: %2$s</p></div>',
                esc_html__('ERROR', 'wc-bitrix24-integration'),
                esc_html__('To integrate with Bitrix24, your must fill webhook field.', 'wc-bitrix24-integration')
            );
        } elseif (filter_var($webhook, FILTER_VALIDATE_URL) === false) {
            $response = sprintf(
                '<div data-ui-component="wcbitrix24notice" class="error notice notice-error"><p><strong>%1$s</strong>: %2$s</p></div>',
                esc_html__('ERROR', 'wc-bitrix24-integration'),
                esc_html__('Web hook url is not valid.', 'wc-bitrix24-integration')
            );
        } else {
            $setting = get_option(Bootstrap::OPTIONS_KEY, []);
            $setting['webhook'] = $webhook;

            update_option(Bootstrap::OPTIONS_KEY, $setting);

            $check = Crm::checkConnection();

            if ($check < 3) {
                if ($check === 1) {
                    $response = sprintf(
                        '<div data-ui-component="wcbitrix24notice" class="error notice notice-error"><p><strong>%1$s</strong>: %2$s</p></div>',
                        esc_html__('ERROR', 'wc-bitrix24-integration'),
                        esc_html__('Insufficient permissions. Check CRM settings.', 'wc-bitrix24-integration')
                    );
                } elseif ($check === 2) {
                    $response = sprintf(
                        '<div data-ui-component="wcbitrix24notice" class="error notice notice-error"><p><strong>%1$s</strong>: %2$s</p></div>',
                        esc_html__('ERROR', 'wc-bitrix24-integration'),
                        esc_html__('Response CRM is not valid. Please check web hook link.', 'wc-bitrix24-integration')
                    );
                }
            } else {
                Crm::updateInformation();

                $response = sprintf(
                    '<div data-ui-component="wcbitrix24notice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
                    esc_html__('Webhook check is successfully.', 'wc-bitrix24-integration')
                );
            }
        }

        echo $response;

        exit;
    }
}
