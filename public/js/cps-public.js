(function( $ ) {
	'use strict';
	$(document).ready(function() {

		$("input[name='_wp_http_referer'], input[name='_wpnonce']").remove();

		$('#cps_search_form .select-single select').select2({
			width: '100%',
			minimumResultsForSearch: -1,
		});

		$('#cps_search_form .select-multi-checkbox select').select2({
			width: '100%',
			closeOnSelect : false,
			placeholder : "Amenities",
			allowClear: true,
			dropdownParent: $('#cps_amenities_field'),
			templateSelection: formatState2,
			scrollAfterSelect: true,
			sorter: function(data) {
				return data.sort(function(a, b) {
					return a.selected > b.selected ? -1 : a.selected > b.selected ? 1 : 0;
				});
			},

		});

		function formatState2 (state) {
			let length = $('#cps_amenities_field select').find('option:selected').length;

			if (!state.id) {
				return state.text;
			}

			var $state = $(
				'<span><span>'
			);

			let html = length + ' Selected';

			$state.find("span").text(state.text);

			if (length > 1) {
				$state.find("span").text(html);
			} else {
				$state.find("span").text(state.text);
			}
			return $state;
		};

		$('#cps_search_form').on('change', '.select-multi-checkbox select', function (e) {

			let length = $('#cps_amenities_field select').find('option:selected').length;

			if (length > 1) {
				$('#cps_amenities_field').find('ul.select2-selection__rendered > li.select2-selection__choice').not(':first').remove();
			}

		});

		$('#cps_search_form select.input-search').select2({
			allowClear: true,
			templateResult: formatState,
			placeholder: 'Location',
			scrollAfterSelect: true,
			minimumInputLength: 1,
			width: '100%',
			ajax: {
				delay: 250,
				url: window.wp_data.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						action : 'get_select_locations',
						get_search_fields_nonce: $('#get_search_fields_nonce').val(),
						search: params.term,
						selected: $(this).val(),
					}
					return query;
				},
				processResults: function (data) {
					console.log(data);
					return {
						results: data
					};
				},

				// Additional AJAX parameters go here; see the end of this chapter for the full code of this example
			}

		});
		function formatState (state) {
			if (!state.id) {
				return state.text;
			}
			var $state = $(
				'<span class="data-location-option" data-parents="' + state.parents + '">' + state.text + '</span>'
			);
			return $state;
		};

		$('#cps_search_form select.input-search').on('select2:select', function (e) {
			let data = e.params.data;
			let selected = $(this).val();
			let select = $(this);
			$(selected).each(function (index, elem) {
				if ($.inArray(+elem, data.parents) != -1) {
					$(select).find('option[value="' + elem + '"]').remove().trigger('change');
				}
			})
		});
		$('#cps_search_form').removeClass('hidden');

		console.log();
		// Set up the Select2 control
		$('#cps_operation').on('change', 'select', function(){
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: window.wp_data.ajax_url,
				data: {
					action : 'get_search_fields',
					field_type: $(this).val(),
					get_search_fields_nonce: $('#get_search_fields_nonce').val(),
				},
				success: function (response) {
					console.log(response);

					if (response.action != 'get_search_fields') return

					$('.search-property-row-2').first().html(response.content);

					$('#cps_search_form .select-single select').select2({
						width: '100%',
						minimumResultsForSearch: -1,
					});

					$('#cps_search_form .select-multi-checkbox select').select2({
						width: '100%',
						allowClear: true,
						closeOnSelect : false,
						placeholder : "Amenities",
						dropdownParent: $('#cps_amenities_field'),
						templateSelection: formatState2,
						scrollAfterSelect: true,
						sorter: function(data) {
							return data.sort(function(a, b) {
								return a.selected > b.selected ? -1 : a.selected > b.selected ? 1 : 0;
							});
						},


					});

					$('#cps_search_form .select-multi select').select2({
						width: '100%',
						minimumResultsForSearch: -1,
						allowClear: true,
					});
				}
			});
		})

		$("#historyback").on("click", function(e){
			window.history.back();
			setTimeout(function(){
				window.location.href = window.location.origin;
			}, 1000);
		});

		$("#search-find").on("click", function(e){
			e.preventDefault();

			var check_link = 'index.php?post_type=property';
			var check_cps = ['cps_operation', 'cps_locations', 'cps_type', 'cps_contract', 'cps_furnishing', 'cps_minprice', 'cps_maxprice', 'cps_minroom', 'cps_maxroom',
							 'cps_area_min', 'cps_area_max', 'cps_minbath_room', 'cps_maxbath_room'];

			check_cps.forEach(function(item, i, check_cps) {
				var check = ''
				if (item == 'cps_type') {
					check = $("select[name='" + item + "']").select2("val");
					item = 'cps_type2';
				}else if (item == 'cps_locations') {
					check = $("select[name='" + item + "'] option:selected").text();
					if (check.includes('(')) {
						check = check.split('(')[0].slice(0, -1);
					}
					check = check.split(' ').join('-');
				} else {
					check = $("select[name='" + item + "']").select2("val");
				}
				if (check) {
						check_link = check_link + '&' + item + '=' + check;
					}
			});
			var first_link = '';
			var startcheck = 0;
			$.ajax({
				url: window.wp_data.ajax_url, // this is the object instantiated in wp_localize_script function
				type: 'POST',
				data:{
				  action: 'myaction', // this is the function in your functions.php that will be triggered
				  link: check_link
				},
				success: function( data ){
				  if (data) {
					first_link = data.slice(1,-3);
				  }
				  if (first_link) {
					var link = window.location.origin;
					window.location.href = link + '/' + first_link;
				  } else {
		
					var link = window.location.origin + '';
					var order = $("#cps_order").val().split(',');
		
					order.forEach(function(item, i, order) {
						if (item == 'cps_location') { item = 'cps_locations'; }
						var op = '';
						 if (item == 'cps_operation') {
							op = $("select[name='cps_operation']").val();
						 } else if (item == 'cps_locations') {
							op = $("select[name='" + item + "'] option:selected").text();
							if (op.includes('(')) {
								op = op.split('(')[0].slice(0, -1);
							}
							op = op.split(' ').join('-');
						 } else {
							if (startcheck == 0) {
								
								var type_1 = $("select[name='cps_type'] option:selected").text();
								var room_1 = $("select[name='cps_minroom'] option:selected").text();
								if (type_1 == 'Property Type' && room_1 == 'Min. bed') {
									op = ''
								} else {
									var type = $("select[name='cps_type'] option:selected").text();
									var room = $("select[name='cps_minroom'] option:selected").text();
									if (room) {
										type = type.split(' ').join('-');
										if (type == 'Property-Type') { type = 'property'}
										room = room.split(' ').join('-') + '-';
										if (room == 'Min.-bed-') { room = ''}
										op = room + type;
									} else {
										op = type.split(' ').join('-');
									}
								}
								startcheck = 1;
							}
						 }
						 if (op) {
							link = link + '/' + op;
						 } else {
							if (item == 'cps_type') {
								link = link + '/property';
							}
							if (item == 'cps_locations') {
								link = link + '/uae';
							}
							// if (item == 'cps_minroom') {
							// 	link = link + '/with-any-badrooms';
							// }
						 }
					  });
		
		
					var other_link = '?';
					var other_cps = ['cps_contract', 'cps_furnishing', 'cps_minprice', 'cps_maxprice', 'cps_maxroom',
									 'cps_area_min', 'cps_area_max', 'cps_minbath_room', 'cps_maxbath_room'];
		
					other_cps.forEach(function(item, i, other_cps) {
						var val = $("select[name='" + item + "']").select2("val");
						if (val) {
							if (i == 0) {
								other_link = other_link + item + '=' + val;
							} else {
								other_link = other_link + '&' + item + '=' + val;
							}
						}
					});
					window.location.href = link + other_link;
				  }
				}
			  });

			// console.log(link + other_link);

			// console.log($("select[name='cps_operation']").val());
			// console.log($("select[name='cps_type'] option:selected").text());
			// console.log($("#cps_minroom_field").select2("val"));

		});

		$( "#plusS" ).click(function() {
			plusSlides(-1);
		});

		$( "#minusS" ).click(function() {
			plusSlides(1);
		});

		var slideIndex = 1;
		showSlides(slideIndex);
		
		// Next/previous controls
		function plusSlides(n) {
		  showSlides(slideIndex += n);
		}
		
		// Thumbnail image controls
		function currentSlide(n) {
		  showSlides(slideIndex = n);
		}
		
		function showSlides(n) {
		  var i;
		  var slides = document.getElementsByClassName("mySlides");
		  if (n > slides.length) {slideIndex = 1}
		  if (n < 1) {slideIndex = slides.length}
		  for (i = 0; i < slides.length; i++) {
			  slides[i].style.display = "none";
		  }
		  slides[slideIndex-1].style.display = "block";
		}


		$(".dropdown").click(function() {
			var X = $(this).attr('id');
			if (X == 1) {
				$(".submenu").hide();
				$(this).attr('id', '0');
			}
			else {
				$(".submenu").show();
				$(this).attr('id', '1');
			}
		});
		
		//Mouse click on sub menu
		$(".submenu").mouseup(function() {
			return false
		});
		
		//Mouse click on my account link
		$(".dropdown").mouseup(function() {
			return false
		});

		$(document).mouseup(function() {
			$(".submenu").hide();
			$(".account").attr('id', '');
		});
		
	});

})( jQuery );
