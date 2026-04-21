var current_url = location.href,
	checkout_url = "/checkout/",
	cart_url = "/cart/",
	cart_timer,
	single_product_slider

function xs_copy(str) 
{
	var area = document.createElement('textarea');

	document.body.appendChild(area);  
	area.value = str;
	area.select();
	document.execCommand("copy");
	document.body.removeChild(area);  
}


// Обновление цены в карточке товара

function update_single_product_price()
{
	var c = jQuery('.p-product__price'),
		c_b = jQuery('.p-product__bonuse-value'),
		p = 0,
		p_r = 0,
		image_id = 0,
		image_src = "",
		text_price = "",
		percent = "",
		id = 0
	
	if(c.length)
	{
		if(jQuery('.p-product__lead .attribute input[type=radio]:not(:disabled):visible').length > 0)
		{
			jQuery('.p-product__lead .attribute').each(function()
			{
				if(jQuery(this).find('input[type=radio]:checked').length == 0 && jQuery(this).find('input[type=radio][data-is_default="y"]:not(:disabled):visible').length)
					jQuery(this).find('input[type=radio][data-is_default="y"]:not(:disabled):visible:first').prop('checked', true)
				else if(jQuery(this).find('input[type=radio]:checked').length == 0 && jQuery(this).find('input[type=radio]:not(:disabled):visible').length)
					jQuery(this).find('input[type=radio]:not(:disabled):visible:first').prop('checked', true)
			})
		}
		
		if(jQuery('.p-product__lead input[type=radio]:checked').length)
		{
			var xs_data = jQuery('.p-product__lead .variations_form').data('product_variations')
			
			for(var i in xs_data)
			{
				var d = xs_data[i],
					active = true
				
				for(var j in d.attributes)
				{
					if(jQuery("input[name="+j+"]:checked").length == 0)
						jQuery("input[name="+j+"]:first").prop('checked', true)
					
					if((jQuery("input[name="+j+"]:checked").val() != d.attributes[j]) && d.attributes[j] != '')
						active = false
				}
				
				if(active == true)
				{
					console.log(d)
					p = d.price
					p_r = d.regular_price
					percent = d.percent
					image_id = d.image_id
					image_src = d.image_src
					id = d.variation_id
				}	
			}
		}
		else
		{
			var i = jQuery('.p-product__lead input[type=hidden][name=add-to-cart]')
			
			p = i.data('price')
			p_r = i.data('regular_price')
			percent = i.data('percent')
			image_id = i.data('image_id')
			image_src = i.data('image_src')
			id = i.val()
		}


		p_r = parseInt(p_r)
		
		if(p_r > 0)		
			p_r = p_r - p
		
		if(jQuery('.p-decoration_products__select option:selected').length)
		{
			jQuery('.p-decoration_products__select option:selected').each(function()
			{
				p = p + jQuery(this).data('price')
			})
		}
		
		if(jQuery('.p-addition_products__select option:selected').length)
		{
			jQuery('.p-addition_products__select option:selected').each(function()
			{
				p = p + jQuery(this).data('price')
			})
		}
	
		if(p_r > 0)
		{
			p_r = p_r + p
			
			text_price += '<span class="expire"><span class="woocommerce-Price-amount amount"><bdi>' + p_r + '&nbsp;<span class="woocommerce-Price-currencySymbol">руб.</span></bdi></span></span> '
		}
		
		text_price += '<span class="valid"><span class="woocommerce-Price-amount amount"><bdi>' + p + '&nbsp;<span class="woocommerce-Price-currencySymbol">руб.</span></bdi></span></span>'
		
		c.html(text_price)
		
		if(c_b.length)
			c_b.text(Math.round(p*0.05))
		
		
		if(percent == "" || percent == "0" || percent == 0 || percent == undefined)
			jQuery('.p-product__images-inn .sale_percent').remove()
		else
		{
			if(!jQuery('.p-product__images-inn .sale_percent').length)
				jQuery('.p-product__images-inn').prepend('<span class="sale_percent"></span>')
			
			jQuery('.p-product__images-inn .sale_percent').text(percent)
		}
		
		if(image_id != "" && image_id != "0" && image_id != 0)
		{
			if(jQuery('.p-product__images-inn[data-image_id="' + image_id + '"]').length)
			{
				jQuery('.p-product__imagesslider').slick('slickGoTo', jQuery('.p-product__images-inn[data-image_id="' + image_id + '"]').data('slick-index'), true)
				
				jQuery('.p-product__images-inn[data-image_id="' + image_id + '"] .p-product__img').attr('src', image_src)
			}
			else
			{
				jQuery('.p-product__images-inn.slick-current .p-product__img').attr('src', image_src)
			}
		}
		
		jQuery("[name='add-to-cart']").val(id)
		
		if(id == 0)
			jQuery('.p-product__pay_buttons').hide()
		else
			jQuery('.p-product__pay_buttons').show()
	}
}


// Вывод полей в оформлении заказа

function set_checkout_fields()
{
	jQuery('.woocommerce-billing-fields input[type=radio]:checked').each(function()
	{
		var e = jQuery(this),
			v = e.val(),
			n = e.attr('name'),
			b = jQuery('.block'+n)
			
		if(b.length > 0)
		{
			if(b.data('show') == v)
				b.removeClass('hide')
			else
				b.addClass('hide')
		}
	})
}


function tpaneScroll()
{
	var scrollTop = parseInt(jQuery(window).scrollTop()),
		scrollPane = jQuery('body'),
		h = 0
		
	if(scrollTop > h) 
		scrollPane.addClass('fix')
	else
		scrollPane.removeClass('fix')
}


// Замена get параметров в строке

function set_url(prmName,val,u)
{
	var res = '',
		d = u.split("#")[0].split("?"),
		base = d[0],
		query = d[1]
		
	if(query) 
	{
		var params = query.split("&")
		for(var i = 0; i < params.length; i++) 
		{
			var keyval = params[i].split("=")
			if(keyval[0] != prmName)
				res += params[i] + '&'
		}
	}
	res += prmName + '=' + val
	return base + '?' + res
}


// Замена всех вхождений строки
    
function replaceAll(str, find, replace) 
{
	return str.split(find).join(replace);
}


// Очистка кеша страницы
    
function xs_clear_cache() 
{
	jQuery.ajax({
		url:set_url('clear_cache', 'y', current_url),
		cache:false,
		success: function(){console.log(21)}
	})
}


// Ширина области контетнта со слвайдером

function set_width_content()
{
	if(jQuery('.xs_sidebar').length > 0)
	{
		var w = parseInt(jQuery('.side_container').width()) - parseInt(jQuery('.xs_sidebar').width()) - parseInt(jQuery('.xs_sidebar').css("margin-right"))
		jQuery('.xs_sidebar + .xs_content').css({"max-width": w + "px"}) 
	}
}


