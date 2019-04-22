jQuery(document).ready(function($){

	var sliderInit = function () {

		// Counts number of decimals in current
		var countDecimals = function (value) {
		    if(Math.floor(value) === value) return 0;
		    return value.toString().split(".")[1].length || 0;
		}

		var renderSlider = function () {
			// Insert the slider div after a slider input
			// $("<div class='slider-display'></div>").insertAfter("input.slider");

			// Do this for each slider div
			$('.slider-display').each(function(i,GFSlider) {

				// Retrieve variables from DOM
				var slider = $(this);
				var input = slider.prev(':input');
				var value = input.val();
				var tabindex = input.attr('tabindex');
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
					tooltips: formatTooltip,
				});

				var handle = GFSlider.querySelector('.noUi-handle');

					handle.setAttribute('tabindex', tabindex);

					handle.addEventListener('click', function(){
						this.focus();
					});

					handle.addEventListener('keydown', function( e ) {

						var sliderValue = Number( GFSlider.noUiSlider.get() );

						switch ( e.which ) {
							case 37: GFSlider.noUiSlider.set( sliderValue - step );
								break;
							case 39: GFSlider.noUiSlider.set( sliderValue + step );
								break;
						}
					});

				GFSlider.noUiSlider.on('update', function(sliderVal) {

					input.attr('value',sliderVal);

					// input.value = sliderValue;

					//Hide the input
					input.hide();

					// Triggers update of merge tags on mouseup and keyup
					$('.gfield .slider').trigger('change');

				})


				// If no default value, then remove value from input
				if ( ! value ) {
					input.val('');
				}

				// Add min and max relation note
				slider.append('<span class="min-val-relation">' + minrel + '</span><span class="max-val-relation">' + maxrel + '</span>' );
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
