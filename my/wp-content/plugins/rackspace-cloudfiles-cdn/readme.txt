=== Rackspace CloudFiles CDN ===
Contributors: richardroyal
Donate link: http://labs.saidigital.co
Tags: cdn, rackspace
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin that moves attachments to Rackspace Cloudfiles CDN.

== Description ==

Moves files uploaded through Media Manager to Cloudfiles automatically, and rewrites URL in content. Optionally delete local files after CDN upload.

= Beta Version 0.0.1 =

Still working to get URL rewriting in all cases so you may not want to setup the delete local files cron job yet. This plugin should function as-is when creating a new client project and configuring the CDN prior to adding content.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory 2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Is this a caching plugin? =

No. This plugin is for putting WordPress Attachments and files in the WordPress uploads directory on a CDN where they belong because WordPress has no native feature for that. This is not a caching plugin and it is not intended to be.

= Does this plugin require other plugins to be installed to work correctly? =

No. This plugin simply pushes all uploaded files to a CDN network.

= Does this plugin remove the local copy of the file? =

You can optionally have the plugin remove the local copy of the file since it is not needed and taking up valuable server space. 

= Does this plugin handle theme or other plugin assets (Images, CSS, JavaScript)? =

No. Those things typically belong in revision control and you should have your deployment system take care of their linking.

== Screenshots ==

1. Beautifully simple management
2. Example output in content

== Changelog ==

= 0.0.1 =
* Originating Version.

== Upgrade Notice ==

= 0.0.1 =
* Originating Version.
