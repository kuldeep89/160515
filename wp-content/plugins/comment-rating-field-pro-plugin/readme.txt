=== Comment Rating Field Pro Plugin ===
Contributors: n7studios,wpcube
Donate link: http://www.wpcube.co.uk/plugins/comment-rating-field-pro-plugin
Tags: comment,field,rating,star,gd,comments,review
Requires at least: 3.6
Tested up to: 3.9.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a 5 star rating field to the end of a comment form in WordPress.

== Description ==

Adds a 5 star rating field to the end of a comment form in WordPress, allowing the site visitor to optionally submit a rating along with their comment. Ratings are displayed as stars below the comment text.

= Support =

Please email support@wpcube.co.uk, with your license key.

= WP Cube =
We produce free and premium WordPress Plugins that supercharge your site, by increasing user engagement, boost site visitor numbers
and keep your WordPress web sites secure.

Find out more about us:

* <a href="http://www.wpcube.co.uk">Our Plugins</a>
* <a href="http://www.facebook.com/wpcube">Facebook</a>
* <a href="http://twitter.com/wp_cube">Twitter</a>
* <a href="https://plus.google.com/b/110192203343779769233/110192203343779769233/posts?rel=author">Google+</a>

== Installation ==

1. Upload the `comment-rating-field-pro-plugin` folder to the `/wp-content/plugins/` directory
2. Active the Comment Rating Field Pro through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the `Comment Rating Field Pro` menu that appears in your admin menu

== Frequently Asked Questions ==



== Screenshots ==

1. Comment Rating Field Plugin on Comment Form
2. Star rating displayed below comment text 

== Changelog ==

= 2.4.5 =
* Fix: Use $wpdb->prepare() instead of mysql_real_escape_string for better SQL injection compatibility protection.
* Fix: When a rating field is required and replies are disabled, prevent validation checks for ratings on replies.
* Added: Anchor link option from Average Rating to Comments section on Excerpt
* Added: Anchor link option from Average Rating to Comments section on Content
* Added: Anchor link option from Average Rating to Comments section on Shortcode

= 2.4.4 =
* Added: Import + Export Settings, allowing users to copy settings to other plugin installations
* Added: Average Rating + Total Rating keys are set on all Pages, Posts and Custom Post Types, to ensure all Posts are included when using orderby=meta_value_num on WP_Query

= 2.4.3 =
* Fix: License server endpoint

= 2.4.2 =
* Fix: CRFP Top Rating Widget showing too many stars when a half average rating defined
* Fix: CSS styling on rating inputs
* Fix: Ratings not displaying on some comments

= 2.4.1 =
* Fix: Undefined properties on CRFP Top Rating Widget
* Fix: CRFP Top Rating Widget - use siderbar defined heading tag for title
* Fix: Force license key check method to beat aggressive server caching 

= 2.4 =
* Fix: Undefined property: CRFPTopRatedPosts::$plugin
* Fix: Rating fields incorrectly displaying when "Disable on Replies" = Yes and Javascript on WordPress Comments are enabled
* Fix: Ratings in WordPress Admin > Comments not displaying correctly in WordPress 3.9+
* Fix: Ratings in WordPress Admin > Comments > Edit not displaying correctly in WordPress 3.9+
* Fix: Average rating recalculation on comment published -> trash
* Fix: Average rating recalculation on comment trash -> delete
* Fix: Average rating recalculation on comment trash -> restore
* Added: Filter: crfp_display_rating_field
* Added: Filter: crfp_display_comment_rating 
* Added: Filter: crfp_display_post_rating
* Added: Filter: crfp_display_post_rating_excerpt
* Added: Filter: crfp_display_post_rating_content
* Added: Filter: crfp_display_post_rating_shortcode
* Added: Support menu with debug information
* Added: Half ratings option (.5)

= 2.3 =
* Added text-domain to all strings

= 2.2.9 =
* Fix for character encoding issues for Greek + Portugese when using PHP < 5.4

= 2.2.8 =
* Added translation support and .pot file

= 2.2.7 =
* Shortcode supports new `id` parameter to optionally specify which Post ID to get average rating from

= 2.2.6 =
* Changed filter for outputting ratings in comments to prevent duplicate display

= 2.2.5 =
* Better license key transient check / refresh to prevent frontend functionality from not working

= 2.2.4 =
* Fix: Better JS targeting to prevent conflicts with PremiumPress Themes

= 2.2.3 =
* Pro Version: Added display_average_rating() function to manually output average rating.
* Fix: Internationalization support added.

