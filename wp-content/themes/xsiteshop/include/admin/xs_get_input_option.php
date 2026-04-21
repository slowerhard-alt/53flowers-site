<?
function xs_get_input_option($option)
{
    $value = xs_get_option($option['id']);

    if ($option['type'] == "text") { ?>
        <input type="<?= $option['type']; ?>" name="<?= $option['id']; ?>" value="<?= $value; ?>"/>
    <? }
    if ($option['type'] == "number") { ?>
        <input type="<?= $option['type']; ?>" name="<?= $option['id']; ?>" value="<?= $value; ?>"
               min="<?= $option['min']; ?>" step="<?= $option['step']; ?>"/>
    <? }
    if ($option['type'] == "number_color") { ?>
		<div class="xs_flex xs_middle">
			<input type="number" name="<?= $option['id']; ?>[k]" value="<?= $value['k']; ?>"
               min="<?= $option['min']; ?>" step="<?= $option['step']; ?>"/>
			   &nbsp;&nbsp;&nbsp;
			<input type="color" name="<?= $option['id']; ?>[c]" value="<?= $value['c']; ?>"/>
		</div>
    <? }
    if ($option['type'] == "color") { ?>
        <input type="color" name="<?= $option['id']; ?>" value="<?= $value; ?>">
    <? }
    if ($option['type'] == "checkbox") { ?>
        <input type="<?= $option['type']; ?>" name="<?= $option['id'];  ?>" id="<?= $option['id'];  ?>" <? if ($value == true) echo "checked";?> />
    <? }
    if ($option['type'] == "textarea") { ?>
        <textarea style="height:150px" name="<?= $option['id']; ?>"><?= $value; ?></textarea>
    <? }
    if ($option['type'] == "select") { ?>
        <select name="<?= $option['id']; ?>">
            <? foreach ($option['option'] as $val) { ?>
                <option <? if (xs_get_option($option['id']) == $val['value']) echo "selected"; ?>
                    value="<?= $val['value']; ?>"><?= $val['name']; ?></option>
            <? } ?>
        </select>
    <? }
    if ($option['type'] == "range") { ?>
        <input type="<?= $option['type']; ?>" name="<?= $option['id']; ?>" min="<?= $option['min']; ?>"
               max="<?= $option['max']; ?>" step="<?= $option['step']; ?>" value="<?= $value; ?>">
    <? }
    if ($option['type'] == "file") {
        $val = xs_get_option($option['id']);

        $src = wp_get_attachment_url($val);

        ?>
		<div class="image-upload">
			<img data-src="<?= $src; ?>" src="<?= $src; ?>"/>
		</div>
        <div class="uploader">
            <input type="hidden" name="<?= $option['id']; ?>" id="<?= $option['id']; ?>" value="<?= xs_get_option($option['id']); ?>"/>
            <button type="submit" class="upload_image_button button">Загрузить</button>
            <button type="submit" class="remove_image_button button">&times;</button>
        </div>
    <? }
    if ($option['type'] == "pages") { ?>
		<? $opt = json_decode(xs_get_option($option['id']), true);?>
        <div class="vertical_scroll">
			<ul class="categorychecklist">
				<? $args = array(
					'sort_order' => 'ASC',
					'sort_column' => 'post_title',
					'hierarchical' => 0,
				); 
				$pages = get_pages($args);
				foreach($pages as $post){ ?> 
					<li>
						<label>
							<input name="<?=$option['id'];?>[<?=$post->ID;?>]" <? if(isset($opt[$post->ID]) && $opt[$post->ID] == "on") echo "checked";?> type="checkbox"> <?=$post->post_title;?>
						</label>
					</li>
				<? } ?>
			</ul>
		</div>
    <? }
    if ($option['type'] == "categories") { ?>
		<? $opt = json_decode(xs_get_option($option['id']), true);?>
        <div class="vertical_scroll">
			<ul class="categorychecklist">
				<? $args = array(
					'type' 			=> 'post',
					'sort_order' 	=> 'ASC',
					'sort_column' 	=> 'name',
					'taxonomy'      => 'category',
					'hierarchical' 	=> 0,
				); 
				$pages = get_categories($args);
				foreach($pages as $post){ ?> 
					<li>
						<label>
							<input name="<?=$option['id'];?>[<?=$post->cat_ID;?>]" <? if(isset($opt[$post->cat_ID]) && $opt[$post->cat_ID] == "on") echo "checked";?> type="checkbox"> <?=$post->name;?>
						</label>
					</li>
				<? } ?>
			</ul>
		</div>
    <? }
}