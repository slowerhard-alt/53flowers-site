var time = parseInt(jQuery('.fonCallBack').data('time')),
	timer = time,
	messageRecall = 'Спасибо за вашу заявку!<br/>Наш специалист свяжется с вами в ближайшее время.'

function showCallBack(){
	var height = jQuery(window).height();
	jQuery('body > table:first').addClass('razmitie');
	jQuery('.fonCallBack').css({'height':height}).show();
	jQuery.cookie('show_callback', 'true', { path: '/'})
}

function closeCallBack(){
	jQuery('body > table:first').removeClass('razmitie');
	jQuery('.fonCallBack').hide();
}

function getMobile(){
	if(parseInt(screen.width) < 500){
		jQuery('#callbackMobile').show();
		jQuery('#callback').addClass('hide');
		jQuery('body').addClass('mobile');
	} else {
		jQuery('#callbackMobile').hide();
		jQuery('#callback').removeClass('hide');
		jQuery('body').removeClass('mobile');
	}
}

jQuery(function($){
	
	var show_callback = jQuery.cookie('show_callback')
	
	if(show_callback != 'true')
	{
		if(parseInt(screen.width) > 500){
			timer = setTimeout(function(){
				if(!jQuery('.fonCallBack').is(':visible') && $('.modal-backdrop.in').length == 0 && !$('.overlay').is(':visible')){
					jQuery('.oknoCallBack form.clbh_banner-body .timeCallBack').show()
					jQuery('.oknoCallBack form.clbh_banner-body .textClick').hide()
					jQuery('.oknoCallBack form.clbh_banner-body .timeCallBack').children('#timeCallBack').text(time)
					jQuery('.oknoCallBack form.clbh_banner-body.email').hide()
					jQuery('.oknoCallBack form.clbh_banner-body.recall').show()
					showCallBack()
				}
			}, time*1000);	
		}
	}
	
	getMobile();
	
	jQuery(document).resize(function(){
		var height = jQuery(window).height();
		jQuery('.fonCallBack').css({'height':height});
		getMobile();
	});
	jQuery('#callback').on("click", function(){
		if(parseInt(screen.width) > 500){
			clearTimeout(timer);
			jQuery('.oknoCallBack form.clbh_banner-body .timeCallBack').hide();
			jQuery('.oknoCallBack form.clbh_banner-body .textClick').show();
			jQuery('.oknoCallBack form.clbh_banner-body.email').hide();
			jQuery('.oknoCallBack form.clbh_banner-body.recall').show();
			showCallBack();
		}
	});
	jQuery('.oknoCallBack .exitCallBack').on("click", function(){
		closeCallBack();
	});
	jQuery('.fonCallBack').on("click", function(e){
		var div = $('.fonCallBack .oknoCallBack')
		if (!div.is(e.target) && div.has(e.target).length === 0) {
				closeCallBack()
		}
	});
	jQuery('.oknoCallBack form.clbh_banner-body.recall .clbh_banner-textbox').on("click", function(){
		if(jQuery(this).val() == "")
			jQuery(this).val("+7");
	});
	jQuery('.oknoCallBack form.clbh_banner-body.recall').on("submit", function(){
		var cont = jQuery('.oknoCallBack form.clbh_banner-body.recall');
		var phone = cont.find('.clbh_banner-textbox');
		var phoneVal = jQuery.trim(phone.val());
		
		var policy = jQuery('.oknoCallBack .policy input').is(':checked')
		
		
		if(policy)
		{
		//alert(phoneVal);
			phone.removeClass('errorCallBack');
			if(phoneVal != "+7" && phoneVal != "+" && phoneVal != ""){
				cont.load(
					cont.attr('action'),
					{
						phone: phoneVal
					},function(){
						cont.html('<div class="clbh_banner-h1">'+messageRecall+'</div>');
					}
				);
			} else {
				phone.addClass('errorCallBack');
			}
		}
		else
			alert('Форма не отправлена. Для отправки формы вы должны принять согласие на обработку персональных данных.')
		return false;
	});
	
	jQuery('#callback').hover(function(){
		$(this).removeClass('cbh-static');
		$(this).addClass('cbh-hover');
	}, function(){
		$(this).removeClass('cbh-hover');
		$(this).addClass('cbh-static');
	});
	
	var timerHov;
	
	$(document).scroll(function(){
		$('#callback').addClass('xs_scroll');
		
		clearTimeout(timerHov);
		
		timerHov = setTimeout(function(){
			$('#callback').removeClass('xs_scroll');
		}, 1000);
	});
});