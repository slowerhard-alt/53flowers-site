<? 
function mytheme_admin()
{
    wp_enqueue_script('media-upload');
	
    global $themename, $shortname, $options;
    $i = 0;
	
    if (!did_action('wp_enqueue_media'))
        wp_enqueue_media();
	
    ?><div class="wrap xs_wrap"><?
	
        if ($_GET['saved'] == true)
            echo '<div id="message" class="updated fade"><p><strong>Настройки '.$themename.' успешно сохранены</strong></p></div>';
        
		
		?><div class="xs_opts">
            <form method="post">
                <input name="tab" type="hidden" value="<?= $_GET["tab"]; ?>"/>

                <h2 class="nav-tab-wrapper">
                    <?php
                    $i = 0;
                    foreach ($options as $tab) 
					{
						if(!isset($_GET["tab"]) || empty($_GET["tab"]))
							$_GET["tab"] = 'main';
						
                        $i++;
                        if ($_GET["tab"] == $tab['code']) $xs = $tab; ?>
                        <a href="admin.php?page=xs_setting&tab=<?= $tab['code']; ?>"
                           class="nav-tab<? if ($tab['code'] == $_GET["tab"]) echo " nav-tab-active"; ?>"><?= $tab['name']; ?></a>
                    <? } ?>
                </h2>

                <div class="xs_content"><?
                    
					if (!isset($xs))
                        $xs = $options[0];
                        $tab = $xs;
                    
					?><div class="xs_wind"><?
					
						if(isset($tab['desc']) && !empty($tab['desc']))
						{
							?><p class="desc"><?=$tab['desc'] ?><br/><br/></p><?
						}
						
                        foreach ($tab["group"] as $group) { ?>
                            <div class="xs_group" id="<?=$group['name'] != "" ? "id_".$group['name'] : ""; ?>">
                                <? if ($group['name'] != "") { ?>
                                    <h3 class="xs_group_name"><?= $group['name']; ?></h3>
                                <? } ?>
                                <? if ($group['desc'] != "") { ?>
                                    <p class="xs_group_name"><?= $group['desc']; ?></p>
                                <? } ?>
                                <? if (!isset($group['type'])) { ?>
                                    <table class="xs_options">
                                        <? foreach ($group["options"] as $option) { ?>
                                            <tr>
                                                <th>
                                                    <?= $option['name']; ?>
                                                </th>
                                                <td class="option">
                                                    <? xs_get_input_option($option); ?>
                                                </td>
                                                <td>
                                                    <? if ($option['desc']) { ?>
                                                        <div class="xs_desc">
                                                            <?= $option['desc']; ?>
                                                        </div>
                                                    <? } ?>
                                                </td>
                                            </tr>
                                        <? } ?>
                                    </table>
                                <? } else { ?>
                                    <table class="xs_options">
                                        <tr>
                                        <? foreach ($group["options"] as $option) { ?>
                                                <td class="option gr">
                                                    <label for="<?= $option['id']; ?>"><?= $option['name']; ?></label> <? xs_get_input_option($option); ?>
                                                    <? if ($option['desc']) { ?>
                                                        <p>
                                                        <?= $option['desc']; ?>
                                                        </p>
                                                    <? } ?>
                                                </td>

                                        <? } ?>
                                        </tr>
                                    </table>
                                <? } ?>
                            </div>
                        <? } ?>
                    </div>
                </div>
                <input type="hidden" name="action" value="saved"/>

                <p class="submit">
                    <input name="save" class="button-primary" type="submit" value="Сохранить изменения">
                </p>
            </form>
        </div>
    </div>

<?php } ?>