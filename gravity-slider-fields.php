<?php
/**
 * Plugin Name: Gravity Slider Fields
 * Plugin URI: http://vandercar.net/wp/gravity-slider-fields
 * Description: Adds slider fields to Gravity Forms
 * Version: 0.8
 * Author: UaMV
 * Author URI: http://vandercar.net
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Gravity Forms Slider Fields
 * @version 0.8
 * @author UaMV
 * @copyright Copyright (c) 2014, UaMV
 * @link http://vandercar.net/wp/gravity-slider-fields
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Define the plugin directory url
define( 'GSF_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'GSF_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GSF_VERSION', '0.8' );

// Do checks after plugins have loaded
function gsf_gravity_form_check() {

	// If Gravity Forms is enabled, require the slider field class
	if ( class_exists( 'GFForms' ) && class_exists( 'GF_Fields' ) ) {

		require_once( GSF_DIR_PATH . 'class-gf-field-slider.php' );

	}

	// If Gravity Forms is not enabled and current user can activate_plugins disable Gravity Slider Fields and display notice.
	else if ( current_user_can( 'activate_plugins' ) ) {

		add_action( 'admin_init', 'gsf_plugin_deactivate' );
		add_action( 'admin_notices', 'gsf_plugin_deactivate_admin_notice' );

		function gsf_plugin_deactivate() {
			
			deactivate_plugins( plugin_basename( __FILE__ ) );

		} // end gsf_plugin_deactivate

		function gsf_plugin_deactivate_admin_notice() {
			
			echo '<div class="error"><p><strong>Gravity Slider Fields</strong> has been deactivated, as it requires Gravity Forms v1.9 or greater.</p></div>';
			if ( isset( $_GET['activate'] ) ) {

				unset( $_GET['activate'] );

			}

		} // end gsf_plugin_deactivate_admin_notice

	}

} // end gsf_gravity_form_check
add_action( 'plugins_loaded', 'gsf_gravity_form_check' );

// Reassign the slider field button to advanced group
function gsf_add_field_buttons( $field_groups ) {

	// Loop through field groups
	foreach( $field_groups as $gkey => $group ) {

		// Find standard group
		if ( 'standard_fields' == $group['name'] ) {

			// Loop through standard fields
			foreach ( $group['fields'] as $fkey => $field ) {

				// If slider field, then grab it and unset from standard field group array
				if ( isset( $field['onclick'] ) && "StartAddField('slider');" == $field['onclick'] ) {

					$slider = $field;
					unset( $field_groups[ $gkey ]['fields'][ $fkey ] );
					break;

				}

			}
			break;

		}

	}

	// Loop through field groups
	foreach( $field_groups as &$group ) {

		// Find advanced group
		if( 'advanced_fields' == $group['name'] ) {

			// Add slider field
			$group['fields'][] = $slider;
			break;

		}

	}

	return $field_groups;

} // end gsf_add_field_buttons
add_filter( 'gform_add_field_buttons', 'gsf_add_field_buttons' );

// Set default values when adding a slider
function gsf_set_defaults() {
	?>
	    case "slider" :
	    	field.label = "Untitled";
	        field.numberFormat = "decimal_dot";
	        field.rangeMin = 0;
	        field.rangeMax = 10;
	        field.slider_step = 1;
	        field.slider_value_visibility = "hidden";
	    break;
	<?php
} // end gsf_set_defaults
add_action( 'gform_editor_js_set_default_values', 'gsf_set_defaults' );

// Execute javascript for proper loading of field
function gsf_editor_js(){
	?>
		<script type='text/javascript'>
			jQuery(document).ready(function($) {
				
				// Bind to the load field settings event to initialize the slider settings
				$(document).bind("gform_load_field_settings", function(event, field, form){
					jQuery("#slider_min_value_relation").val(field['slider_min_value_relation']);
					jQuery("#slider_max_value_relation").val(field['slider_max_value_relation']);
					jQuery("#slider_step").val(field['slider_step']);
					jQuery("#slider_value_visibility").val(field['slider_value_visibility']);
				});

			});
		</script>
	<?php
} // end gsf_editor_js
add_action( 'gform_editor_js', 'gsf_editor_js' );

// Render custom options for the field
function gsf_slider_settings( $position, $form_id ) {
	
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
		<?php
	}
} // end gsf_slider_settings
add_filter( 'gform_field_standard_settings' , 'gsf_slider_settings' , 10, 2 );

// Add tooltips for the custom options
function gsf_tooltips( $tooltips ) {

	$tooltips['slider_value_relations'] = __( '<h6>Value Relations</h6>Enter descriptive terms that relate to the min and max number values of the slider.', 'gsf-locale' );
	$tooltips['slider_step'] = __( '<h6>Step</h6>Enter a value that each interval or step will take between the min and max of the slider. The full specified value range of the slider (max - min) should be evenly divisible by the step and the step should not exceed a precision of two decimal places. (default: 1)', 'gsf-locale' );
	$tooltips['slider_value_visibility'] = __( '<h6>Value Visibility</h6>Select whether to hide, show on hover & drag, or always show the currently selected value.', 'gsf-locale' );
	
	return $tooltips;

} // end gsf_tooltips
add_filter( 'gform_tooltips', 'gsf_tooltips');

// Enqueue all scripts and styles
function gsf_enqueue() {

	// Enqueue the styles
	wp_enqueue_style( 'noUiSlider', GSF_DIR_URL . 'noUiSlider/jquery.nouislider.min.css', array(), GSF_VERSION );
	wp_enqueue_style( 'noUiSlider-pips', GSF_DIR_URL . 'noUiSlider/jquery.nouislider.pips.min.css', array(), GSF_VERSION );
	wp_enqueue_style( 'gslider-fields', GSF_DIR_URL . 'slider.min.css', array(), GSF_VERSION );

	// Enqueue necessary scripts
	wp_enqueue_script( 'noUiSlider', GSF_DIR_URL . 'noUiSlider/jquery.nouislider.all.min.js', array( 'jquery' ), GSF_VERSION );
	wp_enqueue_script( 'gslider-fields', GSF_DIR_URL . 'slider.min.js', array( 'jquery', 'noUiSlider' ), GSF_VERSION );

} // end gsf_enqueue

// Add our scripts and styles if a slider field exists in the form
function gsf_enqueue_scripts( $form, $is_ajax ) {

	// Loop through form fields
	foreach ( $form['fields'] as $field ) {
		
		// If a slider is found
		if ( 'slider' == $field['type'] ) {

			gsf_enqueue();

			// Then stop looking through the fields
			break;

		}

	}

} // end gsf_enqueue_scripts
add_action( 'gform_enqueue_scripts' , 'gsf_enqueue_scripts', 20, 2 );

// Add our scripts and styles if a slider field exists in the form
function gsf_admin_enqueue_scripts() {

	if ( 'gf_edit_forms' == $_GET['page'] ) {

		gsf_enqueue();

	}

} // end gsf_enqueue_scripts
add_action( 'admin_enqueue_scripts', 'gsf_admin_enqueue_scripts' );

function gsf_register_safe_script( $scripts ){

    //registering my script with Gravity Forms so that it gets enqueued when running on no-conflict mode
    $scripts[] = 'noUiSlider';
    $scripts[] = 'gslider-fields';
    
    return $scripts;

} // end gsf_register_safe_script
add_filter( 'gform_noconflict_scripts', 'gsf_register_safe_script' );

// Append min/max relation notes to label in notifications, confirmations and entry detail
function gsf_pre_submission_filter( $form ) {

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

} // gsf_pre_submission_filter
add_filter( 'gform_pre_submission_filter', 'gsf_pre_submission_filter' );
( isset( $_GET['page'] ) && 'gf_entries' == $_GET['page'] ) ? add_filter( 'gform_admin_pre_render', 'gsf_pre_submission_filter' ) : FALSE;