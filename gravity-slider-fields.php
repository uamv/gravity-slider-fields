<?php
/*
Plugin Name: Gravity Slider Fields
Plugin URI: https://wordpress.org/plugins/gravity-slider-fields/
Description: Adds slider fields to Gravity Forms
Version: 2.0
Author: Typewheel
Author URI: https://typewheel.xyz/

------------------------------------------------------------------------
Copyright 2012-2017 Typewheel LLC

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

define( 'GF_SLIDER_FIELDS_VERSION', '2.0' );

add_action( 'gform_loaded', array( 'GF_Slider_Fields_Bootstrap', 'load' ), 5 );

class GF_Slider_Fields_Bootstrap {

    public static function load() {

        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        require_once( 'class-gfsliderfields.php' );


		// If Gravity Forms is enabled, require the slider field class
		if ( class_exists( 'GF_Fields' ) ) {

			require_once( 'class-gf-field-slider.php' );

		}

        GFAddOn::register( 'GFSliderFields' );

    }

}

function gf_slider_fields() {
    return GFSliderFields::get_instance();
}

/**** DECLARE TYPEWHEEL NOTICES ****/
require_once( 'typewheel-notice/class-typewheel-notice.php' );

if ( apply_filters( 'gsf_show_notices', false ) ) {

	add_action( 'admin_notices', 'typewheel_gravity_slider_field_notices' );

	/**
	 * Displays a plugin notices
	 *
	 * @since    1.0
	 */
	function typewheel_gravity_slider_field_notices() {

		$prefix = str_replace( '-', '_', dirname( plugin_basename(__FILE__) ) );

			// Define the notices
			$typewheel_notices = array(
				$prefix . '-give' => array(
					'trigger' => true,
					'time' => time() + 2592000,
					'dismiss' => array( 'month' ),
					'type' => '',
					'content' => 'Is <strong>Gravity Slider Fields</strong> working well for you? Please consider giving <a href="https://wordpress.org/support/plugin/gravity-slider-fields/reviews/?rate=5#new-post" target="_blank"><i class="dashicons dashicons-star-filled"></i> a review</a>, <a href="https://twitter.com/intent/tweet/?url=https%3A%2F%2Fwordpress.org%2Fplugins%2Fgravity-slider-fields%2F" target="_blank"><i class="dashicons dashicons-twitter"></i> a tweet</a> or <a href="https://typewheel.xyz/give/?ref=Gravity%20Slider%20Fields" target="_blank"><i class="dashicons dashicons-heart"></i> a donation</a> to encourage further development. Thanks! <a href="https://twitter.com/uamv/">@uamv</a>',
					// 'icon' => 'heart',
					'style' => array( 'background-image' => 'linear-gradient( to left, rgb(215, 215, 215), rgb(220, 213, 206) )', 'border-left-color' => '#3F3F3F' ),
					'location' => array( 'admin.php?page=gf_edit_forms', 'admin.php?page=gf_entries', 'admin.php?page=gf_settings', 'admin.php?page=gf_addons' ),
				),
			);

			// get the notice class
			new Typewheel_Notice( $prefix, $typewheel_notices );

	} // end display_plugin_notices

}

/**
 * Deletes activation marker so it can be displayed when the plugin is reinstalled or reactivated
 *
 * @since    1.0
 */
function typewheel_gravity_slider_fields_remove_activation_marker() {

	$prefix = str_replace( '-', '_', dirname( plugin_basename(__FILE__) ) );

	delete_option( $prefix . '_activated' );

}
register_deactivation_hook( dirname(__FILE__) . '/gravity-slider-fields.php', 'typewheel_gravity_slider_fields_remove_activation_marker' );
