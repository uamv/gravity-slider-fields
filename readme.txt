=== Gravity Slider Fields ===

Contributors: UaMV
Donate link: https://typewheel.xyz/give/?ref=Gravity%20Slider%20Fields
Tags: gravity, forms, slider, field, number
Requires at least: 3.1
Requires PHP: 5.6
Tested up to: 6.02
Stable tag: 2.0
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
* handle connects
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
* connect (visually connect the handle to the upper or lower edge of the slider)

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

= 2.0 =
🐛 FIX: Maybe fix acceptance of default value
🐛 FIX: Default input not being hidden properly

= 1.9 =
📦 NEW: Use sliders in conditional logic
✨ IMPROVE: Performance when triggering change
✨ IMPROVE: Disable delayed admin notice
✨ IMPROVE: Use emoji-log for changelog

= 1.8 =
🐛 FIX: Properly handle connect option for existing slider fields

= 1.7 =
📦 NEW: Allow connecting handle to lower or upper edge

= 1.6 =
✨ IMPROVE: Remove call to deprecated GF_Field::get_conditional_logic_event

= 1.5 =
✨ IMPROVE: Update noUiSlider to 13.1.4
📦 NEW: Add calculation merge tags
🐛 FIX: Hover-drag issue
🐛 FIX: Tab focus issue
🐛 FIX: error: `Slider was already initialized`

= 1.4 =
✨ IMPROVE: Delay the notice

= 1.3 =
🐛 FIX: Better fix

= 1.2 =
🐛 FIX: jQuery error in loading noUiSlider
📦 NEW: Add delayed & dismissible admin notice

= 1.1 =
📦 NEW: The slider merge tag can now be used in calculations

= 1.0 =
✨ IMPROVE: Rewrite codebase to use GFAddOn

= 0.9 =
✨ IMPROVE: Play nicer with Gravity Forms
✨ IMPROVE: Update noUiSlider version

= 0.8 =
📦 NEW: Add rendering of field in form editor

= 0.7 =
📦 NEW: Add class existence check for GF_Fields

= 0.6 =
📖 DOC: Add cautionary note in the readme.

= 0.5 =
🐛 FIX: Slider failure in certain instance

= 0.4 =
🚀 RELEASE: Initial
