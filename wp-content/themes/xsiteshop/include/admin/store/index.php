<h1 class="wp-heading-inline"><?

if((isset($xs_data['category_tree']) && count($xs_data['category_tree']) > 0) || $xs_data['is_favorite'] == 'y')
{
	?><a href="/wp-admin/admin.php?page=store">Складской учёт</a><?
	
	foreach($xs_data['category_tree'] as $v)
	{
		echo " -> ";
		
		if($v->id != $xs_data['category_id'] || ($v->id == $xs_data['category_id'] && $xs_data['is_favorite'] == 'y'))
		{
			?><a href="/wp-admin/admin.php?page=<?=xs_format($_GET['page']) ?>&category_id=<?=$v->id ?>"><?=$v->name ?></a><?
		}
		else
			echo $v->name;
	}
			
	if($xs_data['is_favorite'] == 'y')
		echo " -> Избранное";
}
else
	echo "Складской учёт";

?></h1>
<a href="#add_component_form" class="fancybox page-title-action">Добавить компонент</a>
<hr class="wp-header-end"><?


?><br/><?

?><form class="xs_filter only_search" method="get" action="">
	<label>
		<input type="text" name="filter[search]" value="<?=$xs_filter['search']?>" placeholder="Поиск" />
	</label>
	
	<!--- ////////////////////////// --->
	
	
	<label class="xs_submit">
		<input type="submit" value="Искать" class="button-primary" />
	</label>
	<?
		if($setFilter)
		{
			?><label class="xs_submit"><a href="/wp-admin/admin.php?page=<?=$_GET['page']?>&category_id=<?=$_GET['category_id']?>&favorite=<?=$_GET['favorite']?>&orderby=<?=$orderby?>&order=<?=$order?>">× Сбросить фильтр</a></label><?
		}
	?>
	<input type="hidden" name="page" value="<?=$_GET['page']?>" />
	<input type="hidden" name="category_id" value="<?=$_GET['category_id']?>" />
	<input type="hidden" name="favorite" value="<?=$_GET['favorite']?>" />
	<input type="hidden" name="orderby" value="<?=$orderby?>" />
	<input type="hidden" name="order" value="<?=$order?>" />
	<input type="hidden" name="paged" value="1" />
	
</form><?

if(!$setFilter && $xs_data['is_favorite'] != 'y')
{
	?><div class="store_categories xs_flex xs_wrap xs_start"><?
		
		if($xs_data['categories'] > 0)
		{
			?><div class="item" style="background-color:#fa9801"><?
				?><a href="<?=setUrl($big_data['current_url'], "favorite", "y") ?>" class="link" style="color:#fff"><?
					?><span class="icon"><? get_svg("star", "#fff") ?></span><?
					?><span class="name_container">Избранное</span><?
				?></a><?
			?></div><?
			
			foreach($xs_data['categories'] as $v)
			{
				?><div class="item" style="background-color:<?=$v->background ?>"><?
				
					?><a href="<?=setUrl($big_data['current_url'], "delete", $v->id) ?>" onclick="if(!window.confirm('При удалении категории все входящие в неё компоненты и подкатегории станут родительскими.\nВы действительно хотите удалить категорию <?=$v->name ?>?')) return false;" title="Удалить" class="delete"></a><?
					?><a href="#edit_category_form_<?=$v->id ?>" title="Изменить" class="edit fancybox"></a><?
					
					?><a href="<?=setUrl($big_data['current_url'], ["category_id", "favorite"], [$v->id, ""]) ?>" class="link" style="color:<?=$v->color ?>"><?
						?><span class="icon"><? get_svg(($v->id == 1 ? "star" : "folder"), $v->color) ?></span><?
						?><span class="name_container"><?=$v->name ?></span><?
					?></a><?
				?></div><?
				
				?><form id="edit_category_form_<?=$v->id ?>" method="post" class="fancy_form"><?

					?><h2>Изменение категории</h2><?
					
					$ar_categories = $big_data['category_list'];
					unset($ar_categories[$v->id]);
					
					xs_input("name", $v->name, "Название категории", ['required' => true]);
					xs_input("sort", $v->sort, "Сортировка", ['type' => 'number', 'min' => 10, 'max' => 999, 'step' => 1]);
					xs_input("background", $v->background, "Цвет фона", ['type' => 'color']);
					xs_input("color", $v->color, "Цвет текста", ['type' => 'color']);
					xs_input("parent_id", $v->parent_id, "Родительская категория", ['type' => 'select', 'options' => $ar_categories]);
					xs_input("category_id", $v->id, "", ['type' => 'hidden']);
					
					?><br/><br/><?
					?><input type="submit" name="edit_category" value="Сохранить изменения" class="button-primary"><?

				?></form><?
			}
		}
		
		?><div class="item add"><?
			?><a href="#add_category_form" class="fancybox link xs_flex xs_middle xs_center"><span class="name_container xs_flex xs_start xs_middle"><span class="name">Создать категорию</span></span></a><?
		?></div><?
		
	?></div><?

	?><form id="add_category_form" method="post" class="fancy_form"><?

		?><h2>Создание категории</h2><?
		
		xs_input("name", "", "Название категории", ['required' => true]);
		xs_input("sort", 10, "Сортировка", ['type' => 'number', 'min' => 10, 'max' => 999, 'step' => 1]);
		xs_input("background", "", "Цвет фона", ['type' => 'color']);
		xs_input("color", "#ffffff", "Цвет текста", ['type' => 'color']);
		
		?><br/><br/><?
		?><input type="submit" name="add_category" value="Создать категорию" class="button-primary"><?

	?></form><?
}

