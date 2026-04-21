<?

$banners = get_posts(
	array(
		'post_type'       => 'banner',
		'post_status'     => 'publish'
	)
); 

if(count($banners) > 0)
{
?><div class="wr_banners"><?
	?><div class="container"><?

		?><div class="banners xs_flex xs_wrap"><?
			
			foreach($banners as $v)
			{
				$url = wp_get_attachment_url( get_post_thumbnail_id($v->ID) );
				$xs_options = get_post_meta($v->ID, 'xs_options', true);
				
				$style = array();
				
				$style[] = "height:".xs_get_option('xs_banner_height', 240)."px";
				$style[] = "width:".xs_get_option('xs_banner_width', 30)."%";
				
				if(!empty($xs_options['color']))
					$style[] = "color:".$xs_options['color'];
				
				if(!empty($xs_options['color_bg']))
				{
					if(!empty($xs_options['color_bg2']))
						$style[] = "background:linear-gradient(to bottom, ".$xs_options['color_bg']." 0%,".$xs_options['color_bg2']." 100%)";
					else
						$style[] = "background:".$xs_options['color_bg'];
				}
				
				?><div class="item" style="<?=implode(';',$style)?>"><?
				
					if($url && !empty($url))
					{
						?><div class="image" data-src="<? echo $url ?>"></div><?
					}
				
					if(!empty($xs_options['color_filter']) && $xs_options['color_filter'] != "rgba(0, 0, 0, 0)")
					{
						?><div class="filter" style="background:<?=$xs_options['color_filter'] ?>"></div><?
					}
					
					if(is_super_admin())
					{
						?><a target="_blank" href="/wp-admin/post.php?post=<?=$v->ID ?>&action=edit" class="xs_link_edit"></a><?
					}
						
					if(!empty($xs_options['url']))
					{
						?><a <? if($xs_options['target'] == 'y') echo 'target="_blank" ' ?>href="<?=$xs_options['url'] ?>" class="full_link"></a><?
					}
					
					if(!empty($v->post_excerpt))
					{
						?><div class="name"><?=trim($v->post_excerpt); ?></div><?
					}
					
					if(!empty($v->post_content))
					{
						?><div class="description"><?=trim($v->post_content) ?></div><?
					}
					
					if($xs_options['arrow'])
					{
						?><b class="hi-icon-white"></b><?
					}
					
				?></div><?
			}
			
			echo str_repeat('<div class="item empty"></div>', 4);
			
		?></div><?
	?></div><?
?></div><?
}

/*
<?	
	$loop = get_posts(
		array(
			'post_type'       => 'banner',
			'post_status'     => 'publish'
		)
	); 
?>
<? if(count($loop) > 0){ ?>
	<div class="banners col<?=get_option('xs_banners_count_in_row');?>">
		<?php foreach ( $loop as $banner ) { ?>
			<? $url = wp_get_attachment_url( get_post_thumbnail_id($banner->ID) ) ?>
			<? if ($url) { ?>
				<div class="banner">
					<div class="cont <?if(get_post_meta($banner->ID, 'xs_zoom', TRUE) == true) echo 'zoom';?>" style="height:<?=get_option('xs_banner_height');?>px">
						<? $link = get_post_meta($banner->ID, 'xs_bnrurl', TRUE); ?>
						<? if(isset($link) && !empty($link)){?>
							<a href="<?=$link;?>" <?if(get_post_meta($banner->ID, 'xs_target', TRUE) == true) echo 'target="_blank"';?>>
						<? } ?>
							<div class="fade lazyload" data-src="<?=$url;?>" style="height:<?=get_option('xs_banner_height');?>px"></div>
							<div class="text">
								<?if(get_post_meta($banner->ID, 'xs_show_title', TRUE) == true) { ?>
									<span class="title" <? if($color = get_post_meta($banner->ID, 'xs_title_color', TRUE)) { ?> style="color:<?=$color?>" <? } ?>><?=$banner->post_title;?></span>
								<? } ?>
								<?if(get_post_meta($banner->ID, 'xs_show_description', TRUE) == true) { ?>
									<span class="desc" <? if($color = get_post_meta($banner->ID, 'xs_description_color', TRUE)) { ?> style="color:<?=$color?>" <? } ?>>
										<?=get_post_meta($banner->ID, 'xs_bnrdesc', TRUE)?>
									</span>
								<? } ?>
							</div>
						<? if(isset($link) && !empty($link)){?>
							</a>
						<? } ?>
					</div>
				</div>
			<? } ?>
		<? } ?>
		<div class="clear"></div>
	</div>
<? } ?>