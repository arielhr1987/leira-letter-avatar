=== Leira Letter Avatar ===
Contributors: arielhr1987, jlcd0894, ivankuraev
Donate link: https://github.com/arielhr1987
Tags: user, avatar, image, letter, initial
Requires at least: 4.7
Tested up to: 6.6
Stable tag: 1.3.9
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically generate beautiful user avatars using their initial letters.

== Description ==

Letters Avatar allows you to automatically generate user avatar based on their initial letters.

Fully customizable from the admin area. You will be able to edit avatar settings like shape, background color, etc.

= Compatibility =

This plugin is compatible with any properly coded theme. However, some plugins may cause conflicts with this one. If you find an issue with your theme, please create a post in the community forum.
So far this plugin is compatible with:
* [BuddyPress](https://wordpress.org/plugins/buddypress/)
* [Ultimate Member](https://wordpress.org/plugins/ultimate-member/)
* [wpDiscuz](https://wordpress.org/plugins/wpdiscuz/)
* [BuddyPress Profile Completion](https://wordpress.org/plugins/buddypress-profile-completion/)
* [YITH WooCommerce Advanced Reviews](https://wordpress.org/plugins/yith-woocommerce-advanced-reviews/)
* [Flyzoo Chat](https://wordpress.org/plugins/flyzoo/)
* [BuddyBoss](https://buddyboss.com/)

= Contributors =

This plugins has been possible thanks to:
* [Jose Luis Chavez](https://profiles.wordpress.org/jlcd0894/) - Icons, banners and designs
* [ivankuraev](https://profiles.wordpress.org/ivankuraev/) - Support for russian characters

= Development =

You can contribute to this plugin development on [Github](https://github.com/arielhr1987/leira-letter-avatar)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/leira-letter-avatar` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Letter Avatar screen to configure the plugin
4. Happy coding :)

== Frequently Asked Questions ==

= What is the format of the generated avatars?  =

You can select from .svg (Recommended), .png and .jpg.
To use .png and .jpg you will need the GD image library installed to work.

= Which format should I use?  =

The recommended format(.svg) is the best option. This option generates the images with the best quality and the resource required to generate it is very low.
However, there are some cases where you will need to user .png or .jpg, for example, if you're sending avatar images in your emails.
There is a know issue that email clients like gmail use a proxy to show the images and at this point don't support .svg format.

= How are avatars generated?  =

The plugin generates the avatar automatically using defined config.
Avatars are generated only once, so your site performance won't get affected

= How are avatars stored?  =

Avatars are stored in your uploads folder.

= Will I be able to select my own letters? =

No. The plugin automatically generates the image with the initial letters of your First name and Last name.

= Can I use gravatar if the user has one? =

Yes. There is an option in plugin settings that allows you to show user gravatar if available instead of letter avatar.

= I see a black background in the avatars =

Probably you're using .jpg format to generate a round avatar. This format doesn't support image transparency.
Use .png format instead.

== Screenshots ==
1. You will find a new menu item "Letter Avatar" under "Settings" menu.
2. A new option "Letter" will be available for "Default Avatar."
3. Comments list with generated user letter avatar.
4. Users list with generated user letter avatar.
5. Comments in Dashboard with letter avatar.
6. Comments in frontend with letter avatar.

== Changelog ==

= 1.3.9 =
* Wordpress 6.6 compatibility check
* Improve text escape
* Use wp_rand over php built-in rand function
* Improve file handling using related wp functions

= 1.3.8 =
* Use gravatar https url

= 1.3.7 =
* Fix bug with php8 and $size variable (intval)

= 1.3.6 =
* Wordpress 5.7 compatibility check
* Fix BuddyPress deprecated filter bp_core_avatar_default
* Improve Gravatar image check method

= 1.3.5 =
* Fix wpDiscuz issue with social avatars

= 1.3.4 =
* Wordpress 5.7 compatibility check
* New filter leira_letter_avatar_image_content

= 1.3.3 =
* Fix variable typo
* Replace screenshot 4

= 1.3.2 =
* Support for WP 4.9

= 1.3.1 =
* Change deprecated method get_user_by_email in favor of get_user_by
* Bug fix. Upon deactivation set avatar_default option back to mystery

= 1.3.0 =
* Adding support for .png and .jpg image formats
* Fix https avatar url issue

= 1.2.6 =
* Wordpress 5.6 compatibility check
* Handle other plugins incorrect usage of um_user_avatar_url_filter filter

= 1.2.5 =
* Fix random background color generation issue

= 1.2.4 =
* Match plugin version with readme.txt version

= 1.2.3 =
* Adding Github Actions automatic deploy
* Adding Github Actions automatic update assets and readme file

= 1.2.2 =
* Adding support for letters color
* YITH WooCommerce Advanced Reviews compatibility

= 1.2.1 =
* Bug fix incorrect avatar size
* BuddyPress compatibility improved

= 1.2.0 =
* Ultimate Member compatibility
* wpDiscuz compatibility
* Support for russian characters
* New option to use gravatar if available
* Rate us link in admin area footer
* Some code refactored
* Bug fixes

= 1.1.0 =
* BuddyPress integration
* CSS fix in admin area
* Support link updated
* Source code refactored
* Screenshots description updated

= 1.0.0 =
* The first plugin release
