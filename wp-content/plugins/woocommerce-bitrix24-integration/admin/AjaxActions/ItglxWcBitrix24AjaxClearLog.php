<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\AjaxActions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;

class ItglxWcBitrix24AjaxClearLog
{
    public function __construct()
    {
        add_action('wp_ajax_itglxWcBitrix24AjaxClearLog', [$this, 'actionProcessing']);
    }

    public function actionProcessing()
    {
        if (!current_user_can('manage_options')) {
            exit;
        }

        Bootstrap::$common->logger->ajaxLogsClear();
    }
}
