=== Diskusijam.lv WordPress plugin ===
Contributors: akmentins
Tags: comments, diskusijam.lv, draugiem pase, comment platform
Requires at least: 2.9
Tested up to: 3.1.1
Stable tag: 1.3.2

Plugin for integrating diskusijam.lv comment system in Wordpress.

== Description ==

This plugin allows you to install diskusijam.lv comments in your WordPress page.
First of all you need to register on http://diskusijam.lv and create your page. After that go to "API" section to get your API keys.
Copy theese API keys in your WordPress admin page an you are ready to use your new comment system.

== Installation ==

1. Upload folder `/diskusijamlv-wordpress-plugin/` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Register a page on http://diskusijam.lv and get your API keys in "API" section.
4. Configure your API keys in 'Comments'->'Diskusijam.lv'->'Settings' menu in WordPress.
5. You are ready to use diskusijam.lv comment system.

== Frequently Asked Questions ==

= Can I switch back to my default WordPress comments if I change my mind? =

Yes, you can switch back simply by deactivating diskusijam.lv plugin.

= Will I loose all my diskusijam.lv comments if I switch back to default Wordpress comments? =

If you set up diskusijam.lv plugin no synchronize comments, all comments will be stored in your Wordpress database while diskusijam.lv plugin is active.

== Screenshots ==

1. Diskusijam.lv administration panel
2. Diskusijam.lv comments form and comments

== Changelog ==

= 1.3.2 =
* Disable comments if comment_status is 'closed'

= 1.3.1 =
* Fixed comment count loading on document ready state

= 1.3 =
* Fixed syncing problem from diskusijam.lv to WP

= 1.2 =
* Fixed comment parent_id's when syncing from diskusijam.lv to WP

= 1.1 =
* Some small fixes

= 1.0 =
* First release