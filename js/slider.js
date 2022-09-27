jQuery(document).ready(function($){

	var sliderInit = function () {

		// Counts number of decimals in current
		var countDecimals = function (value) {
		    if(Math.floor(value) === value) return 0;
		    return value.toString().split(".")[1].length || 0;
		}

		var renderSlider = function () {

			// Do this for each slider div
			$('.slider-display').each(function(i,GFSlider) {

				if ( ! $(this).hasClass('slider-initialized') ) {

					// Retrieve variables from DOM
					var slider = $(this);
					var input = slider.prev(':input');
					var value = input.val();
					var gfield = input.attr('id');
					var tabindex = input.attr('tabindex');
					var minrel = input.data('min-relation');
					var maxrel = input.data('max-relation');
					var min = parseFloat(input.attr('min'));
					var max = parseFloat(input.attr('max'));
					var step = parseFloat(input.attr('step'));
					var visibility = input.data('value-visibility');
               var connect = input.data('connect');
					var format = input.data('value-format');

					// Check whether step needs to be limited by the decimals available in the currency
					if ( 'currency' == format ) {
						var currency = input.data('currency');
						if ( currency['decimals'] < countDecimals(step) ) {
							if ( currency['decimals'] == 0 ) {
								var step = 1;
							} else if ( currency['decimals'] == 1 ) {
								var step = 0.1;
							} else if ( currency['decimals'] == 2 ) {
								var step = 0.01;
							}
						}
						var decs = currency['decimals'];
					} else {
						var decs = countDecimals(step);
					}

					// Determine handle value visibility and link to handle value
					if ( 'hover-drag' == visibility || 'show' == visibility ) {

						if ( 'currency' == format ) {
							var currency = input.data('currency');
							var formatTooltip = wNumb({
								decimals: currency['decimals'],
								mark: currency['decimal_separator'],
								thousand: currency['thousand_separator'],
								prefix: currency['symbol_left'] + currency['symbol_padding'],
								postfix: currency['symbol_padding'] + currency['symbol_right'],
							});
						} else if ( 'decimal_comma' == format ) {
							var formatTooltip = wNumb({
								decimals: countDecimals( step ),
								mark: ',',
								thousand: '.',
							});
						} else {
							var formatTooltip = wNumb({
								decimals: countDecimals( step ),
							});
						}

					} else {
						formatTooltip = false;
					}

					noUiSlider.create(GFSlider, {
						start: [ value ],
						step: step,
						range: {
							'min': [ min ],
							'max': [ max ]
						},
						format: wNumb({
							decimals: decs,
						}),
                  connect: connect,
						tooltips: formatTooltip,
					});

					// Prevents re-initializing sliders on form pagination
					slider.addClass('slider-initialized');

					GFSlider.noUiSlider.on('update', function(sliderVal) {

						input.attr('value',sliderVal);

						// Triggers update of merge tags on mouseup and keyup
						$('.gfield .slider').trigger('change');

					});

					document.getElementById(gfield).addEventListener('change', function () {
					    GFSlider.noUiSlider.set(this.value);
					});

					// Add min and max relation note
					slider.append('<span class="min-val-relation">' + minrel + '</span><span class="max-val-relation">' + maxrel + '</span>' );

				}

			});
		}

		renderSlider();
	};

	jQuery(document).bind('gform_page_loaded', function() {
		if ( $('.gfield .slider').length ) {
			sliderInit();
		}
	});

	sliderInit();

});
