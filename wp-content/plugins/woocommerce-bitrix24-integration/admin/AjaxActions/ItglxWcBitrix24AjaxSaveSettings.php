<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;

class ItglxWcBitrix24AjaxSaveSettings
{
    public function __construct()
    {
        add_action('wp_ajax_itglxWcBitrix24AjaxSaveSettings', [$this, 'actionProcessing']);
    }

    public function actionProcessing()
    {
        if (!current_user_can('manage_options')) {
            exit;
        }

        parse_str(trim(wp_unslash($_POST['form'])), $data);

        $setting = (array) get_option(Bootstrap::OPTIONS_KEY);
        $data['webhook'] = isset($setting['webhook']) ? $setting['webhook'] : '';

        update_option(Bootstrap::OPTIONS_KEY, $data);

        $response = sprintf(
            '<div data-ui-component="wcbitrix24notice" class="updated notice notice-success is-dismissible"><p>%s</p></div>',
            esc_html__('Settings successfully updated.', 'wc-bitrix24-integration')
        );

        echo $response;

        exit;
    }
}
