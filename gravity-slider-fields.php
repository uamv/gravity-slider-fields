<?php
/*
Plugin Name: Gravity Slider Fields
Plugin URI: https://wordpress.org/plugins/gravity-slider-fields/
Description: Adds slider fields to Gravity Forms
Version: 1.1
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

define( 'GF_SLIDER_FIELDS_VERSION', '1.1' );

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
