<h1 class="wp-heading-inline"><?

?><a href="/wp-admin/admin.php?page=store">Складской учёт</a><?
	
if((isset($xs_data['category_tree']) && count($xs_data['category_tree']) > 0) || $xs_data['is_favorite'] == 'y')
{
	foreach($xs_data['category_tree'] as $v)
	{
		echo " -> ";
		
		?><a href="/wp-admin/admin.php?page=<?=xs_format($_GET['page']) ?>&category_id=<?=$v->id ?>"><?=$v->name ?></a><?
	}
			
	if($xs_data['is_favorite'] == 'y')
	{
		?> -> <a href="/wp-admin/admin.php?page=<?=xs_format($_GET['page']) ?>&category_id=<?=$xs_data['result']->category_id ?>&favorite=y">Избранное</a><?
	}
}
?> -> <?=$xs_data['result']->name ?></h1>
<hr class="wp-header-end"/>
<br/><?
?><form action="/wp-admin/admin.php?page=<?=$_GET['page'] ?>&section=<?=$_GET['section'] ?>&id=<?=$xs_data['result']->id ?>" method="post">
	<div class="xs_cols col2 clearfix">
		<div class="xs_col">
			<div class="xs_block"><?
				
				if(isset($xs_data['result']->id))
				{
					?><div class="xs_flex xs_middle input_container">
						<div class="xs_label">ID:</div>
						<div class="input"><strong><?=$xs_data['result']->id ?></strong></div>
					</div><?
				}
				
				?><div class="xs_flex xs_middle input_container">
					<div class="xs_label">Текущая цена:</div>
					<div class="input"><strong><?=wc_price($xs_data['result']->price, ['decimals' => 2]) ?></strong><?
					
					if($xs_data['result']->sale_price > 0)
						echo "&nbsp;&nbsp;&nbsp;<span style='text-decoration: line-through;'>".wc_price($xs_data['result']->sale_price, ['decimals' => 2])."</span>";
					
					?></div>
				</div><?
				
				$q_time = filemtime($_SERVER["DOCUMENT_ROOT"]."/moysklad/log_quantity") + (3600*3);
				$p_time = filemtime($_SERVER["DOCUMENT_ROOT"]."/moysklad/log_all") + (3600*3);
				
				?><div class="xs_flex xs_middle input_container">
					<div class="xs_label">Остатки в Моём складе:</div>
					<div class="input"><strong><?=$xs_data['result']->ms_quantity ?> шт.</strong>&nbsp;&nbsp;&nbsp;(последнее обновление <?=date("d.m.Y H:i:s", ($q_time > $p_time ? $q_time : $p_time)) ?>)</div>
				</div><?
				
				?><br/><?
				
				xs_input('name', $xs_data['result']->name, 'Название', ['required' => true]);				
				xs_input('name_for_user', $xs_data['result']->name_for_user, 'Название для покупателей');				
				xs_input("category_id", $xs_data['result']->category_id, "Категория", ['type' => 'select', 'options' => $big_data['category_list']]);
				xs_input("group_id", $xs_data['result']->group_id, "Группа", ['type' => 'select', 'options' => $big_data['store_groups']]);
				xs_input('days', $xs_data['result']->days, 'Дни', ['type' => 'number', 'min' => 0, 'step' => 1]);
				xs_input('forced_price', $xs_data['result']->forced_price, 'Приор цена', ['type' => 'number', 'min' => 0, 'step' => 0.01]);
				xs_input("is_set_forced_price", $xs_data['result']->is_set_forced_price, 'Дни влияют на приоритетную цену', ['type' => 'checkbox']);
				xs_input('purchase_price', $xs_data['result']->purchase_price, 'Закуп цена', ['type' => 'number', 'min' => 0, 'step' => 1]);
				xs_input('original_price', $xs_data['result']->original_price, 'Себес', ['type' => 'number', 'min' => 0, 'step' => 1]);

				xs_input('sale_rules][type', $xs_data['result']->sale_rules['type'], 'Тип скидки', [
					'type' => 'select', 
					'options' => $big_data['sale_rules'], 
					'actions' => ['onchange' => "jQuery('.component_sale_rules').hide(); jQuery('.component_sale_rules--' + jQuery(this).val()).show()"]
				]);
				
				?><div class="component_sale_rules component_sale_rules--"<?=!isset($xs_data['result']->sale_rules['type']) || empty($xs_data['result']->sale_rules['type']) ? '' : ' style="display:none"' ?>><?
				
					xs_input('sale_rules][percent', $xs_data['result']->sale_rules['percent'], 'Процент скидки', ['type' => 'number', 'min' => 0, 'step' => 1]);
					
				?></div><?
				
				?><div class="component_sale_rules component_sale_rules--count"<?=isset($xs_data['result']->sale_rules['type']) && $xs_data['result']->sale_rules['type'] == 'count' ? '' : ' style="display:none"' ?>><?
				
					xs_input('sale_rules][min_quantity', $xs_data['result']->sale_rules['min_quantity'], 'Мин. остаток для активации скидки', ['type' => 'number', 'min' => 0, 'step' => 1]);
					xs_input('sale_rules][min_days', $xs_data['result']->sale_rules['min_days'], 'Мин. дней для активации скидки', ['type' => 'number', 'min' => 0, 'step' => 1]);
					
				?></div><?
				
				xs_input('sort', $xs_data['result']->sort, 'Сортировка', ['type' => 'number', 'min' => 0, 'step' => 1]);
				xs_input("color", $xs_data['result']->color, "Цвет фона в отчёте", ['type' => 'color']);
				xs_input("show_in_product", $xs_data['result']->show_in_product, 'Отображать для покупателей в карточке товара', ['type' => 'checkbox']);
				xs_input("hide_quantity_in_product", $xs_data['result']->hide_quantity_in_product, 'Скрыть количество компонента в карточке товара', ['type' => 'checkbox']);
				xs_input("is_fresh_delivery", $xs_data['result']->is_fresh_delivery, 'Учитывать для «свежая поставка»', ['type' => 'checkbox']);
				xs_input('favorite', $xs_data['result']->favorite, "Избранное", ['type' => 'checkbox']);				
				xs_input('ms_ids', $xs_data['result']->ms_ids, 'ID в Моём складе', ['placeholder' => 'через запятую', 'type' => 'textarea']);		
				xs_input('ms_is_update_price', $xs_data['result']->ms_is_update_price, "Обновлять себестоимость с Моего склада", ['type' => 'checkbox']);				
				xs_input('ms_is_update_days', $xs_data['result']->ms_is_update_days, "Обновлять дни с Моего склада", ['type' => 'checkbox']);				
				xs_input('ms_quantity_for_days', $xs_data['result']->ms_quantity_for_days, "Остаток, при котором перестают обновляться дни", ['type' => 'number', 'min' => 0, 'step' => 1]);
				xs_input('ms_percent_outstock', $xs_data['result']->ms_percent_outstock, "Процент, прибавляемый при нулевом остатке", ['type' => 'number', 'min' => 0, 'step' => 0.01]);
				xs_input('ms_is_update_retail_price', $xs_data['result']->ms_is_update_retail_price, "Обновлять розничную цену в Моем складе", ['type' => 'checkbox']);

				?><div class="xs_flex xs_middle input_container">
					<div class="xs_label">Изображение:</div>
					<div class="input">
						<div class="input_upload" data-component_id="<?=$xs_data['result']->id ?>">
							<label>
								<input type="file" accept="image/*">
								<div class="xs_upload_result"><?
								
								$file = false;
								
								if(!empty($xs_data['result']->image) && file_exists($_SERVER['DOCUMENT_ROOT'].$big_data['component_image_path'].$xs_data['result']->image))
								{
									?><a href="<?=$big_data['component_image_path'].$xs_data['result']->image ?>" target="_blank"><img src="<?=$big_data['component_image_path'].$xs_data['result']->image ?>" alt="" /></a><?
									
									$file = true;
								}
								
								?></div>
								<div class="button btn"<?=$file ? ' style="display:none"' : '' ?>>Загрузить изображение</div>
								<a href="" class="xs_red"<?=!$file ? ' style="display:none"' : '' ?>>Удалить фото</a>
							</label>
						</div>
					</div>
				</div><?
				
				xs_input('ms_image_id', $xs_data['result']->ms_image_id, "ID МС для загрузки изображения");
				
				?><br/><br/>
				<div class="xs_flex xs_middle">
					<input type="submit" class="button-primary" name="submit_save" value="Сохранить изменения" />
					<a class="xs_red" onclick="if(!window.confirm('При удалении компонента будут удалены все входящие в него транзакции.\nКрайне не рекомендуется удалять компоненты, привязанные к товарам, это может привести к ошибкам!\nВы действительно хотите удалить компонент <?=$xs_data['result']->name ?>?')) return false;" href="/wp-admin/admin.php?page=store&category_id=57&favorite&delete_component=<?=$xs_data['result']->id ?>">Удалить компонент</a>
				</div>
			</div>
		</div>
	
		<div class="xs_col">
			<div class="xs_block">
				<h2>Конкуренты</h2><?
	
					for($i = 0; $i < $big_data['store_competitors_count']; $i++)
					{
						if($i != 0)
						{
							?><br/><?
							?><hr/><?
						}
						
						xs_input('competitors]['.$i.'][p', $xs_data['result']->competitors[$i]['p'], 'Цена '.xs_get_option('xs_competitor_'.$i), ['type' => 'number', 'min' => 0, 'step' => 1]);
						xs_input('competitors]['.$i.'][l', $xs_data['result']->competitors[$i]['l'], 'Ссылка '.xs_get_option('xs_competitor_'.$i));
					}
					
				?><br/><br/>
				<input type="submit" class="button-primary" name="submit_save" value="Сохранить изменения" />
			</div>
		</div><?
				
	?></div>
