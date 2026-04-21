<?
// Табы

global $product, $big_data;

$active_tab = '';
$ar_tabs_id = [];
$ar_tabs = [];

/*
if($tabs = get_post_meta($post->ID, '_tabs', true))
{
	foreach($tabs as $k => $v)
		if($v == 'y')
			$ar_tabs_id[] = $k;
}
*/


$_id = $post->ID;

if($product->product_type == "variable")
{
	$available_variations = $product->get_available_variations();
	
	foreach($available_variations as $k => $v)
	{
		$_id = $v['variation_id'];
		break;
	}		
}

$_structure_product = "";

if($structure_product = get_structure_for_product($_id))
	$_structure_product = $structure_product;

$post->post_content = '<div class="structure_product">'.$_structure_product."</div>".wpautop(insert_structure_in_content($post->post_content, ""));


if(!empty($post->post_content))
{
	$ar_tabs['detail_text'] = [
		'name' => 'Описание',
		'content' => $post->post_content
	];
}

$is_show_tab = true;

if($terms = get_the_terms($post->ID, 'product_cat'))
	foreach($terms as $v)
		if($v->term_id == $big_data['present_term_id'] || $v->parent == $big_data['present_term_id'])
			$is_show_tab = false;

//if(count($ar_tabs_id) > 0)
if($is_show_tab)
{
	$db_tab = get_posts([
		'post_type' => 'tabs',
		//'include' => $ar_tabs_id
	]);
	
	if($db_tab)
	{
		foreach($db_tab as $v)
		{
			if(!empty($v->post_content))
			{
				$ar_tabs[$v->post_name] = [
					'name' => $v->post_title,
					'content' => $v->post_content
				];
			}
		}
	}
}

if(count($ar_tabs) > 0)
{
	if(!(count($ar_tabs) == 1 && isset($ar_tabs['detail_text'])))
	{
	?><div class="tabs"><?
	
		?><div class="tabs__overflow"><?
			?><div class="tabs__buttons"><?
				
				$i = 0;
				
				foreach($ar_tabs as $k => $v)
				{
					?><div class="tabs__button<?=$i == 0 ? ' active' : '' ?>" data-tab="<?=$k ?>"><?
					
						echo $v['name'];

					?></div><?
					
					$i++;
				}
				
			?></div><?
		?></div><?
		
		?><div class="tabs__container"><?
	}
			
			$i = 0;
			
			foreach($ar_tabs as $k => $v)
			{
				?><div class="tabs__content<?=$i == 0 ? ' active' : '' ?>" data-tab="<?=$k ?>"><?
				
					echo $v['content'];

				?></div><?
				
				$i++;
			}
		
	if(!(count($ar_tabs) == 1 && isset($ar_tabs['detail_text'])))
	{
		?></div><?
			
	?></div><?
	}
}