<?php
/**
 * Система логирования xs_store
 * Формат: [YYYY-MM-DD HH:MM:SS] [ACTION] message
 * Ротация: при превышении 1MB переименовываем в .log.1
 */

if (!function_exists("xs_log")) {
    function xs_log($action, $message) {
        $log_dir  = dirname(__FILE__) . "/../logs/";
        $log_file = $log_dir . "xs_store.log";
        $log_bak  = $log_dir . "xs_store.log.1";

        // Ротация
        if (file_exists($log_file) && filesize($log_file) > 1024 * 1024) {
            @rename($log_file, $log_bak);
        }

        $date    = date("Y-m-d H:i:s");
        $action  = strtoupper(trim($action));
        $line    = "[" . $date . "] [" . $action . "] " . $message . "\n";

        @file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
    }
}
