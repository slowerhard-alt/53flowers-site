<?
// добавляем галочку "Включить категорию в подвал"

function category_custom_fields_form($tag)
{	
	$is_hide = get_term_meta($tag->term_id, 'is_hide', true);
	
	?><tr class="form-field">
        <th scope="row" valign="top">
            
        </th>
        <td>
			<label>
				<input type="checkbox" name="is_hide"<?=$is_hide == 'y' ? ' checked' : '' ?> value="y">
				Скрыть категорию с сайта
			</label>
        </td>
    </tr><?
	
	$is_product_hide = get_term_meta($tag->term_id, 'is_product_hide', true);
	
	?><tr class="form-field">
        <th scope="row" valign="top">
            
        </th>
        <td>
			<label>
				<input type="checkbox" name="is_product_hide"<?=$is_product_hide == 'y' ? ' checked' : '' ?> value="y">
				Скрыть товары категории с сайта
			</label>
        </td>
    </tr><?

	?><tr class="form-field">
        <th scope="row" valign="top">
            <label for="extra1"><? _e('Заголовок H1'); ?></label>
        </th>
        <td>
            <input type="text" name="h1" value="<?=get_term_meta($tag->term_id, 'h1', true) ?>">
        </td>
    </tr><?
	?><tr class="form-field">
        <th scope="row" valign="top">
            <label for="extra1"><? _e('Заголовок H2'); ?></label>
        </th>
        <td>
            <input type="text" name="h2" value="<?=get_term_meta($tag->term_id, 'h2', true) ?>">
        </td>
    </tr><?
	?><tr class="form-field">
        <th scope="row" valign="top">
            <label for="extra1"><? _e('Заголовок для композиций (праздники)'); ?></label>
        </th>
        <td>
            <input type="text" name="h3" value="<?=get_term_meta($tag->term_id, 'h3', true) ?>">
        </td>
    </tr><?
	?><tr class="form-field">
        <th scope="row" valign="top">
            <label for="extra1"><? _e('Заголовок для главной страницы'); ?></label>
        </th>
        <td>
            <input type="text" name="home_title" value="<?=get_term_meta($tag->term_id, 'home_title', true) ?>">
        </td>
    </tr><?
	
	$top = get_term_meta($tag->term_id, 'top', true);
	
	?><tr class="form-field">
        <th scope="row" valign="top">
            <label for="extra1"><? _e('Вид отображения ТОПа'); ?></label>
        </th>
        <td>
			<label>
				<input type="radio" name="top"<?=!$top || $top != 'p' ? ' checked' : '' ?> value="v">
				По 4 в ряд с заполненными картинками
			</label>
			<br/>
			<label>
				<input type="radio" name="top"<?=$top && $top == 'p' ? ' checked' : '' ?> value="p">
				По 4 в ряд с прозрачным фоном
			</label>
        </td>
    </tr><?
	
	$is_hide_related = get_term_meta($tag->term_id, 'is_hide_related', true);
	
	?><tr class="form-field">
        <th scope="row" valign="top">
            
        </th>
        <td>
			<label>
				<input type="checkbox" name="is_hide_related"<?=$is_hide_related == 'y' ? ' checked' : '' ?> value="y">
				Скрыть блок "Так же вы можете добавить"
			</label>
        </td>
    </tr><?
}

function category_custom_fields_save($term_id)
{
	update_term_meta($term_id, 'h1', xs_format($_POST['h1']));
	update_term_meta($term_id, 'h2', xs_format($_POST['h2']));
	update_term_meta($term_id, 'h3', xs_format($_POST['h3']));
	update_term_meta($term_id, 'home_title', xs_format($_POST['home_title']));
	update_term_meta($term_id, 'top', xs_format($_POST['top']));
	update_term_meta($term_id, 'is_hide_related', xs_format($_POST['is_hide_related']));
	update_term_meta($term_id, 'is_hide', xs_format($_POST['is_hide']));
	update_term_meta($term_id, 'is_product_hide', xs_format($_POST['is_product_hide']));
} 

