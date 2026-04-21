<?php

/**
 * WP CLI Command for XML for Google Merchant Center.
 *
 * This class provides WP-CLI commands to generate, manage, and monitor XML feed creation
 * for the XFGMC plugin. Designed for automation, cron jobs, and server-side integration.
 *
 * @link       https://icopydoc.ru
 * @since      4.1.0
 * @version    4.3.0 (05-04-2026)
 *
 * @package    XFGMC
 * @subpackage XFGMC/includes/wp-cli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CLI command to generate and manage XML feed.
 *
 * Provides three main commands:
 *
 * - `wp xfgmc generate` — Step-by-step feed generation with progress bar (recommended for large feeds).
 * - `wp xfgmc quick` — Fast synchronous feed generation (suitable for small feeds).
 * - `wp xfgmc status` — Check current status and progress of feed generation.
 *
 * ### Examples
 *
 * Run step-by-step generation:
 *     wp xfgmc generate --feed_id=1
 *
 * Run quick generation:
 *     wp xfgmc quick --feed_id=1
 *
 * Check status:
 *     wp xfgmc status --feed_id=1
 *
 * Use in system cron with post-processing:
 *     0 3 * * * cd /var/www/site && wp xfgmc generate --feed_id=1 --path=/var/www/site --allow-root && sed -i 's|old-domain.com|new-domain.com|g' wp-content/uploads/feed-xml-1.xml && rsync -av wp-content/uploads/feed-xml-1.xml user@remote:/feeds/
 *
 * @since      4.1.0
 * @package    XFGMC
 * @subpackage XFGMC/includes/wp-cli
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class XFGMC_WP_CLI_Command extends WP_CLI_Command {

	/**
	 * List all XML feeds with their status and last generation time.
	 *
	 * Displays a table containing feed ID, URL, start/end timestamps, and current status.
	 * Useful for monitoring multiple feeds at once.
	 *
	 * ## EXAMPLES
	 *
	 *     wp xfgmc feedlist
	 * 
	 * @param array $args       Positional arguments (not used).
	 * @param array $assoc_args Associative arguments from CLI (not used).
	 * 
	 * @return void
	 */
	public function feedlist( $args, $assoc_args ) {

		// Предположим, у вас есть способ получить все feed_id
		// Например, через опции или базу
		$all_feed_ids = $this->get_all_feed_ids(); // ваш метод

		$table = [];
		foreach ( $all_feed_ids as $feed_id ) {
			$feed_url = common_option_get( 'xfgmc_feed_url', '', $feed_id, 'xfgmc' );
			$date_sborki_start = common_option_get( 'xfgmc_date_sborki_start', '', $feed_id, 'xfgmc' );
			$date_sborki_end = common_option_get( 'xfgmc_date_sborki_end', '', $feed_id, 'xfgmc' );
			$status_sborki = (int) common_option_get( 'xfgmc_status_sborki', '-1', $feed_id, 'xfgmc' );
			$status_map = [
				'-1' => 'Completed',
				'1' => 'Initializing',
				'2' => 'In progress',
				'3' => 'Finalizing',
				'4' => 'Archiving'
			];
			$status_text = $status_map[ $status_sborki ] ?? 'Unknown';

			$table[] = [
				'ID' => $feed_id,
				'Feed URL' => $feed_url,
				'Last Generated Start' => $date_sborki_start ?: '-',
				'Last Generated End' => $date_sborki_end ?: '-',
				'Status' => $status_text,
			];
		}

		WP_CLI\Utils\format_items(
			'table',
			$table,
			[ 'ID', 'Feed URL', 'Last Generated Start', 'Last Generated End', 'Status' ]
		);

	}

	/**
	 * Display current status of feed generation.
	 *
	 * Shows:
	 * - Current status code and description
	 * - Number of processed products
	 * - Start/end times
	 * - Progress percentage
	 *
	 * ## OPTIONS
	 *
	 * [--feed_id=<id>]
	 * : The feed ID to check.
	 * ---
	 * default: 1
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp xfgmc status --feed_id=1
	 *
	 * @param array $args       Positional arguments (not used).
	 * @param array $assoc_args Associative arguments from CLI.
	 *
	 * @return void
	 */
	public function status( $args, $assoc_args ) {

		$feed_id = WP_CLI\Utils\get_flag_value( $assoc_args, 'feed_id', '1' );

		$status_code = (int) common_option_get( 'xfgmc_status_sborki', -1, $feed_id, 'xfgmc' );
		$last_element = (int) univ_option_get( 'xfgmc_last_element_feed_' . $feed_id, 0 );
		$total_products = $this->get_total_products_count( $feed_id );
		$date_start = common_option_get( 'xfgmc_date_sborki_start', '', $feed_id, 'xfgmc' );
		$date_end = common_option_get( 'xfgmc_date_sborki_end', '', $feed_id, 'xfgmc' );

		$status_map = [
			-1 => 'Completed or stopped',
			1 => 'Started (initializing)',
			2 => 'In progress (processing products)',
			3 => 'Finalizing (writing footer)',
			4 => 'Archiving and finishing'
		];

		$status_text = isset( $status_map[ $status_code ] ) ? $status_map[ $status_code ] : 'Unknown';

		WP_CLI::line( "Feed ID: $feed_id" );
		WP_CLI::line( "Status: $status_code — $status_text" );
		WP_CLI::line( "Processed: $last_element / $total_products products" );
		WP_CLI::line( "Start time: " . ( $date_start ?: 'Not started' ) );
		WP_CLI::line( "End time: " . ( $date_end ?: 'Not completed' ) );

		if ( $total_products > 0 ) {
			$percent = round( ( $last_element / $total_products ) * 100, 1 );
			WP_CLI::line( "Progress: $percent%" );
		}

	}

	/**
	 * Retrieves all feed IDs configured in the plugin.
	 *
	 * Fetches the list of feed IDs from the stored settings array.
	 * Each key in 'xfgmc_settings_arr' corresponds to a feed ID.
	 *
	 * @return array List of feed IDs as strings.
	 */
	private function get_all_feed_ids() {

		$feed_ids = [];
		$settings_arr = univ_option_get( 'xfgmc_settings_arr' );
		$settings_arr_keys_arr = array_keys( $settings_arr );
		for ( $i = 0; $i < count( $settings_arr_keys_arr ); $i++ ) {
			$feed_ids[] = (string) $settings_arr_keys_arr[ $i ];
		}
		return $feed_ids;

	}

	/**
	 * Generates XML feed step-by-step with real-time status updates.
	 *
	 * Starts feed generation process and monitors it in a loop until completion.
	 * Uses incremental processing via cron manager to handle large product catalogs.
	 * Outputs progress information including percentage and processed product count.
	 *
	 * ## OPTIONS
	 *
	 * [--feed_id=<id>]
	 * : The ID of the feed to generate.
	 * ---
	 * default: 1
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp xfgmc generate --feed_id=1
	 *
	 * @param array $args       Positional arguments (not used).
	 * @param array $assoc_args Associative arguments from CLI.
	 * 
	 * @return void
	 */
	public function generate( $args, $assoc_args ) {

		if ( ! class_exists( 'XFGMC_Generation_XML' ) ) {
			WP_CLI::error( 'XFGMC_Generation_XML class not found. Plugin may be inactive.' );
			return;
		}

		$feed_id = WP_CLI\Utils\get_flag_value( $assoc_args, 'feed_id', '1' );

		// Получаем общее количество товаров
		$total_products = $this->get_total_products_count( $feed_id );
		if ( $total_products === 0 ) {
			WP_CLI::warning( "No products found for feed ID $feed_id." );
			return;
		}

		// Проверяем статус сборки
		$status_sborki = (string) common_option_get(
			'xfgmc_status_sborki',
			'-1',
			$feed_id,
			'xfgmc'
		);

		if ( $status_sborki !== '-1' ) {
			// HACK: почему то через WP_CLI::confirm был баг и `y` не прокатывал. Заменил на readline
			$input = readline(
				sprintf( 'Feed #%s. %s. %s? [y/N]:',
					$feed_id,
					__( 'This feed is currently being created', 'xml-for-google-merchant-center' ),
					__(
						'Are you sure you want to interrupt the current process and start generating from scratch',
						'xml-for-google-merchant-center'
					)
				)
			);
			// Останавить сборку, если она уже запущена?
			if ( strtolower( $input ) !== 'y' ) {
				// Да. Останавливаем сборку, если она уже запущена
				WP_CLI::warning( __( 'The action was canceled by the user', 'xml-for-google-merchant-center' ) );
				return;
			}
		}

		try {

			// wp_clear_scheduled_hook( 'xfgmc_cron_start_feed_creation', [ $feed_id ] );
			wp_clear_scheduled_hook( 'xfgmc_cron_sborki', [ $feed_id ] );

			// счётчик завершенных товаров в положение 0.
			univ_option_upd(
				'xfgmc_last_element_feed_' . $feed_id,
				'0',
				'no'
			);
			// сборку начали
			common_option_upd(
				'xfgmc_status_sborki',
				'1',
				'no',
				$feed_id,
				'xfgmc'
			);
			$date_sborki_start = current_time( 'Y-m-d H:i' );
			common_option_upd(
				'xfgmc_date_sborki_start',
				$date_sborki_start,
				'no',
				$feed_id,
				'xfgmc'
			);

			$cron_manager = new XFGMC_Cron_Manager();
			// Получаем общее количество товаров
			$total_products = $this->get_total_products_count( $feed_id );

			// Читаем время выполнения шага из настроек
			$script_execution_time = (int) common_option_get(
				'xfgmc_script_execution_time',
				'26',
				$feed_id,
				'xfgmc'
			);
			$sleep_interval = max( $script_execution_time + 2, 5 ); // минимум 5 сек

			WP_CLI::warning( sprintf( 'Feed #%s. %s %s %s',
				$feed_id,
				__( 'The maximum step execution according to the settings is', 'xml-for-google-merchant-center' ),
				$sleep_interval,
				__( 'seconds', 'xml-for-google-merchant-center' )
			) );

			while ( true ) {

				$start_time = microtime( true );
				// Проверяем статус сборки
				$status_sborki = (string) common_option_get(
					'xfgmc_status_sborki',
					'-1',
					$feed_id,
					'xfgmc'
				);

				switch ( $status_sborki ) {

					case '-1':

						$feed_url = common_option_get(
							'xfgmc_feed_url',
							'',
							$feed_id,
							'xfgmc'
						);
						wp_clear_scheduled_hook( 'xfgmc_cron_sborki', [ $feed_id ] );
						WP_CLI::success( sprintf( 'Feed #%s. %s! %s: %s.',
							$feed_id,
							__(
								'The creation of the feed has been completed successfully',
								'xml-for-google-merchant-center'
							),
							__(
								'Feed URL',
								'xml-for-google-merchant-center'
							),
							$feed_url
						) );
						return;

						break;
					case '1':

						WP_CLI::line( sprintf( 'Feed #%s. %s.',
							$feed_id,
							__( 'Creating feed headers', 'xml-for-google-merchant-center' )
						) );

						break;
					case '2':

						$last_element_feed = (int) univ_option_get(
							'xfgmc_last_element_feed_' . $feed_id,
							0
						);

						WP_CLI::line( sprintf( 'Feed #%s. %s. %s: %s / %s (%s%%)',
							$feed_id,
							__( 'Creating temporary feed files', 'xml-for-google-merchant-center' ),
							__( 'The number of processed products', 'xml-for-google-merchant-center' ),
							$last_element_feed,
							$total_products,
							round( ( $last_element_feed / $total_products ) * 100, 1 )
						) );

						break;
					case '3':

						WP_CLI::line( sprintf( 'Feed #%s. %s.',
							$feed_id,
							__( 'Gluing the feed', 'xml-for-google-merchant-center' )
						) );

						break;
					case '4':

						WP_CLI::line( sprintf( 'Feed #%s. %s...',
							$feed_id,
							__( 'Completing the assembly', 'xml-for-google-merchant-center' )
						) );

						break;

				}

				$r = $cron_manager->do_it_every_minute( $feed_id ); // прямой вызов
				usleep( 2000000 ); // 2 сек
				$execution_time = microtime( true ) - $start_time;
				WP_CLI::debug( sprintf( '%s sec',
					$feed_id,
					$execution_time
				) );

			}
		} catch (\Exception $e) {
			WP_CLI::error( "Feed generation failed: " . $e->getMessage() );
		}

	}

	/**
	 * Perform a quick synchronous feed generation.
	 *
	 * This method generates the entire feed in a single pass. Suitable for small feeds
	 * (< 1000 products). May cause timeout or memory issues with large catalogs.
	 *
	 * ## OPTIONS
	 *
	 * [--feed_id=<id>]
	 * : The feed ID to generate.
	 * ---
	 * default: 1
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp xfgmc quick --feed_id=1
	 *
	 * @param array $args       Positional arguments (not used).
	 * @param array $assoc_args Associative arguments from CLI.
	 *
	 * @return void
	 */
	public function quick( $args, $assoc_args ) {

		$feed_id = WP_CLI\Utils\get_flag_value( $assoc_args, 'feed_id', '1' );

		if ( ! class_exists( 'XFGMC_Generation_XML' ) ) {
			WP_CLI::error( 'XFGMC_Generation_XML class not found. Plugin may be inactive.' );
			return;
		}

		WP_CLI::line( "Starting QUICK feed generation for feed ID: $feed_id" );

		try {
			$generation = new XFGMC_Generation_XML( $feed_id );
			$generation->quick_generation();
			$feed_url = common_option_get(
				'xfgmc_feed_url',
				'',
				$feed_id,
				'xfgmc'
			);
			WP_CLI::success( sprintf( 'Feed #%s. %s! %s: %s.',
				$feed_id,
				__(
					'Quick feed build generation has been completed successfully',
					'xml-for-google-merchant-center'
				),
				__(
					'Feed URL',
					'xml-for-google-merchant-center'
				),
				$feed_url
			) );
		} catch (\Exception $e) {
			WP_CLI::error( "Quick feed generation failed: " . $e->getMessage() );
		}

	}

	/**
	 * Get the total number of published products for the feed.
	 *
	 * Applies filters like `xfgmc_f_query_args` to respect custom query logic.
	 * Counts only products that match the feed criteria and are publicly visible.
	 *
	 * @param string $feed_id The feed ID used to apply filters.
	 *
	 * @return int Total number of products that match the feed criteria.
	 */
	private function get_total_products_count( $feed_id ) {

		$args = apply_filters(
			'xfgmc_f_query_args',
			[
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids'
			],
			$feed_id
		);

		$query = new \WP_Query( $args );
		return $query->found_posts;

	}

}

WP_CLI::add_command( 'xfgmc', 'XFGMC_WP_CLI_Command' );