?><form id="add_component_form" method="post" class="fancy_form"><?

	?><h2>Создание компонента</h2><?
	
	xs_input("name", "", "Название компонента", ['required' => true]);
	xs_input('name_for_user', "", 'Название для покупателей');				
	xs_input("category_id", $xs_data['category_id'], "Категория", ['type' => 'select', 'options' => $big_data['category_list']]);
	xs_input("group_id", 0, "Группа", ['type' => 'select', 'options' => $big_data['store_groups']]);
	xs_input('days', "", 'Дни', ['type' => 'number', 'min' => 0, 'step' => 1]);
	xs_input('forced_price', "", 'Приоритетная цена', ['type' => 'number', 'min' => 0, 'step' => 0.01]);
	xs_input("is_set_forced_price", "", 'Дни влияют на приоритетную цену', ['type' => 'checkbox']);
	xs_input('purchase_price', "", 'Закупочная цена', ['type' => 'number', 'min' => 0, 'step' => 1]);
	xs_input('original_price', "", 'Себестоимость', ['type' => 'number', 'min' => 0, 'step' => 1]);

	xs_input('sale_rules][type', "", 'Тип скидки', [
		'type' => 'select', 
		'options' => $big_data['sale_rules'], 
		'actions' => ['onchange' => "jQuery('.component_sale_rules').hide(); jQuery('.component_sale_rules--' + jQuery(this).val()).show()"]
	]);
	
	?><div class="component_sale_rules component_sale_rules--"><?
	
		xs_input('sale_rules][percent', "", 'Процент скидки', ['type' => 'number', 'min' => 0, 'step' => 1]);
		
	?></div><?
	
	?><div class="component_sale_rules component_sale_rules--count" style="display:none"><?
	
		xs_input('sale_rules][min_quantity', "", 'Мин. остаток для активации скидки', ['type' => 'number', 'min' => 0, 'step' => 1]);
		xs_input('sale_rules][min_days', "", 'Мин. дней для активации скидки', ['type' => 'number', 'min' => 0, 'step' => 1]);
		
	?></div><?

	xs_input('sort', 0, 'Сортировка', ['type' => 'number', 'min' => 0, 'step' => 1]);
	xs_input("color", '#ffffff', "Цвет фона в отчёте", ['type' => 'color']);
	xs_input("show_in_product", "", 'Отображать для покупателей в карточке товара', ['type' => 'checkbox']);
	xs_input("hide_quantity_in_product", "", 'Скрыть количество компонента в карточке товара', ['type' => 'checkbox']);
	xs_input("is_fresh_delivery", "", 'Учитывать для «свежая поставка»', ['type' => 'checkbox']);
	xs_input("favorite", "", "Избранное", ['type' => 'checkbox']);
	xs_input('ms_ids', "", 'ID в Моём складе', ['placeholder' => 'через запятую']);		
	xs_input('ms_is_update_price', "", "Обновлять себестоимость с Моего склада", ['type' => 'checkbox']);				
	xs_input('ms_is_update_days', "", "Обновлять дни с Моего склада", ['type' => 'checkbox']);				
	xs_input('ms_quantity_for_days', "", "Остаток, при котором перестают обновляться дни", ['type' => 'number', 'min' => 0, 'step' => 1]);				
	xs_input('ms_percent_outstock', "", "Процент, прибавляемый при нулевом остатке", ['type' => 'number', 'min' => 0, 'step' => 0.01]);				
	xs_input('ms_is_update_retail_price', "", "Обновлять розничную цену в Моем складе", ['type' => 'checkbox']);
	xs_input('ms_image_id', "", "ID МС для загрузки изображения");
	
	for($i = 0; $i < $big_data['store_competitors_count']; $i++)
	{
		?><br/><?
		?><hr/><?
		xs_input('competitors]['.$i.'][p', "", 'Цена '.xs_get_option('xs_competitor_'.$i), ['type' => 'number', 'min' => 0, 'step' => 1]);
		xs_input('competitors]['.$i.'][l', "", 'Ссылка '.xs_get_option('xs_competitor_'.$i));
	}
	
	?><br/><br/><?
	?><input type="submit" name="add_component" value="Создать компонент" class="button-primary"><?

