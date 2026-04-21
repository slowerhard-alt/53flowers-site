<?
// Добавляем фильтр по меткам и топам для post_type = product

add_action('restrict_manage_posts', 'add_event_table_filters');

function add_event_table_filters($post_type)
{
	if($post_type == 'product')
	{
		wp_dropdown_categories([
			'taxonomy'   => 'product_cat',
			'show_option_none' => 'Все топы',
			'option_none_value' => "",
			'selected'   => isset($_GET['product_top']) ? $_GET['product_top'] : "",
			'hide_empty' => false,
			'orderby' => 'name',
			'name' => 'product_top',
			'hierarchical' => true,
			'value_field' => 'term_id'
		]);

		wp_dropdown_categories([
			'taxonomy'   => 'product_tag',
			'show_option_none' => 'Все метки',
			'option_none_value' => "",
			'selected'   => isset($_GET['product_tag']) ? $_GET['product_tag'] : "",
			'hide_empty' => false,
			'orderby' => 'name',
			'name' => 'product_tag',
			'value_field' => 'slug'
		]);
	}
}

add_action( 'pre_get_posts', 'add_event_table_filters_handler' );

function add_event_table_filters_handler($query)
{
	$cs = function_exists('get_current_screen') 
		? get_current_screen() 
		: null;

	if(!is_admin() || empty($cs->post_type) || $cs->post_type != 'product')
		return;

	if(isset($_GET['product_top']) && (int)($_GET['product_top']) > 0)
	{
		$query->set('meta_query', [
			[
				'key' => 'top_category',
				'value' => '"'.(int)($_GET['product_top']).'"',
				'compare' => 'LIKE'
			]
		]);
	}
}


// Создаём колонку Топов у товаров

add_filter('manage_edit-product_columns', 'add_views_column', 4);

function add_views_column($columns)
{
	$num = 7; // после какой по счету колонки вставлять новые

	$new_columns = array(
		'product_top' => 'Топ в категориях',
		'status' => 'Статус',
		'components' => 'Компоненты',
	);

	return array_slice($columns, 0, $num) + $new_columns + array_slice($columns, $num);
}

// Заполняем колонку данными

add_action('manage_product_posts_custom_column', 'fill_views_column', 5, 2);

function fill_views_column($colname, $post_id)
{
	if($colname == 'product_top')
	{
		$ar_tops = get_post_meta($post_id, 'top_category', true);
		
		if($ar_tops && is_array($ar_tops) && count($ar_tops) > 0)
		{
			$product_top = get_terms([
				'taxonomy' => 'product_cat',
				'orderby' => 'name',
				'hide_empty' => false,
				'include' => $ar_tops
			]);
			
			if($product_top)
			{
				$i = 0;
				
				foreach($product_top as $v)
				{
					?><a href="/wp-admin/edit.php?post_type=product&product_top=<?=$v->term_id ?>"><?=$v->name ?></a><?
					
					$i++;
					
					if(count($product_top) > $i)
						echo ", ";
				}
			}
		}
	}
	elseif($colname == 'status')
	{
		$status = get_post_meta($post_id, '_stock_status', true);
		
		$ar_status = [
			'instock' => __('In stock', 'woocommerce'),
			'outofstock' => __('Out of stock', 'woocommerce'),
			'onrequest' => __('Под заказ', 'woocommerce'),
		];
		
		?><div class="xs_stock_status xs_stock_status--<?=$status ?>"><?=$ar_status[$status] ?></div><?
	}
	elseif($colname == 'components')
	{
		$product = wc_get_product($post_id);
	
		$child_variations = [];
		
		if($product->product_type == "variable")
		{
			$variation_ids = $product->get_children();
			
			if($variation_ids)
			{
				foreach($variation_ids as $variation_id)
				{
					$variation = wc_get_product($variation_id);
					
					if(!$variation || !$variation->variation_is_visible())
						continue;
					
					$child_variations[] = $variation;
				}
			}
		}
		
		$variations = ($child_variations && count($child_variations) > 0) 
			? $child_variations 
			: [$product];
			
		foreach($variations as $variation)
		{
			$db_components = get_store_components_product($variation->get_id(), true);
			
			if($db_components && is_array($db_components) && count($db_components))
			{
				if(count($variations) > 1)
					echo '<div class="list_product_components__name" onclick="jQuery(this).toggleClass(\'active\')">'.$variation->get_name().'</div>';
				
				?><div class="list_product_components__val"><?
				
					foreach($db_components as $v)
					{
						?><div class="list_product_components__item">
							<a href="/wp-admin/admin.php?page=store&section=detail&id=<?=$v->component_id ?>" target="_blank"><?=$v->name ?></a>&nbsp;×&nbsp;<?=$v->quantity ?>шт.
						</div><?
					}
				
				?></div><?
			}
		}
	}
}


