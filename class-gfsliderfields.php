<?php

GFForms::include_addon_framework();

class GFSliderFields extends GFAddOn {

	protected $_version = GF_SLIDER_FIELDS_VERSION;
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'slider-fields';
	protected $_path = 'gravity-slider-fields/gravity-slider-fields.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Slider Fields';
	protected $_short_title = 'Gravity Slider Fields';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFCustomEntryLimit
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFSliderFields();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
        
		parent::init();

	    load_plugin_textdomain( 'typewheel', false, basename( dirname( __file__ ) ) . '/languages/' );

        // creating the slider field
        add_action( 'gform_editor_js_set_default_values', array( $this, 'set_defaults' ) );
        add_action( 'gform_editor_js', array( $this, 'editor_js' ) );
        add_filter( 'gform_field_standard_settings' , array( $this, 'slider_settings' ) , 10, 2 );

        // add tooltips
        $this->add_tooltip( 'slider_value_relations', sprintf(
            '<h6>%s</h6> %s',
            __( 'Value Relations', 'typewheel' ),
            __( 'Enter descriptive terms that relate to the min and max number values of the slider.', 'typewheel' )
        ) );
        $this->add_tooltip( 'slider_step', sprintf(
            '<h6>%s</h6> %s',
            __( 'Step', 'typewheel' ),
            __( 'Enter a value that each interval or step will take between the min and max of the slider. The full specified value range of the slider (max - min) should be evenly divisible by the step and the step should not exceed a precision of two decimal places. (default: 1)', 'typewheel' )
        ) );
        $this->add_tooltip( 'slider_value_visibility', sprintf(
            '<h6>%s</h6> %s',
            __( 'Value Visibility', 'typewheel' ),
            __( 'Select whether to hide, show on hover & drag, or always show the currently selected value.', 'typewheel' )
        ) );
        $this->add_tooltip( 'slider_connect', sprintf(
            '<h6>%s</h6> %s',
            __( 'Connecting Elements', 'typewheel' ),
            __( 'Select whether to visually connect the handle to the upper or lower edge of the slider.', 'typewheel' )
        ) );

		add_filter( 'gform_custom_merge_tags', array( $this, 'slider_calculation_merge_tags' ), 10, 4 );

