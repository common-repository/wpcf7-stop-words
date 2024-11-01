=== WPCF7 Stop words ===
Contributors: Social Media Ltd
Tags: antispam, contact form 7, form validation
Requires at least: 4.1
Tested up to: 4.6.1
Stable tag: 1.1.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

A plugin developed by [Social Media Ltd](https://social-media.co.uk), to prevent form submission when the message contains custom predefined words.
Use as many stop words as you wish, and manage those easily through the settings page in the admin panel. When any WPCF7 form is submitted, textareas' content is checked against those words, and if a hit is found, an error comes up, and the form submission is cancelled.

== Installation ==

This section describes how to install the plugin and get it working.

1. install the plugin through the WordPress plugins screen directly, or upload the plugin files to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Setup the plugin through the Settings > WPCF7 Stop Words screen
1. Enter each word or phrase you wish on a separate line
1. Hit the Save Changes button, and you are good to go

== Frequently Asked Questions ==

= How does this work =

If someone tries to submit a form containing any of the words or phrases you have specified, the submission will fail with an error message.

= Do I need any other plugin to make this work =

Since WPCF7 Stop Words plugin works for the Contact Form 7 plugin, you must first install and activate [Contact Form 7](https://wordpress.org/plugins/contact-form-7/)