</form><?

if(!empty($xs_data['result']->ms_components) && is_array($xs_data['result']->ms_components) && count($xs_data['result']->ms_components))
{
	?><form action="/wp-admin/admin.php?page=<?=$_GET['page'] ?>&section=<?=$_GET['section'] ?>&id=<?=$xs_data['result']->id ?>" method="post">
		<h1>Мой склад</h1>
		<br/>
		<table class="xs_align_center reating_table widefat striped xs_data_table xs_users">
			<thead>
				<tr>
					<td style="width:100px;">Код</td>
					<td>Название</td>
					<td>Себестоимость</td>
					<td>Остатки</td>
					<td>Дней на складе</td><?
					
					/*
					<td>Влияет на себестоимость [<a href="#" onclick="jQuery('.xs_set_price_radios_td').find('input[type=radio][value=y]').prop('checked', true);jQuery('.xs_set_price_radios_td').find('input[type=radio][value=n]').prop('checked', false);return false">да</a>/<a href="#" onclick="jQuery('.xs_set_price_radios_td').find('input[type=radio][value=y]').prop('checked', false);jQuery('.xs_set_price_radios_td').find('input[type=radio][value=n]').prop('checked', true);return false">нет</a>]</td>
					<td>Влияет на дни [<a href="#" onclick="jQuery('.xs_set_days_radios_td').find('input[type=radio][value=y]').prop('checked', true);jQuery('.xs_set_days_radios_td').find('input[type=radio][value=n]').prop('checked', false);return false">да</a>/<a href="#" onclick="jQuery('.xs_set_days_radios_td').find('input[type=radio][value=y]').prop('checked', false);jQuery('.xs_set_days_radios_td').find('input[type=radio][value=n]').prop('checked', true);return false">нет</a>]</td>
					*/
					
				?></tr>
			</thead>
			<tbody><? 
			
				foreach($xs_data['result']->ms_components as $v)
				{
					?><tr>
						<td><a href="https://online.moysklad.ru/app/#good?global_codeFilter=<?=$v['code'] ?>,equals" target="_blank"><?=$v['code'] ?></a></td>
						<td><a href="https://online.moysklad.ru/app/#good?global_codeFilter=<?=$v['code'] ?>,equals" target="_blank"><?=$v['name'] ?></a></td>
						<td><?=wc_price($v['price'], ['decimals' => 2]) ?></td>
						<td><?=$v['quantity'] ?> шт.</td>
						<td><?=$v['days'] ?></td><?
						
						/*
						<td class="xs_set_price_radios_td"><? 

							xs_input('ms]['.$v['code'].'][set_price', $v['set_price'], '', ['type' => 'yesno']);
							
						?></td>
						<td class="xs_set_days_radios_td"><?
							
							xs_input('ms]['.$v['code'].'][set_days', $v['set_days'], '', ['type' => 'yesno']);
						
						?></td>
						*/
						
					?></tr><?  
				}
				
			?></tbody>
		</table><?
		
		/*
		<br/>
		<input type="submit" class="button-primary" name="submit_save_ms" value="Сохранить параметры склада" />
		*/
		
	?></form>
	<br/><br/><?
}
	