add_action('product_cat_edit_form_fields', 'category_custom_fields_form');
add_action('product_cat_add_form_fields', 'category_custom_fields_form');

add_action('edited_product_cat', 'category_custom_fields_save');
add_action('create_product_cat', 'category_custom_fields_save');


// Добавляем возможность деактивировать товары

function category_custom_fields_form_deactivate($tag)
{
	$price_for_deactivate = (int)get_term_meta($tag->term_id, 'price_for_deactivate', true);
	
	if($price_for_deactivate == 0)
		$price_for_deactivate = "";
	
	?><tr class="form-field">
        <th scope="row" valign="top">
            <label for="extra1"><? _e('Деактивировать товары, у которых цена меньше чем'); ?></label>
        </th>
        <td>
            <div class="xs_flex xs_start xs_top">
				<input style="max-width:200px;" type="number" min="0" step="1" name="price_for_deactivate" value="<?=$price_for_deactivate ?>">
				&nbsp;&nbsp;
				<button type="button" class="button button-primary" onclick="jQuery('[name=is_product_deactivate]').val('y'); jQuery(this).parents('form').submit()">Применить</button>
			</div>
			<input type="hidden" name="is_product_deactivate" value="n">
        </td>
    </tr><?
}

add_action('product_cat_edit_form_fields', 'category_custom_fields_form_deactivate', 99999);

