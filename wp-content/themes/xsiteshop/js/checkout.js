var autocomplete_timer,
	xs_address_input = 'input[name="billing_address_1"]', // Заменить на селектор инпута
	xs_container_input = '.woocommerce-input-wrapper' // Заменить на селектор инпута

function xs_set_address(v)
{
	var e = jQuery(xs_address_input)
	e.val(v)
	e.focus()
}

function xs_get_addreses(e)
{
	var c = e.parent(xs_container_input)
		
	clearTimeout(autocomplete_timer)
	
	jQuery('.xs_autocomplete_result').remove()
	
	autocomplete_timer = setTimeout(function()
	{
		var u = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address",
			t = "57108440836b724f6852359ca888e599824fde61",
			q = jQuery.trim(e.val()),
			r = '<div class="xs_autocomplete_result">'
		
		if(q != "")
		{
			var options = {
				method: "POST",
				mode: "cors",
				headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
					"Authorization": "Token " + t
				},
				body: JSON.stringify({
					query: q,
					count: 8,
					locations: [{
						region: "новгородская",
					}],
					locations_boost: [{
						city: "великий новгород"
					}],
					to_bound: {"value": "house-flat"}
				})
			}

			fetch(u, options)
			.then(response => response.json())
			.then(result => 
				{
					if(result.suggestions != undefined && result.suggestions.length > 1)
					{
						for(i in result.suggestions)
						{
							r += '<div onclick="xs_set_address(jQuery(this).text())" class="xs_autocomplete_result__item">'+result.suggestions[i].value+'</div>'
						}
						
						r += '</div>'
						
						c.append(r)
					}
				}
			)
			.catch(error => console.log("error", error));
		}
	}, 300)
}

jQuery(function($)
{
	$(document).on('keyup', xs_address_input, function()
	{
		xs_get_addreses($(this))
	})
	
	$(document).on('focus', xs_address_input, function()
	{
		xs_get_addreses($(this))
	})
	
	$(document).click(function(event)
	{
		if (
			$(event.target).closest(xs_address_input).length ||
			$(event.target).closest(".xs_autocomplete_result").length 
		) return;

		$(".xs_autocomplete_result").remove()

		event.stopPropagation();
	})
	
	if (window.yandex && window.yandex.app) 
	{
		window.yandex.autofill.getProfileData(['name', 'email', 'phone', 'address'])
		    .then((result) => 
		    {
		      	if(result['firstName'] == null)
		      		result['firstName'] = ""

	      		if(result['lastName'] == null)
		      		result['lastName'] = ""

		      	/*if(result['email'] == null)
		      		result['email'] = ""*/

	      		if(result['phoneNumber'] == null)
		      		result['phoneNumber'] = ""

		      	if(result['streetAddress'] == null)
		      		result['streetAddress'] = ""



		    	if($('#billing_first_name').length)
		    		$('#billing_first_name').val($.trim(result['firstName'] + " " + result['lastName']))

		    	/*if($('#billing_first_name').length)
		    		$('#billing_first_name').val(result['email'])*/

		    	if($('#billing_phone').length)
		    		$('#billing_phone').val(result['phoneNumber'])

		    	if($('#billing_address_1').length)
		    		$('#billing_address_1').val(result['streetAddress'])

		    },(error) => {
		      console.log(error);
		})
	}
	
	$('[name=_delivery_date]').datepicker({
		minDate: new Date(),
	})
})

