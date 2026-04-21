<?php

/**
 * This class adds metaboxes to the product edit page.
 *
 * @link       https://icopydoc.ru
 * @since      0.1.0
 * @version    6.0.0 (20-03-2025)
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 */

/**
 * This class adds metaboxes to the product edit page.
 *
 * Depends on: 
 *   - classes: `Y4YM_Error_Log`
 *   - functions: `common_option_get`, `common_option_upd`
 *   - constants: `Y4YMP_PLUGIN_VERSION`, `Y4YMP_PLUGIN_BASENAME`, `Y4YMP_PLUGIN_SLUG`.
 *
 * @package    Y4YMP
 * @subpackage Y4YMP/admin
 * @author     Maxim Glazunov <icopydoc@gmail.com>
 */
class Y4YMP_Metaboxes {

	/**
	 * The version of this plugin.
	 *
	 * @since 0.1.0
	 * @access private
	 * @var string $post_type Example: `post`, `product`
	 */
	public $post_type;

	/**
	 * This class adds metaboxes to the product edit page.
	 *
	 * @param string $post_type Example: `post`, `product`
	 */
	public function __construct( $post_type = 'product' ) {

		$this->post_type = $post_type;

		// add_action( 'add_meta_boxes', [ $this, 'add_metabox' ] );
		add_action( 'save_post_' . $this->post_type, [ $this, 'save_metabox' ] );
		add_action( 'admin_print_footer_scripts', [ $this, 'show_assets' ], 10, 999 );
		add_action( 'y4ymp_append_options_group', [ $this, 'render_metabox' ], 10, 999 );

	}

	/**
	 * Эта функция добавляет матабоксы.
	 */
	public function add_metabox() {

		add_meta_box(
			'box_info_company',
			'Информация о компании',
			[ $this, 'render_metabox' ],
			$this->post_type,
			'advanced',
			'high'
		);

	}

	/**
	 * Отображает метабокс на странице редактирования поста.
	 */
	public function render_metabox( $post ) {
		?>

		<p class="form-field form-table company-info">
			<label>План обучения<br />
				<span class="y4ymp_add">Добавить</span>
			</label>
			<i class="y4ymp_plans_list">
				<?php
				$input = '<i class="y4ymp_plans">
					<input 
						style="float: none; max-width: 800px"
						type="text"
						name="_plan_unit[]" 
						placeholder="Название этапа программы" 
						value="%s"><br/>
					<input 
						style="float: none; max-width: 800px"
						type="number"
						min="1"
						step="1"
						name="_plan_hour[]" 
						placeholder="Длительность этапа в часах" 
						value="%s"><br/>
					<textarea 
						style="height: 102px; float: none; max-width: 800px" 
						name="_plan_desc[]"
						placeholder="Описание содержания этапа программы" 
						rows="2" 
						cols="20">%s</textarea><br/>
					<span class="y4ymp_remove">Удалить</span><br/>
				</i>';
				$plan_unit = get_post_meta( $post->ID, '_plan_unit', true );
				$plan_hour = get_post_meta( $post->ID, '_plan_hour', true );
				$plan_desc = get_post_meta( $post->ID, '_plan_desc', true );
				if ( is_array( $plan_unit ) ) {
					$i = 0;
					foreach ( $plan_unit as $addr ) {
						printf(
							$input,
							esc_attr( $plan_unit[ $i ] ),
							esc_attr( $plan_hour[ $i ] ),
							esc_attr( $plan_desc[ $i ] )
						);
						$i++;
					}
				} else {
					printf( $input, '', '', '' );
				}
				?>
			</i>
		</p>
		<?php

	}

	/**
	 * Очищает и сохраняет значения полей.
	 */
	public function save_metabox( $post_id ) {

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( isset( $_POST['_plan_unit'] ) && is_array( $_POST['_plan_unit'] ) ) {
			$unit = $_POST['_plan_unit'];
			$unit = array_map( 'sanitize_text_field', $unit ); // очистка

			if ( $unit ) {
				update_post_meta( $post_id, '_plan_unit', $unit );
			} else {
				delete_post_meta( $post_id, '_plan_unit' );
			}
		}

		if ( isset( $_POST['_plan_hour'] ) && is_array( $_POST['_plan_hour'] ) ) {
			$unit = $_POST['_plan_hour'];
			$unit = array_map( 'sanitize_text_field', $unit ); // очистка

			if ( $unit ) {
				update_post_meta( $post_id, '_plan_hour', $unit );
			} else {
				delete_post_meta( $post_id, '_plan_hour' );
			}
		}

		if ( isset( $_POST['_plan_desc'] ) && is_array( $_POST['_plan_desc'] ) ) {
			$unit = $_POST['_plan_desc'];
			$unit = array_map( 'sanitize_text_field', $unit ); // очистка

			if ( $unit ) {
				update_post_meta( $post_id, '_plan_desc', $unit );
			} else {
				delete_post_meta( $post_id, '_plan_desc' );
			}
		}

	}

	/**
	 * Подключает скрипты и стили.
	 */
	public function show_assets() {

		if ( is_admin() && get_current_screen()->id == $this->post_type ) {
			$this->show_styles();
			$this->show_scripts();
		}

	}

	/**
	 * Выводит на экран стили.
	 */
	public function show_styles() {

		?>
		<style>
			.y4ymp_add {
				color: #00a0d2;
				cursor: pointer;
			}

			.y4ymp_remove {
				color: brown;
				cursor: pointer;
			}
		</style>
		<?php

	}

	/**
	 * Выводит на экран JS.
	 */
	public function show_scripts() {

		?>
		<script>
			jQuery(document).ready(function ($) {
				var $companyInfo = $('.company-info');

				// Добавляет бокс с вводом адреса фирмы
				$('.y4ymp_add', $companyInfo).click(function () {
					var $list = $('.y4ymp_plans_list');
					$item = $list.find('.y4ymp_plans').first().clone();

					$item.find('input').val(''); // чистим знанчение

					$list.append($item);
				});

				// Удаляет бокс с вводом адреса фирмы
				$companyInfo.on('click', '.y4ymp_remove', function () {
					if ($('.y4ymp_plans').length > 1) {
						$(this).closest('.y4ymp_plans').remove();
					}
					else {
						$(this).closest('.y4ymp_plans').find('input').val('');
					}
				});
			});
		</script>
		<?php

	}

}