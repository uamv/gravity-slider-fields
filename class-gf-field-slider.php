<?php

class GF_Field_Slider extends GF_Field {

	public $type = 'slider';

	public function get_form_editor_field_title() {
		return __( 'Slider', 'gravityforms' );
	}

	function get_form_editor_field_settings() {
		return array(
			'label_setting',
			'description_setting',
			'number_format_setting',
			'range_setting',
			'label_placement_setting',
			'css_class_setting',
			'admin_label_setting',
			'default_value_setting',
			'visibility_setting',
			'prepopulate_field_setting',
			'conditional_logic_field_setting',
			'slider_value_relations',
			'slider_step',
			'slider_value_visibility'
		);
	}

	public function is_conditional_logic_supported(){
		return false;
	}

	public function get_value_submission( $field_values, $get_from_post_global_var = true ) {

		$value = $this->get_input_value_submission( 'input_' . $this->id, $this->inputName, $field_values, $get_from_post_global_var );
		$value = trim( $value );
		if ( $this->numberFormat == 'currency' ) {
			require_once( GFCommon::get_base_path() . '/currency.php' );
			$currency = new RGCurrency( GFCommon::get_currency() );
			$value    = $currency->to_number( $value );
		} else if ( $this->numberFormat == 'decimal_comma' ) {
			$value = GFCommon::clean_number( $value, 'decimal_comma' );
		} else if ( $this->numberFormat == 'decimal_dot' ) {
			$value = GFCommon::clean_number( $value, 'decimal_dot' );
		}

		return $value;
	}

	public function validate( $value, $form ) {

		// the POST value has already been converted from currency or decimal_comma to decimal_dot and then cleaned in get_field_value()

		$value     = GFCommon::maybe_add_leading_zero( $value );
		$raw_value = $_POST[ 'input_' . $this->id ]; //Raw value will be tested against the is_numeric() function to make sure it is in the right format.

		$is_valid_number = $this->validate_range( $value ) && GFCommon::is_numeric( $raw_value, $this->numberFormat );

		if ( ! $is_valid_number ) {
			$this->failed_validation  = true;
			$this->validation_message = empty( $this->errorMessage ) ? $this->get_range_message() : $this->errorMessage;
		}

	}

	/**
	 * Validates the range of the number according to the field settings.
	 *
	 * @param array $value A decimal_dot formatted string
	 *
	 * @return true|false True on valid or false on invalid
	 */
	private function validate_range( $value ) {

		if ( ! GFCommon::is_numeric( $value, 'decimal_dot' ) ) {
			return false;
		}

		if ( ( is_numeric( $this->rangeMin ) && $value < $this->rangeMin ) ||
			( is_numeric( $this->rangeMax ) && $value > $this->rangeMax )
		) {
			return false;
		} else {
			return true;
		}
	}

	public function get_range_message() {
		$min     = $this->rangeMin;
		$max     = $this->rangeMax;
		$message = '';

		if ( is_numeric( $min ) && is_numeric( $max ) ) {
			$message = sprintf( __( 'Please enter a value between %s and %s.', 'gravityforms' ), "<strong>$min</strong>", "<strong>$max</strong>" );
		} else if ( is_numeric( $min ) ) {
			$message = sprintf( __( 'Please enter a value greater than or equal to %s.', 'gravityforms' ), "<strong>$min</strong>" );
		} else if ( is_numeric( $max ) ) {
			$message = sprintf( __( 'Please enter a value less than or equal to %s.', 'gravityforms' ), "<strong>$max</strong>" );
		} else if ( $this->failed_validation ) {
			$message = __( 'Please enter a valid number', 'gravityforms' );
		}

		return $message;
	}

