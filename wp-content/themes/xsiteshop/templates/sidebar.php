<div class="goods__filter"><? 

	if(is_active_sidebar('shop'))
	{
		?><div class="goods__filter-filter"><?
			
			dynamic_sidebar('shop');
			
		?></div><?
	}

	?><div class="goods__filter-sort"><?

		if(is_active_sidebar('shop'))
		{
			?><div class="goods__filter-price-icon" onclick="jQuery('.goods__sidebar .widget').removeClass('roll_up');jQuery('.goods__sidebar .widget.widget_price_filter').addClass('roll_up');jQuery('.select').removeClass('active');jQuery(this).parents('.goods__content').removeClass('space--tune');jQuery(this).parents('.goods__body').removeClass('space--tune');jQuery('.goods__filter').toggleClass('goods__filter--price')">
				<svg class="icon-price">
					<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-price">
				</svg>				
			</div><?
		}
		
		?><div class="goods__filter-sort-icon">
			<svg class="icon-compare">
				<use xlink:href="<?php bloginfo('template_url'); ?>/images/icons/sprite.svg#icon-compare">
			</svg>
		</div>
	</div>
</div>