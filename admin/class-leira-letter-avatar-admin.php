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
	 * Helper class
	 *
	 * @var Leira_Letter_Avatar_Sanitizer
	 */
	protected $sanitize = null;

	/**
	 * Action to generate nonce
	 *
	 * @var string
	 * @since 1.2.0
	 */
	protected $nonce_action = 'leira-letter-avatar';

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
		$this->sanitize    = leira_letter_avatar()->sanitizer;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles( $page ) {

		/**
		 * Hide admin bar avatar icon border if letter avatar is rounded.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leira-letter-avatar-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $page The name of the page being loaded
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts( $page ) {

		/**
		 * Add js files if we're in our settings page
		 */
		if ( $page === 'settings_page_leira_letter_avatar' ) {

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leira-letter-avatar-admin.js', array(
				'jquery',
				'wp-util',
				'wp-color-picker'
			), $this->version, false );
		}

	}

	/**
	 * Add letter avatar classes to admin body if is enabled
	 * New classes will help to fix css errors.
	 *
	 * @param string $classes
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function admin_body_class( $classes ) {
		if ( leira_letter_avatar()->is_active() ) {
			$classes .= ' leira_letter_avatar';
			if ( get_network_option( null, 'leira_letter_avatar_rounded', true ) ) {
				$classes .= ' leira_letter_avatar_rounded';
			}
		}

		return $classes;
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
	public function plugin_action_links( $plugin_actions, $plugin_file ) {

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
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'leira-letter-avatar' ) );
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
			'<p>' . __( '<a href="https://wordpress.org/support/plugin/leira-letter-avatar/">Support</a>', 'leira-letter-avatar' ) . '</p>' .
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
		register_setting( 'leira_letter_avatar_settings', 'avatar_default', array(
			'type'              => 'string',
			'sanitize_callback' => array( $this->sanitize, 'avatar_default' ),
			'default'           => 'mystery'
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_gravatar', array(
			'type'              => 'boolean',
			'sanitize_callback' => array( $this->sanitize, 'boolean' ),
			'default'           => false
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_format', array(
			'type'              => 'string',
			'sanitize_callback' => array( $this->sanitize, 'format' ),
			'default'           => 'svg'
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_rounded', array(
			//Valid values: 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
			'type'              => 'boolean',
			//A description of the data attached to this setting.
			'description'       => '',
			//A callback function that sanitizes the option's value.
			'sanitize_callback' => array( $this->sanitize, 'boolean' ),
			'default'           => true
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_letters', array(
			'type'              => 'integer',
			'description'       => '',
			'sanitize_callback' => array( $this->sanitize, 'letters' ),
			'default'           => 2
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_bold', array(
			'type'              => 'boolean',
			'description'       => '',
			'sanitize_callback' => array( $this->sanitize, 'boolean' ),
			'default'           => false
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_uppercase', array(
			'type'              => 'boolean',
			'description'       => '',
			'sanitize_callback' => array( $this->sanitize, 'boolean' ),
			'default'           => true
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_color_method', array(
			'type'              => 'string',
			'description'       => '',
			'sanitize_callback' => array( $this->sanitize, 'color_method' ),
			'default'           => 'auto'
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_color', array(
			'type'              => 'string',
			'description'       => '',
			'sanitize_callback' => array( $this->sanitize, 'background' ),
			'default'           => 'ffffff'
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_method', array(
			'type'              => 'string',
			'description'       => '',
			'sanitize_callback' => array( $this->sanitize, 'method' ),
			'default'           => 'auto'
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_bg', array(
			'type'              => 'string',
			'description'       => '',
			'sanitize_callback' => array( $this->sanitize, 'background' ),
			'default'           => 'fc91ad'
		) );
		register_setting( 'leira_letter_avatar_settings', 'leira_letter_avatar_bgs', array(
			'type'              => 'string',
			'description'       => '',
			'sanitize_callback' => array( $this->sanitize, 'backgrounds' ),
			'default'           => ''
		) );
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
			'leira_letter_avatar_checkbox_field_4',
			__( 'Gravatar', 'leira-letter-avatar' ),
			array( $this, 'render_gravatar_settings' ),
			'leira_letter_avatar_settings',
			'general'
		);

		add_settings_field(
			'leira_letter_avatar_checkbox_field_6',
			__( 'Format', 'leira-letter-avatar' ),
			array( $this, 'render_format_settings' ),
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
			'leira_letter_avatar_checkbox_field_5',
			__( 'Color', 'leira-letter-avatar' ),
			array( $this, 'render_color_settings' ),
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
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'leira-letter-avatar' ) );
		}
		?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Letter Avatar Settings', 'leira-letter-avatar' ) ?></h1>
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
	public function render_active_settings() {

		$option = get_network_option( null, 'avatar_default', 'mystery' );
		$option = $this->sanitize->avatar_default( $option );
		?>
        <label for="settings_avatar_default">
            <input type='checkbox' name='avatar_default' id="settings_avatar_default"
                   value='leira_letter_avatar' <?php checked( $option, 'leira_letter_avatar' ); ?>>
			<?php esc_html_e( 'Enable use of letter avatar', 'leira-letter-avatar' ); ?>
        </label>

		<?php
	}

	/**
	 * Render Gravatar settings field
	 *
	 * @since 1.2.0
	 */
	public function render_gravatar_settings() {

		$gravatar = get_network_option( null, 'leira_letter_avatar_gravatar', false );
		$gravatar = $this->sanitize->boolean( $gravatar );
		?>
        <label for="settings_gravatar">
            <input type='checkbox' name='leira_letter_avatar_gravatar' id="settings_gravatar"
                   value='1' <?php checked( true, $gravatar ); ?>>
			<?php esc_html_e( 'Use Gravatar profile picture if available', 'leira-letter-avatar' ); ?>
        </label>

		<?php
	}

	/**
	 * Render Format settings field
	 *
	 * @since 1.2.0
	 */
	public function render_format_settings() {

		$format = get_network_option( null, 'leira_letter_avatar_format', 'svg' );
		$format = $this->sanitize->format( $format );
		?>
        <label for="settings_format">
            <select name="leira_letter_avatar_format" id="settings_format">
                <option value="svg" <?php selected( 'svg', $format ) ?>><?php esc_html_e( '.svg', 'leira-letter-avatar' ) ?></option>
                <option value="png" <?php selected( 'png', $format ) ?>><?php esc_html_e( '.png', 'leira-letter-avatar' ) ?></option>
                <option value="jpg" <?php selected( 'jpg', $format ) ?>><?php esc_html_e( '.jpg', 'leira-letter-avatar' ) ?></option>
            </select>
        </label>

		<?php
	}

	/**
	 * Render shape settings input
	 *
	 * @since 1.0.0
	 */
	public function render_shape_settings() {
		$rounded = get_network_option( null, 'leira_letter_avatar_rounded', true );
		$rounded = $this->sanitize->boolean( $rounded );
		?>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php esc_html_e( 'Shape settings', 'leira-letter-avatar' ) ?></span>
            </legend>
            <label for="settings_shape_circle">
                <input type="radio" name="leira_letter_avatar_rounded" value="1"
                       id="settings_shape_circle" <?php checked( true, $rounded ) ?>>
				<?php esc_html_e( 'Circle', 'leira-letter-avatar' ) ?>
            </label>
            <br>
            <label for="settings_shape_rectangle">
                <input type="radio" name="leira_letter_avatar_rounded" value="0"
                       id="settings_shape_rectangle" <?php checked( false, $rounded ) ?>>
				<?php esc_html_e( 'Square', 'leira-letter-avatar' ) ?>
            </label>
        </fieldset>
		<?php
	}

	/**
	 * Render letters settings input
	 *
	 * @since 1.0.0
	 */
	public function render_letters_settings() {
		$letters = get_network_option( null, 'leira_letter_avatar_letters', 2 );
		$letters = $this->sanitize->letters( $letters );

		$bold = get_network_option( null, 'leira_letter_avatar_bold', false );
		$bold = $this->sanitize->boolean( $bold );

		$uppercase = get_network_option( null, 'leira_letter_avatar_uppercase', true );
		$uppercase = $this->sanitize->boolean( $uppercase );
		?>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php esc_html_e( 'Letters settings', 'leira-letter-avatar' ) ?></span>
            </legend>
            <label for="settings_letter">
				<?php esc_html_e( 'Fill avatar image with at most', 'leira-letter-avatar' ) ?>
                <select name="leira_letter_avatar_letters" id="settings_letter">
                    <option value="1" <?php selected( 1, $letters ) ?>><?php esc_html_e( '1 letter', 'leira-letter-avatar' ) ?></option>
                    <option value="2" <?php selected( 2, $letters ) ?>><?php esc_html_e( '2 letters', 'leira-letter-avatar' ) ?></option>
                </select>
            </label>
            <p class="description">
				<?php esc_html_e( 'The letters are the initials of the user taken from first name and last name. If those fields are not set, the plugin will try to determine letters base on Nickname, Display Name, username or email, in that order.', 'leira-letter-avatar' ) ?>
            </p>
            <br>
            <label for="settings_bold">
                <input type='checkbox'
                       id="settings_bold"
                       name='leira_letter_avatar_bold'
					<?php checked( true, $bold ); ?>
                       value='1'>
				<?php echo wp_kses_post( __( 'Make letters <b>bold</b>', 'leira-letter-avatar' ) ) ?>
            </label>
            <br>
            <label for="settings_uppercase">
                <input type='checkbox'
                       id="settings_uppercase"
                       name='leira_letter_avatar_uppercase'
					<?php checked( true, $uppercase ); ?>
                       value='1'>
				<?php esc_html_e( 'Make letters uppercase', 'leira-letter-avatar' ) ?>
            </label>
        </fieldset>
		<?php
	}

	/**
	 * Render color settings
	 *
	 * @since 1.2.2
	 */
	public function render_color_settings() {
		$color_method = get_network_option( null, 'leira_letter_avatar_color_method', 'auto' );
		$color_method = $this->sanitize->color_method( $color_method );

		$color = get_network_option( null, 'leira_letter_avatar_color', 'ffffff' );
		$color = $this->sanitize->background( $color );
		?>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php esc_html_e( 'Color settings', 'leira-letter-avatar' ) ?></span>
            </legend>
            <div>
                <div>
                    <label for="leira_letter_avatar_color_method_auto">
                        <input type="radio"
                               id="leira_letter_avatar_color_method_auto"
                               name="leira_letter_avatar_color_method"
                               value="auto"
							<?php checked( 'auto', $color_method ); ?>>
						<?php esc_html_e( 'Automatically determine letters color based on background color (Recommended)', 'leira-letter-avatar' ) ?>
                    </label>
                </div>
                <div>
                    <label for="leira_letter_avatar_color_method_fixed">
                        <input type="radio"
                               id="leira_letter_avatar_color_method_fixed"
                               name="leira_letter_avatar_color_method"
                               value="fixed"
							<?php checked( 'fixed', $color_method ); ?>>
						<?php esc_html_e( 'Use this color for the letters', 'leira-letter-avatar' ) ?>
                    </label>
                    <input type="text"
                           name="leira_letter_avatar_color"
                           data-picker_default="#ffffff"
                           value="#<?php echo esc_attr( $color ); ?>"
                           class="leira-letter-avatar-color-field">
                </div>
            </div>
        </fieldset>
		<?php
	}

	/**
	 * Render letters settings input
	 *
	 * @since 1.0.0
	 */
	public function render_background_settings() {
		$method = get_network_option( null, 'leira_letter_avatar_method' );
		$method = $this->sanitize->method( $method );

		$bg = get_network_option( null, 'leira_letter_avatar_bg', 'fc91ad' );
		$bg = $this->sanitize->background( $bg );

		$bgs = get_network_option( null, 'leira_letter_avatar_bgs', '' );
		$bgs = $this->sanitize->backgrounds( $bgs );
		?>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php esc_html_e( 'Background settings', 'leira-letter-avatar' ) ?></span>
            </legend>
            <div>
                <div>
                    <label for="leira_letter_avatar_method_auto">
                        <input type="radio" name="leira_letter_avatar_method" value="auto"
                               id="leira_letter_avatar_method_auto" <?php checked( 'auto', $method ); ?>>
						<?php esc_html_e( 'Automatically determine background color for each user (Recommended)', 'leira-letter-avatar' ) ?>
                    </label>
                </div>
                <div>
                    <label for="leira_letter_avatar_method_fixed">
                        <input type="radio" name="leira_letter_avatar_method" value="fixed"
                               id="leira_letter_avatar_method_fixed" <?php checked( 'fixed', $method ); ?>>
						<?php esc_html_e( 'Use this background color for all users', 'leira-letter-avatar' ) ?>
                    </label>
                    <input type="text"
                           name="leira_letter_avatar_bg"
                           data-picker_default="#fc91ad"
                           data-picker_palettes="#fc91ad,#37c5ab,#fd9a00,#794fcf,#19C976"
                           value="#<?php echo esc_attr( $bg ); ?>"
                           class="leira-letter-avatar-color-field">
                </div>
                <div>
                    <label for="leira_letter_avatar_method_random">
                        <input type="radio" name="leira_letter_avatar_method"
                               id="leira_letter_avatar_method_random"
                               value="random" <?php checked( 'random', $method ); ?>>
						<?php esc_html_e( 'Use a random background color from the list below:', 'leira-letter-avatar' ) ?>
                    </label>
                    <p>
                        <textarea name="leira_letter_avatar_bgs" rows="3" cols="50" id=""
                                  class="large-text code"><?php echo esc_textarea( $bgs ) ?></textarea>
                    </p>
                    <p class="description">
						<?php esc_html_e( 'Use comma to separate each color. Colors should be in hex format (i.e. fc91ad).', 'leira-letter-avatar' ) ?>
                    </p>
                </div>
            </div>
        </fieldset>
		<?php
	}


	/**
	 * Change the admin footer text on Settings page
	 * Give us a rate
	 *
	 * @param $footer_text
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();

		// Add the dashboard pages
		$pages[] = 'settings_page_leira_letter_avatar';

		if ( isset( $current_screen->id ) && in_array( $current_screen->id, $pages ) ) {
			// Change the footer text
			if ( ! get_network_option( null, 'leira_letter_avatar_footer_rated' ) ) {

				ob_start(); ?>
                <a href="https://wordpress.org/support/plugin/leira-letter-avatar/reviews/?filter=5" target="_blank"
                   class="leira-letter-avatar-admin-rating-link"
                   data-rated="<?php esc_attr_e( 'Thanks :)', 'leira-letter-avatar' ) ?>"
                   data-nonce="<?php echo esc_html( wp_create_nonce( $this->nonce_action ) ) ?>">
                    &#9733;&#9733;&#9733;&#9733;&#9733;
                </a>
				<?php $link = ob_get_clean();

				ob_start();
				/* translators: link to rate the plugin */
				printf( esc_html__( 'If you like Letter Avatar please consider leaving a %s review. It will help us to grow the plugin and make it more popular. Thank you.', 'leira-letter-avatar' ), wp_kses_post( $link ) ) ?>

				<?php $footer_text = ob_get_clean();
			}
		}

		return $footer_text;
	}

	/**
	 * When user clicks the review link in backend
	 *
	 * @since 1.2.0
	 */
	public function footer_rated() {
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, $this->nonce_action ) ) {
			wp_send_json_error( esc_js( __( 'Wrong Nonce', 'leira-letter-avatar' ) ) );
		}

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Please login as administrator', 'leira-letter-avatar' ) );
		}

		update_network_option( null, 'leira_letter_avatar_footer_rated', 1 );
		wp_send_json_success();
	}

}
