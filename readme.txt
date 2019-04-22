=== Gravity Slider Fields ===

Contributors: UaMV
Donate link: https://typewheel.xyz/give
Tags: gravity, forms, slider, field, number
Requires at least: 3.1
Tested up to: 4.9
Stable tag: 1.4
License: GPLv2 or later

Adds slider fields to Gravity Forms

== Description ==

Gravity Forms does not yet support slider fields. Until that time, this plugin may serve the purpose well.

The plugin adds a new slider field within the advanced field group. Customize your slider with the following set of options.

= Supported Options =

**Native to Gravity Forms**

* field label
* description
* number format
* range
* field label visibility
* description placement
* custom css class
* field size
* admin field label
* default value
* visibility
* allow field to be populated dynamically
* enable conditional logic

**Custom**

* value relations (descriptive terms that relate to min & max number values of slider)
* step (precision of values that can selected along the slider)
* show value (select whether to hide, show on hover & drag, or always shows the currently selected value)

If value relations have been defined, they will be displayed along with the field label in form entry details and when using merge tags.

= Credits =
In order to play nice with touch devices, Gravity Slider Fields utilizes [noUiSlider](http://refreshless.com/nouislider "noUiSlider") (developed by [Léon Gersen](http://twitter.com/LeonGersen))

== Installation ==

1. Upload the `gravity-slider-fields` directory to `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Does this plugin rely on anything? =
Yes. You must install [Gravity Forms](http://gravityforms.com "Gravity Forms") for this plugin to work.

== Screenshots ==

1. Rendered sliders
1. Slider field selection
1. Custom slider options

== Changelog ==

= 1.4 =
* Delay the notice

= 1.3 =
* Fix the fix

= 1.2 =
* Fixes jQuery error in loading noUiSlider
* Adds delayed & dismissible admin notice

= 1.1 =
* The slider merge tag can now be used in calculations *

= 1.0 =
* rewrite codebase to use GFAddOn

= 0.9 =
* Plays nicer with Gravity Forms
* Updates noUiSlider version

= 0.8 =
* Adds rendering of field in form editor

= 0.7 =
* Added class existence check for GF_Fields

= 0.6 =
* Cautionary note in the readme.

= 0.5 =
* Fix for slider failure in certain instance

= 0.4 =
* Initial Release

== Upgrade Notice ==

= 1.3 =
* Fix the fix introduced in v1.2

= 1.2 =
* Fixes jQuery error in loading noUiSlider
* Adds delayed & dismissible admin notice

= 1.1 =
* You can now use slider merge tags in calculations

= 0.9 =
* Plays nicer with Gravity Forms
* Updates noUiSlider version

= 0.8 =
* Field rendering on form editor.

= 0.7 =
* First stable version!

= 0.4 =
* Initial Release
