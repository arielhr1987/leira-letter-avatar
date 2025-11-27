<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://leira.dev
 * @since      1.0.0
 *
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/includes
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Letter_Avatar_Deactivator{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		/**
		 * Restore default avatar.
		 * We don't need to do the same thing on uninstallation as you can delete an active plugin.
		 * You need to deactivate the plugin first to delete it.
		 *
		 * @since 1.3.1
		 */
		if ( get_option( 'avatar_default' ) == 'leira_letter_avatar' ) {
			update_option( 'avatar_default', 'mystery' );
		}
	}

}