// https://wp-kama.ru/id_9077/svoi-gruppovye-dejstviya-v-spiske-postov-stranits-yuzerov-kommentov.html


// Добавляем действие для товаров

add_filter('bulk_actions-edit-product', 'register_my_bulk_actions');

function register_my_bulk_actions($bulk_actions)
{
	$bulk_actions['add_tags'] = 'Добавить метки';
	$bulk_actions['remove_tags'] = 'Убрать метки';
	$bulk_actions['add_top'] = 'Добавить в топ';
	$bulk_actions['remove_top'] = 'Убрать из топа';
	//$bulk_actions['add_tabs'] = 'Добавить табы';
	//$bulk_actions['remove_tabs'] = 'Убрать табы';
	return $bulk_actions;
}


// Обработчик действия

add_filter('handle_bulk_actions-edit-product', 'my_bulk_action_handler', 10, 3);

function my_bulk_action_handler($redirect_to, $doaction, $post_ids)
{
	if(!in_array($doaction, ['add_tags', 'remove_tags', 'add_top', 'remove_top', 'add_tabs', 'remove_tabs']))
		return $redirect_to;

	// Метки
	
	if(isset($_GET['tax_input']) && is_array($_GET['tax_input']) && count($_GET['tax_input']) > 0)
	{
		foreach($_GET['tax_input'] as $k => $v)
		{
			foreach($v as $_k => $_v)
				if($_v == '0')
					unset($v[$_k]);
			
			// Добавляем метски
			
			if($doaction == 'add_tags')
			{
				foreach($post_ids as $product_id)
				{
					wp_set_post_terms($product_id, $v, $k, true);
				}
			}
			
			// Удаляем метски
			
			if($doaction == 'remove_tags')
			{
				foreach($post_ids as $product_id)
				{
					wp_remove_object_terms($product_id, $v, $k);
				}
			}
		}
	}
	
	// Топы
	
	if(isset($_GET['top_category']) && is_array($_GET['top_category']) && count($_GET['top_category']) > 0)
	{
		// Добавляем
		
		if($doaction == 'add_top')
		{
			foreach($post_ids as $product_id)
			{
				if(!$ar_terms = get_post_meta($product_id, 'top_category', true))
					$ar_terms = [];
				
				foreach($_GET['top_category'] as $v)
				{
					if(!in_array($v, $ar_terms))
						$ar_terms[] = $v;
				}
				
				update_post_meta($product_id, 'top_category', $ar_terms);
			}
		}
		
		// Удаляем
		
		if($doaction == 'remove_top')
		{
			foreach($post_ids as $product_id)
			{
				if(!$ar_terms = get_post_meta($product_id, 'top_category', true))
					$ar_terms = [];
				
				foreach($ar_terms as $k => $v)
				{
					if(in_array($v, $_GET['top_category']))
						unset($ar_terms[$k]);
				}
				
				update_post_meta($product_id, 'top_category', $ar_terms);
			}
		}
	}
	
	// Табы
	
	if(isset($_GET['_tabs']) && is_array($_GET['_tabs']) && count($_GET['_tabs']) > 0)
	{
		// Добавляем
		
		if($doaction == 'add_tabs')
		{
			foreach($post_ids as $product_id)
			{
				if(!$ar_tabs = get_post_meta($product_id, '_tabs', true))
					$ar_tabs = [];
				
				foreach($_GET['_tabs'] as $k => $v)
				{
					if(!isset($ar_tabs[$k]) || $ar_tabs[$k] != 'y')
						$ar_tabs[$k] = 'y';
				}
				
				update_post_meta($product_id, '_tabs', $ar_tabs);
			}
		}
		
		// Удаляем
		
		if($doaction == 'remove_tabs')
		{
			foreach($post_ids as $product_id)
			{
				if(!$ar_tabs = get_post_meta($product_id, '_tabs', true))
					$ar_tabs = [];
				
				foreach($_GET['_tabs'] as $k => $v)
					unset($ar_tabs[$k]);
				
				update_post_meta($product_id, '_tabs', $ar_tabs);
			}
		}
	}

	$redirect_to = add_query_arg('my_bulk_action_done', count($post_ids), $redirect_to);

	return $redirect_to;
}


