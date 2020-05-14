<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://leira.dev
 * @since      1.0.0
 *
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/public
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Letter_Avatar_Public{

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
	 * Helper class
	 *
	 * @var Leira_Letter_Avatar_Sanitizer
	 */
	protected $sanitize = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
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
	 * Register the stylesheets for the public-facing side of the site.
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

		if ( is_admin_bar_showing() && get_option( 'avatar_default', 'mystery' ) === 'leira_letter_avatar' ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leira-letter-avatar-public.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leira-letter-avatar-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Generate custom url for user avatar if "Letters" option is enable
	 *
	 * @param string $url         The URL of the avatar.
	 * @param mixed  $id_or_email The Gravatar to retrieve. Accepts a user ID, Gravatar MD5 hash,
	 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_avatar_url( $url, $id_or_email, $args ) {

		if ( is_admin() && function_exists( 'get_current_screen' ) && $screen = get_current_screen() ) {
			$screen = empty( $screen ) ? false : $screen->id;
			if ( in_array( $screen, array( 'dashboard', 'edit-comments' ) ) ) {
				$default = get_option( 'avatar_default', 'mystery' );
				if ( $default === 'leira_letter_avatar' ) {
					$args['default'] = 'leira_letter_avatar';
				}
			}
		}

		if ( ! isset( $args['default'] ) || $args['default'] !== 'leira_letter_avatar' ) {
			/**
			 * User didn't activate "Letters" options
			 */
			return $url;
		}

		$email_hash = '';
		$user       = false;
		$email      = false;

		if ( is_object( $id_or_email ) && isset( $id_or_email->comment_ID ) ) {
			$id_or_email = get_comment( $id_or_email );
		}

		// Process the user identifier.
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', absint( $id_or_email ) );
		} elseif ( is_string( $id_or_email ) ) {
			if ( strpos( $id_or_email, '@md5.gravatar.com' ) ) {
				// MD5 hash.
				list( $email_hash ) = explode( '@', $id_or_email );
			} else {
				// Email address.
				$email = $id_or_email;
			}
			$user = get_user_by_email( $id_or_email );
		} elseif ( $id_or_email instanceof WP_User ) {
			// User object.
			$user = $id_or_email;
		} elseif ( $id_or_email instanceof WP_Post ) {
			// Post object.
			$user = get_user_by( 'id', (int) $id_or_email->post_author );
		} elseif ( $id_or_email instanceof WP_Comment ) {
			if ( ! is_avatar_comment_type( get_comment_type( $id_or_email ) ) ) {
				$args['url'] = false;

				/** This filter is documented in wp-includes/link-template.php */
				return apply_filters( 'get_avatar_data', $args, $id_or_email );
			}

			if ( ! empty( $id_or_email->user_id ) ) {
				$user = get_user_by( 'id', (int) $id_or_email->user_id );
			}
			if ( ( ! $user || is_wp_error( $user ) ) && ! empty( $id_or_email->comment_author_email ) ) {
				$email = $id_or_email->comment_author_email;
			}
		}

		if ( ! $email_hash ) {
			if ( $user ) {
				$email = $user->user_email;
			}

			if ( $email ) {
				$email_hash = md5( strtolower( trim( $email ) ) );
			}
		}

		$args['found_avatar'] = true;

		/**
		 * Determine avatar url parameters base on user and email hash
		 */

		$current_option = get_option( 'leira_letter_avatar_method', 'auto' );
		$current_option = $this->sanitize->method( $current_option );

		/**
		 * Determine background
		 */
		switch ( $current_option ) {
			case  'fixed':
				$bg = get_option( 'leira_letter_avatar_bg' );
				$bg = $this->sanitize->background( $bg );
				break;
			case 'random':

				$bg = $user instanceof WP_User ? $user->get( '_leira_letter_avatar_bg' ) : get_comment_meta( $id_or_email->comment_ID, '_leira_letter_avatar_bg' );

				if ( empty( $bg ) || ! ctype_xdigit( $bg ) ) {
					//calculate and save
					$backgrounds = get_option( 'leira_letter_avatar_bgs', 'fc91ad' );
					$backgrounds = $this->sanitize->backgrounds( $backgrounds );
					$backgrounds = explode( ',', $backgrounds );
					if ( empty( $backgrounds ) ) {
						$backgrounds = array( 'fc91ad' ); // 'fc91ad', '37c5ab','fd9a00', '794fcf', '19C976'
						//$backgrounds[] = sprintf( '%06X', mt_rand( 0, 0xFFFFFF ) ); //random background
					}
					$bg = rand( 0, count( $backgrounds ) - 1 );
					$bg = $backgrounds[ $bg ]; //random background from array

					if ( $user instanceof WP_User ) {
						update_user_meta( $user->ID, '_leira_letter_avatar_bg', $bg );
					} else {
						update_comment_meta( $id_or_email->comment_ID, '_leira_letter_avatar_bg', $bg );
					}

				}
				break;
			case 'auto':
			default:
				$bg = substr( $email_hash, 0, 6 );
		}
		$bg = trim( trim( $bg ), '#' );
		$bg = $this->sanitize->background( $bg );

		/**
		 * Determine letters to show in the avatar
		 */
		$letters = '';
		if ( $user instanceof WP_User ) {
			$strings = array(
				$user->get( 'first_name' ) . ' ' . $user->get( 'last_name' ),
				$user->get( 'nickname' ),
				$user->get( 'display_name' ),
				$email
			);
			foreach ( $strings as $string ) {
				if ( ! empty( trim( $string ) ) ) {
					$letters = trim( $string );
					break;
				}
			}
		} else {
			$letters = ! empty( trim( $id_or_email->comment_author ) ) ? trim( $id_or_email->comment_author ) : trim( $id_or_email->comment_author_email );
		}

		$regex         = '/([^\pL]*(\pL)\pL*)/';// \pL => matches any kind of letter from any language
		$letters_count = get_option( 'leira_letter_avatar_letters', 2 );
		$letters_count = $this->sanitize->letters( $letters_count );
		$letters       = preg_replace( $regex, "$2", $letters );//get all initials in the string
		$letters       = substr( $letters, 0, $letters_count );//reduce to 2 or less initials

		if ( get_option( 'leira_letter_avatar_uppercase', true ) ) {
			$letters = strtoupper( $letters );//uppercase initials
		}

		/** Parameters:
		 *  size        => size [16 , 512] default 64
		 *  font-size   => font_size default
		 *  length      => characters length [1|2] default 2
		 *  name        => text to show
		 *  round       => make a circle or square icon
		 *  bold        => text bold
		 *  uppercase   => text uppercase
		 *  background  => background color
		 *  color       => text color
		 */
		$url_args = array(
			'size'       => $args['size'],
			'background' => $bg,
			'name'       => $letters,
			'round'      => get_option( 'leira_letter_avatar_rounded', true ) ? '1' : 'no',
			'color'      => $this->get_contrast_color( $bg ),
			'bold'       => get_option( 'leira_letter_avatar_bold', false ) ? '1' : 'no'
		);

		/**
		 * Base url for avatar
		 */
		//$url = plugin_dir_url( dirname( plugin_basename( __FILE__ ) ) ) . 'public/';
		$url = 'https://us-central1-leira-letter-avatar.cloudfunctions.net/generate';

		/**
		 * Add parameters to base url
		 */
		$url = add_query_arg(
			rawurlencode_deep( array_filter( $url_args ) ),
			set_url_scheme( $url, $args['scheme'] )
		);

		return $url;
	}

	/**
	 * Return best color given a background color
	 *
	 * @param string $hexcolor Hex color
	 *
	 * @return string Hex color
	 */
	public function get_contrast_color( $hexcolor ) {
		$r   = hexdec( substr( $hexcolor, 1, 2 ) );
		$g   = hexdec( substr( $hexcolor, 3, 2 ) );
		$b   = hexdec( substr( $hexcolor, 5, 2 ) );
		$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

		return ( $yiq >= ( 256 * 0.75 ) ) ? '000' : 'fff';
	}
}