function category_custom_fields_save_deactivate($term_id)
{
	global $wpdb;
	
	if(isset($_POST['is_product_deactivate']) && $_POST['is_product_deactivate'] == 'y')
	{
		$price_for_deactivate = isset($_POST['price_for_deactivate']) && !empty($_POST['price_for_deactivate'])
			? (int)$_POST['price_for_deactivate']
			: 0;
			
		update_term_meta($term_id, 'price_for_deactivate', $price_for_deactivate);

		$product_ids = [];
		
		$db_products = new WP_Query([
			'tax_query' => array(
				[
					'taxonomy' => 'product_cat',
					'field' => 'id',
					'terms' => $term_id
				],
				'relation' => 'AND'
			), 
			'posts_per_page' => -1,
			'post_type' => ['product'],
		]);

		if($db_products->have_posts())
		{
			while($db_products->have_posts())
			{
				$db_products->the_post();
				global $product;
				$product_ids[] = $product->get_id();
			}
		}
		
		if($price_for_deactivate == 0)
		{
			// Активируем все
			
			$wpdb->get_results("
				UPDATE 
					`xsite_posts` 
				SET
					`post_status` = 'publish'
				WHERE 
					`post_type` IN ('product', 'product_variation') AND 
					`post_status` = 'pending' AND 
					(
						`ID` IN ('".implode("','", $product_ids)."') OR 
						`post_parent` IN ('".implode("','", $product_ids)."')
					)
			");
		}
		else
		{
			// Деактивируем с низкой ценой
			
			if($db_deactivate_products = $wpdb->get_results("
				SELECT 
					p.`ID`
				FROM
					`xsite_posts` p
				WHERE 
					p.`post_type` IN ('product', 'product_variation') AND 
					p.`post_status` = 'publish' AND 
					(
						p.`ID` IN ('".implode("','", $product_ids)."') OR 
						p.`post_parent` IN ('".implode("','", $product_ids)."')
					) AND
					CONVERT((SELECT pm.`meta_value` FROM `xsite_postmeta` pm WHERE pm.`meta_key` = '_price' AND pm.`post_id` = p.`ID` ORDER BY pm.`meta_value` DESC LIMIT 1),UNSIGNED INTEGER) < '".$price_for_deactivate."'
			"))
			{
				$ar_deactivate_products = [];
				
				foreach($db_deactivate_products as $v)
					$ar_deactivate_products[] = $v->ID;
				
				$wpdb->get_results("
					UPDATE 
						`xsite_posts`
					SET
						`post_status` = 'pending'
					WHERE 
						`ID` IN ('".implode("','", $ar_deactivate_products)."')
				");
			}
			
			// Активируем с высокой ценой
			
			if($db_activate_products = $wpdb->get_results("
				SELECT 
					p.`ID`
				FROM
					`xsite_posts` p
				WHERE 
					p.`post_type` IN ('product', 'product_variation') AND 
					p.`post_status` = 'pending' AND 
					(
						p.`ID` IN ('".implode("','", $product_ids)."') OR 
						p.`post_parent` IN ('".implode("','", $product_ids)."')
					) AND
					CONVERT((SELECT pm.`meta_value` FROM `xsite_postmeta` pm WHERE pm.`meta_key` = '_price' AND pm.`post_id` = p.`ID` ORDER BY pm.`meta_value` DESC LIMIT 1),UNSIGNED INTEGER) >= '".$price_for_deactivate."'
			"))
			{
				$ar_activate_products = [];
				
				foreach($db_activate_products as $v)
					$ar_activate_products[] = $v->ID;
					
				$wpdb->get_results("
					UPDATE 
						`xsite_posts`
					SET
						`post_status` = 'publish'
					WHERE 
						`ID` IN ('".implode("','", $ar_activate_products)."')
				");
			}
			
			// Активируем родительские товары с существующими активными вариациями
			
			if($db_activate_products = $wpdb->get_results("
				SELECT 
					p.`ID`
				FROM
					`xsite_posts` p
				WHERE 
					p.`post_type` = 'product' AND 
					p.`post_status` = 'pending' AND 
					p.`ID` IN ('".implode("','", $product_ids)."') AND
					(SELECT COUNT(*) FROM `xsite_posts` _p WHERE _p.`post_parent` = p.`ID` AND _p.`post_status` = 'publish') > '0'
			"))
			{
				$ar_activate_products = [];
				
				foreach($db_activate_products as $v)
					$ar_activate_products[] = $v->ID;
					
				$wpdb->get_results("
					UPDATE 
						`xsite_posts`
					SET
						`post_status` = 'publish'
					WHERE 
						`ID` IN ('".implode("','", $ar_activate_products)."')
				");
			}
		}
		
		// Пересчитываем цены
				
		foreach($product_ids as $v)
			set_product_price($v);
	}
}

add_action('edited_product_cat', 'category_custom_fields_save_deactivate');


// Добавляем возможность деактивировать атрибут

function attr_custom_fields_form($tag)
{
	?><tr class="form-field">
        <th scope="row" valign="top">
            <label for="extra1"><? _e('Не показывать в фильтре'); ?></label>
        </th>
        <td>
			<label>
				<input type="checkbox" name="disabled"<?=get_term_meta($tag->term_id, 'disabled', true) == 'y' ? ' checked' : '' ?> value="y">
			</label>
			<br/>
        </td>
    </tr><?
}

function attr_custom_fields_form_add($tag)
{
	?><div class="form-field">
		<label>
			<input type="checkbox" name="disabled"<?=get_term_meta($tag->term_id, 'disabled', true) == 'y' ? ' checked' : '' ?> value="y">
			<? _e('Не показывать в фильтре'); ?>
		</label>
	</div><br/><?
}

function attr_custom_fields_save($term_id)
{
	update_term_meta($term_id, 'disabled', xs_format($_POST['disabled']));
} 

$ar_attributes = wc_get_attribute_taxonomies();

foreach($ar_attributes as $v)
{
	add_action('pa_'.$v->attribute_name.'_edit_form_fields', 'attr_custom_fields_form');
	add_action('pa_'.$v->attribute_name.'_add_form_fields', 'attr_custom_fields_form_add');

	add_action('edited_pa_'.$v->attribute_name, 'attr_custom_fields_save');
	add_action('create_pa_'.$v->attribute_name, 'attr_custom_fields_save');
}