// Выводим уведомление

add_action('admin_notices', 'my_bulk_action_admin_notice');

function my_bulk_action_admin_notice()
{
	if(empty($_GET['my_bulk_action_done']))
		return;

	$data = $_GET['my_bulk_action_done'];

	$msg = 'Успешно изменено товаров: '.(int)$data.'.';

	echo '<div id="message" class="updated"><p>'. $msg .'</p></div>';
}


// Видоизменяем мета бокс меток

function udalenie_metaboksa_metok() 
{
    remove_meta_box('tagsdiv-product_tag', 'product', 'side');
    add_meta_box('truetagsdiv-product_tag', 'Метки товаров', 'xs_product_tag', 'product', 'side', 'default');
    add_meta_box('truetagsdiv-product_top_category', 'Топ в категориях', 'xs_product_product_top_category', 'product', 'side', 'default');
	add_meta_box('xs_store_product', 'Состав товара', 'xs_store_product', 'product', 'normal', 'low');
	add_meta_box('xs_price_history', 'История изменения цен', 'xs_price_history', 'product', 'normal', 'low');
}
add_action( 'admin_menu', 'udalenie_metaboksa_metok');
 
function xs_product_tag($post) 
{	
    $vse_metki = get_terms('product_tag', array('hide_empty' => 0) ); 
    $id_metok_posta = [];

	if($post)
	{
		$vse_metki_posta = get_the_terms( $post->ID, 'product_tag' );  
 
		if ($vse_metki_posta)
			foreach($vse_metki_posta as $metka)
				$id_metok_posta[] = $metka->term_id;
	}
 
    // начинаем выводить HTML
    echo '<div id="taxonomy-product_tag" class="categorydiv">';
    echo '<input type="hidden" name="tax_input[product_tag][]" value="0" />';
    echo '<ul>';
    // запускаем цикл для каждой из меток
    foreach( $vse_metki as $metka ){
        // по умолчанию чекбокс отключен
        $checked = "";
        // но если ID метки содержится в массиве присвоенных меток поста, то отмечаем чекбокс
        if ( in_array( $metka->term_id, $id_metok_posta ) ) {
            $checked = " checked='checked'";
        } 
        // ID чекбокса (часть) и ID li-элемента
        $id = 'product_tag-' . $metka->term_id;
        echo "<li id='{$id}'>";
        echo "<input type='checkbox' name='tax_input[product_tag][]' id='in-$id'". $checked ." value='$metka->slug' /><label for='in-$id'> $metka->name</label><br />";
        echo "</li>";
    }
    echo '</ul></div>'; // конец HTML
}


// Выводим мета бокс топа категорий

