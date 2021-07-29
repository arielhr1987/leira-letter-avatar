<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://leira.dev
 * @since      1.0.0
 *
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @property Leira_Letter_Avatar_Admin     admin
 * @property Leira_Letter_Avatar_Public    public
 * @property Leira_Letter_Avatar_Sanitizer sanitizer
 *
 * @since      1.0.0
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/includes
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Letter_Avatar{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Leira_Letter_Avatar_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Singleton instance
	 *
	 * @since    1.0.0
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * The Singleton method
	 *
	 * @return self
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function __construct() {
		if ( defined( 'LEIRA_LETTER_AVATAR_VERSION' ) ) {
			$this->version = LEIRA_LETTER_AVATAR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'leira-letter-avatar';

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Leira_Letter_Avatar_Loader. Orchestrates the hooks of the plugin.
	 * - Leira_Letter_Avatar_i18n. Defines internationalization functionality.
	 * - Leira_Letter_Avatar_Admin. Defines all hooks for the admin area.
	 * - Leira_Letter_Avatar_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leira-letter-avatar-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leira-letter-avatar-i18n.php';

		/**
		 * Helper class with sanitation methods.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-leira-letter-avatar-sanitizer.php';

		if ( is_admin() ) {
			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-leira-letter-avatar-admin.php';
		}

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-leira-letter-avatar-public.php';

		/**
		 * The class responsible for defining all actions to ensure compatibility with third party plugins.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-leira-letter-avatar-compatibility.php';

		$this->loader = new Leira_Letter_Avatar_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Leira_Letter_Avatar_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Leira_Letter_Avatar_i18n();
		$this->get_loader()->set( 'i18n', $plugin_i18n );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_sanitizer = new Leira_Letter_Avatar_Sanitizer();
		$this->loader->set( 'sanitizer', $plugin_sanitizer );

		if ( is_admin() ) {
			$plugin_admin = new Leira_Letter_Avatar_Admin( $this->get_plugin_name(), $this->get_version() );
			$this->loader->set( 'admin', $plugin_admin );

			$this->loader->add_filter( 'avatar_defaults', $plugin_admin, 'avatar_defaults' );

			$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_admin_menu' );

			$this->loader->add_action( 'admin_init', $plugin_admin, 'init_settings' );

			$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'plugin_action_links', 10, 2 );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

			$this->loader->add_filter( 'admin_body_class', $plugin_admin, 'admin_body_class' );

			$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'admin_footer_text', 1000 );

			$this->loader->add_action( 'wp_ajax_leira_letter_avatar_footer_rated', $plugin_admin, 'footer_rated' );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Leira_Letter_Avatar_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->set( 'public', $plugin_public );

		/**
		 * BuddyPress hooks get_avatar_url with priority 10, we need to hook earlier
		 */
		$this->loader->add_filter( 'get_avatar_url', $plugin_public, 'get_avatar_url', 5, 3 );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		/**
		 * Handle third party plugins compatibility
		 */
		$plugin_compatibility = new Leira_Letter_Avatar_Compatibility();
		$this->loader->set( 'compatibility', $plugin_compatibility );

		$this->loader->add_filter( 'bp_core_fetch_avatar_no_grav', $plugin_compatibility, 'bp_core_fetch_avatar_no_grav', 10, 2 );//BuddyPress integration

		//$this->loader->add_filter( 'bp_core_default_avatar_user', $plugin_compatibility, 'bp_core_default_avatar', 10, 2 );//BuddyPress integration

		if(function_exists('bp_get_version') && $bp_version = bp_get_version()){
			if(version_compare($bp_version, '8.0.0', '<')){
				$this->loader->add_filter( 'bp_core_avatar_default', $plugin_compatibility, 'bp_core_avatar_default', 10, 2 );//BuddyPress integration
			}else{
				$this->loader->add_filter( 'bp_core_default_avatar', $plugin_compatibility, 'bp_core_avatar_default', 10, 2 );//BuddyPress integration
			}
		}

		$this->loader->add_filter( 'um_user_avatar_url_filter', $plugin_compatibility, 'um_user_avatar_url_filter', 10, 3 );//Ultimate Membership integration

		$this->loader->add_filter( 'get_avatar_url', $plugin_compatibility, 'wpdiscuz_get_avatar_url', 10, 3 );//wpdiscuz integration

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function run() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 * @access    public
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Leira_Letter_Avatar_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 * @access    public
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 * @access    public
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Gets an instance from the loader
	 *
	 * @param string $key
	 *
	 * @return mixed|null The instance
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 */
	public function __get( $key ) {
		return $this->get_loader()->get( $key );
	}

	/**
	 * Sets an instance in the loader
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 */
	public function __set( $key, $value ) {
		$this->get_loader()->set( $key, $value );
	}

	/**
	 * Determine if "Letter" is enabled as Avatar
	 *
	 * @return bool
	 */
	public function is_active() {
		$option = get_network_option( null, 'avatar_default', 'mystery' );

		return $option === 'leira_letter_avatar';
	}

}
