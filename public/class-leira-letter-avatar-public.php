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

		if ( is_admin_bar_showing() && leira_letter_avatar()->is_active() ) {
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

		/**
		 * Fix problem with forced avatar types in dashboard and comments
		 */
		if ( is_admin() && function_exists( 'get_current_screen' ) && $screen = get_current_screen() ) {
			$screen = empty( $screen ) ? false : $screen->id;
			if ( in_array( $screen, array( 'dashboard', 'edit-comments' ) ) ) {
				if ( leira_letter_avatar()->is_active() ) {
					$args['default'] = 'leira_letter_avatar';
				}
			}
		}

		/**
		 * Fix to avoid loose avatar while "Quick Edit" comment
		 */
		if ( is_admin() && wp_doing_ajax() ) {
			// do something
			$current_action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : false;
			if ( $current_action == 'edit-comment' ) {
				$args['default'] = 'leira_letter_avatar';
			}
		}

		//$args = apply_filters( 'leira_letter_avatar_args', $args, $id_or_email );

		/**
		 * System forces to generate avatar with specific format.
		 * This fix discussion settings repeated images
		 */
		$force_default = isset( $params['force_default'] ) ? $params['force_default'] : false;
		if ( $force_default && isset( $args['default'] ) && $args['default'] !== 'leira_letter_avatar' ) {
			/**
			 * WP request an specific avatar type
			 * We are in Discussion setting page
			 */
			return $url;
		}

		if ( ! isset( $args['default'] ) || $args['default'] !== 'leira_letter_avatar' ) {
			/**
			 * User didn't activate "Letters" options
			 */
			return $url;
		}

		if ( $id_or_email instanceof WP_Comment ) {
			/**
			 * Check if this comment type allow avatars to be retrieved.
			 */
			if ( ! is_avatar_comment_type( get_comment_type( $id_or_email ) ) ) {
				$args['url'] = false;

				/**
				 * This filter is documented in wp-includes/link-template.php
				 */
				return apply_filters( 'get_avatar_data', $args, $id_or_email );
			}
		}

		/**
		 * $args should contain size
		 */
		$size       = isset( $args['size'] ) ? $args['size'] : 64;
		$avatar_url = $this->generate_letter_avatar_url( $id_or_email, array( 'size' => $size ) );
		if ( $avatar_url ) {
			/**
			 * If it was generated correctly use this avatar url
			 */
			$url = $avatar_url;
		}

		return $url;
	}

	/**
	 * Return best color given a background color
	 *
	 * @param string $hexcolor Hex color
	 *
	 * @return string Hex color
	 * @since 1.0.0
	 */
	public function get_contrast_color( $hexcolor ) {
		$r   = hexdec( substr( $hexcolor, 1, 2 ) );
		$g   = hexdec( substr( $hexcolor, 3, 2 ) );
		$b   = hexdec( substr( $hexcolor, 5, 2 ) );
		$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

		return ( $yiq >= ( 256 * 0.75 ) ) ? '000' : 'fff';
	}

	/**
	 * Generates image url for a User, Comment etc.
	 * This method will determine background color, letters, shape etc to build the image
	 *
	 * @param mixed $id_or_email The object to generate the avatar for
	 * @param array $args        Array of arguments to pass to method.
	 *                           For backward compatibility ff args is numeric its considered the size
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function generate_letter_avatar_url( $id_or_email, $args = array() ) {

		/**
		 * backward compatibility
		 *
		 * @since 1.2.0
		 */
		$args = is_numeric( $args ) ? array( 'size' => $args ) : $args;//
		if ( ! is_array( $args ) ) {
			return false;//Just in case
		}
		$args = array_merge( $args, array(
			'size'     => 300,
			'gravatar' => get_option( 'leira_letter_avatar_gravatar', false ),
			'rating'   => get_option( 'avatar_rating', 'G' )
		) );

		$size       = $args['size'];
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

		/**
		 * Use Gravatar if exist
		 */
		if ( $args['gravatar'] && $this->gravatar_exist( $email_hash ) ) {
			/**
			 * Generate gravatar url adn return
			 */
			if ( is_ssl() ) {
				$gravatar = 'https://secure.gravatar.com/avatar/';
			} else {
				$gravatar = sprintf( 'http://%d.gravatar.com/avatar/', rand( 0, 2 ) );
			}

			// Append email hash to Gravatar.
			$gravatar .= $email_hash;

			// Main Gravatar URL args.
			$url_args = array(
				's' => $size
			);

			if ( is_string( $args['rating'] ) && ! empty( $args['rating'] ) ) {
				$url_args['r'] = strtolower( $args['rating'] );
			}

			// Set up the Gravatar URL.
			$gravatar = esc_url( add_query_arg(
				rawurlencode_deep( array_filter( $url_args ) ),
				$gravatar
			) );

			return $gravatar;
		}

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

				$bg = '';
				if ( $user instanceof WP_User ) {
					$bg = $user->get( '_leira_letter_avatar_bg' );
				} else if ( $id_or_email instanceof WP_Comment ) {
					$bg = get_comment_meta( $id_or_email->comment_ID, '_leira_letter_avatar_bg' );
				}
				//$bg = $user instanceof WP_User ? $user->get( '_leira_letter_avatar_bg' ) : get_comment_meta( $id_or_email->comment_ID, '_leira_letter_avatar_bg' );

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
					} else if ( $id_or_email instanceof WP_Comment ) {
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
		} else if ( $id_or_email instanceof WP_Comment ) {
			$letters = ! empty( trim( $id_or_email->comment_author ) ) ? trim( $id_or_email->comment_author ) : trim( $id_or_email->comment_author_email );
		}

		$regex         = '/([^\pL]*(\pL)\pL*)/u';// \pL => matches any kind of letter from any language
		$letters_count = get_option( 'leira_letter_avatar_letters', 2 );
		$letters_count = $this->sanitize->letters( $letters_count );
		$letters       = preg_replace( $regex, "$2", $letters );//get all initials in the string
		$letters       = mb_substr( $letters, 0, $letters_count, 'UTF-8' );//reduce to 2 or less initials

		if ( get_option( 'leira_letter_avatar_uppercase', true ) ) {
			$letters = strtoupper( $letters );//uppercase initials
		}

		/**
		 *  Parameters:
		 *  size        => size [16 , 512] default 64
		 *  font-size   => font_size default
		 *  length      => characters length [1|2] default 2
		 *  name        => text to show
		 *  rounded     => make a circle or square icon
		 *  bold        => text bold
		 *  uppercase   => text uppercase
		 *  background  => background color
		 *  color       => text color
		 */
		$url_args = array(
			'cache'      => 1, //change this number to force regenerate images
			'size'       => $size,
			'font-size'  => $size / 2,
			//'length'     => '', //image text length already set
			'name'       => $letters,
			'rounded'    => get_option( 'leira_letter_avatar_rounded', true ),
			'background' => $bg,
			'bold'       => get_option( 'leira_letter_avatar_bold', false ),
			//'uppercase'  => '', //already set
			'color'      => $this->get_contrast_color( $bg ),
			'format'     => 'svg'
		);

		$url_args = apply_filters( 'leira_letter_avatar_url_args', $url_args, $id_or_email );

		/**
		 * Generate Avatar image
		 */
		$url = $this->generate_image( $url_args );

		return $url;
	}

	/**
	 * Generate if it doesn't exist an image file from parameters.
	 * If system is unable to create the image, an image service is used instead
	 *
	 * @param array $data Array containing all parameters to generate image
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function generate_image( array $data ) {

		$url = 'https://us-central1-leira-letter-avatar.cloudfunctions.net/generate';

		$params            = $data;//clone array
		$params['rounded'] = ( isset( $params['rounded'] ) && $params['rounded'] ) ? '1' : 'no';
		$params['bold']    = ( isset( $params['bold'] ) && $params['bold'] ) ? '1' : 'no';

		$url = add_query_arg( rawurlencode_deep( array_filter( $params ) ), set_url_scheme( $url ) );

		$save_to_uploads = true;
		if ( $save_to_uploads ) {

			//WP folders
			$wp_uploads_dir      = wp_upload_dir();
			$wp_uploads_dir_base = trailingslashit( $wp_uploads_dir['basedir'] ); //path to uploads
			$wp_uploads_url_base = trailingslashit( $wp_uploads_dir['baseurl'] ); //url to uploads

			//Avatar folder
			$avatar_folder = apply_filters( 'leira_letter_avatar_upload_folder', 'letter-avatar' );
			$avatar_folder = trailingslashit( $avatar_folder );

			//Avatar folder path
			$avatar_folder_path = $wp_uploads_dir_base . $avatar_folder;

			//Avatar filename
			$avatar_filename = md5( json_encode( $data ) ) . '.' . $data['format'];

			//Avatar path
			$avatar_path = $avatar_folder_path . $avatar_filename;

			$avatar_exist = file_exists( $avatar_path );
			if ( ! $avatar_exist ) {
				/**
				 * Lets generate the image and save it to uploads folder
				 */
				$size       = isset( $data['size'] ) ? $data['size'] : 2;
				$font_size  = isset( $data['font-size'] ) ? $data['font-size'] : abs( $size ) / 2;
				$text       = isset( $data['name'] ) ? $data['name'] : true;
				$background = isset( $data['background'] ) ? $data['background'] : true;
				$color      = isset( $data['color'] ) ? $data['color'] : true;
				$rounded    = isset( $data['rounded'] ) ? $data['rounded'] : true;
				$bold       = isset( $data['bold'] ) ? $data['bold'] : true;

				$avatar = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="' . $size . 'px" height="' . $size . 'px" viewBox="0 0 ' . $size . ' ' . $size . '" style="user-select: none;" version="1.1"><' . ( $rounded ? 'circle' : 'rect' ) . ' fill="#' . trim( $background, '#' ) . '" cx="' . ( $size / 2 ) . '" width="' . $size . '" height="' . $size . '" cy="' . ( $size / 2 ) . '" r="' . ( $size / 2 ) . '"/><text x="50%" y="50%" style="color: #' . trim( $color, '#' ) . '; line-height: 1;font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\', \'Ubuntu\', \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;" alignment-baseline="middle" text-anchor="middle" font-size="' . $font_size . '" font-weight="' . ( $bold ? 600 : 400 ) . '" dy=".1em" dominant-baseline="middle" fill="#' . trim( $color, '#' ) . '">' . $text . '</text></svg>';

				if ( ! file_exists( $avatar_folder_path ) ) {
					@mkdir( $avatar_folder_path, ( fileperms( ABSPATH ) & 0777 | 0755 ), true );
				}
				//$avatar_exist = $wp_filesystem->put_contents( $avatar_path, $avatar, FS_CHMOD_FILE );
				$fp = @fopen( $avatar_path, 'wb' );
				if ( $fp ) {
					mbstring_binary_safe_encoding();
					$data_length   = strlen( $avatar );
					$bytes_written = fwrite( $fp, $avatar );
					reset_mbstring_encoding();
					fclose( $fp );
					if ( $data_length !== $bytes_written ) {
						$avatar_exist = false;
					} else {
						$avatar_exist = true;
					}
					chmod( $avatar_path, ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
				}
			}
			if ( $avatar_exist ) {
				$url = $wp_uploads_url_base . $avatar_folder . $avatar_filename;
			}
		}

		return $url;
	}

	/**
	 * Check if a gravatar exists given an email hash value
	 * https://codex.wordpress.org/Using_Gravatars#Checking_for_the_Existence_of_a_Gravatar
	 * This method uses default wordpress cache system. Additional plugin is required for better performance.
	 * https://codex.wordpress.org/Class_Reference/WP_Object_Cache#Persistent_Caching
	 * https://codex.wordpress.org/Class_Reference/WP_Object_Cache
	 *
	 * @param string $email Email or Hash value to check against gravatar
	 *
	 * @return bool
	 * @since 1.2.0
	 */
	public function gravatar_exist( $email ) {

		// Craft a potential url and test its headers
		$hash = mb_strpos( $email, '@' ) !== false ? md5( strtolower( trim( $email ) ) ) : trim( $email );
		if ( empty( $hash ) ) {
			return false;
		}

		$cache = wp_cache_get( $hash, 'leira_letter_avatar_gravatar', false, $found );
		if ( $found ) {
			return $cache;
		}

		$uri     = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
		$headers = @get_headers( $uri );
		if ( ! preg_match( "|200|", $headers[0] ) ) {
			$has_valid_avatar = false;
		} else {
			$has_valid_avatar = true;
		}

		$expire = 60 * 60 * 3;//3 hours
		wp_cache_set( $hash, $has_valid_avatar, 'leira_letter_avatar_gravatar', $expire );

		return $has_valid_avatar;
	}
}
