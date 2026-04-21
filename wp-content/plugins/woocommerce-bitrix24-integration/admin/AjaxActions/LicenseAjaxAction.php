<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions;

use Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts\LicenseSection;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;

class LicenseAjaxAction
{
    public static $name = 'itglx/wc/bitrix24/license';

    public function __construct()
    {
        add_action('wp_ajax_' . self::$name, [$this, 'action']);
    }

    public function action()
    {
        if (!current_user_can('manage_woocommerce')) {
            exit;
        }

        if (isset($_POST['code'])) {
            $response = Bootstrap::$common->requester->code(
                isset($_POST['type']) && $_POST['type'] === 'verify' ? 'code_activate' : 'code_deactivate',
                trim(wp_unslash($_POST['code']))
            );

            if ($response['state'] == 'successCheck') {
                echo sprintf(
                    '<div class="updated notice notice-success" data-ui-component="itglx-license-notice"><p>%s</p></div>',
                    esc_html($response['message'])
                );
            } elseif ($response['message']) {
                echo sprintf(
                    '<div class="error notice notice-error" data-ui-component="itglx-license-notice"><p>%s</p></div>',
                    esc_html($response['message'])
                );
            }
        }

        LicenseSection::render();

        exit;
    }
}
