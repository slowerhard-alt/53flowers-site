<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes\SendActions;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Crm;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class OrderNoteSendAction
{
    /**
     * Hook name.
     *
     * @var string
     */
    public static $name = 'itglx/wc/bx24/send_order_note_to_crm';

    private static $instance = false;

    protected function __construct()
    {
        \add_action(self::$name, [$this, 'action']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param int $noteID Comment entry ID.
     *
     * @return void
     */
    public function action($noteID)
    {
        Helper::log('[note/cron] process send - ' . $noteID);

        $note = \get_comment($noteID);

        if (\is_wp_error($note) || empty($note)) {
            return;
        }

        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);
        $order = \wc_get_order($note->comment_post_ID);

        if ($settings['type'] === 'lead') {
            $bitrixEntryID = $order->get_meta('_wc_bitrix24_lead_id', true);
        } else {
            $bitrixEntryID = $order->get_meta('_wc_bitrix24_deal_id', true);
        }

        if (!$bitrixEntryID) {
            return;
        }

        Crm::sendTimelineComment(
            $bitrixEntryID,
            $settings['type'] === 'lead' ? 'lead' : 'deal',
            $note->comment_content
        );
    }
}
