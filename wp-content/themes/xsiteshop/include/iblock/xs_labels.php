<?

// Регистрируем новую таксономия "Ярлыки"

function label_tax() {
 
    $labels = array(
        'name'                       => 'Ярлыки',
        'singular_name'              => 'Ярлыки товаров',
        'menu_name'                  => 'Ярлыки',
        'all_items'                  => 'Ярлыки',
        'parent_item'                => 'Ярлыки',
        'parent_item_colon'          => 'Ярлыки:',
        'new_item_name'              => 'Новый ярлык',
        'add_new_item'               => 'Добавить новый ярлык',
        'edit_item'                  => 'Редактировать ярлык',
        'update_item'                => 'Обновить ярлык',
        'search_items'               => 'Найти',
        'add_or_remove_items'        => 'Добавить или удалить ярлык',
        'choose_from_most_used'      => 'Поиск среди популярных',
        'not_found'                  => 'Не найдено',
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
		'meta_box_cb' 				 => 'post_categories_meta_box',
	);
    register_taxonomy( 'label', 'product', $args );
	register_taxonomy_for_object_type( 'label', 'product' );
}
 
add_action( 'init', 'label_tax', 0 ); // инициализируем

add_action( "label_add_form_fields", 'add_term_label', 10, 2 );
add_action( "label_edit_form_fields", 'update_term_label', 10, 2 );
add_action( "created_label", 'updated_term_label', 10, 2 );
add_action( "edited_label", 'updated_term_label', 10, 2 );

add_filter( "manage_edit-label_columns", 'add_label_column');
add_filter( "manage_label_custom_column", 'fill_label_column', 10, 3 );


## поля при создании термина
function add_term_label( $taxonomy )
{
	?><div class="xs_flex xs_start">
		<div class="form-field term-group">
			<label><?php _e('Цвет фона', 'default'); ?></label>
			<div class="term__image__wrapper">
				<input type="text" name="xs_label_bg" class="color" value="#e60000" />
			</div>
		</div>&nbsp;&nbsp;&nbsp;<?
		
		?><div class="form-field term-group">
			<label><?php _e('Цвет текста', 'default'); ?></label>
			<div class="term__image__wrapper">
				<input type="text" name="xs_label_color" class="color" value="#ffffff" />
			</div>
		</div>
	</div><br/><?
}


## поля при редактировании термина
function update_term_label( $term, $taxonomy )
{
	$bg = get_term_meta( $term->term_id, 'xs_label_bg', true );
	
	?><tr class="form-field term-group-wrap">
		<th scope="row"><?php _e( 'Цвет фона', 'default' ); ?></th>
		<td>
			<div class="term__image__wrapper">
				<input type="text" name="xs_label_bg" class="color" value="<?=$bg ? $bg : "#e60000"; ?>" />
			</div>
		</td>
	</tr><?
	
	$color = get_term_meta( $term->term_id, 'xs_label_color', true );
	
	?><tr class="form-field term-group-wrap">
		<th scope="row"><?php _e( 'Цвет текста', 'default' ); ?></th>
		<td>
			<div class="term__image__wrapper">
				<input type="text" name="xs_label_color" class="color" value="<?=$color ? $color : "#ffffff"; ?>" />
			</div>
			<br/>
		</td>
	</tr><?
}


## Добавляет колонку в таблицу терминов

function add_label_column( $columns )
{
	$columns['example'] = '';
	return $columns;
}


function fill_label_column( $string, $column_name, $term_id )
{
	if($column_name == 'example')
	{
		$color = get_term_meta( $term_id, 'xs_label_color', 1 );
		$bg = get_term_meta( $term_id, 'xs_label_bg', 1 );
		
		if(!$color)
			$color = "#ffffff";
		
		if(!$bg)
			$bg = "#e60000";
		
		$string = '<div style="color:'.$color.';background:'.$bg.';padding:4px 10px;display:inline-block">Пример</div>';
	}
	
	return $string;
}


function updated_term_label( $term_id, $tt_id )
{
	if( isset($_POST['xs_label_bg']) && !empty($_POST['xs_label_bg']))
		update_term_meta( $term_id, 'xs_label_bg', xs_format($_POST['xs_label_bg']));
	else
		delete_term_meta( $term_id, 'xs_label_bg' );
	
	if( isset($_POST['xs_label_color']) && !empty($_POST['xs_label_color']))
		update_term_meta( $term_id, 'xs_label_color', xs_format($_POST['xs_label_color']) );
	else
		delete_term_meta( $term_id, 'xs_label_color' );
}