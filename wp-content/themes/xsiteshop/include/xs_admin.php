<?
if(is_user_logged_in())
{
	global $big_data;

	if(isset($_POST['xs_count_pagenavigation']))
		$_SESSION['xs_count_pagenavigation'] = xs_format($_POST['xs_count_pagenavigation']);
		
	if(isset($_SESSION['xs_count_pagenavigation']))
		$big_data['number'] = $_SESSION['xs_count_pagenavigation'];
	else
		$big_data['number'] = 24; // Количество записей на странице
	
	if(isset($_GET['orderby']))
		$_SESSION['xs_orderby'] = str_replace("\\", "", $_GET['orderby']);
	
	$big_data['orderby'] = isset($_SESSION['xs_orderby']) && !empty($_SESSION['xs_orderby'])
		? $_SESSION['xs_orderby'] 
		: "";
	
	if(isset($_GET['order']))
		$_SESSION['xs_order'] = str_replace("\\", "", $_GET['order']);
	
	$big_data['order'] = isset($_SESSION['xs_order']) && !empty($_SESSION['xs_order']) 
		? $_SESSION['xs_order'] 
		: "";
		
	$big_data['paged'] = isset($_GET['paged']) ? $_GET['paged'] : 1; // Номер текущей страницы

	if(isset($_GET['paged']))
		$big_data['paged'] = (int)xs_format($_GET['paged']);
	elseif(isset($_GET['pagenav'])) 
		$big_data['paged'] = (int)xs_format($_GET['pagenav']);
	else 
		$big_data['paged'] = 1;

	$big_data['offset'] = ($big_data['paged'] - 1) * $big_data['number']; // Отступ 
	
	// Получаем значение сортировки и limit для запросов

	function get_order_limit($xs_orderby, $xs_order, $get_limit = true)
	{		
		global $big_data;
		
		$orderby = $big_data['orderby'];
		$order = $big_data['order'];
		$number = $big_data['number'];
		$paged = $big_data['paged'];
		$offset = $big_data['offset'];
		
		$orderby = empty($orderby) ? $xs_orderby : $orderby;
		$order = empty($order) ? $xs_order : $order;

		$orderby = str_replace("\\", "", $orderby);
		
		$result = "";
		
		if(!empty($orderby) && !empty($order))
			$result .= " ORDER BY ".$orderby." ".$order;
		
		if($get_limit)
			$result .= " LIMIT ".$offset.",".$number;
		
		return $result;		
	}
	
	
	// Получаем ссылку с сортировкой

	function get_order_th($name, $sort)
	{
		global $big_data;
		
		if($big_data['orderby'] == $sort)
			$sortorder = $big_data['order'] == 'ASC' ? 'DESC' : 'ASC';
		else
			$sortorder = 'DESC';
		
		?><th class="sortable <?=mb_strtolower($sortorder, "utf-8") ?>">
			<a href="<?=setUrl($big_data['current_url'], array("orderby", "order"), array($sort, $sortorder));?>">
				<span><?=$name ?></span>
				<span class="sorting-indicator"></span>
			</a>
		</th><?
	}


	// Подключаем helpers-ы для админ панели

	if(is_admin() && isset($_GET['page']) && !empty($_GET['page']))
	{
		if(isset($_GET['section']) && !empty($_GET['section']) && file_exists($_SERVER['DOCUMENT_ROOT']."/wp-content/themes/xsiteshop/include/admin/".$_GET["page"]."/helpers/".$_GET['section'].".php"))
			include $_SERVER['DOCUMENT_ROOT']."/wp-content/themes/xsiteshop/include/admin/".$_GET["page"]."/helpers/".$_GET['section'].".php"; 
		elseif(file_exists($_SERVER['DOCUMENT_ROOT']."/wp-content/themes/xsiteshop/include/admin/".$_GET["page"]."/helpers/index.php") && (!isset($_GET['section']) || empty($_GET['section'])))
			include $_SERVER['DOCUMENT_ROOT']."/wp-content/themes/xsiteshop/include/admin/".$_GET["page"]."/helpers/index.php"; 
	}
	
	
	// Выводим поле для формы в админке

	global $input_id;
	$input_id = 0;

	function xs_input($name = "", $value = "", $title = "", $arg = [])
	{
		global $input_id, $big_data;
		
		$input_id++;

		if(!is($name))
			return false;
		
		if(!is($arg['type']))
			$arg['type'] = "text";
		
		if(!is($arg['required']))
			$arg['required'] = false;
			
		if(is($_POST['post_data']))
			$post_data = xs_format($_POST['post_data']);
		
		if(!is($post_data[$name]))
			$post_data[$name] = $value;
		else
		{
			if(mb_strpos($name, '][', 0, 'utf-8') !== false)
			{
				$s = explode('][', $name);
				
				if(count($s) == 2)
					$post_data[$name] = $post_data[$s[0]][$s[1]];
				
				if(count($s) == 3)
					$post_data[$name] = $post_data[$s[0]][$s[1]][$s[2]];
				
				if(count($s) == 4)
					$post_data[$name] = $post_data[$s[0]][$s[1]][$s[2]][$s[4]];
			}
		}

		$arAction = [];
		
		if(isset($arg['actions']) && is_array($arg['actions']) && count($arg['actions']) > 0)
		{
			
			foreach($arg['actions'] as $k => $v)
				$arAction[] = $k.' = "'.$v.'"';
		}

		$arData = [];
		
		if(isset($arg['data']) && is_array($arg['data']) && count($arg['data']) > 0)
		{
			
			foreach($arg['data'] as $k => $v)
				$arData[] = 'data-'.$k.' = "'.$v.'"';
		}
				
		if(is($arg['type']) && $arg['type'] == 'hidden')
		{
			?><input name="post_data[<?=$name ?>]" value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>"<?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> type="hidden" /><?
		}
		else
		{
			?><div class="type <?=$arg['type'] ?> xs_flex xs_middle input_container<?=$arg['required'] ? ' required' : '' ?> <?=$arg['class'] ?>"><?
			
				if($arg['type'] != 'checkbox' && is($title))
				{
					?><div class="xs_label"><?=$title ?></div><?
				}
				
				?><div class="input"><?
					
					if($arg['type'] == 'text')
					{
						?><input <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" type="text" /><?
					}
					
					if($arg['type'] == 'color')
					{
						?><input <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" type="color" /><?
					}
					
					if($arg['type'] == 'autocomplete')
					{
						?><input <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" autocomplete="off" class="xs_autocomplete" type="text" data-source="<?=implode("|", $arg['options'])?>" /><?
					}
					
					if($arg['type'] == 'autocomplete_ajax')
					{
						?><span class="xs_relative"><input <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" data-helper="<?=$arg['options']?>" autocomplete="off" class="xs_autocomplete_ajax" type="text" /><span class="result"></span></span><?
					}
					
					if($arg['type'] == 'phone')
					{
						$post_data[$name] = string_to_int($post_data[$name]); 
						
						?><input <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> value="<?=is($post_data[$name]) ? substr(string_to_int($post_data[$name]), 1)  : '' ?>" class="xs_phone" type="text" /><?
					}
					
					if($arg['type'] == 'email')
					{
						?><input <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" type="email" /><?
					}
					
					if($arg['type'] == 'date')
					{
						if(!empty($post_data[$name]) && strtotime($post_data[$name]) == 0)
							$post_data[$name] = '';
						
						?><input <?=implode(" ", $arAction) ?> class="<?=strpos($name, 'birstdate') !== false ? 'xs_datepicker-here' : 'datepicker-here' ?>"<?=$arg['disabled'] ? ' disabled' : ''?> autocomplete="off" data-position="bottom left"<?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" type="text" /><?
					}
					
					if($arg['type'] == 'time')
					{
						if(!empty($post_data[$name]) && strtotime($post_data[$name]) == 0)
							$post_data[$name] = '';
						
						?><input <?=implode(" ", $arAction) ?> class="xs_time"<?=$arg['disabled'] ? ' disabled' : ''?> <?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" type="text" /><?
					}
					
					if($arg['type'] == 'datetime')
					{
						if(!empty($post_data[$name]) && strtotime($post_data[$name]) == 0)
							$post_data[$name] = '';
						
						?><input <?=implode(" ", $arAction) ?> class="xs_datetime-here"<?=$arg['disabled'] ? ' disabled' : ''?> autocomplete="off" data-position="bottom left"<?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" type="text" /><?
					}
					
					elseif($arg['type'] == 'select')
					{
						?><select <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?>><?
					
						foreach($arg['options'] as $key => $val)
						{
							?><option value="<?=$key ?>" <?=$key == $post_data[$name] ? 'selected' : "" ?>><?=$val ?></option><?
						}
						
						?></select><?				
					}
					
					elseif($arg['type'] == 'multiselect')
					{
						?><select <?=implode(" ", $arAction) ?> multiple name="post_data[<?=$name ?>][]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?>><?
					
						foreach($arg['options'] as $k => $v)
						{
							?><option value="<?=$k ?>" <?=in_array($v, $post_data[$name]) || in_array($k, $post_data[$name]) ? 'selected' : "" ?>><?=$v ?></option><?
						}
						
						?></select><?				
					}
					
					elseif($arg['type'] == 'selectator')
					{
						?><select <?=implode(" ", $arAction) ?> class="selectator" multiple name="post_data[<?=$name ?>][]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?>><?
					
						foreach($arg['options'] as $k => $v)
						{
							?><option value="<?=$k ?>" <?=in_array($v, $post_data[$name]) || in_array($k, $post_data[$name]) ? 'selected' : "" ?>><?=$v ?></option><?
						}
						
						?></select><?				
					}
					
					if($arg['type'] == 'checkbox')
					{
						?><input <?=implode(" ", $arAction) ?> id="radio_<?=$name ?>_<?=$input_id ?>"<?=$arg['disabled'] ? ' disabled' : ''?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?><?=(isset($post_data[$name]) && $post_data[$name] == 'y') || $arg['options'] == 'checked' ? ' checked' : '' ?> value="y" type="checkbox" /><?
						
						?><label for="radio_<?=$name ?>_<?=$input_id ?>"><?=$title ?></label><?
					}
					
					if($arg['type'] == 'radio')
					{					
						$i = 0;
						
						foreach($arg['options'] as $k => $v)
						{
							?><div class="radio_item"><?
								?><input <?=implode(" ", $arAction) ?> id="radio_<?=$name ?>_<?=$input_id ?>_<?=$i ?>"<?=$arg['disabled'] ? ' disabled' : ''?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' checked' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?><?=$k == $post_data[$name] ? 'checked' : "" ?> value="<?=$k ?>" type="radio" /><?
						
								?><label class="radio" for="radio_<?=$name ?>_<?=$input_id ?>_<?=$i ?>"><?=$v ?></label><?
								$i++;
							?></div><?
						}
					}
					
					if($arg['type'] == 'yesno')
					{
						?><div class="yesno"><?
						
							?><input <?=implode(" ", $arAction) ?> <?=implode(" ", $arData) ?> id="radio_<?=$name ?>_<?=$input_id ?>_1"<?=$arg['disabled'] ? ' disabled' : ''?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' checked' : '' ?><?=(isset($post_data[$name]) && $post_data[$name] == 'y') || $values == 'checked' ? ' checked' : '' ?> value="y" type="radio" /><?
							
							?><label class="radio yes" for="radio_<?=$name ?>_<?=$input_id ?>_1">Да</label><?

							?><input <?=implode(" ", $arAction) ?> <?=implode(" ", $arData) ?> id="radio_<?=$name ?>_<?=$input_id ?>_2"<?=$arg['disabled'] ? ' disabled' : ''?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' checked' : '' ?><?=(!isset($post_data[$name]) || $post_data[$name] != 'y') || $values == 'checked' ? ' checked' : '' ?> value="n" type="radio" /><?
							
							?><label class="radio no" for="radio_<?=$name ?>_<?=$input_id ?>_2">Нет</label><?
							
						?></div><?
					}
					
					if($arg['type'] == 'textarea')
					{
					?><textarea <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?>><?=is($post_data[$name]) ? $post_data[$name] : '' ?></textarea><?
					}
					
					if($arg['type'] == 'number')
					{
					?><input <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" type="number" min="<?=$arg['min'] ?>" max="<?=$arg['max'] ?>" step="<?=$arg['step'] ?>" /><?
					}
					
					if($arg['type'] == 'password')
					{
					?><input <?=implode(" ", $arAction) ?> name="post_data[<?=$name ?>]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?><?=$arg['placeholder'] ? ' placeholder="'.$arg['placeholder'].'"' : '' ?><?=$arg['form'] ? ' form="'.$arg['form'].'"' : '' ?> value="<?=is($post_data[$name]) ? $post_data[$name] : '' ?>" type="password" /><?
					}
					
					
					if($arg['type'] == 'file')
					{
						if(is_array($value) && count($value) > 0)
						{
							?><div class="attach_files"><?
								
							foreach($value as $v)
							{
								?><div class="file"><a href="<?=$big_data['attach_url'].$v['name'] ?>" target="_blank"><?=$v['o_name'] ?></a> <span data-file="<?=$v['name'] ?>" title="Удалить" class="xs_delete_file"></span></div><?
							}
								
							?></div><?
						}
						
						?><label class="input_upload"><? 
							?><input onchange="if(jQuery(this).val() != '') {jQuery(this).next('.xs_attach_button').addClass('hover').html('Файл прикреплён')} else {jQuery(this).next('.attache').removeClass('hover').text('Прикрепить файл(ы)')}" type="file" name="attach[]"<?=$arg['required'] ? ' required' : '' ?><?=$arg['disabled'] ? ' disabled' : ''?> multiple accept=".doc,.docx,image/*,.pdf,.xls,.xlsx,.ppt,.pptx,.xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"><? 
							?><div class="xs_attach_button btn gray">Прикрепить файл(ы)</div><?
						?></label><?
					}
					
					if(!empty($arg['after_text']))
						echo " ".$arg['after_text'];
					
				?></div><?
			?></div><?
		}
	}
	

	// Добавляем пункты меню в админку

	function mytheme_add_admin_admin()
	{
		global $wpdb;

		add_menu_page('Склад', 'Склад', 'manage_options', 'store', 'mytheme_admin_store', 'dashicons-networking', 50);
		add_submenu_page('store', 'Отчёт', 'Отчёт', 'manage_options', 'admin.php?page=store&section=report', '');
		add_submenu_page('store', 'Группы компонентов', 'Группы компонентов', 'manage_options', 'admin.php?page=store&section=group', '');
		add_submenu_page('store', 'Группы коэффициентов', 'Группы коэффициентов', 'manage_options', 'admin.php?page=store&section=coefficient_groups', '');
		add_submenu_page('store', 'Проверить цены', 'Проверить цены', 'manage_options', 'admin.php?page=store&section=check', '');
		add_submenu_page('store', 'Калькулятор', 'Калькулятор', 'manage_options', 'admin.php?page=calculate', ''); 

		add_menu_page('Поиск', 'Поиск', 'manage_options', 'search', 'mytheme_admin_store', 'dashicons-search', 55);
		add_menu_page('Калькулятор', 'Калькулятор', 'read', 'calculate', 'mytheme_admin_store', 'dashicons-networking', 50);
	}

	add_action('admin_menu', 'mytheme_add_admin_admin');


	function mytheme_admin_store()
	{
		wp_enqueue_script('media-upload');
		
		global $big_data, $wpdb, $xs_total, $xs_data, $xs_filter, $setFilter, $xs_query, $xs_set_total, $xs_check_data;
		
		$i = 0;
		if(!did_action('wp_enqueue_media'))
			wp_enqueue_media();
		
		?><div class="wrap xs_wrap"><?

			?><div class="xs_content_container <?=$_GET["page"] ?>"><?

				if(isset($xs_set_total) && $xs_set_total > 0)
					$xs_total = $xs_set_total;
				else
				{
					if(isset($xs_query) && !empty($xs_query))
					{
						if(mb_strpos($xs_query, 'GROUP BY', 0, 'utf-8') === false)
							$xs_total = $wpdb->get_var(preg_replace('|(SELECT).+(FROM)|isU', "$1"." COUNT(*) "."$2",$xs_query));
						else
						{
							$r = $wpdb->get_results($xs_query);
							$xs_total = count($r);
						}
					}
					else
						$xs_total = 0;
				}
				
				if($_GET['section'] != 'report' && $_GET['section'] != 'group' && $_GET['section'] != 'check' && $_GET['section'] != 'coefficient_groups')
					xs_get_message();
				
				if(isset($_GET['page']) && !empty($_GET['page']))
					if(isset($_GET['section']) && !empty($_GET['section']))
						include $_SERVER['DOCUMENT_ROOT']."/wp-content/themes/xsiteshop/include/admin/".$_GET["page"]."/".$_GET['section'].".php"; 
					else
						include $_SERVER['DOCUMENT_ROOT']."/wp-content/themes/xsiteshop/include/admin/".$_GET["page"]."/index.php"
				
				
				?><div class="xs_pages"><?
					
					if(
						mb_strpos($_GET['section'], "detail", 0, "utf-8") === false &&
						$_GET['section'] != "divisions" &&
						$_GET['page'] != "courses" &&
						$_GET['section'] != "report" &&
						$_GET['section'] != "group" &&
						$_GET['section'] != "check" &&
						$_GET['section'] != "coefficient_groups" &&
						$xs_total > 0
					)
					{
						?><form method="post" action="" class="xs_flex"><?
							
							?><div><?
							
								echo paginate_links(array(  
									  'base'      => isset($_GET["section"]) ? 'admin.php?page='.$_GET["page"].'&section='.$_GET["section"].'%_%' : 'admin.php?page='.$_GET["page"].'%_%',  
									  'format'    => '&paged=%#%',  
									  'current'   => $big_data['paged'],  
									  'total'     => ceil($xs_total / $big_data['number']),  
									  'prev_next' => false,  
									  'type'      => 'list',  
								)); 

							?></div><?
							
							?><div><?
								?>Записей на странице:
								<select onchange="jQuery(this).parents('form').submit()" name="xs_count_pagenavigation">
									<option <? if($big_data['number'] == 24) echo "selected" ?> value="24">24</option>
									<option <? if($big_data['number'] == 48) echo "selected" ?> value="48">48</option>
									<option <? if($big_data['number'] == 100) echo "selected" ?> value="100">100</option>
									<option <? if($big_data['number'] == 200) echo "selected" ?> value="200">200</option><?
									
									/*
									<option <? if($big_data['number'] == 9999999999) echo "selected" ?> value="9999999999">Все</option>
									*/
									
								?></select><?
							?></div><?
						?></form><? 
					}
				?></div><?
			
			?></div>
		</div><?
	}
}
