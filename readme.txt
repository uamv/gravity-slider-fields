=== Gravity Slider Fields ===

Contributors: UaMV
Donate link: http://vandercar.net/wp
Tags: gravity, forms, slider, field, number
Requires at least: 3.1
Tested up to: 4.0
Stable tag: 0.8
License: GPLv2 or later

Adds slider fields to Gravity Forms

== Description ==

> **PLEASE NOTE & BEWARE:** With only 13 downloads, I have already received one report of this plugin taking a site down upon installation. I am investigating and hope to diagnose the conflict. If you can assist my sleuthing in any way, post to the support thread or ping me @UaMV on Twitter. Proceed with caution and courage. Thank you! [5 Nov 2014]

> **UPDATE:** Thanks to [@sccr410](https://profiles.wordpress.org/sccr410 "@sccr410") who identified my ineptitude in developing against a project in beta. Gravity Slider Fields v0.4, v0.5, & v0.6 are unstable will likely take down your site. Version 0.7 requires Gravity Forms v1.9 which is currently in beta. [6 Nov 2014]

> This plugin is an add-on for the [Gravity Forms](http://gravityforms.com "Gravity Forms") plugin.
> Gravity Forms v1.9 or greater is required.

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

= Please Note =
Currently, slider fields are not supported for ajax enabled forms.

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

= 0.8 =
* Field rendering on form editor.

= 0.7 =
* First stable version!

= 0.4 =
* Initial Release