jQuery(function($)
{
	var h = window.location.hash.substr(1)
	
	if(h != undefined && h != '')
		ajax_filter(h)
	
	set_checkout_fields()
	
	$(document).on('change', '.woocommerce-billing-fields input[type=radio]', function()
	{
		set_checkout_fields()
	})

	function xs_init(p)
	{
		p.find(".fancybox").fancybox(
		{
			'padding'			: 20,
			'width'				: 250,
			'height'			: "auto",
			'autoDimensions'	: false,
			'centerOnScroll'	: 'yes',
			'titleShow'			: false,
			'touch'				: false,
			'cache'				: false
		})
		
		p.find(".fancybox-labels").fancybox(
		{
			'padding'			: 20,
			'width'				: 250,
			'height'			: "auto",
			'autoDimensions'	: false,
			'centerOnScroll'	: 'yes',
			'titleShow'			: false,
			'touch'				: false,
			'cache'				: false,
			'afterClose'		: function(){}
		})
		
		/*
		p.find('[data-src]').lazy(
		{
			effect: 'fadeIn',
			effectTime: 0,
		})
		*/
		
		if(p.find('.xs_sidebar .widget_price_filter, .xs_sidebar .widget_layered_nav.woocommerce-widget-layered-nav').length == 0)
			p.find('.xs_sidebar .change_trigger').addClass('catalog_only')
		
		jQuery('.goods__sidebar .widget').removeClass('roll_up');
		jQuery('.goods__sidebar .widget.widget_price_filter').removeClass('roll_up');
		jQuery('.select').removeClass('active');
		jQuery('.goods__content').removeClass('space--tune');
		jQuery('.goods__body').removeClass('space--tune');
		jQuery('.goods__filter').removeClass('goods__filter--price')
		
		
		// Слайдер описания в товаре
		
		p.find('.similar__inner--slider').slick({
			slidesToShow: 5,
			slidesToScroll: 1,
			arrows: true,
			responsive: [
			{
				breakpoint: 1100,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 700,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 500,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 2
				}
			}]
		});
		
				
		p.find('.similar__inner--slider').on('setPosition', function(event, slick, direction)
		{
			var e = $(this)
				h = parseInt(e.find('.slick-list').height())
			
			if(h > 600)
				h = 600
			
			e.find('.product.slick-slide .product__wrap').css({'min-height':h+'px'})
		})

	}
	
	xs_init($('body'))

	var i = 0
	
	$('.wp-block-gallery').each(function()
	{
		i++
		
		$(this).find('.blocks-gallery-item a').each(function(){
			$(this).attr('data-fancybox', 'gallery'+i)
		})
	})

	/*
	tpaneScroll()
	set_width_content()
	$(window).resize(function(){tpaneScroll(); set_width_content()})
	$(document).scroll(function(){tpaneScroll()})
	*/
	
	// Маска для телефона
	
	if($('input.phone').length)
		$('input.phone').inputmask("+9 (999) 999-99-999");
	
	if($('input[name=xs_phone]').length)
		$('input[name=xs_phone]').inputmask("+9 (999) 999-99-999");
	
	if($('input#billing_phone').length)
		$('input#billing_phone').inputmask("+9 (999) 999-99-999"); 
	
	if($('input#_recipient_phone').length)
		$('input#_recipient_phone').inputmask("+9 (999) 999-99-999");
	
	if($('input#_delivery_date').length)
		$('input#_delivery_date').inputmask("99.99.9999");
	
	if($('input#_delivery_time_exact').length)
		$('input#_delivery_time_exact').inputmask("99:99");
	

	// Скролл к элементам с хэшем

	$('.xs_hash').click(function(event)
	{
		var height = parseInt(Math.round($($(this).attr('href')).offset().top)) - parseInt($('header').height())
		
		$('html, body').stop().animate({
			scrollTop: height
		}, 500, "linear")
		
		return false
	})
	
	
	// Закрываем все фильтры
	
	if(parseInt($(window).width()) <= 900)
	{
		$('.goods__sidebar > .widget').removeClass('roll_up')
	}
	
	
	// Выдвигаем адаптивное меню
		
	    $('.buttonMenu, .mobile-nav__item--catalog').click(function() {
	        $('body').toggleClass('show_menu');
	    });
	    
	    // Обработчик для закрытия меню
	    $('header nav .menu_container .close').click(function() {
	        $('body').removeClass('show_menu');
	    });
	    
	    // Обработчик для кликов вне меню
	    $(document).click(function(event) {
	        if (
	            $(event.target).closest("header nav .menu_container .menu_wrapper").length ||
	            $(event.target).closest("header nav .buttonMenu").length ||
	            $(event.target).closest(".mobile-nav__item--catalog").length
	        ) return;

	        $('body').removeClass('show_menu');
	    });

	
	// Скрытие селектора при клике вне его
	
	$(document).mouseup(function (e)
	{
		var div = $(".hide_click_away")
		
		if (!div.is(e.target) && div.has(e.target).length === 0) 
			div.hide();
	})
	
	
	// Обратная связь
	
	$('a[href="#xs_recall"]').click(function()
	{
		var t = $(this).data('theme'),
			b = $(this).data('button'),
			y = $(this).data('yandexid'),
			g = $(this).data('googleid')
			
		$('#xs_recall input[type=submit]').val(b)
		$('#xs_recall .text_button').text(b)
		$('#xs_recall input[name=xs_theme]').val(t)
		$('#xs_recall .title').text(t)
		
		if(y !== undefined)
			$('#xs_recall .xs_send_form').data('yandexid', y)
		else
			$('#xs_recall .xs_send_form').data('yandexid', '')
		
		if(g !== undefined)
			$('#xs_recall .xs_send_form').data('googleid', g)
		else
			$('#xs_recall .xs_send_form').data('googleid', '')
		
		$('.xs_result').text('');
	})
	
	$('a[href="#xs_recall_one_click"]').click(function()
	{
		var t = $(this).data('theme'),
			b = $(this).data('button'),
			y = $(this).data('yandexid'),
			g = $(this).data('googleid'),
			c = []
		
		if($('.p-product__aname h1').length)
			c.push($('.p-product__aname h1').text())
		
		if($('.modal_variation__item input:checked').length)
		{
			var h = $('.modal_variation__item input:checked + label').clone()
			
			h.find('.old_price').remove()
			c.push($('.modal_variation__attribute_name:visible').text() + ": " + $.trim(h.text()))
		}
		
		if($('select[name="decoration_product"] option:selected').length)
		{
			var s = [],
				e = $(this)
			
			$('select[name="decoration_product"] option:selected').each(function()
			{
				s.push($(this).text())
			})
			
			if(s.length)
				c.push($('select[name="decoration_product"]').prev('.p-decoration_products__label:visible').text() + " " + s.join(", "))
		}
		
		if($('select[name="addition_product"] option:selected').length)
		{
			var s = [],
				e = $(this)
			
			$('select[name="addition_product"] option:selected').each(function()
			{
				s.push($(this).text())
			})
			
			if(s.length)
				c.push($('select[name="addition_product"]').prev('.p-addition_products__label:visible').text() + " " + s.join(", "))
		}
		
		c = c.join("\n\n")
		
		console.log(c)
		
		$('#xs_recall_one_click input[type=submit]').val(b)
		$('#xs_recall_one_click .text_button').text(b)
		$('#xs_recall_one_click input[name=xs_theme]').val(t)
		$('#xs_recall_one_click input[name=xs_comment]').val(c)
		$('#xs_recall_one_click .title').text(t)
		
		if(y !== undefined)
			$('#xs_recall_one_click .xs_send_form').data('yandexid', y)
		else
			$('#xs_recall_one_click .xs_send_form').data('yandexid', '')
		
		if(g !== undefined)
			$('#xs_recall_one_click .xs_send_form').data('googleid', g)
		else
			$('#xs_recall_one_click .xs_send_form').data('googleid', '')
		
		$('.xs_result').text('');
	})
	
	if($('input[name=xs_link]').length > 0)
		$('input[name=xs_link]').val(window.location.href)
	
	$(document).on('submit', '.xs_send_form', function(e)
	{
		e.preventDefault()

		var metrika_id = 41837129
		
		var f = $(this),
			yandexid = f.data('yandexid'),
			googleid = $(this).data('googleid')
		
		f.addClass('xs_load')
		
		$.ajax({
			url: '/wp-content/themes/xsiteshop/load/xs_mail.php',
			method: 'post',
			data: f.serialize(),
			success: function(data)
			{
				if(data == 'error')
					alert('Ошибка при отправке данных. Пожалуйста заполните обязательное поле "Телефон"')
				else if(data == 'error-p')
					alert('Ошибка при отправке данных. Пожалуйста проверьте корректность заполнения номера телефона')
				else if(data == 'error-e')
					alert('Ошибка при отправке данных. Пожалуйста проверьте корректность заполнения адреса электронной почты')
				else
				{
					if(yandexid !== undefined && yandexid != '')
					{
						ym(metrika_id,'reachGoal',yandexid)
						console.log(metrika_id+' '+yandexid)
					}
						//yaCounter69142432.reachGoal(yandexid)

					
					//if(googleid !== undefined && googleid != '')
					//	ga('send', 'event', googleid);
				
					f.find('input[type=text],textarea,input[type=url],input[type=number],select,input[type=email],input[type=date],input[type=tel]').val('')
					f.find('.xs_result').html(data)
				}
				
				f.removeClass('xs_load')
			}
		})
	})



	// Слайдер на главной

	var s = $('.wr_slider')
	
	s.slick({
  		slidesToShow: 1,
  		slidesToScroll: 1,
		arrows: s.data('arrows') == '1' ? true : false,
		dots: s.data('dots') == '1' ? true : false,
		autoplay: s.data('autoplay') == '1' ? true : false,
		autoplaySpeed: s.data('autoplay_speed'),
		speed: s.data('speed'),
		infinite: true,
		pauseOnFocus: false,
		pauseOnHover: s.data('stophover') == '1' ? true : false
	});
	
	s.on('afterChange', function(event, slick, currentSlide, nextSlide)
	{
		/*
		s.find('[data-src]').lazy(
		{
			effect: 'fadeIn',
			effectTime: 0,
		})
		*/
	})


	// Слайдер новостей

	$('.wr_news .slider').slick({
  		slidesToShow: 3,
  		slidesToScroll: 1,
		arrows: false,
		infinite: false,
		dots: false,
  		responsive: [
		{
		    breakpoint: 1000,
		    settings: {
				slidesToShow: 4,
				slidesToScroll: 1
			}
		},
		{
		    breakpoint: 900,
		    settings: {
				slidesToShow: 3,
				slidesToScroll: 1
			}
		},
		{
		    breakpoint: 700,
		    settings: {
				slidesToShow: 2,
				slidesToScroll: 2
			}
		}]
	});

	
	// Слайдер товаров
	
	if($('.wr_goods .slider').length > 0)
	{
		$('.wr_goods .slider').each(function()
		{
			var e = $(this),
				count_slide = (e.parents('.side_container').find('.xs_sidebar').length > 0) ? 3 : 4
				
			e.slick({
				slidesToShow: count_slide,
				slidesToScroll: 1,
				arrows: false,
				infinite: true,
				dots: false,
				responsive: [
				{
					breakpoint: 1300,
					settings: {
						slidesToShow: count_slide-1,
						slidesToScroll: 1
					}
				},
				{
					breakpoint: 900,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 1
					}
				},
				{
					breakpoint: 700,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				}]
			})
		})
	}


	// форма поиска при нажатии

	$('header .land .search form input.your_latters').click(function(){
		$('.search.unick').addClass('active')
		$('header .land li.wr_catalog').removeClass('active');
		$('body').removeClass('cover');
	})

	$('header .land .search form .close_it').click(function(){
		$('.search.unick').removeClass('active')
		$('body').removeClass('cover');
		$('body').removeClass('xs_search')
	}) 
	
    $(document).click(function(event3) {
	    if(
			$(event3.target).closest("header .land ul.wr_catalog, header .land li").length || 
			$(event3.target).closest("header .land li.wr_catalog").length ||
			$(event3.target).closest("header .land .search.active form input.your_latters").length ||
			$(event3.target).closest(".search_result").length
		) return;

	    $('header .land li.wr_catalog').removeClass('active');
		$('body').removeClass('cover');
		$('body').removeClass('xs_search');
	})




    // меню каталог при нажатии

    $('header .land a.catalog').click(function(){
        $('header .land li.wr_catalog').toggleClass('active');
        $('body').toggleClass('cover');
		$('body').removeClass('xs_search');
		$('header .land .search.active').removeClass('active');

        return false;
    });

    $('header .land li.wr_catalog ul .close_btn').click(function(){
    	$('header .land li.wr_catalog').removeClass('active');
    	$('body').removeClass('cover');
    })

    if($(document).width() <= 1200)
	{
	    $('header .land a.catalog').click(function()
		{
			$(this).toggleClass('rotate')

			var menu = $(this).next() 
			
			if($(menu).is(':visible'))
				$(menu).slideUp(300)
			else
				$(menu).slideDown(300)

			return false;
		})
	}


	// Инициализация фильтра при клике вне ссылки
	
	$(document).on('click', '.xs_sidebar .widget_layered_nav ul li', function(e)
	{
	    if($(e.target).closest('a').length) return
		
		if($(this).find('a').length > 0)
			$(this).find('a').trigger('click')
	})
	
	
	// Показать ещё
	
	$(document).on('click', '.xs_learn_more', function(e)
	{
		var xs_nav = $(this).next(".xs_pagination"),
			read_more = $('.xs_learn_more'),
			next_page = xs_nav.find('li .next'),
			products_container = $(this).prev('.wr_goods'),
			load_container = read_more
		
		if(xs_nav.length > 0 && products_container.length > 0)
		{
			e.preventDefault()
			
			if(next_page.length > 0)
			{
				load_container.addClass('xs_load')
				
				jQuery.ajax({ 
					url: next_page.attr('href'),
					method: 'get',
					cache: false,
					success: function(data)
					{
						var products = jQuery('<div>'+data+'</div>').find('.wr_goods').html()
						
						products_container.append(products)							

						xs_nav.html(jQuery('<div>'+data+'</div>').find('.xs_pagination').html())
						
						if(xs_nav.find('li .next').length == 0)
							read_more.addClass('hide')
						
						xs_init(products_container)
						
						load_container.removeClass('xs_load')
					}
				})
			}
			else
			{
				read_more.addClass('hide')
			}
		}
	})
	
	
	// Ajax переключение страниц постраничной навигации
	
	$(document).on('click', '.xs_pagination a', function(e)
	{
		var xs_nav = $(this).parents(".xs_pagination"),
			read_more = $('.xs_learn_more'),
			next_page = $(this),
			products_container = $('.wr_goods'),
			load_container = xs_nav
		
		if(xs_nav.length > 0 && products_container.length > 0)
		{
			e.preventDefault()
			
			if(next_page.length > 0)
			{
				load_container.addClass('xs_load')
				
				jQuery.ajax({ 
					url: next_page.attr('href'),
					method: 'get',
					cache: false,
					success: function(data)
					{
						var products = jQuery('<div>'+data+'</div>').find('.wr_goods').html()
						
						products_container.html(products)							

						xs_nav.html(jQuery('<div>'+data+'</div>').find('.xs_pagination').html())
						
						if(xs_nav.find('li .next').length == 0)
							read_more.addClass('hide')
						else
							read_more.removeClass('hide')
						
						xs_init(products_container)
						
						load_container.removeClass('xs_load')
						
						var height = parseInt(Math.round(products_container.offset().top))
						
						$('html, body').stop().animate({
							scrollTop: height
						}, 500, "linear")
					}
				})
			}
			else
			{
				read_more.addClass('hide')
			}
		}
	})
	
	
	// Ajax обновление сортировки
	
	$(document).on('submit', '.form_sorting', function(e)
	{
		var f = $(this),
			c = f.parents('.goods__body'),
			o = f.find('input[name=orderby]').val(),
			v = (o != undefined && o != '') ? o : 'price'
		
		if(c.length)
		{
			e.preventDefault()
			
			ajax_filter(set_url('orderby', v, current_url))
			
		}
	})
	
	
	// Ajax фильтр в списке товаров
	
	var filter_timer
	
	function ajax_filter(u)
	{		
		if(u.substring(0, 1) == "?")
		{
			if((window.location.href).indexOf('/catalog/flowers-instock/') != -1) 
				u = "/catalog/flowers-instock/" + u;
			else
				u = "/catalog/populyarnyj-tovar-glavnaya/" + u;
		}
		
		window.location.hash = u
		
		$('body').removeClass('active_filter')
		
		$(".goods__body").addClass("xs_load"), 

		current_url = u
		
		$.ajax({
			url: u,
			cache: false,
			success: function(data) 
			{
				$(".goods__body").html($("<div>"+data+"</div>").find(".goods__body").html())
				
				hide_disabled_term_filter()
				
				if($(".price_slider_wrapper").length)
				{
					if("undefined"==typeof woocommerce_price_slider_params)return!1;function d(){$("input#min_price, input#max_price").hide(),$(".price_slider, .price_label").show();var d=$(".price_slider_amount #min_price").data("min"),r=$(".price_slider_amount #max_price").data("max"),i=$(".price_slider_amount").data("step")||1,c=$(".price_slider_amount #min_price").val(),a=$(".price_slider_amount #max_price").val();$(".price_slider:not(.ui-slider)").slider({range:!0,animate:!0,min:d,max:r,step:i,values:[c,a],create:function(){$(".price_slider_amount #min_price").val(c),$(".price_slider_amount #max_price").val(a),$(document.body).trigger("price_slider_create",[c,a])},slide:function(d,r){$("input#min_price").val(r.values[0]),$("input#max_price").val(r.values[1]),$(document.body).trigger("price_slider_slide",[r.values[0],r.values[1]])},change:function(d,r){$(document.body).trigger("price_slider_change",[r.values[0],r.values[1]])}})}$(document.body).bind("price_slider_create price_slider_slide",function(d,r,i){$(".price_slider_amount span.from").html(accounting.formatMoney(r,{symbol:woocommerce_price_slider_params.currency_format_symbol,decimal:woocommerce_price_slider_params.currency_format_decimal_sep,thousand:woocommerce_price_slider_params.currency_format_thousand_sep,precision:woocommerce_price_slider_params.currency_format_num_decimals,format:woocommerce_price_slider_params.currency_format})),$(".price_slider_amount span.to").html(accounting.formatMoney(i,{symbol:woocommerce_price_slider_params.currency_format_symbol,decimal:woocommerce_price_slider_params.currency_format_decimal_sep,thousand:woocommerce_price_slider_params.currency_format_thousand_sep,precision:woocommerce_price_slider_params.currency_format_num_decimals,format:woocommerce_price_slider_params.currency_format})),$(document.body).trigger("price_slider_updated",[r,i])}),d(),"undefined"!=typeof wp&&wp.customize&&wp.customize.selectiveRefresh&&wp.customize.widgetsPreview&&wp.customize.widgetsPreview.WidgetPartial&&wp.customize.selectiveRefresh.bind("partial-content-rendered",function(){d()})
				}
				
				var t = parseInt(Math.round($(".goods__body").offset().top)) - 20;
				$("html, body").stop().animate({
					scrollTop: t
				}, 500), 
				
				xs_init($(".goods__body")), 
				
				$(".goods__body").removeClass("xs_load")
			}
		})
	}
	
	$(document).on('click', '.goods__sidebar .widget_layered_nav ul li a, .widget.woocommerce.widget_layered_nav_filters li a', function(e)
	{
		e.preventDefault()
		
		var u = $(this).attr("href")
		
		ajax_filter(u)
		
	})
	
	
	// Инициализация фильтра при клике вне ссылки
	
	$(document).on('click', '.goods__sidebar .widget_layered_nav ul li', function(e)
	{
	    if($(e.target).closest('a').length) return
		
		if($(this).find('a').length > 0)
			$(this).find('a').trigger('click')
	})
	
	

	$(document.body).on('price_slider_change', function()
	{
		clearTimeout(filter_timer)
		
		filter_timer = setTimeout(function(){
			ajax_filter(set_url('min_price', $('input#min_price').val(),set_url('max_price', $('input#max_price').val(),current_url)))
		}, 500)
	})
	
	
	// Раскрытие блоков в сайдбаре
	
	$(document).on('click', '.goods__sidebar .widget .widget-title', function()
	{
		var w = $(this).parent('.widget'),
			h = w.hasClass('roll_up')
		
		if(parseInt($(window).width()) <= 900)
		{
			$('.goods__sidebar .widget').removeClass('roll_up')
			$('.goods__filter').removeClass('goods__filter--price')
			
			$('.select').removeClass('active')
			$(this).parents('.goods__content').removeClass('space--tune')
			$(this).parents('.goods__body').removeClass('space--tune')
		}
		
		if(h)
			w.removeClass('roll_up')
		else
			w.addClass('roll_up')
	})

	
	// Ajax выбор вариации для оформления заказа
	
	$(document).on("click", ".xs_get_variation", function(e)
	{
		e.preventDefault()
		
		var id = $(this).data('product_id')
		
		if(id != undefined)
		{
			$.fancybox.open({
				src: '/wp-content/themes/xsiteshop/load/xs_variations.php?product_id='+id,
				type: 'ajax',
				touch: false
			});
		}
		else
		{
			alert("Ошибка при оформлении заказа. Пожалуйста обратитесь к администратору сайта.")
		}
	})
	
	
	// Ajax добавление в корзину
	
	function add_to_cart(f)
	{
		$.ajax({
			url:window.location.href,
			method: 'post',
			data: f.serialize(),
			success: function(data)
			{
				$.fancybox.close()
				
				$("[data-load='xs_cart'],[data-load='xs_mobile_cart']").each(function()
				{
					var e = $(this)
					
					$.ajax({
					url: '/wp-content/themes/xsiteshop/load/'+e.data('load')+'.php',
						cache: false,
						success: function(data)
						{
							e.addClass('loaded').html(data)
						}
					})
				})
				
				f.removeClass('xs_load')
				
				$.fancybox.open('<div class="message_add_to_cart">'+
					'<div class="modal_variation__title">Товар добавлен в корзину</div>'+
					'<div class="xs_flex xs_center xs_wrap">'+
						'<span onclick="jQuery.fancybox.close()" class="btn empty modal_variation__btn">Продолжить покупки</span>'+
						'<a class="btn modal_variation__btn" href="/checkout/">Оформить заказ</a>'+
					'</div>'+
				'</div>');
			}
		})
	}
	
	$(document).on("submit", ".xs_add_to_cart_form", function(e)
	{
		e.preventDefault()
		
		var f = $(this)
		
		if(!f.find('.p-product__pay_buttons').is(':visible'))
		{
			alert("Этот товар нельзя купить")
			return
		}
		
		f.addClass('xs_load')
		
		if(
			(f.find('select[name=addition_product]').length > 0 && f.find('select[name=addition_product]').val() != '') || 
			(f.find('select[name=decoration_product]').length > 0 && f.find('select[name=decoration_product]').val() != '') || 
			$('.p-decoration_products__input:checked').length > 0)
		{
			var ar_ids = []
			
			if($('.p-decoration_products__input:checked').length > 0)
			{
				$('.p-decoration_products__input:checked').each(function()
				{
					if(parseInt($(this).val()) > 0)
						ar_ids.push(parseInt($(this).val()))
				})
			}
			
			if(f.find('select[name=addition_product]').length > 0 && f.find('select[name=addition_product]').val() != '')
			{
				var ar_values = f.find('select[name=addition_product]').val()
				
				for(i in ar_values)
					ar_ids.push(parseInt(ar_values[i]))
			}
			
			if(f.find('select[name=decoration_product]').length > 0 && f.find('select[name=decoration_product]').val() != '')
			{
				var ar_values = f.find('select[name=decoration_product]').val()
				
				for(i in ar_values)
					ar_ids.push(parseInt(ar_values[i]))
			}
			
			console.log(ar_ids)
			
			$.ajax({
				url:window.location.href,
				method: 'post',
				data: {
					'add-to-cart': ar_ids.join(','),
					'quantity': 1
				},
				success: function(data){
					add_to_cart(f)
				}
			})
		}
		else
			add_to_cart(f)
	})
	
	
    // Пересчёт количества товара
    
	function set_quantity(e)
	{
		var c = e.attr('class'),
			input = e.parents('.xs_count_container').find('input'),
			v = parseInt(input.val())
			
		if(c == 'plus')
			v++
		else if(c == 'minus')
		{
			v--
			if(v < 1)
				v = 1;
		}
		
		if(isNaN(v)) 
			v = 1
		
		input.val(v)
		
		
		// Обновление страницы корзины
		
		if(e.parents('.xs_cart_container').length > 0 && e.parents('form').length > 0)
		{
			var f = e.parents('form'),
				c = e.parents('.xs_cart_container .cart_form')
			
			clearTimeout(cart_timer)
			
			cart_timer = setTimeout(function()
			{
				f.trigger('submit')
			}, 1000)
		}
	} 


	$(document).on('click', '.xs_count_container .plus, .xs_count_container .minus', function()
	{
		set_quantity($(this))
	})
	
	$(document).on('change', '.xs_count_container input', function()
	{
		set_quantity($(this))
	})
	
	$(document).on('keypress', '.xs_count_container input', function(e)
	{
		if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57))
			return false
	})
	
	
	// Ajax удаление товара из корзины
	
	$(document).on("click", ".shop_table tr td.product-remove .remove", function(e) 
	{
		e.preventDefault()
		
		var e = $(this),
			f = e.parents('.xs_cart_container .cart_form')
		
		f.addClass('xs_load')
	
		$.ajax({
			url: e.attr('href'),
			cache: false,
			method: 'post',
			success: function()
			{
				f.trigger('submit')
			}				
		})
	})
	
	
	// Ajax бновление корзины
	
	$(document).on('submit', '.xs_cart_container .cart_form', function(e)
	{
		e.preventDefault()
		
		var f = $(this)
		
		f.addClass('xs_load')
		
		$.ajax({
			url: f.attr('action'),
			data: f.serialize(),
			cache: false,
			method: 'post',
			success: function()
			{
				$.ajax({
					url: checkout_url,
					cache: false,
					success: function(data)
					{
						if($("<div>"+data+"</div>").find('.xs_cart_container .cart_form').length > 0)
						{
							f.html($("<div>"+data+"</div>").find('.xs_cart_container .cart_form').html())
							xs_init($('.xs_cart_container .cart_form'))
							$('body').trigger('update_checkout')
							f.removeClass('xs_load')
						}
						else
							window.location.href = cart_url
					}				
				})
			}				
		})
	})
	
	
	// Переключение табов в карточке товара
	
	$(document).on("click", ".detail_more .tab_buttons li", function(e) 
	{
		e.preventDefault()
		
		var t = $(this).data('tab')
		
		$('.detail_more .tab_buttons li.active, .detail_more .tab_container .tab.active').removeClass('active')
		$('.detail_more .tab_buttons li[data-tab="'+t+'"], .detail_more .tab_container .tab[data-tab="'+t+'"]').addClass('active')
	})

	
	// Поиск по сайту
	
	var search_timer
	
	function xs_search()
	{
		var e = $('.form_search .input_search'),
			v = $.trim(e.val()),
			c = $('header .search_result')

		clearTimeout(search_timer)
			
		if(v != "" && v.length >= 3)
		{
			search_timer = setTimeout(function()
			{
				e.addClass('load')
				
				$.ajax({
					url: "/?"+$('.form_search').serialize(),
					method: "get",
					cache: false,
					success: function(data)
					{
						if($("<div>"+data+"</div>").find('.single_product').length)
						{
							var h = $("<div>"+data+"</div>").find('meta[property="og:url"]').attr('content'),
								t = $("<div>"+data+"</div>").find('meta[property="og:title"]').attr('content')
							
							if(h != undefined && h != '' && t != undefined && t != '')
								c.html('<div class="container"><ul><li><a href="' + h + '">' + t + '</a></li></ul><a href="#" rel="nofollow" onclick="jQuery(this).parents(\'header\').find(\'.form_search\').submit();return false;" class="more_search">Показать все результаты</a></div>')
							else
								c.html('<div class="container"><p class="result_empty">По данному запросу ничего не найдено...</p></div>')
						}
						else
						{
							var r = $("<div>"+data+"</div>").find('.xs_content .goods .item')
							
							if(r.length > 0)
							{
								var h = "",
									i = 0
								
								r.find('.fireline .info').each(function()
								{
									h += '<li><a href="' + $(this).parents('.item').find('a.img').attr('href') + '">' + $(this).html() + '</a></li>'
									i++
									
									if(i > 10)
										return false
								})
								
								c.html('<div class="container"><ul>' + h + '</ul><a href="#" rel="nofollow" onclick="jQuery(this).parents(\'header\').find(\'.form_search\').submit();return false;" class="more_search">Показать все результаты</a></div>')
							}
							else
								c.html('<div class="container"><p class="result_empty">По данному запросу ничего не найдено...</p></div>')
						}

						$('body').addClass('xs_search')
						
						e.removeClass('load')
					}
				})
			}, 500)
		}
		else
			$('body').removeClass('xs_search')
	}
	
	
	$('.form_search .input_search').keyup(function(eventObject)
	{
		if( eventObject.which != 27)
		{
			xs_search()
		}
		else
			$('body').removeClass('xs_search')
	})
	
	$('.form_search .input_search').focus(function(eventObject)
	{
		xs_search()
	})
	
	$(document).keydown(function(eventObject)
	{
		if( eventObject.which == 27 )
			$('body').removeClass('xs_search')
	})
	
	
	// Закрытие окна сообщений
	
	$(document).on("click", ".xs_message .notice-dismiss", function(e) 
	{
		$(this).parent('.xs_message').remove()
	})
	
	
	// Открытие подкатегорий и фильтра

	$(document).on("click", ".xs_sidebar .change_trigger .catalog", function()
	{
		var i = $(this).hasClass('active')
		
		$('.xs_sidebar .widget').removeClass('show')
		$('.xs_sidebar .change_trigger .item').removeClass('active')
			
		if(!i)
		{
			$(this).addClass('active')
			$('.xs_sidebar .widget_product_categories').addClass('show')
		}
	})

	$(document).on("click", ".xs_sidebar .change_trigger .filter", function()
	{
		var i = $(this).hasClass('active')
		
		$('.xs_sidebar .widget').removeClass('show')
		$('.xs_sidebar .change_trigger .item').removeClass('active')

		if(!i)
		{
			$(this).addClass('active');
			$('.xs_sidebar .widget_price_filter, .xs_sidebar .widget_layered_nav.woocommerce-widget-layered-nav').addClass('show')
		}
	})
	
	
	// Публикация отзыва
	
	$(document).on("submit", "#add_review", function(e)
	{
		e.preventDefault()
		
		var f = $(this),
			formdata = new FormData(this)
		
		f.find('.form').addClass('xs_load')
		
		$.ajax({
			url: '/wp-content/themes/xsiteshop/load/xs_review.php',
			method: 'post',
			data: formdata,
			cache: false,
			contentType: false,
			processData: false,
			success: function(data)
			{
				f.find('.xs_result').html(data)
				f.find('input[type=text], input[type=file], textarea').val('')
				f.find('.form').removeClass('xs_load')
			}
		})
	})


	/* Интернет-магазин Цветы.ру */

	// Слайдер на главной
	$('.main-slider__inner').slick({
  		slidesToShow: 1,
  		slidesToScroll: 1,
		arrows: true,
		fade: true,
 		autoplay: true,
		autoplaySpeed: 5000,
 		responsive: [
		{
		    breakpoint: 800,
		    settings: {
				dots: true,
			}
		}]
	});

	// Слайдер списка товаров
	$('.squeeze__slider').slick({
  		slidesToShow: 1,
  		slidesToScroll: 1,
  		speed: 700,
		arrows: true,
		infinite: true,
		autoplay: true,
		autoplaySpeed: 6000
	});

	// Слайдер описания в товаре
	$(document).on('click', '.p-product__learn-tlt', function(){
		var parent = $(this).parents('.p-product__learn');
		var menu = $(this).next();

		$(this).removeClass('active')

		if( $(menu).is(':visible')){
			$(menu).slideUp(200)
			$(parent).addClass('active')
		}
		else{
			$(menu).slideDown(200)
			$(parent).removeClass('active')
		}
	})


	// Отмена клика на произвольные ссылки в пунктах меню
	$(document).on('click', '.header__menu a', function() {
		$empty_link = $(this).attr('title')

		if($empty_link == 'empty-link')
			return false;
	})

	// Сайдбар раскрытие
	$(document).on('click', '.goods__sidebar-title', function() {

		var parent = $(this).parents('.goods__sidebar-item');
		var menu = $(this).next();

		if( $(menu).is(':visible')) {
			$(menu).slideUp(200)
			$(parent).removeClass('active')
		}
		else {
			$('.faq-content .line .answer').slideUp(200)
			$(menu).slideDown(200)
			$(parent).addClass('active')
		}
	})


	// В слайдере отзывов меняем текущей номер
	$('.squeeze__slider').on('afterChange', function(a, b, c){
		$(this).parents('.squeeze__wr-slider').find('.squeeze__current-numb').text(c+1)
	})

	// Выпадающий список в сортировке и выбор поля
	$(document).on('click', '.sort .select_field', function() 
	{
		var s = $(this).parents('.select'),
			f = s.find('.select_fields')

		if(f.is(':visible'))
			s.removeClass('active')
		else
			s.addClass('active')
	})

	$(document).on('click', '.sort .select_fields .field', function() {
		$('.sort .select').removeClass('active')
		set_text = $(this).text()
		$('.sort .select_field').text(set_text)
		$('.sort .select_fields .field').removeClass('selected')
		$(this).addClass('selected')
	})

	// разворот дочерних пунктов меню
	if( $(document).width() <= 960)
	{	
		$(document).on('click', '.header__menu ul li.menu-item-has-children > a', function() {
		
		 	$(this).toggleClass('rotate');

	        var menu = $(this).next(); 
	        if( $(menu).is(':visible')){
	            $(menu).slideUp(0);
	        }
	        else{
	            $(menu).slideDown(0);
	        }
			
			return false;
		});
	}

	
	// Слайдеры в карточке товара
	
	single_product_slider = $('.p-product__imagesslider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		fade: true,
		asNavFor: '.p-product__thunbslider'
	})

	$('.p-product__thunbslider').slick({
		slidesToShow: 5,
		slidesToScroll: 1,
		asNavFor: '.p-product__imagesslider',
		dots: false,
		centerMode: false,
		focusOnSelect: true,
		arrows: false,
	})


	// В адаптиве отследить скролл и добавить кнопку вверх
	$(window).scroll(function() { 
		$('.mobile-scroll').addClass('active')
	});

	$(window).scroll(function() {
		clearTimeout($.data(this, 'scrollTimer'));
		$.data(this, 'scrollTimer', setTimeout(function() {
			$('.mobile-scroll').removeClass('active')
		}, 2000));
	});

	// Выдвигаем фильтр в адаптиве
	$(document).on('click', '.mobile-filer__filt', function()
	{
		$('body').addClass('active_filter')
	})

	$(document).on('click', '.goods__sidebar-close', function()
	{
		$('body').removeClass('active_filter')
	})


	// Раскрытие фильтра в адаптиве
	$(document).on('click', '.goods__filter-sort-icon', function()
	{	
		parent_1 = $(this).parents('.goods__content')
		parent_2 = $(this).parents('.goods__body')

		
		$('.widget').removeClass('roll_up')
		$('.goods__filter').removeClass('goods__filter--price')
			
		$('.select').toggleClass('active')
		parent_1.toggleClass('space--tune')
		parent_2.toggleClass('space--tune')
	})

	
	// Загрузка популярных товаров на главную страницу
	
	if($('.front_page_popular').length)
	{
		var load_container = $('.front_page_popular')
		
		current_url = '/catalog/populyarnyj-tovar-glavnaya/'
			
		jQuery.ajax({ 
			url: current_url,
			success: function(data)
			{
				var products = jQuery('<div>'+data+'</div>').find('.goods__body').html()
				
				load_container.html(products)							
				xs_init(load_container)
				
				if($(".price_slider_wrapper").length)
				{
					if("undefined"==typeof woocommerce_price_slider_params)return!1;function d(){$("input#min_price, input#max_price").hide(),$(".price_slider, .price_label").show();var d=$(".price_slider_amount #min_price").data("min"),r=$(".price_slider_amount #max_price").data("max"),i=$(".price_slider_amount").data("step")||1,c=$(".price_slider_amount #min_price").val(),a=$(".price_slider_amount #max_price").val();$(".price_slider:not(.ui-slider)").slider({range:!0,animate:!0,min:d,max:r,step:i,values:[c,a],create:function(){$(".price_slider_amount #min_price").val(c),$(".price_slider_amount #max_price").val(a),$(document.body).trigger("price_slider_create",[c,a])},slide:function(d,r){$("input#min_price").val(r.values[0]),$("input#max_price").val(r.values[1]),$(document.body).trigger("price_slider_slide",[r.values[0],r.values[1]])},change:function(d,r){$(document.body).trigger("price_slider_change",[r.values[0],r.values[1]])}})}$(document.body).bind("price_slider_create price_slider_slide",function(d,r,i){$(".price_slider_amount span.from").html(accounting.formatMoney(r,{symbol:woocommerce_price_slider_params.currency_format_symbol,decimal:woocommerce_price_slider_params.currency_format_decimal_sep,thousand:woocommerce_price_slider_params.currency_format_thousand_sep,precision:woocommerce_price_slider_params.currency_format_num_decimals,format:woocommerce_price_slider_params.currency_format})),$(".price_slider_amount span.to").html(accounting.formatMoney(i,{symbol:woocommerce_price_slider_params.currency_format_symbol,decimal:woocommerce_price_slider_params.currency_format_decimal_sep,thousand:woocommerce_price_slider_params.currency_format_thousand_sep,precision:woocommerce_price_slider_params.currency_format_num_decimals,format:woocommerce_price_slider_params.currency_format})),$(document.body).trigger("price_slider_updated",[r,i])}),d(),"undefined"!=typeof wp&&wp.customize&&wp.customize.selectiveRefresh&&wp.customize.widgetsPreview&&wp.customize.widgetsPreview.WidgetPartial&&wp.customize.selectiveRefresh.bind("partial-content-rendered",function(){d()})
				}
			
				if(parseInt($(window).width()) > 900)
					$('.widget.woocommerce.widget_layered_nav.woocommerce-widget-layered-nav:last').addClass('roll_up')
				
				hide_disabled_term_filter()
			}
		})
	}
	
	$(document).on('keypress', '.price_slider_amount #min_price, .price_slider_amount #max_price', function(event)
	{
		if(event.key != 'Enter' && "1234567890".indexOf(event.key) == -1)
			event.preventDefault();
	})
	
	$(document).on('change', '.price_slider_amount #min_price', function(event)
	{
		if(parseInt($(this).val()) < parseInt($(this).data('min')))
			$(this).val($(this).data('min'))
		else if(parseInt($(this).val()) > parseInt($('.price_slider_amount #max_price').data('max')))
			$(this).val($('.price_slider_amount #max_price').data('max'))
		
		if(parseInt($(this).val()) > parseInt($('.price_slider_amount #max_price').val()))
		{
			var a = $('.price_slider_amount #max_price').val()
			$('.price_slider_amount #max_price').val($(this).val())
			$(this).val(a)
		}
		
		$(this).parents('form').submit()
	})
	
	$(document).on('change', '.price_slider_amount #max_price', function(event)
	{
		if(parseInt($(this).val()) > parseInt($(this).data('max')))
			$(this).val($(this).data('max'))
		else if(parseInt($(this).val()) < parseInt($('.price_slider_amount #min_price').data('min')))
			$(this).val($('.price_slider_amount #min_price').data('min'))
		
		if(parseInt($(this).val()) < parseInt($('.price_slider_amount #min_price').val()))
		{
			var a = $('.price_slider_amount #min_price').val()
			$('.price_slider_amount #min_price').val($(this).val())
			$(this).val(a)
		}
		$(this).parents('form').submit()
	})
	
	$(document).on('submit', '.widget_price_filter form', function(event)
	{
		event.preventDefault();
		
		ajax_filter(set_url('min_price', $('input#min_price').val(),set_url('max_price', $('input#max_price').val(),current_url)))
	})
	
	$(document).on('click', '#place_order', function(event)
	{
		$('input[name=billing_phone]').val($('input[name=billing_phone]').val().replace('+8', '+7'))
		$('input[name=_recipient_phone]').val($('input[name=_recipient_phone]').val().replace('+8', '+7'))
	})
	
	$('select.selectator').each(function () 
	{
		var $this = $(this),
			options = {}
			
		$.each($this.data(), function (_key, _value) 
		{
			if (_key.substring(0, 10) == 'selectator')
				options[_key.substring(10, 11).toLowerCase() + _key.substring(11)] = _value
		})
		$this.selectator(options)
	})
	
	$(document).click(function(event)
	{
		if (
			$(event.target).closest(".goods__filter").length ||
			$(event.target).closest(".select_field").length  ||
			$(event.target).closest(".select_fields").length 
		) return;

		jQuery('.goods__sidebar .widget').removeClass('roll_up');
		jQuery('.goods__sidebar .widget.widget_price_filter').removeClass('roll_up');
		jQuery('.select').removeClass('active');
		jQuery('.goods__content').removeClass('space--tune');
		jQuery('.goods__body').removeClass('space--tune');
		jQuery('.goods__filter').removeClass('goods__filter--price')

		event.stopPropagation();
	})
	
	update_single_product_price()
	
	$(document).on('change', '.p-product__lead input', function()
	{
		update_single_product_price()
		
		var variation_id = jQuery("[name='add-to-cart']").val()
		
		if($('.structure_product').length)
		{
			$.ajax({
				url: '/wp-content/themes/xsiteshop/load/xs_get_structure_product.php',
				method: 'post',
				data: {
					'variation_id': variation_id
				},
				success: function(data){
					$('._system_structure_text').remove()
					$('.structure_product').html(data)
					xs_init($('.structure_product'))
					$('.structure_product').removeClass('loader')
				}
			})
		}
	})
	
	$(document).on('change', '.p-addition_products__select, .p-decoration_products__select', function()
	{
		update_single_product_price()
	})
	
	$(document).on('click', '.tabs__button', function()
	{
		var e = $(this),
			t = e.data('tab')
			
		$('.tabs__button, .tabs__content').removeClass('active')
		$('.tabs__button[data-tab='+t+'], .tabs__content[data-tab='+t+']').addClass('active')
	})
	
	$(document).on('click', '.subcategories-list--added .subcategories-list__item:not(.subcategories-list__item--active)', function()
	{
		var e = $(this),
			id = e.data('id')
		
		$('.similar--added').addClass('xs_load')
		$('.subcategories-list--added .subcategories-list__item').removeClass('subcategories-list__item--active')
		e.addClass('subcategories-list__item--active')
		
		$.ajax({
			method: 'post',
			data: {
				added_category_id: id
			},
			success: function(data)
			{
				if($('<div>' + data + '</div>').find('.similar__inner--added').length)
				{
					$('.similar__inner--added').html($('<div>' + data + '</div>').find('.similar__inner--added').html())
					xs_init($('.similar__inner--added'))
				}
				
				$('.similar--added').removeClass('xs_load')
			}
		})
	})
	
	$(document).on('change', '.xs_set_tags input[type=checkbox]', function()
	{
		var e = $(this),
			id = e.parents('.xs_set_tags').find('input[name=product_id]').val(),
			c = '.xs_set_tags #taxonomy-product_tag'
		
		$(c).addClass('xs_load')
		
		$.ajax({
			url: '/wp-content/themes/xsiteshop/load/xs_set_tags.php?product_id=' + id,
			method: 'post',
			data: $('.xs_set_tags').serialize(),
			cache: false,
			success: function(data)
			{
				if($('<div>' + data + '</div>').find(c).length)
					$(c).html($('<div>' + data + '</div>').find(c).html())
				
				$(c).removeClass('xs_load')
			}
		})
	})
	
	$(document).on('change', '.xs_set_top input[type=checkbox]', function()
	{
		var e = $(this),
			id = e.parents('.xs_set_top').find('input[name=product_id]').val(),
			c = '.xs_set_top .categorychecklist'
		
		$(c).addClass('xs_load')
		
		$.ajax({
			url: '/wp-content/themes/xsiteshop/load/xs_set_top.php?product_id=' + id,
			method: 'post',
			data: $('.xs_set_top').serialize(),
			cache: false,
			success: function(data)
			{
				if($('<div>' + data + '</div>').find(c).length)
					$(c).html($('<div>' + data + '</div>').find(c).html())
				
				$(c).removeClass('xs_load')
			}
		})
	})
	
	$(document).on('click', '.set_product_status__set', function()
	{
		var e = $(this),
			id = e.data('product_id'),
			c = e.parents('.set_product_status')
		
		c.addClass('xs_load')
		
		$.ajax({
			url: '/wp-content/themes/xsiteshop/load/xs_set_status.php',
			method: 'post',
			data: {
				product_id: id
			},
			cache: false,
			success: function(data)
			{
				c.replaceWith(data)
			}
		})
	})



	$('.salesline__slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
  		fade: true,
		autoplay:true,
		autoplaySpeed:3000
	})


	$(document).on('click', '.salesline-close', function()
	{
		$('.salesline').addClass('hide')
		$.cookie('salesline', 'hide', { expires: 7, path: '/' });
	})


	$(document).on('click', '.squeeze__listing-item-more', function()
	{
		var k = 0,
			step = 5
			
		if($(this).parents('.squeeze__listing-body').find('.squeeze__listing-item--hide:visible').length == $(this).parents('.squeeze__listing-body').find('.squeeze__listing-item--hide').length)
		{
			$(this).parents('.squeeze__listing-body').find('.squeeze__listing-item--hide').hide()
			$(this).text('Показать ещё')
		}
		else
		{
			$(this).parents('.squeeze__listing-body').find('.squeeze__listing-item--hide').each(function()
			{
				if(step <= k)
					return
				
				if(!$(this).is(':visible'))
				{
					$(this).show()
					console.log(k)
					k++
				}
			})
			
			if($(this).parents('.squeeze__listing-body').find('.squeeze__listing-item--hide:visible').length == $(this).parents('.squeeze__listing-body').find('.squeeze__listing-item--hide').length)
				$(this).text('Свернуть')
		}		
	})


	$(document).on('click', '.modal_variation__more', function()
	{
		var k = 0,
			step = 5
			
		if($(this).hasClass('show'))
		{
			$('.modal_variation__item.is_more').hide()
			$(this).find('.modal_variation__more-label').text('Показать ещё')
			$(this).removeClass('show')
		}
		else
		{
			$('.modal_variation__item.is_more').show()
			$(this).find('.modal_variation__more-label').text('Свернуть')
			$(this).addClass('show')
		}		
	})


	$(document).on('click', '.xs_admin_structure_product__head-ms', function()
	{
		var e = $(this),
			step = parseInt(e.data('step'))
		
		step++
		
		if(step > 3)
			step = 1
		
		e.data('step', step)
		
		if(step == 1)
		{
			$('.xs_admin_structure_product__ms-items').hide()
			e.removeClass('active')
		}
		else if(step == 2)
		{
			if(!$('.structure_product').hasClass('loader'))
			{
				$('.structure_product').addClass('xs_load')
				
				$.ajax({
					url: '/wp-content/themes/xsiteshop/load/xs_update_ms_quantity.php',
					method: 'post',
					data: {
						'product_id': e.data('id')
					},
					success: function(data){
						$('.structure_product').html(data)
						$('.xs_admin_structure_product__head-ms').data('step', step)
						$('.xs_admin_structure_product__head-ms').addClass('active')
						$('.xs_admin_structure_product__ms-items').show()
						$('.xs_admin_structure_product__ms-item.not_found').show()
						$('.structure_product').removeClass('xs_load')
						$('.structure_product').addClass('loader')
					}
				})
			}
			else
			{
				$('.xs_admin_structure_product__ms-items').show()
				$('.xs_admin_structure_product__ms-item.not_found').show()
				e.addClass('active')
			}
		}
		else if(step == 3)
		{
			$('.xs_admin_structure_product__ms-items').show()
			$('.xs_admin_structure_product__ms-item.not_found').hide()
			e.addClass('active')
		}
	})
	
	
	// ajax поиск
	
	
	const search_input = $(".header__search-input")
    const search_results = $(".header__search_result")
	
	function search_ajax()
	{
		clearTimeout(timer_search)
		
        let search_value = $.trim(search_input.val())

		if(search_query != search_value)
		{
			if(search_value.length > 2) 
			{
				timer_search = setTimeout(function()
				{
					$('.search_form').addClass('loaded')
					
					$.ajax({
						url: "/wp-admin/admin-ajax.php",
						type: "POST",
						data: {
							"action": "ajax_search",
							"term": search_value
						},
						success: function (results) 
						{
							search_results.show().html(results)
							search_query = search_value
							
							$('.search_form').removeClass('loaded')
						}
					})
				}, 500)
			} 
			else 
			{
				search_results.hide()
				search_query = search_value
			}
		}
	}
	
	var timer_search,
		search_query = $.trim(search_input.val())
	
    search_input.keyup(function() 
	{
		search_ajax()
    })

    // Закрытие поиска при клике вне его
    
	$(document).mouseup(function(e) 
	{
        if (
            (search_input.has(e.target).length === 0) &&
            (search_results.has(e.target).length === 0)
        )
            search_results.hide()
    })
	
	$(document).on('click', '.header__search_item--all', function()
	{
		$('form.search_form').submit()
	})




	// Скролл showup
	var items = document.querySelectorAll(".showup__inner");
	var isDragging = false;
	var startPosition = 0;
	var scrollPosition = 0;

	items.forEach(function (item) {
	  item.addEventListener("mouseenter", function () {
	    item.classList.add("inactive-scroll");
	  });

	  item.addEventListener("mouseleave", function () {
	    item.classList.remove("inactive-scroll");
	  });

	  item.addEventListener("wheel", function (e) {
	    e.preventDefault();
	    item.scrollLeft += e.deltaY;
	  });

	  item.addEventListener("mousedown", function (e) {
	    isDragging = true;
	    startPosition = e.clientX;
	    scrollPosition = item.scrollLeft;
	    item.classList.add("dragging");
	  });

	  document.addEventListener("mousemove", function (e) {
	    if (isDragging && item.classList.contains("dragging")) {
	      var distance = e.clientX - startPosition;
	      item.scrollLeft = scrollPosition - distance;
	    }
	  });

	  document.addEventListener("mouseup", function () {
	    isDragging = false;
	    item.classList.remove("dragging");
	  });

	  item.addEventListener("mouseleave", function () {
	    if (isDragging) {
	      isDragging = false;
	      item.classList.remove("dragging");
	    }
	  });
	});

	
})