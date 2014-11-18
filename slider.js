jQuery(document).ready(function($){

	// Counts number of decimals in current
	var countDecimals = function (value) {
	    if(Math.floor(value) === value) return 0;
	    return value.toString().split(".")[1].length || 0; 
	}

	var renderSlider = function () {
		// Insert the slider div after a slider input
		$("<div class='slider-display'></div>").insertAfter("input.slider");

		// Do this for each slider div
		$('.slider-display').each(function() {

			// Retrieve variables from DOM
			var slider = $(this);
			var input = slider.prev(':input');
			var value = input.val();
			var minrel = input.data('min-relation');
			var maxrel = input.data('max-relation');
			var min = parseFloat(input.attr('min'));
			var max = parseFloat(input.attr('max'));
			var step = parseFloat(input.attr('step'));
			var visibility = input.data('value-visibility');
			var format = input.data('value-format');

			// If no default value, then add class
			if( '' == input.attr('value') ) {
				slider.addClass('gsf-inactive');
				slider.find('.tooltip').hide();
			}

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

			// Initialize noUiSlider
			slider.noUiSlider({
				start: [ value ],
				step: step,
				range: {
					'min': [ min ],
					'max': [ max ]
				}
			});

			// Link to input field
			slider.Link('lower').to(input, null, wNumb({
				// Prefix the value with an Euro symbol
				// Write the value without decimals
				decimals: decs,
			}));

			// Hide the input
			input.hide();

			// If show, then add class
			slider.attr('data-value-visibility',visibility);

			// Remove inactive class once slider slides
			slider.on({
				slide: function() {
					slider.removeClass('gsf-inactive');
					slider.find('.tooltip').show();
				}
			});

			// Determine handle value visibility and link to handle value
			if ( 'hover-drag' == visibility || 'show' == visibility ) {

			    slider.Link('lower').to('-inline-<div class="tooltip"></div>', function ( val ) {

				    if ( 'currency' == format ) {
				    	var currency = input.data('currency');
				    	var formatVal = wNumb({
				    		decimals: currency['decimals'],
							mark: currency['decimal_separator'],
							thousand: currency['thousand_separator'],
							prefix: currency['symbol_left'] + currency['symbol_padding'],
							postfix: currency['symbol_padding'] + currency['symbol_right'],
						});
					} else if ( 'decimal_comma' == format ) {
						var formatVal = wNumb({
				    		decimals: countDecimals( step ),
							mark: ',',
							thousand: '.',
						});
					} else {
						var formatVal = wNumb({
				    		decimals: countDecimals( step ),
						});
					}

					$(this).html(
						'<span>' + formatVal.to( parseFloat( val ) ) + '</span>'
					);
				});
			}

			// If no default value, then remove value from input
			if ( ! value ) {
				input.val('');
			}

			// Add min and max relation note
			slider.append('<span class="min-val-relation">' + minrel + '</span><span class="max-val-relation">' + maxrel + '</span>' );
		});
	}

	renderSlider();

});