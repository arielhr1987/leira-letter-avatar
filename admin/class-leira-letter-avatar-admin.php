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
	 * User capability to access
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var string
	 */
	protected $capability = 'manage_options';

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

		if ( get_option( 'avatar_default', 'mystery' ) === 'leira_letter_avatar' ) {
			/**
			 * Style to fix admin bar icon border
			 */
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leira-letter-avatar-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $page The name of the page being loaded
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $page ) {

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
		if ( get_option( 'avatar_default', 'mystery' ) === 'leira_letter_avatar' && $page === 'settings_page_leira_letter_avatar' ) {

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leira-letter-avatar-admin.js', array( 'wp-color-picker' ), $this->version, false );
		}

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
		$url = esc_url( add_query_arg(
			'page',
			'leira_letter_avatar',
			get_admin_url() . 'admin.php'
		) );

		$settings = sprintf( '<a href="%s" class="">%s</a>', $url, __( 'Settings', 'leira-letter-avatar' ) );
		$text     = __( 'Letters (Generated)', 'leira-letter-avatar' );

		$avatar_defaults['leira_letter_avatar'] = $text . ' ' . $settings;

		return $avatar_defaults;
	}

	/**
	 * Add Settings link to plugin list item
	 *
	 * @param array  $plugin_actions Array of links
	 * @param string $plugin_file    Plugin file path relative to plugins directory
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function plugin_action_links( $plugin_actions, $plugin_file ) {

		if ( 'leira-letter-avatar/leira-letter-avatar.php' === $plugin_file ) {
			$url = esc_url( add_query_arg(
				'page',
				'leira_letter_avatar',
				get_admin_url() . 'admin.php'
			) );

			$settings = sprintf( '<a href="%s" class="">%s</a>', $url, __( 'Settings', 'leira-letter-avatar' ) );

			$plugin_actions['settings'] = $settings;
		}

		return $plugin_actions;
	}

	/**
	 * Add plugin settings menu item
	 *
	 * @since 1.0.0
	 */
	public function add_settings_admin_menu() {
		$hook = add_options_page(
			__( 'Letter Avatar', 'leira-letter-avatar' ), //Page Title
			__( 'Letter Avatar', 'leira-letter-avatar' ), //Menu title
			$this->capability, //Capability
			'leira_letter_avatar',//menu slug
			array( $this, 'render_settings_page' ),//render page
			null //position
		);

		if ( ! empty( $hook ) ) {
			add_action( "load-$hook", array( $this, 'settings_page_load' ) );
		}
	}

	/**
	 * Add screen help tab
	 */
	public function settings_page_load() {
		if ( ! current_user_can( $this->capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'leira-letter-avatar' ) );
		}
		/**
		 * Add screen help
		 */
		get_current_screen()->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => __( 'Overview', 'leira-letter-avatar' ),
				'content' =>
					'<p>' . __( 'Letter Avatar is a lightweight plugin that helps you to add simple good looking user avatars', 'leira-letter-avatar' ) . '</p>' .
					'<p>' . __( 'The plugin is highly customizable by using settings page and hooks.', 'leira-letter-avatar' ) . '</p>' .
					''
			)
		);

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'leira-letter-avatar' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>', 'leira-letter-avatar' ) . '</p>' . //TODO: Change to github plugin page
			'<p>' . __( '<a href="https://github.com/arielhr1987/leira-letter-avatar/issues">Report an issue</a>', 'leira-letter-avatar' ) . '</p>'
		);
	}

	/**
	 * Register all plugin settings, sections and fields
	 *
	 * @since 1.0.0
	 */
	public function init_settings() {
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}
		/**
		 * Register settings
		 */
		register_setting( 'leira_letter_avatar_settings', 'avatar_default', array( 'default' => 'mystery' ) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_rounded', array( 'default' => true ) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_letters', array( 'default' => 2 ) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_bold', array( 'default' => false ) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_uppercase', array( 'default' => true ) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_method', array( 'default' => 'auto' ) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_bg', array( 'default' => '' ) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_bgs', array( 'default' => '' ) );

		/**
		 * Register sections
		 */
		add_settings_section(
			'general',
			'',//__( 'Your section description', 'leira-letter-avatar' ),
			array( $this, 'render_settings_section' ),
			'leira_letter_avatar_settings'
		);

		/**
		 * Register fields
		 */
		add_settings_field(
			'leira_letter_avatar_checkbox_field_0',
			__( 'Active', 'leira-letter-avatar' ),
			array( $this, 'render_active_settings' ),
			'leira_letter_avatar_settings',
			'general'
		);

		add_settings_field(
			'leira_letter_avatar_select_field_3',
			__( 'Shape', 'leira-letter-avatar' ),
			array( $this, 'render_shape_settings' ),
			'leira_letter_avatar_settings',
			'general'
		);

		add_settings_field(
			'leira_letter_avatar_checkbox_field_1',
			__( 'Letters', 'leira-letter-avatar' ),
			array( $this, 'render_letters_settings' ),
			'leira_letter_avatar_settings',
			'general'
		);

		add_settings_field(
			'leira_letter_avatar_select_field_2',
			__( 'Background', 'leira-letter-avatar' ),
			array( $this, 'render_background_settings' ),
			'leira_letter_avatar_settings',
			'general'
		);
	}

	/**
	 * Render settings page
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( $this->capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'leira-letter-avatar' ) );
		}
		?>
        <div class="wrap">
            <h1><?php _e( 'Letter Avatar Settings', 'leira-letter-avatar' ) ?></h1>
            <form action='options.php' method='post'>
				<?php
				settings_fields( 'leira_letter_avatar_settings' );
				do_settings_sections( 'leira_letter_avatar_settings' );
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	/**
	 * Render settings section description
	 *
	 * @since 1.0.0
	 */
	public function render_settings_section() {
		//echo '<p>' . __( 'This section description', 'leira-letter-avatar' ) . '</p>';
	}

	/**
	 * Render active settings field
	 *
	 * @since 1.0.0
	 */
	function render_active_settings() {

		$option = get_option( 'avatar_default', 'mystery' );
		?>
        <label for="settings_avatar_default">
            <input type='checkbox' name='avatar_default' id="settings_avatar_default"
                   value='leira_letter_avatar' <?php checked( $option, 'leira_letter_avatar' ); ?>>
			<?php _e( 'Enable use of letter avatar', 'leira-letter-avatar' ); ?>
        </label>

		<?php
	}

	/**
	 * Render shape settings input
	 *
	 * @since 1.0.0
	 */
	function render_shape_settings() {
		$rounded = get_option( 'leira_letter_avatar_rounded', true );
		$rounded = filter_var( $rounded, FILTER_VALIDATE_BOOLEAN );
		?>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php _e( 'Shape settings', 'leira-letter-avatar' ) ?></span>
            </legend>
            <label for="settings_shape_circle">
                <input type="radio" name="leira_letter_avatar_rounded" value="1"
                       id="settings_shape_circle" <?php checked( true, $rounded ) ?>>
				<?php _e( 'Circle', 'leira-letter-avatar' ) ?>
            </label>
            <br>
            <label for="settings_shape_rectangle">
                <input type="radio" name="leira_letter_avatar_rounded" value="0"
                       id="settings_shape_rectangle" <?php checked( false, $rounded ) ?>>
				<?php _e( 'Square', 'leira-letter-avatar' ) ?>
            </label>
        </fieldset>
		<?php
	}

	/**
	 * Render letters settings input
	 *
	 * @since 1.0.0
	 */
	function render_letters_settings() {
		$letters = get_option( 'leira_letter_avatar_letters', 2 );
		$letters = filter_var( $letters, FILTER_VALIDATE_INT );
		$letters = $letters < 1 ? 1 : $letters;
		$letters = $letters > 2 ? 2 : $letters;

		$bold = get_option( 'leira_letter_avatar_bold', false );
		$bold = filter_var( $bold, FILTER_VALIDATE_BOOLEAN );

		$uppercase = get_option( 'leira_letter_avatar_uppercase', true );
		$uppercase = filter_var( $uppercase, FILTER_VALIDATE_BOOLEAN );
		?>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php _e( 'Letters settings', 'leira-letter-avatar' ) ?></span>
            </legend>
            <label for="settings_letter">
				<?php _e( 'Fill avatar image with at most', 'leira-letter-avatar' ) ?>
                <select name="leira_letter_avatar_letters" id="settings_letter">
                    <option value="1" <?php selected( 1, $letters ) ?>><?php _e( '1 letter', 'leira-letter-avatar' ) ?></option>
                    <option value="2" <?php selected( 2, $letters ) ?>><?php _e( '2 letters', 'leira-letter-avatar' ) ?></option>
                </select>
            </label>
            <p class="description">
				<?php _e( 'The letters are the initials of the user taken from first name and last name. If those fields are not set, the plugin will try to determine letters base on Nickname, Display Name, username or email, in that order.', 'leira-letter-avatar' ) ?>
            </p>
            <br>
            <label for="settings_bold">
                <input type='checkbox'
                       id="settings_bold"
                       name='leira_letter_avatar_bold'
					<?php checked( true, $bold ); ?>
                       value='1'>
				<?php _e( 'Make letters <b>bold</b>', 'leira-letter-avatar' ) ?>
            </label>
            <br>
            <label for="settings_uppercase">
                <input type='checkbox'
                       id="settings_uppercase"
                       name='leira_letter_avatar_uppercase'
					<?php checked( true, $uppercase ); ?>
                       value='1'>
				<?php _e( 'Make letters uppercase', 'leira-letter-avatar' ) ?>
            </label>
            <p class="description">
				<?php _e( 'The color of the letters is determined automatically to ensure best contrast.', 'leira-letter-avatar' ) ?>
            </p>
        </fieldset>
		<?php
	}

	/**
	 * Render letters settings input
	 *
	 * @since 1.0.0
	 */
	function render_background_settings() {
		$method  = get_option( 'leira_letter_avatar_method' );
		$methods = array( 'auto', 'fixed', 'random' );
		$method  = in_array( $method, $methods ) ? $method : 'auto';

		$bg  = get_option( 'leira_letter_avatar_bg', 'fc91ad' );
		$bgs = get_option( 'leira_letter_avatar_bgs' );
		?>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php _e( 'Background settings', 'leira-letter-avatar' ) ?></span>
            </legend>
            <div>
                <div>
                    <label for="leira_letter_avatar_method_auto">
                        <input type="radio" name="leira_letter_avatar_method" value="auto"
                               id="leira_letter_avatar_method_auto" <?php checked( 'auto', $method ); ?>>
						<?php _e( 'Automatically determine background color for each user (Recommended)', 'leira-letter-avatar' ) ?>
                    </label>
                </div>
                <div>
                    <label for="leira_letter_avatar_method_fixed">
                        <input type="radio" name="leira_letter_avatar_method" value="fixed"
                               id="leira_letter_avatar_method_fixed" <?php checked( 'fixed', $method ); ?>>
						<?php _e( 'Use this background color for all users', 'leira-letter-avatar' ) ?>
                    </label>
                    <input type="text"
                           name="leira_letter_avatar_bg"
                           data-default="#<?php echo esc_attr( $bg ); ?>"
                           value="#<?php echo esc_attr( $bg ); ?>"
                           class="leira-letter-avatar-color-field">
                </div>
                <div>
                    <label for="leira_letter_avatar_method_random">
                        <input type="radio" name="leira_letter_avatar_method"
                               id="leira_letter_avatar_method_random"
                               value="random" <?php checked( 'random', $method ); ?>>
						<?php _e( 'Use a random background color from the list below:', 'leira-letter-avatar' ) ?>
                    </label>
                    <p>
                        <textarea name="leira_letter_avatar_bgs" rows="3" cols="50" id=""
                                  class="large-text code"><?php echo esc_textarea( $bgs ) ?></textarea>
                    </p>
                    <p class="description">
						<?php _e( 'Use comma to separate each color. Colors should be in hex format (i.e. fc91ad).', 'leira-letter-avatar' ) ?>
                    </p>
                </div>
            </div>
        </fieldset>
		<?php
	}

	/**
	 * Sanitize settings before save
	 *
	 * @param mixed  $value     The new, unserialized option value.
	 * @param string $option    Name of the option.
	 * @param mixed  $old_value The old option value.
	 *
	 * @return mixed Sanitized value
	 * @since 1.0.0
	 */
	public function pre_update_option( $value, $option, $old_value ) {

		switch ( $option ) {
			case 'avatar_default':
				$avatar_defaults = array(
					'mystery',
					'blank',
					'gravatar_default',
					'identicon',
					'wavatar',
					'monsterid',
					'retro',
				);
				$avatar_defaults = array_fill_keys( $avatar_defaults, '' );
				$avatar_defaults = apply_filters( 'avatar_defaults', $avatar_defaults );
				$value           = isset( $avatar_defaults[ $value ] ) ? $value : 'mystery';
				break;
			case 'leira_letter_avatar_rounded':
			case 'leira_letter_avatar_bold':
			case 'leira_letter_avatar_uppercase':
				$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				break;
			case 'leira_letter_avatar_letters':
				$value = filter_var( $value, FILTER_VALIDATE_INT );
				$value = $value > 2 ? 2 : $value;
				$value = $value < 1 ? 1 : $value;
				break;
			case 'leira_letter_avatar_method':
				$value   = sanitize_text_field( $value );
				$methods = array( 'auto', 'fixed', 'random' );
				$value   = in_array( $value, $methods ) ? $value : 'auto';
				break;
			case 'leira_letter_avatar_bg':
				$value = trim( $value, '#' );
				$value = ctype_xdigit( $value ) ? $value : $old_value;
				break;
			case 'leira_letter_avatar_bgs':
				$value  = sanitize_text_field( $value );
				$values = explode( ',', $value );
				$values = array_map( 'trim', $values );
				$values = array_filter( $values, 'ctype_xdigit' );
				$value  = implode( $values, ',' );
				break;
		}

		return $value;
	}

}
