(function( $ ) {
	'use strict';

	$(document).on('click', '#custom-title-add', (function(e){
		let text = $(this).closest('form').find('input[name="cps_title"]');
		let url = $(this).closest('form').find('input[name="cps_url"]');
		if(url.value.charAt(0) === '/') {
			url.value = url.substring(1, url.value.length);;
		  }
		if ( text.val() === '') {
			$(text).addClass('error-form');
			e.preventDefault();
		} else {
			$(text).removeClass('error-form');
		}
	}));

	$(document).on('change', '#custom-titles-table select', (function(e){
		let name = $(this).attr('name');
		let val = $(this).val();
		$('#custom-titles-table select[name="' + name + '"]').each(function () {
			$(this).val(val);
		});
	}));

	$(document).on('change', '#custom-titles-table input', (function(e){
		let name = $(this).attr('name');
		let val = $(this).val();
		$('#custom-titles-table input[name="' + name + '"]').each(function () {
			$(this).val(val);
		});
	}));

	$(document).on('click', '.cps_title-edit', (function(e){
		$(this).closest('tbody').find('.edit-title').removeClass('edit-title');
		$(this).closest('tr').addClass('edit-title');
		$('#add-new-title-box').addClass('edit-title');
		$('#custom-title-add').hide();

		let update_id = $(this).closest('tr').find('input[name="licids[]"]').val();

		let buttons = '<input type="submit" name="custom-title-update" id="custom-title-update" class="button btn-primary" value="Update Title">'+
			'<input type="button" id="custom-title-cancel" class="button btn-primary" value="Cancel">'+
			'<input type="hidden" name="update" value="' + update_id + '">';
		$('#additional-button').html(buttons);

		let row = $(this).closest('tr')

		let cps_operation	= $(row).find('.cps_operation input').val();
		let cps_type		= $(row).find('.cps_type input').val();
		let cps_location	= $(row).find('.cps_location input').val();
		let cps_minroom		= $(row).find('.cps_minroom input').val();
		let cps_maxroom		= $(row).find('.cps_maxroom input').val();
		let cps_bath_room	= $(row).find('.cps_bath_room input').val();
		let cps_amenities	= $(row).find('.cps_amenities input').val();
		let cps_furnishings	= $(row).find('.cps_furnishings input').val();
		let cps_title		= $(row).find('.cps_title span').text();
		let cps_keywords	= $(row).find('.cps_keywords span').text();
		let cps_url			= $(row).find('.cps_url span').text();
		let cps_meta		= $(row).find('.cps_meta span').text();
		
		set_cps_option(
			cps_operation	,
			cps_type		,
			cps_location	,
			cps_minroom		,
			cps_maxroom		,
			cps_bath_room	,
			cps_amenities	,
			cps_furnishings	,
			cps_title		,
			cps_keywords	,
			cps_url			,
			cps_meta			,
		);

		setTimeout(function() {
			var cps_customize_form 	= $('#add-new-title-box');
			var cps_operation_val 	= $(cps_customize_form).find('select[name="cps_operation"] option:selected').data('operation')
			var cps_type_val 		= $(cps_customize_form).find('select[name="cps_type"] option:selected').val()

			if (!cps_operation_val) 	cps_operation_val = 0;
			if (!cps_type_val) 			cps_type_val = 0;

			var data = {
				action: 'get_cps_fields_for_operation',
				cps_operation: cps_operation_val,
				cps_type_val: cps_type_val,
			};

			$.post(ajaxurl, data, function(response) {
				console.log(response);
				$(cps_customize_form).find('select[name="cps_type"]').html(response['cps_type'])
				$(cps_customize_form).find('select[name="cps_amenities"]').prop('disabled', !response['cps_amenities'])
				$(cps_customize_form).find('select[name="cps_furnishings"]').prop('disabled', !response['cps_furnishings'])

				$(cps_customize_form).find('select[name="cps_minroom"]').prop('disabled', !response['cps_minroom'])
				$(cps_customize_form).find('select[name="cps_maxroom"]').prop('disabled', !response['cps_maxroom'])
				$(cps_customize_form).find('select[name="cps_bath_room"]').prop('disabled', !response['cps_bath_room'])
			});
			},
		150);

	}))

	$(document).on('click', '#custom-title-cancel', (function(e){
		$('#custom-titles-table').find('.edit-title').removeClass('edit-title');
		$('#add-new-title-box').removeClass('edit-title');
		$('#custom-title-add').show();
		$('#additional-button').empty();
	}));

	function set_cps_option(
		cps_operation,
		cps_type,
		cps_location,
		cps_minroom,
		cps_maxroom,
		cps_bath_room,
		cps_amenities,
		cps_furnishings,
		cps_title,
		cps_keywords,
		cps_url,
		cps_meta
	) {

		if (!cps_operation) 	cps_operation = 0;
		if (!cps_type) 			cps_type = 0;
		if (!cps_location) 		cps_location = 0;
		if (!cps_minroom) 			cps_minroom = 0;
		if (!cps_maxroom) 			cps_maxroom = 0;
		if (!cps_bath_room) 	cps_bath_room = 0;
		if (!cps_amenities) 	cps_amenities = 0;
		if (!cps_furnishings) 	cps_furnishings = 0;

		get_cps_locations(cps_location);

		$('#add-new-title-box select[name="cps_operation"		]').val(cps_operation	)
		$('#add-new-title-box select[name="cps_type"			]').val(cps_type		)
		$('#add-new-title-box select[name="cps_location"		]').val(cps_location	)
		$('#add-new-title-box select[name="cps_minroom"				]').val(cps_minroom		)
		$('#add-new-title-box select[name="cps_maxroom"				]').val(cps_maxroom		)
		$('#add-new-title-box select[name="cps_bath_room"		]').val(cps_bath_room	)
		$('#add-new-title-box select[name="cps_amenities"		]').val(cps_amenities	)
		$('#add-new-title-box select[name="cps_furnishings"		]').val(cps_furnishings	)
		$('#add-new-title-box input[name="cps_title"			]').val(cps_title		)
		$('#add-new-title-box input[name="cps_keywords"			]').val(cps_keywords	)

		//sort
		$('#add-new-title-box input[name="cps_url"			]').val(cps_url	)
		$('#add-new-title-box input[name="cps_meta"			]').val(cps_meta )

	}

	//Update cps_type select
	$('#add-new-title-box').on('change', 'select[name="cps_operation"]', (function(e){
		var cps_customize_form = $('#add-new-title-box');
		var cps_operation = $(cps_customize_form).find('select[name="cps_operation"] option:selected').data('operation')
		var cps_type_val = $(cps_customize_form).find('select[name="cps_type"] option:selected').val()

		var data = {
			action: 'get_cps_fields_for_operation',
			cps_operation: cps_operation,
			cps_type_val: cps_type_val,
		};

		$.post(ajaxurl, data, function(response) {

			$(cps_customize_form).find('select[name="cps_type"]').html(response['cps_type'])
			$(cps_customize_form).find('select[name="cps_amenities"]').prop('disabled', !response['cps_amenities'])
			$(cps_customize_form).find('select[name="cps_furnishings"]').prop('disabled', !response['cps_furnishings'])

			$(cps_customize_form).find('select[name="cps_minroom"]').prop('disabled', !response['cps_minroom'])
			$(cps_customize_form).find('select[name="cps_maxroom"]').prop('disabled', !response['cps_maxroom'])
			$(cps_customize_form).find('select[name="cps_bath_room"]').prop('disabled', !response['cps_bath_room'])

		});
	}));
	$('#add-new-title-box').on('change', 'select[name="cps_type"]', (function(e){
		var cps_customize_form = $('#add-new-title-box');
		var cps_type = $(cps_customize_form).find('select[name="cps_type"] option:selected').data('type')
		var cps_operation_val = $(cps_customize_form).find('select[name="cps_operation"] option:selected').val();

		let data = {
			action: 'get_cps_fields_for_type',
			cps_type: cps_type,
			cps_operation_val: cps_operation_val,
		};

		$.post(ajaxurl, data, function(response) {
			$(cps_customize_form).find('select[name="cps_operation"]').html(response['cps_operation'])
			$(cps_customize_form).find('select[name="cps_amenities"]').prop('disabled', !response['cps_amenities'])
			$(cps_customize_form).find('select[name="cps_furnishings"]').prop('disabled', !response['cps_furnishings'])

			$(cps_customize_form).find('select[name="cps_minroom"]').prop('disabled', !response['cps_minroom'])
			$(cps_customize_form).find('select[name="cps_maxroom"]').prop('disabled', !response['cps_maxroom'])
			$(cps_customize_form).find('select[name="cps_bath_room"]').prop('disabled', !response['cps_bath_room'])

		});
	}));

	$('#add-new-title-box').on('change', 'select[name^="cps_location"]', (function(e){
		let cps_location_id = $(this).val();
		get_cps_locations(cps_location_id);
	}));

	function get_cps_locations(cps_location_id) {
		let data = {
			action: 'get_cps_locations',
			cps_location: cps_location_id,
		};
		$.post(ajaxurl, data, function(response) {
			$(response).each(function (index, elem) {
				let select = $('#add-new-title-box').find('select[name="cps_location[level_' + index + ']"]');
				$(select).empty();
				$(elem).each(function (index, option) {
					$(option).appendTo(select);
				})
			})
		});
	}
})( jQuery );