= 2.2.2 =
* Fix: Better jQuery rating integration
* Pro Version: Edit ratings in Administration > Comments

= 2.2.1 =
* Dashboard CSS + JS enhancements

= 2.2 =
* Added: display_rating_field() function to manually output rating fields on custom comment forms, where comment_form() is not used in the theme
* Notice: Output an error message if comment_form() and display_rating_field() are not included in the active theme's comments.php file
* Notice: Output an error message if no Rating Fields have been defined
* Setting: disable rating fields on comment reply forms
* Fix: Zero ratings are ignored in average / rating calculations

= 2.1.1 =
* Fix: Activation routine for DB table creation

= 2.1 =
* Fix: PHP notice messages
* Fix: Better rating field positioning options and code
* Fix: Licensing and update mechanism enhancements

= 2.0 =
* Pro Version: Support for multiple rating fields
* Pro Version: Additional display settings for ratings on excerpts, content + comments
* Fix: admin_enqueue_scripts used to fix in WordPress 3.6+

= 1.5.1 =
* Pro Version: Improvements to Rich Snippets markup for better chance of display in Google Search Results
* Fix: Removed console.log() messages in JS
* Fix: frontend.js enqueues in footer

= 1.5 =
* Pro Version: Overhauled plugin structure to follow best practices
* Pro Version: Comments form rating field added via PHP instead of JS for better compatibility
* Pro Version: Admin UI improvements
* Pro Version: Average Rating on Excerpts
* Pro Version: Rating Field position above or below comments form

= 1.47 =
* Fix: License key save routine

= 1.46 =
* Pro Version: Version 2 of License Update Manager; uses JSON; removes requirements for cURL and SimpleXML

= 1.45 =
* Pro Version: Change of licensing system and branding to WP Cube
* Pro Version: Tidied up the Settings Panel by grouping settings together by their functionality (Post Types, Rating Input, Average Rating)
* Pro Version: Removed cancel button where rating field is required

= 1.44 =
* Fix: WordPress 3.4 compatibility
* Fix: jQuery Rating Javascript updated to 3.14

= 1.43 =
* Pro Version: Setting for defining Cancel Rating hover title
* Fix: Change conditional tag is_single() to is_singular() for better compatibility on some themes
* Fix: in_array datatype error on some Pages
* Fix: Top Rated Widget for correct post rating order

= 1.42 =
* Fix: HTML fix on Settings field to ensure settings save correctly

= 1.41 =
* Pro Version: Display Average Rating: Options changed to Never, When Rating(s) Exist (default), Always (grey stars if no rating / not a 5 star rating)
* Pro Version: Display Style: Yellow Stars only (default), Yellow Stars with Grey Stars (grey stars if no rating / not a 5 star rating)
* Pro Version: Google Rich Snippets microformats markup added to average rating for display within Google search results, per http://support.google.com/webmasters/bin/answer.py?hl=en&answer=146645#Aggregate_reviews
* Pro Version: Minor HTML and CSS changes to support above

= 1.4 =
* Removal of Donate Button
* On Activation, plugin no longer enables ratings on Pages and Posts by default
* Change: Average Rating displayed below content for better formatting and output on themes
* Fix: Language / localisation support
* Fix: Rating only shows on selected categories where specified in the plugin
* Fix: Recalculation of rating when comment removed
* Fix: Multisite Compatibility
* Fix: W3 Total Cache compatibility
* Pro Version: Support: Access to support ticket system and knowledgebase
* Pro Version: Custom Post Types: Support for rating display and functionality on ANY Custom Post Types and their Taxonomies
* Pro Version: Widgets: List the Top Rated Posts within your sidebars
* Pro Version: Shortcodes: Use a shortcode to display the Average Rating anywhere within your content
* Pro Version: Rating Field: Make rating field a required field
* Pro Version: Display Average Rating: Choose to display average rating above content, below content or above the comments form
* Pro Version: Seamless Upgrade: Retain all current settings and ratings when upgrading to Pro

= 1.3 =
* Javascript changes to fix comment rating field not appearing below comment field on some themes.

= 1.2 =
* Enable on Pages Option Added
* Enable on Post Categories Option Added
* Display Average Option Added - will display the average of all ratings at the top of the comments list.
* Donate Button Added to Settings Panel
* Change to readme.txt file for required ID on comment form.

= 1.01 =
* Fixed paths for CSS and Javascript.

= 1.0 =
* First release.

== Upgrade Notice ==