if($xs_data['products_53flowers'] || $xs_data['products_cvetyru-vn'])
{
	?><h1>Товары, в которые входят компонент</h1>
	<br/>
	<h2 class="nav-tab-wrapper"><?
	
		if($xs_data['products_53flowers'])
		{
			?><a href="#" onclick="jQuery('.tab_shop').hide(); jQuery('.nav-tab-wrapper a').removeClass('nav-tab-active'); jQuery('.tab_shop[data-tab=53flowers]').show(); jQuery(this).addClass('nav-tab-active'); return false" class="nav-tab nav-tab-active">53flowers.com</a><?
		}
		
		if($xs_data['products_cvetyru-vn'])
		{
			?><a href="#" onclick="jQuery('.tab_shop').hide(); jQuery('.nav-tab-wrapper a').removeClass('nav-tab-active'); jQuery('.tab_shop[data-tab=cvetyru-vn]').show(); jQuery(this).addClass('nav-tab-active'); return false" class="nav-tab<?=!$xs_data['products_53flowers'] ? " nav-tab-active" : "" ?>">Cvetyru-vn.ru</a><?
		}
		
	?></h2><?
	
	if($xs_data['products_53flowers'])
	{
		?><div class="reating_block tab_shop" data-tab="53flowers"><?
			?><table class="xs_align_center reating_table widefat striped xs_data_table xs_users">
				<thead>
					<tr>
						<td style="width:100px;">ID</td>
						<td>Товар</td>
						<td>Количество</td>
					</tr>
				</thead>
				<tbody><? 
				
					foreach($xs_data['products_53flowers'] as $v)
					{ 		
						if($v->post_parent > 0)
							$id = $v->post_parent;
						else
							$id = $v->ID;
						
						?><tr>
							<td><a href="/wp-admin/post.php?post=<?=$id ?>&action=edit" target="_blank"><?=$v->ID ?></a></td>
							<td><a href="/wp-admin/post.php?post=<?=$id ?>&action=edit" target="_blank"><?=$v->post_title ?></a></td>
							<td><?=$v->quantity ?></td>
						</tr><?  
					}
					
				?></tbody>
			</table><?
		?></div><?
	}
	
	if($xs_data['products_cvetyru-vn'])
	{
		?><div class="reating_block tab_shop" data-tab="cvetyru-vn"<?=$xs_data['products_53flowers'] ? " style=\"display:none\"" : "" ?>><?
			?><table class="xs_align_center reating_table widefat striped xs_data_table xs_users">
				<thead>
					<tr>
						<td style="width:100px;">ID</td>
						<td>Товар</td>
						<td>Количество</td>
					</tr>
				</thead>
				<tbody><? 
				
					foreach($xs_data['products_cvetyru-vn'] as $v)
					{ 		
						if($v->post_parent > 0)
							$id = $v->post_parent;
						else
							$id = $v->ID;
						
						?><tr>
							<td><a href="https://cvetyru-vn.ru/wp-admin/post.php?post=<?=$id ?>&action=edit" target="_blank"><?=$v->ID ?></a></td>
							<td><a href="https://cvetyru-vn.ru/wp-admin/post.php?post=<?=$id ?>&action=edit" target="_blank"><?=$v->post_title ?></a></td>
							<td><?=$v->quantity ?></td>
						</tr><?  
					}
					
				?></tbody>
			</table><?
		?></div><?
	}
}
