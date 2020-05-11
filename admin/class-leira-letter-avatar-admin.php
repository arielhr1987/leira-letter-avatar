<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://leira.dev
 * @since      1.0.0
 *
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/admin
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Letter_Avatar_Admin{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leira_Letter_Avatar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Letter_Avatar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( is_admin() && get_option( 'avatar_default', 'mystery' ) === 'leira_letter_avatar' ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leira-letter-avatar-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Leira_Letter_Avatar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Leira_Letter_Avatar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leira-letter-avatar-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add new default avatar option to settings page.
	 * Settings > Discussion > Avatars > Default Avatar
	 *
	 * @param array $avatar_defaults Array of system avatar types
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function avatar_defaults( $avatar_defaults ) {
		$avatar_defaults['leira_letter_avatar'] = __( 'Letters (Generated)', 'leira_letter_avatar' );

		return $avatar_defaults;
	}

	protected function getContrastYIQ( $hexcolor ) {
		return ( hexdec( $hexcolor ) > 0xffffff / 2 ) ? '000' : 'fff';

		$r   = hexdec( substr( $hexcolor, 0, 2 ) );
		$g   = hexdec( substr( $hexcolor, 2, 2 ) );
		$b   = hexdec( substr( $hexcolor, 4, 2 ) );
		$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

		return ( $yiq >= 128 ) ? '000' : 'fff';
	}

}