	public function get_field_input( $form, $value = '', $entry = null ) {
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$form_id  = $form['id'];
		$id       = intval( $this->id );
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		$size          = $this->size;
		$disabled_text = $is_form_editor ? "disabled='disabled'" : '';
		$class_suffix  = $is_entry_detail ? '_admin' : '';
		$class         = $this->type . ' ' .$size . $class_suffix;

		$instruction = '';
		$read_only   = '';

		if ( ! $is_entry_detail && ! $is_form_editor ) {

			if ( $this->has_calculation() ) {

				// calculation-enabled fields should be read only
				$read_only = 'readonly="readonly"';

			} else {

				$message          = $this->get_range_message();
				$validation_class = $this->failed_validation ? 'validation_message' : '';

				if ( ! $this->failed_validation && ! empty( $message ) && empty( $this->errorMessage ) ) {
					//$instruction = "<div class='instruction $validation_class'>" . $message . '</div>';
				}
			}
		} else if ( RG_CURRENT_VIEW == 'entry' ) {
			$value = GFCommon::format_number( $value, $this->numberFormat );
		}

		$step = ( isset( $this->slider_step ) && '' != $this->slider_step ) ? $this->slider_step : 1;

		$html_input_type = ! $this->has_calculation() && ( $this->numberFormat != 'currency' && $this->numberFormat != 'decimal_comma' ) ? 'number' : 'text'; // chrome does not allow number fields to have commas, calculations and currency values display numbers with commas
		$step_attr       = "step='{$this->slider_step}'";

		$min = ( isset( $this->rangeMin ) && '' != $this->rangeMin ) ? $this->rangeMin : 0;
		$max = ( isset( $this->rangeMax ) && '' != $this->rangeMax ) ? $this->rangeMax : 10;

		$min_attr = "min='{$min}'";
		$max_attr = "max='{$max}'";

		$logic_event = $this->get_conditional_logic_event( 'keyup' );

		$placeholder_attribute = $this->get_field_placeholder_attribute();

		$tabindex = $this->get_tabindex();

		$data_value_visibility = isset( $this->slider_value_visibility ) ? "data-value-visibility='{$this->slider_value_visibility}'" : "data-value-visibility='hidden'";

		if ( 'currency' == $this->numberFormat ) {
			// get current gravity forms currency
			$code = ! get_option( 'rg_gforms_currency' ) ? 'USD' : get_option( 'rg_gforms_currency' );
			if ( false === class_exists( 'RGCurrency' ) ) {
				require_once( GFCommon::get_base_path() . '/currency.php' );
			}
			$currency = new RGCurrency( GFCommon::get_currency() );
			$currency = $currency->get_currency( $code );

			// encode for html currency attribute
			$currency = "data-currency='" . json_encode($currency) . "'";
		} else {
			$currency = '';
		}

		return sprintf( "<div class='ginput_container'><input name='input_%d' id='%s' type='{$html_input_type}' {$step_attr} {$min_attr} {$max_attr} {$data_value_visibility} value='%s' class='%s' data-min-relation='%s' data-max-relation='%s' data-value-format='%s' {$currency} {$tabindex} {$logic_event} {$read_only} {$placeholder_attribute} %s/>%s</div>", $id, $field_id, esc_attr( $value ), esc_attr( $class ), esc_attr( $this->slider_min_value_relation ), esc_attr( $this->slider_max_value_relation ), esc_attr( $this->numberFormat ), $disabled_text, $instruction );

	}

	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

		return GFCommon::format_number( $value, $this->numberFormat );
	}


	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		return GFCommon::format_number( $value, $this->numberFormat );
	}

	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format ) {

		return GFCommon::format_number( $value, $this->numberFormat );
	}

	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {

		$value = GFCommon::maybe_add_leading_zero( $value );

		$lead  = empty( $lead ) ? RGFormsModel::get_lead( $lead_id ) : $lead;
		$value = $this->has_calculation() ? GFCommon::round_number( GFCommon::calculate( $this, $form, $lead ), $this->calculationRounding ) : GFCommon::clean_number( $value, $this->numberFormat );
		//return the value as a string when it is zero and a calc so that the "==" comparison done when checking if the field has changed isn't treated as false
		if ( $this->has_calculation() && $value == 0 ) {
			$value = '0';
		}

		return $value;
	}

}

GF_Fields::register( new GF_Field_Slider() );