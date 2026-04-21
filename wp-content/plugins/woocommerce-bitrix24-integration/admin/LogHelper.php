<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;

class LogHelper
{
    public function __construct()
    {
        if (isset($_GET[Bootstrap::OPTIONS_KEY . '-logs-get'])) {
            add_action('admin_init', [$this, 'logsGet']);
        }
    }

    public function logsGet()
    {
        if (!current_user_can('manage_options')) {
            exit;
        }

        Bootstrap::$common->logger->logsGet();
    }
}