function xs_product_product_top_category($post) 
{
	//wp_nonce_field('xs_meta_box_nonce', 'meta_box_nonce'); 
	
	global $ar_terms;
	
	$ar_terms = get_post_meta($post->ID, 'top_category', true);
	
	if(!is_array($ar_terms))
		$ar_terms = [];
	
	function xs_get_submenu_top($cat_id = 0)
	{
		global $ar_categories, $ar_terms;
		$taxonomy = 'product_cat'; // Название таксономии
		
		if(!isset($ar_categories) || empty($ar_categories))
		{
			$ar_categories = get_terms([
				'taxonomy' 		=> $taxonomy,
				'hide_empty' 	=> false,
				'exclude'		=> []
			]);
		}
		
		$ar_subcat = [];
		
		foreach($ar_categories as $v)
			if($v->parent == $cat_id)
				$ar_subcat[] = $v;
		
		if(count($ar_subcat) == 0)
			return false;
		
		$result = $cat_id == 0 ? '<ul class="categorychecklist">' : '<ul>';
		
		foreach($ar_subcat as $v)
		{
			$sub = xs_get_submenu_top($v->term_id);
			
			$ar_li_class = [];
			
			if($sub)
				$ar_li_class[] = 'is_parent';
			
			$result .= '<li'.(count($ar_li_class) > 0 ? ' class="'.implode(' ', $ar_li_class).'"' : '').'>';
			
			$result .= '<span class="link"><input id="in-top_category-'.$v->term_id.'" type="checkbox"'.(in_array($v->term_id, $ar_terms) ? 'checked' : '').' value="'.$v->term_id.'" name="top_category[]" /> <label for="in-top_category-'.$v->term_id.'">'.$v->name.'</label></span>';
			
			if($sub)
				$result .= $sub;					
			
			$result .= '</li>';
		}
		
		$result .= '</ul>';
		
		return $result;
	}

	?><div class="top_categorydiv xs_overflow" style="margin:0;padding:0 0 0 2px"><?
		
		echo xs_get_submenu_top();
		
		?><input type="hidden" name="xs_save_top" value="y" /><?
		
	?></div><?
}


