<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\Actions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\SendActions\OrderNoteSendAction;

class OrderNoteAction
{
    private static $instance = false;

    protected function __construct()
    {
        if (!$this->isEnabled()) {
            return;
        }

        OrderNoteSendAction::getInstance();

        /**
         * @see https://developer.wordpress.org/reference/hooks/wp_insert_comment/
         */
        \add_action('wp_insert_comment', [$this, 'action'], PHP_INT_MAX, 2);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param int         $noteID Comment entry ID.
     * @param \WP_Comment $note
     */
    public function action($noteID, $note)
    {
        global $pagenow, $typenow;

        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);

        // if we work only with contacts, then there is no need to process order notes
        if (!empty($settings['type']) && $settings['type'] === 'contact_only') {
            return;
        }

        // if the call from the order list page
        if ($typenow === 'shop_order' && $pagenow === 'edit.php') {
            return;
        }

        // HPOS order list
        if (!empty($_GET['page']) && $_GET['page'] === 'wc-orders' && empty($_GET['id'])) {
            return;
        }

        // if this is not a note to the order
        if (!$note || $note->comment_type !== 'order_note') {
            return;
        }

        Helper::log('[note] register send - ' . $noteID . ', order - ' . $note->comment_post_ID);

        // register one time task
        \as_schedule_single_action(time(), OrderNoteSendAction::$name, [$noteID]);
    }

    /**
     * @return bool
     */
    private function isEnabled()
    {
        if (!Helper::isVerify()) {
            return false;
        }

        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);

        return Helper::isEnabled()
            && empty($settings['do_not_send_data_order_notes_to_crm_feed']);
    }
}
