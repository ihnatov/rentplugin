(function( $ ) {
	'use strict';


	$('#property_meta_box').on('change', '#all_amenities', (function(e){
		let status = $(this).prop('checked');
		$(this).siblings('input').prop('checked', status);
	}));


	$('#field_cps_operation').on('change', 'input', (function(e){

		$('#field_cps_operation .cps-columns-head').removeClass('error-text')
		showPriceFields()

	}));

	$(document).ready(function(){
		showPriceFields()
	});


	$('#field_cps_location').on('change', 'select', (function(e){
		let select_id = $(this).val();
		console.log(select_id);
		let data = {
			action: 'get_cps_locations',
			cps_location: select_id,
		};
		$.post(ajaxurl, data, function(response) {
			console.log(response);
			$(response).each(function (index, elem) {
				let select = $('#field_cps_location').find('select[name="cps_location[]"]:eq(' + index + ')');
				console.log(select);
				$(select).empty();
				$(elem).each(function (index, option) {
					$(option).appendTo(select);
				})
			})
		});
	}));

	$('input').on('keydown', function (e) {

		let property 		= $('#field_cps_operation')

		let property_rent 	= $(property).find('[name="cps_rent"]').prop('checked');
		let property_buy 	= $(property).find('[name="cps_buy"]').prop('checked');

		if( ( (property_rent == false) && (property_buy == false) ) && e.keyCode === 13) {
			e.preventDefault();
			let errorText = $('#field_cps_operation .cps-columns-head');
			$(errorText).addClass('error-text')
			alert('You must definitely choose Property Operation')
			$("html, body").animate({scrollTop: ($(errorText).offset().top - 60) +"px"});
		}
	});
	$('#publish').on('click', function (e) {

		let property 		= $('#field_cps_operation')

		let property_rent 	= $(property).find('[name="cps_rent"]').prop('checked');
		let property_buy 	= $(property).find('[name="cps_buy"]').prop('checked');

		if( (property_rent == false) && (property_buy == false) )  {
			e.preventDefault();
			let errorText = $('#field_cps_operation .cps-columns-head');
			$(errorText).addClass('error-text')
			alert('You must definitely choose Property Operation')
			$("html, body").animate({scrollTop: ($(errorText).offset().top - 60) +"px"});

		}
	});

	function showPriceFields() {
		let property 		= $('#field_cps_operation')

		let property_rent 	= $(property).find('[name="cps_rent"]').prop('checked');
		let property_buy 	= $(property).find('[name="cps_buy"]').prop('checked');

		if (property_rent || property_buy) {
			$('#field_cps_price').removeClass('cps_hidden')
		} else {
			$('#field_cps_price').addClass('cps_hidden')
		}

		if (property_rent) {
			$('#field_cps_price_rent').removeClass('cps_hidden')
		} else {
			$('#field_cps_price_rent').addClass('cps_hidden')
		}

		if (property_buy) {
			$('#field_cps_price_buy').removeClass('cps_hidden')
			$('#field_cps_completion').removeClass('cps_hidden')
		} else {
			$('#field_cps_price_buy').addClass('cps_hidden')
			$('#field_cps_completion').addClass('cps_hidden')
		}
	}

})( jQuery );
