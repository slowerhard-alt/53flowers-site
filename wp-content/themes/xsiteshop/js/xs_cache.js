jQuery(function($)
{
	if(!$('body').hasClass('cache_page') && !$('body').hasClass('login_user'))
	{
		$.ajax({
			url: '/wp-content/themes/xsiteshop/load/xs_cache.php',
			method: 'post',
			cache: false,
			data: {
				url: window.location.href,
			},
			success: function(data)
			{
				//console.log(data) 
			}
		})
	}
	
	$('[data-load]').each(function()
	{
		var e = $(this)
		
		if(!e.hasClass('loaded'))
		{
			$.ajax({
			url: '/wp-content/themes/xsiteshop/load/'+e.data('load')+'.php',
				cache: false,
				success: function(data)
				{
					e.addClass('loaded').html(data)
				}
			})
		}
	})
})