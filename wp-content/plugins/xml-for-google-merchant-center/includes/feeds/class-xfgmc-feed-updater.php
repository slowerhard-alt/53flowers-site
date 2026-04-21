<?php

/**
 * Handles feed updates triggered by product changes.
 *
 * @link       https://icopydoc.ru
 * @since      4.1.0
 * @version    4.1.0 (22-03-2026)
 *
 * @package    XFGMC
 * @subpackage XFGMC/includes/feeds
 */

/**
 * Class for managing feed updates when products are modified.
 *
 * Responsible for:
 * - Running feed update on product save
 * - Running feed update on stock change
 *
 * @package XFGMC
 * @subpackage XFGMC/includes/feeds
 */
class XFGMC_Feed_Updater {

	/**
	 * Checks whether the feed should be updated when a product is updated,
	 * and starts the feed generation process if needed.
	 *
	 * @param int $post_id The ID of the post being saved.
	 *
	 * @return void
	 */
	public static function run_feeds_upd( $post_id ) {

		$settings_arr = univ_option_get( 'xfgmc_settings_arr' );
		$settings_arr_keys_arr = array_keys( $settings_arr );
		for ( $i = 0; $i < count( $settings_arr_keys_arr ); $i++ ) {

			$feed_id = (string) $settings_arr_keys_arr[ $i ]; // ! для правильности работы важен тип string
			$run_cron = common_option_get(
				'xfgmc_run_cron',
				'disabled',
				$feed_id,
				'xfgmc'
			);
			$ufup = common_option_get(
				'xfgmc_ufup',
				'disabled',
				$feed_id,
				'xfgmc'
			);
			if ( $run_cron === 'disabled' || $ufup === 'disabled' ) {
				XFGMC_Error_Log::record( sprintf(
					'FEED #%1$s; INFO: %2$s ($run_cron = %3$s; $ufup = %4$s); %5$s: %6$s; %7$s: %8$s',
					$feed_id,
					__(
						'Creating a cache file is not required for this type',
						'xml-for-google-merchant-center'
					),
					$run_cron,
					$ufup,
					__( 'File', 'xml-for-google-merchant-center' ),
					'class-xfgmc-feed-updater.php',
					__( 'Line', 'xml-for-google-merchant-center' ),
					__LINE__
				) );
				continue;
			}

			$do_cash_file = common_option_get(
				'xfgmc_do_cash_file',
				'enabled',
				$feed_id, 'xfgmc'
			);
			if ( $do_cash_file === 'enabled' || $ufup === 'enabled' ) {
				// если в настройках включено создание кэш-файлов в момент сохранения товара
				// или нужно запускать обновление фида при перезаписи файла
				$result_get_unit_obj = new XFGMC_Get_Unit( $post_id, $feed_id );
				$result_xml = $result_get_unit_obj->get_result();
				// Remove hex and control characters from PHP string
				$result_xml = xfgmc_remove_special_characters( $result_xml );
				new XFGMC_Write_File(
					$result_xml,
					sprintf( '%s.tmp', $post_id ),
					$feed_id
				);
			}

			// нужно ли запускать обновление фида при перезаписи файла
			if ( $ufup === 'enabled' ) {
				$status_sborki = (int) common_option_get(
					'xfgmc_status_sborki',
					-1,
					$feed_id,
					'xfgmc'
				);
				if ( $status_sborki === -1 ) {
					XFGMC_Error_Log::record( sprintf(
						'FEED #%1$s; INFO: %2$s ($i = %3$s; $ufup = %4$s); %5$s: %6$s; %7$s: %8$s',
						$feed_id,
						__(
							'Starting a quick feed build',
							'xml-for-google-merchant-center'
						),
						$i,
						$ufup,
						__( 'File', 'xml-for-google-merchant-center' ),
						'class-xfgmc-feed-updater.php',
						__( 'Line', 'xml-for-google-merchant-center' ),
						__LINE__
					) );
					clearstatcache(); // очищаем кэш дат файлов
					$generation = new XFGMC_Generation_XML( $feed_id );
					$generation->quick_generation();
				}
			}

		} // end for

	}

	/**
	 * Fires when stock reduced to a specific line item.
	 * 
	 * Function for `woocommerce_reduce_order_item_stock` action-hook.
	 * 
	 * @param WC_Order_Item_Product $item Order item data.
	 * @param array $change  Change Details.
	 * @param WC_Order $order  Order data.
	 *
	 * @return void
	 */
	public function check_update_feed_stock_change( $item, $change, $order ) {

		$settings_arr = univ_option_get( 'xfgmc_settings_arr' );
		$settings_arr_keys_arr = array_keys( $settings_arr );
		for ( $i = 0; $i < count( $settings_arr_keys_arr ); $i++ ) {

			$feed_id = (string) $settings_arr_keys_arr[ $i ]; // ! для правильности работы важен тип string
			$run_cron = common_option_get(
				'xfgmc_run_cron',
				'disabled',
				$feed_id,
				'xfgmc'
			);
			$upd_feed_after_stock_change = common_option_get(
				'xfgmc_upd_feed_after_stock_change',
				'disabled',
				$feed_id,
				'xfgmc'
			);
			if ( $run_cron === 'disabled' || $upd_feed_after_stock_change === 'disabled' ) {
				continue;
			}
			if ( $upd_feed_after_stock_change === 'enabled' ) {
				$status_sborki = (int) common_option_get(
					'xfgmc_status_sborki',
					-1,
					$feed_id,
					'xfgmc'
				);
				if ( $status_sborki === -1 ) {
					$planning_result = XFGMC_Cron_Manager::cron_starting_feed_creation_task_planning( $feed_id );
					if ( true === $planning_result ) {
						XFGMC_Error_Log::record( sprintf( 'FEED #%1$s; %2$s; %3$s: %4$s; %5$s: %6$s',
							$feed_id,
							__(
								'After changing the stock product the task of creating the feed has been queued for completion',
								'xml-for-google-merchant-center'
							),
							__( 'File', 'xml-for-google-merchant-center' ),
							'class-xfgmc-feed-updater.php',
							__( 'Line', 'xml-for-google-merchant-center' ),
							__LINE__
						) );
					}
				}
			}

		}

	}

}