?></form><?

if(count($xs_data['components']) > 0)
{	
	$catalog_orderby_options = [
		"name" => "Названию",
		"date" => "Дате добавления",
		"sort" => "Номеру сортировки",
	];
	
	$orderby = $big_data['orderby'];
	$order = $big_data['order'];
	
	?><div class="xs_sort__container"><?
		?><div class="xs_sort__label">Сортировать по:</div><?
		
		foreach($catalog_orderby_options as $code => $name)
		{ 
			?><div class="xs_sort__item xs_sort__item--<?=$order ?><?=$orderby == $code ? " xs_sort__item--selected" : "" ?>"><?
				?><a href="<?=setUrl($big_data['current_url'], ["orderby", "order"], [$code, ($orderby == $code && $order == "asc" ? "desc" : "asc")]) ?>"><?=esc_html($name) ?></a><?
			?></div><?
		}
		
		?><input type="hidden" name="orderby" value="<?=$orderby ?>" /><?
	?></div>
	
	<div class="store_components xs_flex xs_wrap xs_start"><?
	
		foreach($xs_data['components'] as $v)
		{
			?><div class="item"><?
			
				?><a href="<?=setUrl($big_data['current_url'], "delete_component", $v->id) ?>" onclick="if(!window.confirm('При удалении компонента будут удалены все входящие в него транзакции.\nКрайне не рекомендуется удалять компоненты, привязанные к товарам, это может привести к ошибкам!\nВы действительно хотите удалить компонент <?=$v->name ?>?')) return false;" title="Удалить" class="delete"></a><?
				
				?><a href="<?=setUrl($big_data['current_url'], "copying_component", $v->id) ?>" title="Копировать" class="copy"></a><?
				
				?><span class="xs_set_favorite favorite xs_flex xs_middle xs_center<?=$v->favorite == 'y' ? " active" : "" ?>" data-id="<?=$v->id ?>"><? get_svg("star", $v->color) ?></span><?
							
				?><span class="xs_set_view view xs_flex xs_middle xs_center<?=$v->show_in_product == 'y' ? " active" : "" ?>" data-id="<?=$v->id ?>"><? get_svg("view", $v->color) ?></span><?
				
				?><a href="/wp-admin/admin.php?page=<?=xs_format($_GET['page']) ?>&section=detail&id=<?=$v->id ?>" class="link"><?
					?><span class="image"<?=!empty($v->image) ? ' style="background-image:url('.$big_data['component_image_path'].$v->image.')"' : "" ?>></span><?
					?><span class="name_container"><?
						?><?=$v->name ?><?
						?><span class="name_for_user"><?=$v->name_for_user ?></span><?
					?></span><?
					?><span class="xs_flex pq_container"><?
						?><span class="price<?=$v->price <= 0 ? ' xs_red' : ""?>"><?=wc_price($v->price, ['decimals' => 2]) ?></span><?
						?><span class="original_price<?=$v->original_price <= 0 ? ' xs_red' : ""?>"><?=wc_price($v->original_price, ['decimals' => 2]) ?></span><?
						?><span class="group_days days_<?=$v->days ?> group_<?=$v->group_id ?>"><?
							?><span class="group"><?=get_store_group_name($v->group_id) ?></span>&nbsp;/&nbsp;<? 
							?><span class="days"><?=$v->days ?>&nbsp;дн</span><?
						?></span><?
					?></span><?
				?></a><?
			?></div><?
		}
	
	?></div><?
}