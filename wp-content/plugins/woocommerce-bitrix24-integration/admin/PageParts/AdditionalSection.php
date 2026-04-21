<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Helper;

class AdditionalSection
{
    public static function render()
    {
        ?>
        <hr>
        <div class="postbox">
            <h3 class="hndle border-bottom text-uppercase">
                <?php esc_html_e('Additional', 'wc-bitrix24-integration'); ?>
            </h3>
            <div class="inside">
                <input type="submit"
                       class="button button-secondary"
                       data-alert-text="<?php esc_attr_e('Are you sure you want to do this? The action cannot be undone.', 'wc-bitrix24-integration'); ?>"
                       data-wait-text="<?php esc_attr_e('Wait...', 'wc-bitrix24-integration'); ?>"
                       data-ui-component="itglx-wc-bx24-remove-links-with-orders"
                       value="<?php esc_attr_e('Remove from all orders the link with the deal / lead from Bitrix24', 'wc-bitrix24-integration'); ?>"
                       name="submit">
                <br>
                <small>
                    <?php esc_html_e('You can use this if, for some reason, you want to remove information about the created deal / lead in Bitrix24 from all orders on the site, so you get a situation as if orders have not yet been sent to Bitrix24.', 'wc-bitrix24-integration'); ?>
                </small>
                <hr>
                <?php if (Helper::isEnabled()) { ?>
                    <?php $orders = get_option('bx_wc_bulk_order_sent_to_crm', []); ?>
                    <?php if (!empty($orders) && count($orders) > 0) { ?>
                        <?php
                        echo sprintf(
                            '<div style="margin-left: 0; margin-bottom: 5px;" class="updated notice notice-success"><p>%s %s</p></div>',
                            number_format_i18n(count($orders)),
                            esc_html__('orders are in the process of being sent to CRM', 'wc-bitrix24-integration')
                        );
                        ?>
                        <input type="submit"
                               class="button button-primary"
                               data-alert-text="<?php esc_attr_e('Are you sure you want to do this? This will clear the send queue.', 'wc-bitrix24-integration'); ?>"
                               data-wait-text="<?php esc_attr_e('Wait...', 'wc-bitrix24-integration'); ?>"
                               data-ui-component="itglx-wc-bx24-clear-send-queue"
                               value="<?php esc_attr_e('Clear send queue', 'wc-bitrix24-integration'); ?>"
                               name="submit">
                        <hr>
                    <?php } ?>
               <?php } ?>
                <input type="submit"
                       class="button button-secondary"
                       data-wait-text="<?php esc_attr_e('Wait...', 'wc-bitrix24-integration'); ?>"
                       data-ui-component="itglx-wc-bx24-bulk-orders-sent"
                       data-type="all"
                       value="<?php esc_attr_e('Register all orders for sending to the CRM', 'wc-bitrix24-integration'); ?>"
                       name="submit">
                <br><br>
                <input type="submit"
                       class="button button-secondary"
                       data-wait-text="<?php esc_attr_e('Wait...', 'wc-bitrix24-integration'); ?>"
                       data-ui-component="itglx-wc-bx24-bulk-orders-sent"
                       data-type="not-yet-sent"
                       value="<?php esc_attr_e('Register orders not yet sent to the CRM for sending to the CRM', 'wc-bitrix24-integration'); ?>"
                       name="submit">
                <br>
                <small>
                    <?php esc_html_e(
                        'You can use this to re-send data or to send old orders. This is the same as sending via bulk actions with orders in a list.',
                        'wc-bitrix24-integration'
                    ); ?>.
                </small>
            </div>
        </div>
        <?php
    }
}
