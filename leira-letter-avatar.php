<?php

/**
 * The plugin bootstrap file
 *
 * WordPress reads this file to generate the plugin information in the plugin
 * admin area.
 * This file also includes all the dependencies used by the plugin, registers the activation and deactivation
 * functions, and defines a function that starts the plugin.
 *
 * @link              https://leira.dev
 * @since             1.0.0
 * @package           Leira_Letter_Avatar
 *
 * @wordpress-plugin
 * Plugin Name:       Leira Letter Avatar
 * Plugin URI:        https://wordpress.org/plugins/leira-letter-avatar/
 * Description:       Enables custom avatars for users based on its initial letters.
 * Version:           1.3.9
 * Author:            Ariel
 * Author URI:        https://leira.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       leira-letter-avatar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LEIRA_LETTER_AVATAR_VERSION', '1.3.9' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-leira-letter-avatar-activator.php
 */
function activate_leira_letter_avatar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-leira-letter-avatar-activator.php';
	Leira_Letter_Avatar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-leira-letter-avatar-deactivator.php
 */
function deactivate_leira_letter_avatar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-leira-letter-avatar-deactivator.php';
	Leira_Letter_Avatar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_leira_letter_avatar' );
register_deactivation_hook( __FILE__, 'deactivate_leira_letter_avatar' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-leira-letter-avatar.php';

/**
 * Helper method to get the main instance of the plugin
 *
 * @return Leira_Letter_Avatar
 * @since    1.0.0
 * @access   global
 */
function leira_letter_avatar() {
	return Leira_Letter_Avatar::instance();
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
leira_letter_avatar()->run();
