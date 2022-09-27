<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}


class GF_Field_Slider extends GF_Field {

	public $type = 'slider';

	public function get_form_editor_field_title() {
		return esc_attr__( 'Slider', 'gravityforms' );
	}

	function get_form_editor_field_settings() {
		return array(
         'conditional_logic_field_setting',
         'prepopulate_field_setting',
         // 'error_message_setting',
			'label_setting',
         'label_placement_setting',
         // 'sub_label_placement_setting',
         // 'admin_label_setting',
         'default_value_setting',
			'description_setting',
			'number_format_setting',
			'range_setting',
         // 'rules_setting',
			'css_class_setting',
			'admin_label_setting',
			'visibility_setting',
			'slider_value_relations',
			'slider_step',
			'slider_value_visibility',
			'slider_connect'
		);
	}


	// # FORM EDITOR & FIELD MARKUP -------------------------------------------------------------------------------------

   /**
    * Sets field button properties for the form editor
    * @return array
    */
   public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title()
		);
	}

   /**
    * Indicates whether field can be used when configuring conditional logic rules.
    * @return boolean
    */
	public function is_conditional_logic_supported() {
		return true;
	}

   /**
    * Returns the field inner markup.
    * @param  array        $form  The Form Object currently being processed.
    * @param  string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
    * @param  null|array   $entry Null or the Entry Object currently being edited.
    * @return string
    */
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

				$message          = $this->get_failed_validation_message();
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

		if ( '' == $value ) {

			$value = ( $min + $max ) / 2;
		}

		$placeholder_attribute = $this->get_field_placeholder_attribute();

		$tabindex = $this->get_tabindex();

		$data_value_visibility = isset( $this->slider_value_visibility ) ? "data-value-visibility='{$this->slider_value_visibility}'" : "data-value-visibility='hidden'";

      $connects_attr = ( $this->slider_connect == "none" || $this->slider_connect == "" ) ? "data-connect=false" : "data-connect='{$this->slider_connect}'";

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

		return sprintf( "<div class='ginput_container ginput_container_slider'><input name='input_%d' id='%s' type='{$html_input_type}' {$step_attr} {$min_attr} {$max_attr} {$data_value_visibility} ${connects_attr} value='%s' class='%s' data-min-relation='%s' data-max-relation='%s' data-value-format='%s' {$currency} {$tabindex} {$read_only} {$placeholder_attribute} %s/><div id='gsfslider_%d' class='slider-display'></div>%s</div>", $id, $field_id, esc_attr( $value ), esc_attr( $class ), esc_attr( $this->slider_min_value_relation ), esc_attr( $this->slider_max_value_relation ), esc_attr( $this->numberFormat ), $disabled_text, $id, $instruction );

	}


   // # SUBMISSION -----------------------------------------------------------------------------------------------------

   /**
    * Whether this field expects an array during submission.
    * @return boolean
    */
   public function is_value_submission_array() {
      return false; // TODO: setup field type setting and toggle this on that condition
   }

   /**
    * Validate the field value being submitted
    * @param string|array $value The field value from get_value_submission().
	 * @param array        $form  The Form Object currently being processed.
    */
   public function validate( $value, $form ) {

      // the POST value has already been converted from currency or decimal_comma to decimal_dot and then cleaned in get_field_value()

      $value     = GFCommon::maybe_add_leading_zero( $value );
      $raw_value = $_POST[ 'input_' . $this->id ]; //Raw value will be tested against the is_numeric() function to make sure it is in the right format.

      $is_valid_number = $this->validate_range( $value ) && GFCommon::is_numeric( $raw_value, $this->numberFormat );

      if ( ! $is_valid_number ) {
         $this->failed_validation  = true;
         $this->validation_message = empty( $this->errorMessage ) ? $this->get_failed_validation_message() : $this->errorMessage;
      }

   }

   /**
    * Retrieve the field value on submission.
    * @param array     $field_values             The dynamic population parameter names with their corresponding values to be populated.
    * @param bool|true $get_from_post_global_var Whether to get the value from the $_POST array as opposed to $field_values.
    *
    * @return array|string
    */
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

	/**
	 * Validates the range of the number according to the field settings.
	 *
	 * @param array $value A decimal_dot formatted string
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

	public function get_failed_validation_message() {
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


	// # ENTRY RELATED --------------------------------------------------------------------------------------------------

   /**
    * Sanitize and format the value before it is saved to the Entry Object.
    * @param string $value      The value to be saved.
	 * @param array  $form       The Form Object currently being processed.
	 * @param string $input_name The input name used when accessing the $_POST.
	 * @param int    $lead_id    The ID of the Entry currently being processed.
	 * @param array  $lead       The Entry Object currently being processed.
    * @return array|string The safe value.
    */
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

   /**
    * Format the entry value for when the field/input merge tag is processed. Not called for the {all_fields} merge tag.
    *
    * Return a value that is safe for the context specified by $format.
    *
    * @since  Unknown
    * @access public
    *
    * @param string|array $value      The field value. Depending on the location the merge tag is being used the following functions may have already been applied to the value: esc_html, nl2br, and urlencode.
    * @param string       $input_id   The field or input ID from the merge tag currently being processed.
    * @param array        $entry      The Entry Object currently being processed.
    * @param array        $form       The Form Object currently being processed.
    * @param string       $modifier   The merge tag modifier. e.g. value
    * @param string|array $raw_value  The raw field value from before any formatting was applied to $value.
    * @param bool         $url_encode Indicates if the urlencode function may have been applied to the $value.
    * @param bool         $esc_html   Indicates if the esc_html function may have been applied to the $value.
    * @param string       $format     The format requested for the location the merge is being used. Possible values: html, text or url.
    * @param bool         $nl2br      Indicates if the nl2br function may have been applied to the $value.
    *
    * @return string
    */
   public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {

      return GFCommon::format_number( $value, $this->numberFormat );

   }

   /**
    * Format the entry value for display on the entries list page.
    *
    * Return a value that's safe to display on the page.
    *
    * @param string|array $value    The field value.
    * @param array        $entry    The Entry Object currently being processed.
    * @param string       $field_id The field or input ID currently being processed.
    * @param array        $columns  The properties for the columns being displayed on the entry list page.
    * @param array        $form     The Form Object currently being processed.
    *
    * @return string
    */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

		return GFCommon::format_number( $value, $this->numberFormat );

	}

   /**
	 * Format the entry value for display on the entry detail page and for the {all_fields} merge tag.
	 *
	 * Return a value that's safe to display for the context of the given $format.
	 *
	 * @param string|array $value    The field value.
	 * @param string       $currency The entry currency code.
	 * @param bool|false   $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param string       $format   The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param string       $media    The location where the value will be displayed. Possible values: screen or email.
	 *
	 * @return string
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		return GFCommon::format_number( $value, $this->numberFormat );

	}



}

GF_Fields::register( new GF_Field_Slider() );
