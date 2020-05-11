<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://leira.dev
 * @since      1.0.0
 *
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/includes
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Letter_Avatar_i18n{


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'leira-letter-avatar',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}


}