        // control submission & rendering of form_settings
        add_filter( 'gform_pre_submission_filter', array( $this, 'pre_submission_filter' ) );
        ( isset( $_GET['page'] ) && 'gf_entries' == $_GET['page'] ) ? add_filter( 'gform_admin_pre_render', array( $this, 'pre_submission_filter' ) ) : FALSE;

	}

	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$scripts = array(
            array(
				'handle'  => 'noUiSlider',
				'src'     => $this->get_base_url() . '/noUiSlider/nouislider.min.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery' ),
				'enqueue' => array(
                    array( 'admin_page' => array( 'form_settings' ) ),
                    array( 'field_types' => array( 'slider' ) )
				)
			),
            array(
				'handle'  => 'wNumb',
				'src'     => $this->get_base_url() . '/wNumb/wNumb.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery' ),
				'enqueue' => array(
                    array( 'admin_page' => array( 'form_settings' ) ),
                    array( 'field_types' => array( 'slider' ) )
				)
			),
			array(
				'handle'  => 'slider_fields',
				'src'     => $this->get_base_url() . '/js/slider.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery', 'noUiSlider', 'wNumb' ),
				'enqueue' => array(
					array( 'admin_page' => array( 'form_settings' ) ),
                    array( 'field_types' => array( 'slider' ) )
				)
			),

		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {

        $styles = array(
            array(
                'handle'  => 'noUiSlider',
                'src'     => $this->get_base_url() . '/noUiSlider/nouislider.min.css',
                'version' => $this->_version,
                'enqueue' => array(
                    array( 'admin_page' => array( 'form_settings' ) ),
                    array( 'field_types' => array( 'slider' ) )
                )
            ),
            array(
                'handle'  => 'slider_fields',
                'src'     => $this->get_base_url() . '/css/slider.css',
                'version' => $this->_version,
                'enqueue' => array(
                    array( 'admin_page' => array( 'form_settings' ) ),
                    array( 'field_types' => array( 'slider' ) )
                )
            )
        );

        return array_merge( parent::styles(), $styles );

	}


	// # FRONTEND FUNCTIONS --------------------------------------------------------------------------------------------

	/**
	 * Add the text in the plugin settings to the bottom of the form if enabled for this form.
	 *
	 * @param string $button The string containing the input tag to be filtered.
	 * @param array $form The form currently being displayed.
	 *
	 * @return string
	 */



	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

    /**
	 * Set default values when adding a slider
	 *
	 * @since    0.1
	 */
    function set_defaults() {
    	?>
    	    case "slider" :
    	    	field.label = "Untitled";
    	        field.numberFormat = "decimal_dot";
    	        field.rangeMin = 0;
    	        field.rangeMax = 10;
    	        field.slider_step = 1;
    	        field.slider_value_visibility = "hidden";
              field.slider_connect = "none";
    	    break;
    	<?php
    } // end set_defaults

    /**
	 * Execute javascript for proper loading of field
	 *
	 * @since    0.1
	 */
    function editor_js() {
    	?>
    		<script type='text/javascript'>
    			jQuery(document).ready(function($) {

    				// Bind to the load field settings event to initialize the slider settings
    				$(document).bind("gform_load_field_settings", function(event, field, form){
    					jQuery("#slider_min_value_relation").val(field['slider_min_value_relation']);
    					jQuery("#slider_max_value_relation").val(field['slider_max_value_relation']);
    					jQuery("#slider_step").val(field['slider_step']);
    					jQuery("#slider_value_visibility").val(field['slider_value_visibility']);
    					jQuery("#slider_connect").val(field['slider_connect']);
    				});

    			});
    		</script>
    	<?php
    } // end editor_js

    /**
	 * Render custom options for the field
	 *
	 * @since    0.1
	 */
    function slider_settings( $position, $form_id ) {

    	// Create settings on position 1550 (right after range option)
    	if ( 1550 == $position ) {
    		?>
    			<li class="slider_value_relations field_setting">
    				<div style="clear:both;">
    					<?php _e( 'Value Relations', 'gsf-locale' ); ?>
    					<?php gform_tooltip( 'slider_value_relations' ); ?>
    				</div>
    				<div style="width:50%;float:left"><input type="text" id="slider_min_value_relation" style="width:100%;" onchange="SetFieldProperty('slider_min_value_relation', this.value);" /><label for="slider_min_value_relation"><?php _e( 'Min', 'gsf-locale' ); ?></label></div>
    				<div style="width:50%;float:left"><input type="text" id="slider_max_value_relation" style="width:100%;" onchange="SetFieldProperty('slider_max_value_relation', this.value);" /><label for="slider_max_value_relation"><?php _e( 'Max', 'gsf-locale' ); ?></label></div>
    				<br class="clear">
    			</li>
    			<li class="slider_step field_setting">
    				<div style="clear:both;">
    					<?php _e( 'Step', 'gsf-locale' ); ?>
    					<?php gform_tooltip( 'slider_step' ); ?>
    				</div>
    				<div style="width:25%;"><input type="number" id="slider_step" step=".01" style="width:100%;" onchange="SetFieldProperty('slider_step', this.value);" /></div>
    			</li>
    			<li class="slider_value_visibility field_setting">
    				<div style="clear:both;">
    					<?php _e( 'Show Value', 'gsf-locale' ); ?>
    					<?php gform_tooltip( 'slider_value_visibility' ); ?>
    				</div>
    				<div style="width:25%;">
    					<select id="slider_value_visibility" onchange="SetFieldProperty('slider_value_visibility', this.value);">
    						<option value="hidden"><?php _e( 'Hidden', 'gsf-locale' ); ?></option>
    						<option value="hover-drag"><?php _e( 'Hover/Drag', 'gsf-locale' ); ?></option>
    						<option value="show"><?php _e( 'Shown', 'gsf-locale' ); ?></option>
    					</select>
    				</div>
    			</li>
    			<li class="slider_connect field_setting">
    				<div style="clear:both;">
    					<?php _e( 'Connect', 'gsf-locale' ); ?>
    					<?php gform_tooltip( 'slider_connect' ); ?>
    				</div>
    				<div style="width:25%;">
    					<select id="slider_connect" onchange="SetFieldProperty('slider_connect', this.value);">
    						<option value="none"><?php _e( 'None', 'gsf-locale' ); ?></option>
    						<option value="lower"><?php _e( 'Lower', 'gsf-locale' ); ?></option>
    						<option value="upper"><?php _e( 'Upper', 'gsf-locale' ); ?></option>
    					</select>
    				</div>
    			</li>
    		<?php
    	}
    } // end slider_settings

	/**
     * Add merge tags to calculation drop down
     *
     * @since    1.5
     */
	function slider_calculation_merge_tags( $merge_tags, $form_id, $fields, $element_id ) {

		// check the type of merge tag dropdown
		if ( 'field_calculation_formula' != $element_id ) {
			return $merge_tags;
		}

		foreach ( $fields as $field ) {

			// check the field type as we only want to generate merge tags for list fields
			if ( 'slider' != $field->get_input_type() ) {
				continue;
			}

			$merge_tags[] = array( 'label' => $field->label, 'tag' => '{' . $field->label . ':' . $field->id . '}' );

		}

		return $merge_tags;
	} // END slider_calculation_merge_tags

    /**
     * Append min/max relation notes to label in notifications, confirmations and entry detail
     *
     * @since    0.1
     */
    function pre_submission_filter( $form ) {

    	// Loop through form fields
    	foreach ( $form['fields'] as &$field ) {

    		// If a slider is found
    		if ( 'slider' == $field['type'] ) {

    			// Set default min/max values, if they do not exist for the field
    			$min = ( isset( $field['rangeMin'] ) && '' != $field['rangeMin'] ) ? $field['rangeMin'] : 0;
    			$max = ( isset( $field['rangeMax'] ) && '' != $field['rangeMax'] ) ? $field['rangeMax'] : 10;

    			// If min/max relations exist, append them to the field label
    			if ( '' != $field['slider_min_value_relation'] || '' != $field['slider_max_value_relation'] ) {

    				$field['label'] = $field['label'] . ' (' . GFCommon::format_number( $min, $field['numberFormat'] ) . ': ' . $field['slider_min_value_relation'] . ', ' . GFCommon::format_number( $max, $field['numberFormat'] ) . ': ' . $field['slider_max_value_relation'] . ')';

    			}

    		}

    	}

    	return $form;

    } // pre_submission_filter


	public function add_tooltip( $key, $content ) {
		$this->tooltips[ $key ] = $content;
		add_filter( 'gform_tooltips', array( $this, 'load_tooltips' ) );
	}

	public function load_tooltips( $tooltips ) {
		return array_merge( $tooltips, $this->tooltips );
	}

}