function xs_store_product($post) 
{
	wp_nonce_field( 'xs_meta_box_nonce', 'meta_box_nonce' ); 
	global $wpdb, $big_data;
	
	$product = wc_get_product($post);
	
	$child_variations = [];
	
	if($product->product_type == "variable")
	{
		$variation_ids = $product->get_children();
		
		if($variation_ids)
		{
			foreach($variation_ids as $variation_id)
			{
				$variation = wc_get_product($variation_id);
				
				if(!$variation || !$variation->variation_is_visible())
					continue;
				
				$child_variations[] = (object)["ID" => $variation_id, 'price' => $variation->get_price()];
			}
		}
	}
	
	$variations = ($child_variations && count($child_variations) > 0) 
		? $child_variations 
		: [$post];

	$is_sale_on_quantity = get_post_meta($post->ID, "is_sale_on_quantity", true);

	?><br/><?
	
	if(count($variations) > 1)
	{
		// Табы
		
		$k = 0;
		
		?><div class="nav-tab-wrapper"><?
			
		foreach($variations as $k => $v)
		{
			if(!isset($v->post_title))
				$v->post_title = $wpdb->get_var("SELECT `post_title` FROM `xsite_posts` WHERE `ID` = '".$v->ID."'");
			
			$v->post_title = str_replace($product->get_name()." - ", "", $v->post_title);
			$variations[$k]->post_title = $v->post_title;
			
			if(isset($v->price) && $v->price)
				$v->post_title .= " - <span class='price'>".$v->price.'р.</span>';
			
			?><div onclick="
				jQuery('.tab_container--store').hide(); 
				jQuery('.tab_container--store[data-product_id=<?=$v->ID ?>]').show();
				jQuery(this).parent('.nav-tab-wrapper').find('.nav-tab').removeClass('nav-tab-active'); 
				jQuery(this).addClass('nav-tab-active')
			" class="nav-tab<?=$k == 0 ? " nav-tab-active" : "" ?>" data-product_id='<?=$v->ID ?>'><?=$v->post_title ?></div><?
			
			$k++;
		}
		
		?></div><?
		?><br/><?
		
		?><div class="store_components__is_sale_on_quantity" data-product_id="<?=$post->ID ?>">
			<label>
				<input class="store_components__is_sale_on_quantity-input" type="checkbox" name="is_sale_on_quantity" value="y"<?=$is_sale_on_quantity == 'y' ? " checked" : ""?> />
				<span class="store_components__is_sale_on_quantity-name">Скидка от количества</span>
			</label>
		</div><?
		
		$is_markup = get_post_meta($post->ID, "is_markup", true);
		$sale_in_product = get_post_meta($post->ID, "sale_in_product", true);
		
		?><div class="store_components__sale-product" data-product_id="<?=$post->ID ?>"><?	
			?><div class="component_table__nav-name">Общая скидка/наценка:</div><?
			?><div class="component_table__sale component_table__sale--inline"><?
				?><div class="component_table__nav-sale"><?
					?><input class="component_table__nav-input store_components__nav-input" id="component_table__nav-input-plus-<?=$post->ID ?>" type="radio" name="is_markup[<?=$post->ID ?>]" type="radio" value="y"<?=($is_markup == 'y') ? ' checked' : "" ?> /><?
					?><label class="component_table__nav-label component_table__nav-label--plus" for="component_table__nav-input-plus-<?=$post->ID ?>">+</label><?
					?><input class="component_table__nav-input store_components__nav-input" id="component_table__nav-input-minus-<?=$post->ID ?>" type="radio" name="is_markup[<?=$post->ID ?>]" type="radio" value="n"<?=($is_markup != 'y') ? ' checked' : "" ?> /><? 
					?><label class="component_table__nav-label component_table__nav-label--minus" for="component_table__nav-input-minus-<?=$post->ID ?>">&minus;</label><?
				?></div><?					
				?><input type="text" class="component_table__set_sale store_components__set_sale" value="<?=($sale_in_product > 0) ? $sale_in_product : "" ?>" name="sale_in_product[<?=$post->ID ?>]" placeholder="0" /><?
				?>%<?
			?></div><?
		?></div><?
	}
	else
	{
		?><div class="store_components__is_sale_on_quantity" data-product_id="<?=$post->ID ?>">
			<label>
				<input class="store_components__is_sale_on_quantity-input" type="checkbox" name="is_sale_on_quantity" value="y"<?=$is_sale_on_quantity == 'y' ? " checked" : ""?> />
				<span class="store_components__is_sale_on_quantity-name">Скидка от количества</span>
			</label>
		</div><?
	}
	
	$k = 0;
	
	foreach($variations as $v)
	{
		$xs_data['product_id'] = $v->ID;
		
		?><div class="tab_container tab_container--store" data-product_id='<?=$v->ID ?>' data-product_name='<?=str_replace("'", "`", $v->post_title) ?>'<?=$k > 0 ? ' style="display:none"' : '' ?>><?
			?><div class="xs_load_ajax" data-product_id='<?=$v->ID ?>'><?
			
				get_ajax_template('xs_product_components', $xs_data);
				
			?></div><?
		?></div><?
		
		$k++;
	}
	
	?><div class="store_components__nav">
		<div class="store_components__hide_structure">
			<input type="checkbox" id="hide_structure_in_product" name="hide_structure"<?=get_post_meta($post->ID, "hide_structure", true) == 'y' ? " checked" : "" ?> value="y" />
			<label for="hide_structure_in_product" title="Скрыть состав в товаре"><? get_svg("view", "") ?></label>
		</div><?
	
		?><div class="store_components__nav-buttons">			
			<div class="store_components__nav-button store_components__nav-button--clear-sales button" data-product_id="<?=$post->ID ?>">Сбросить все скидки</div><?
			
			if(count($variations) > 1)
			{
				?><div class="store_components__nav-button store_components__nav-button--copy-one button">Скопировать в вариацию</div><?
				?><div class="store_components__nav-button store_components__nav-button--copy button">Скопировать во все вариации</div><?
			}
			
			?><div class="store_components__nav-button store_components__nav-button--clear button" data-product_id="<?=$post->ID ?>">Очистить <?=count($variations) > 1 ? "во всех вариациях" : "всё" ?></div>
		</div>
	</div><?

	?><br/><?
}


// История изменения цен

function xs_price_history($post)
{
	wp_nonce_field('xs_meta_box_nonce', 'meta_box_nonce'); 
	global $wpdb, $big_data;
	
	$product = wc_get_product($post);
	
	$child_variations = [];
	
	if($product->product_type == "variable")
	{
		$variation_ids = $product->get_children();
		
		if($variation_ids)
		{
			foreach($variation_ids as $variation_id)
			{
				$variation = wc_get_product($variation_id);
				
				if(!$variation || !$variation->variation_is_visible())
					continue;
				
				$child_variations[] = (object)["ID" => $variation_id, 'price' => $variation->get_price()];
			}
		}
	}
	
	$variations = ($child_variations && count($child_variations) > 0) 
		? $child_variations 
		: [$post];

	foreach($variations as $k => $v)
	{
		if(!$history = get_price_history($v->ID))
			unset($variations[$k]);
		else
			$v->history = $history;
	}
	
	if(!count($variations))
		return;
	
	?><br/><?


	// Получаем список администраторов

	$users = get_users(array(
		'role' => 'administrator',
		'orderby' => 'name'
	));

	foreach($users as $val)
		$ar_admins[$val->ID] = $val->display_name;
	
	
	if(count($variations) > 1)
	{
		// Табы
		
		$k = 0;
		
		?><div class="nav-tab-wrapper"><?
			
		foreach($variations as $v)
		{
			if(!isset($v->post_title))
				$v->post_title = $wpdb->get_var("SELECT `post_title` FROM `xsite_posts` WHERE `ID` = '".$v->ID."'");
			
			$v->post_title = str_replace($product->get_name()." - ", "", $v->post_title);
			
			if(isset($v->price) && $v->price)
				$v->post_title .= " - <span class='price'>".$v->price.'р.</span>';
			
			?><div onclick="
				jQuery('.tab_container--history').hide(); 
				jQuery('.tab_container--history[data-product_id=<?=$v->ID ?>]').show();
				jQuery(this).parent('.nav-tab-wrapper').find('.nav-tab').removeClass('nav-tab-active'); 
				jQuery(this).addClass('nav-tab-active')
			" class="nav-tab<?=$k == 0 ? " nav-tab-active" : "" ?>" data-product_id='<?=$v->ID ?>'><?=$v->post_title ?></div><?
			
			$k++;
		}
		
		?></div><?
		?><br/><?
	}
	
	$k = 0;
	
	foreach($variations as $v)
	{
		?><div class="tab_container tab_container--history" data-product_id='<?=$v->ID ?>'<?=$k > 0 ? ' style="display:none"' : '' ?>>
			<table class="xs_align_center reating_table widefat striped xs_data_table xs_users">
				<thead>
					<tr>
						<th>Дата</th>
						<th>Пользователь</th>
						<th>Цена продажи</th>
						<th>Зачёркнутая цена</th>
					</tr>
				</thead>
				<tbody><? 
				
					foreach($v->history as $_v)
					{
						?><tr>
							<td><?=date('d.m.Y H:i:s', strtotime($_v->date)) ?></td>
							<td><?=$ar_admins[$_v->user_id] ?></td>
							<td><?=wc_price($_v->price) ?></td>
							<td><?=($_v->regular_price > $_v->price) ? wc_price($_v->regular_price) : "" ?></td>
						</tr><?  
					}
					
				?></tbody>
			</table>			
		</div><?
		
		$k++;
	}

	?><br/><?
}


// Сохраняем параметры

add_action('save_post', 'xs_save_product');

function xs_save_product($post_id)
{
	global $wpdb;

	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'xs_meta_box_nonce' ) || !current_user_can( 'edit_post', $post_id ) || (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)) 
		return; 

	$ar_products[] = $post_id;
	
	if($child_variations = get_posts(['numberposts' => -1, 'post_type' => 'product_variation', 'post_parent' => $post_id]))
	{
		$wpdb->query("DELETE FROM `xsite_store_products` WHERE `product_id` = '".$post_id."' AND `site` = '53flowers'");
		
		foreach($child_variations as $v)
			$ar_products[] = $v->ID;
	}

	if(isset($_POST['xs_save_top']) && $_POST['xs_save_top'] == 'y')
	{
		if(isset($_POST['top_category']))
			update_post_meta($post_id, 'top_category', $_POST['top_category']);
		else
			delete_post_meta($post_id, 'top_category');	

		if(isset($_POST['hide_structure']))
			update_post_meta($post_id, 'hide_structure', $_POST['hide_structure']);
		else
			delete_post_meta($post_id, 'hide_structure');

		if(isset($_POST['is_markup']) && is_array($_POST['is_markup']))
			foreach($_POST['is_markup'] as $k => $v)
				update_post_meta($k, 'is_markup', $v);

		if(isset($_POST['sale_in_product']) && is_array($_POST['sale_in_product']))
			foreach($_POST['sale_in_product'] as $k => $v)
				update_post_meta($k, 'sale_in_product', $v);

		if(isset($_POST['is_sale_on_quantity']))
			update_post_meta($post_id, 'is_sale_on_quantity', $_POST['is_sale_on_quantity']);
		else
			delete_post_meta($post_id, 'is_sale_on_quantity');	
	}
	
	foreach($ar_products as $v)
		set_product_price($v);
	
	return $xsdata;  
}


// Признак отображения вариации в превью

add_action('woocommerce_variation_options_pricing', 'xs_variation_options_pricing', 10, 3);

function xs_variation_options_pricing($loop, $variation_data, $variation) 
{
	?><div class="options"><?
	
	woocommerce_wp_checkbox( 
		array( 
			'id' => 'is_hide['.$variation->ID.']', 
			'class' => 'checkbox', 
			'style' => 'margin-top:4px !important',
			'wrapper_class' => '', 
			'label' => __('Не показывать в превью товара'), 
			'value' => get_post_meta($variation->ID, 'is_hide', true),
		)
	);
	
	woocommerce_wp_checkbox( 
		array( 
			'id' => 'is_disabled['.$variation->ID.']', 
			'class' => 'checkbox', 
			'style' => 'margin-top:4px !important',
			'wrapper_class' => '', 
			'label' => __('Запретить покупку вариации'), 
			'value' => get_post_meta($variation->ID, 'is_disabled', true),
		)
	);
	
	?></div><br/><?
}

add_action('woocommerce_save_product_variation', 'art_save_variation_settings_fields', 10, 2);

function art_save_variation_settings_fields($post_id) 
{
   $is_hide = $_POST['is_hide'][$post_id];
   
   if(is_array($_POST['is_hide']) && isset($is_hide) && !empty($is_hide))
		update_post_meta($post_id, 'is_hide', esc_attr($is_hide));
	else
		delete_post_meta($post_id, 'is_hide');
	
   $is_disabled = $_POST['is_disabled'][$post_id];
   
   if(is_array($_POST['is_disabled']) && isset($is_disabled) && !empty($is_disabled))
		update_post_meta($post_id, 'is_disabled', esc_attr($is_disabled));
	else
		delete_post_meta($post_id, 'is_disabled');
}