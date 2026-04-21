<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

use Itgalaxy\PluginCommon\AnalyticsHelper;
use Itgalaxy\PluginCommon\DependencyPluginChecker;
use Itgalaxy\PluginCommon\MainHelperLoader;
use Itgalaxy\PluginCommon\PluginActionLinks;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\LogHelper;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\Product\Bitrix24ProductDataTab;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\ProductVariation\Bitrix24IdFieldVariation;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\ShopOrderTableColumn;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\WcBulkOrderToCrm;
use Itgalaxy\Wc\Bitrix24\Integration\Admin\WcSettingsPage;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Actions\OrderNoteAction;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Actions\ProductUpdateAction;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Filters\WooCommerceDuplicateProductExcludeMeta;

class Bootstrap
{
    const PLUGIN_ID = '21320217';
    const PLUGIN_VERSION = '1.69.0-117';

    const OPTIONS_KEY = 'wc-bitrix24-integration-settings';
    const PURCHASE_CODE_OPTIONS_KEY = 'wc-bitrix24-purchase-code';

    const LEAD_FIELDS_KEY = '_wc-bitrix24-lead-fields';

    const DEAL_FIELDS_KEY = '_wc-bitrix24-deal-fields';
    const DEAL_CATEGORY_LIST_KEY = '_wc-bitrix24-deal-category-list';

    const TASK_FIELDS_KEY = '_wc-bitrix24-task-fields';
    const CONTACT_FIELDS_KEY = '_wc-bitrix24-contact-fields';
    const COMPANY_FIELDS_KEY = '_wc-bitrix24-company-fields';
    const STATUS_LIST_KEY = '_wc-bitrix24-status-list';

    const UTM_COOKIES = 'wc-bx24-utm-cookie';

    const BITRIX24_HANDLER_GET_KEY = 'itglx_wc_bx24_event_handler';

    const CRON = 'wc-bitrix24-cron';
    const CRON_TASK_SEND = 'bx_wc_single_order_sent_to_crm';
    const CRON_TASK_BULK_ORDERS = 'itglx/wc/bx24/bulk-order-sent';

    const DEPENDENCY_PLUGIN_LIST = ['woocommerce/woocommerce.php'];

    public static $plugin = '';

    /**
     * @var string Absolute path (with a trailing slash) to the plugin directory.
     */
    public static $pluginDir;

    /**
     * @var string URL to the plugin directory (with a trailing slash).
     */
    public static $pluginUrl;

    /**
     * @var string Absolute path to the file for log content.
     */
    public static $pluginLogFile;

    /**
     * @var MainHelperLoader
     */
    public static $common;

    private static $instance = false;

    protected function __construct($file)
    {
        if (!defined('WC_BITRIX24_PLUGIN_LOG_FILE')) {
            define('WC_BITRIX24_PLUGIN_LOG_FILE', wp_upload_dir()['basedir'] . '/logs/wcbx24_' . md5(get_option('siteurl')) . '.log');
        }

        self::$plugin = $file;
        self::$pluginDir = \plugin_dir_path(self::$plugin);
        self::$pluginUrl = \plugin_dir_url(self::$plugin);
        self::$pluginLogFile = WC_BITRIX24_PLUGIN_LOG_FILE;
        self::$common = new MainHelperLoader($this);

        \register_activation_hook(self::$plugin, [self::class, 'pluginActivation']);
        \register_deactivation_hook(self::$plugin, [self::class, 'pluginDeactivation']);

        if (!DependencyPluginChecker::isActivated(self::DEPENDENCY_PLUGIN_LIST)) {
            DependencyPluginChecker::showRequirementPluginsNotice(
                esc_html__('WooCommerce - Bitrix24', 'wc-bitrix24-integration'),
                self::DEPENDENCY_PLUGIN_LIST
            );

            return;
        }

        new Updater($this);
        new Cron();
        OrderToBitrix24::getInstance();
        CustomerToBitrix24::getInstance();
        OrderNoteAction::getInstance();
        ProductUpdateAction::getInstance();
        WcMembershipIntegration::getInstance();
        Bitrix24EventHandler::getInstance();

        // filters
        new WooCommerceDuplicateProductExcludeMeta();

        if (is_admin()) {
            add_action('plugins_loaded', function () {
                new ShopOrderTableColumn();
                new WcBulkOrderToCrm();
                new WcSettingsPage();
                new LogHelper();

                // product
                new Bitrix24ProductDataTab();

                // product variation
                new Bitrix24IdFieldVariation();
            });

            add_action('init', function () {
                new PluginActionLinks(
                    self::$plugin,
                    [
                        '<a href="' . admin_url() . 'admin.php?page=' . Bootstrap::OPTIONS_KEY . '">'
                        . esc_html__('Settings', 'wc-bitrix24-integration')
                        . '</a>',
                    ]
                );
            });
        }

        add_action('init', [$this, 'utmCookies']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);

        add_action('wp_ajax_wcBitrix24AjaxSetUtm', [$this, 'utmCookiesAjax']);
        add_action('wp_ajax_nopriv_wcBitrix24AjaxSetUtm', [$this, 'utmCookiesAjax']);
    }

    public static function getInstance($file)
    {
        if (!self::$instance) {
            self::$instance = new self($file);
        }

        return self::$instance;
    }

    public function utmCookies()
    {
        $utmParams = AnalyticsHelper::getUtmListFromUrl();

        if (!empty($utmParams)) {
            setcookie(self::UTM_COOKIES, \wp_json_encode($utmParams), time() + 86400, '/');
        }
    }

    public function utmCookiesAjax()
    {
        $utmParams = AnalyticsHelper::getUtmListFromUrl();

        if (!empty($utmParams)) {
            echo self::UTM_COOKIES
                . '='
                . urlencode(\wp_json_encode($utmParams))
                . '; path=/; max-age=86400';
            // escape ok
        }

        exit;
    }

    public function enqueueScripts()
    {
        wp_enqueue_script(
            'wc-bitrix24-theme-js',
            self::$common->assetsHelper->getPathAssetFile('/theme/js/app.js'),
            ['jquery'],
            false,
            true
        );
    }

    public static function pluginActivation()
    {
        self::$common->requester->call('plugin_activate');

        DependencyPluginChecker::activateHelper(
            self::$plugin,
            self::DEPENDENCY_PLUGIN_LIST,
            esc_html__('WooCommerce - Bitrix24', 'wc-bitrix24-integration')
        );

        self::$common->logger->prepare();
    }

    public static function pluginDeactivation()
    {
        self::$common->requester->call('plugin_deactivate');
        \wp_clear_scheduled_hook(self::CRON);
        \as_unschedule_action(self::CRON_TASK_BULK_ORDERS);
    }

    public static function pluginUninstall()
    {
        self::$common->requester->call('plugin_uninstall');
    }
}
