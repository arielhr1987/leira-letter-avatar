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
		 * defined in Leira_Letter_Avatar_Loader as all the hooks are defined
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
		 * defined in Leira_Letter_Avatar_Loader as all the hooks are defined
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
	 * @param mixed  $id_or_email The Gravatar to retrieve. Accepts a user ID, Gravatar MD5 hash, user email, WP_User
	 *                            object, WP_Post object, or WP_Comment object.
	 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_avatar_url( $url, $id_or_email, $args ) {

		/**
		 * Fix the problem with forced avatar types in dashboard and comments
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
			$current_action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : false;
			if ( $current_action == 'edit-comment' ) {
				$args['default'] = 'leira_letter_avatar';
			}
		}

		//$args = apply_filters( 'leira_letter_avatar_args', $args, $id_or_email );

		/**
		 * System forces to generate avatar with specific format.
		 * This fix discussion settings repeated images
		 */
		$force_default = isset( $args['force_default'] ) ? $args['force_default'] : false;
		if ( $force_default && isset( $args['default'] ) && $args['default'] !== 'leira_letter_avatar' ) {
			/**
			 * WP request a specific avatar type
			 * We're in Discussion setting page
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
			 * Check if this comment type allows avatars to be retrieved.
			 */
			$comment_type = get_comment_type( $id_or_email );
			if ( function_exists( 'is_avatar_comment_type' ) ) {
				$is_avatar_comment_type = is_avatar_comment_type( $comment_type );
			} else {
				/**
				 * Filters the list of allowed comment types for retrieving avatars.
				 *
				 * @param array $types An array of content types. Default only contains 'comment'.
				 *
				 * @since 3.0.0
				 *
				 */
				$allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );

				$is_avatar_comment_type = in_array( $comment_type, (array) $allowed_comment_types, true );
			}
			if ( ! $is_avatar_comment_type ) {
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
			 * If it was generated correctly, use this avatar url
			 */
			$url = $avatar_url;
		}

		return $url;
	}

	/**
	 * Return the best color given a background color
	 *
	 * @param string $hexcolor Hex color
	 *
	 * @return string Hex color
	 * @since 1.0.0
	 */
	public function get_contrast_color( $hexcolor ) {
		list( $r, $g, $b ) = $this->rgb_from_hex( $hexcolor );
		$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

		return ( $yiq >= ( 256 * 0.75 ) ) ? '000' : 'fff';
	}

	/**
	 * Generates image url for a User, Comment etc.
	 * This method will determine background color, letters, shape, etc. to build the image
	 *
	 * @param mixed $id_or_email The object to generate the avatar for
	 * @param array $args        Array of arguments to pass to method.
	 *                           For backward compatibility, ff args is numeric its considered the size
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
		$args = array_merge( array(
			'size'     => 300,
			'gravatar' => get_network_option( null, 'leira_letter_avatar_gravatar', false ),
			'rating'   => get_network_option( null, 'avatar_rating', 'G' )
		), $args );

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
			$user = get_user_by( 'email', $id_or_email );
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
				$gravatar = sprintf( 'http://%d.gravatar.com/avatar/', wp_rand( 0, 2 ) );
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
		$current_option = get_network_option( null, 'leira_letter_avatar_method', 'auto' );
		$current_option = $this->sanitize->method( $current_option );

		/**
		 * Determine background
		 */
		switch ( $current_option ) {
			case  'fixed':
				$bg = get_network_option( null, 'leira_letter_avatar_bg' );
				$bg = $this->sanitize->background( $bg );
				break;
			case 'random':

				$bg = '';
				if ( $user instanceof WP_User ) {
					$bg = $user->get( '_leira_letter_avatar_bg' );
				} else if ( $id_or_email instanceof WP_Comment ) {
					$bg = get_comment_meta( $id_or_email->comment_ID, '_leira_letter_avatar_bg' );
				}

				$backgrounds = get_network_option( null, 'leira_letter_avatar_bgs', 'fc91ad' );
				$backgrounds = $this->sanitize->backgrounds( $backgrounds );
				$backgrounds = explode( ',', $backgrounds );
				if ( empty( $backgrounds ) ) {
					$backgrounds = array( 'fc91ad' ); // 'fc91ad', '37c5ab','fd9a00', '794fcf', '19C976'
					//$backgrounds[] = sprintf( '%06X', mt_rand( 0, 0xFFFFFF ) ); //random background
				}

				if ( empty( $bg ) || ! in_array( $bg, $backgrounds ) || ! ctype_xdigit( $bg ) ) {
					//calculate and save
					$bg = wp_rand( 0, count( $backgrounds ) - 1 );
					$bg = $backgrounds[ $bg ]; //random background from an array

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
		 * Determine the letters color now that we have background color
		 */
		$color_method = get_network_option( null, 'leira_letter_avatar_color_method', 'auto' );
		$color_method = $this->sanitize->color_method( $color_method );
		//By default, find the best contrast color for the background
		$color = $this->get_contrast_color( $bg );
		if ( $color_method == 'fixed' ) {
			$color = get_network_option( null, 'leira_letter_avatar_color', 'ffffff' );
		}
		$color = trim( trim( $color ), '#' );
		$color = $this->sanitize->background( $color );

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
		} else if ( ! empty( $email ) ) {
			$letters = $email;
		}

		$regex         = '/([^\pL]*(\pL)\pL*)/u';// \pL => matches any kind of letter from any language
		$letters_count = get_network_option( null, 'leira_letter_avatar_letters', 2 );
		$letters_count = $this->sanitize->letters( $letters_count );
		$letters       = preg_replace( $regex, "$2", $letters );//get all initials in the string
		$letters       = mb_substr( $letters, 0, $letters_count, 'UTF-8' );//reduce to 2 or fewer initials

		if ( get_network_option( null, 'leira_letter_avatar_uppercase', true ) ) {
			/**
			 * Use mb_strtoupper in case initials contains letters with accents
			 */
			$letters = mb_strtoupper( $letters );//uppercase initials
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
			'rounded'    => get_network_option( null, 'leira_letter_avatar_rounded', true ),
			'background' => $bg,
			'bold'       => get_network_option( null, 'leira_letter_avatar_bold', false ),
			//'uppercase'  => '', //already set
			'color'      => $color,
			'format'     => get_network_option( null, 'leira_letter_avatar_format', 'svg' )
		);

		$url_args = apply_filters( 'leira_letter_avatar_url_args', $url_args, $id_or_email );

		/**
		 * Generate Avatar image
		 */
		$url = $this->generate_image( $url_args );

		return set_url_scheme( $url );
	}

	/**
	 * Generate, if it doesn't exist, an image file from parameters.
	 * If the system is unable to create the image, an image service is used instead
	 *
	 * @param array $data Array containing all parameters to generate image
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function generate_image( $data ) {

		$url = 'https://www.gravatar.com/avatar/?d=mp';

		$params            = $data;//clone array
		$params['rounded'] = ( isset( $params['rounded'] ) && $params['rounded'] ) ? '1' : 'no';
		$params['bold']    = ( isset( $params['bold'] ) && $params['bold'] ) ? '1' : 'no';

		$url = add_query_arg( rawurlencode_deep( array_filter( $params ) ), set_url_scheme( $url ) );

		$format = mb_strtolower( $data['format'] );
		$format = in_array( $format, leira_letter_avatar()->sanitizer->get_formats() ) ? $format : 'svg';
		if ( $format != 'svg' ) {
			//Check if the system is able to handle image
			if ( ! extension_loaded( 'gd' ) ) {
				//Fallback to svg format
				$format = 'svg';
			}
		}

		$save_to_uploads = true;
		if ( $save_to_uploads ) {

			//WP folders
			$wp_uploads_dir      = wp_upload_dir();
			$wp_uploads_dir_base = trailingslashit( $wp_uploads_dir['basedir'] ); //path to the uploads folder
			$wp_uploads_url_base = trailingslashit( $wp_uploads_dir['baseurl'] ); //url to the uploads folder

			//Avatar folder
			$avatar_folder = apply_filters( 'leira_letter_avatar_upload_folder', 'letter-avatar' );
			$avatar_folder = trailingslashit( $avatar_folder );

			//Avatar folder path
			$avatar_folder_path = $wp_uploads_dir_base . $avatar_folder;

			//Avatar filename
			$avatar_filename = md5( wp_json_encode( $data ) ) . '.' . $format;

			//Avatar path
			$avatar_path = $avatar_folder_path . $avatar_filename;

			$avatar_exist = file_exists( $avatar_path );
			if ( ! $avatar_exist ) {
				/**
				 * Let's generate the image and save it to the uploads folder
				 */
				$avatar = $this->image_content( $data );

				$wp_filesystem = $this->get_filesystem();

				/**
				 * Save image content to file
				 */
				if ( ! $wp_filesystem->exists( $avatar_folder_path ) ) {
					$wp_filesystem->mkdir( $avatar_folder_path, ( fileperms( ABSPATH ) & 0777 | 0755 ) );
				}

				$avatar_exist = $wp_filesystem->put_contents( $avatar_path, $avatar, ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );


//				if ( ! file_exists( $avatar_folder_path ) ) {
//					@mkdir( $avatar_folder_path, ( fileperms( ABSPATH ) & 0777 | 0755 ), true );
//				}

				//$avatar_exist = $wp_filesystem->put_contents( $avatar_path, $avatar, FS_CHMOD_FILE );
//				$fp = @fopen( $avatar_path, 'wb' );
//				if ( $fp ) {
//					mbstring_binary_safe_encoding();
//					$data_length   = strlen( $avatar );
//					$bytes_written = fwrite( $fp, $avatar );
//					reset_mbstring_encoding();
//					fclose( $fp );
//					if ( $data_length !== $bytes_written ) {
//						$avatar_exist = false;
//					} else {
//						$avatar_exist = true;
//					}
//					chmod( $avatar_path, ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
//				}
			}
			if ( $avatar_exist ) {
				$url = $wp_uploads_url_base . $avatar_folder . $avatar_filename;
			}
		}

		return set_url_scheme( $url );
	}

	/**
	 * Generate image content
	 *
	 * @param array $data Configuration array
	 *
	 * @return false|string
	 * @since 1.3.0
	 */
	protected function image_content( $data ) {
		/**
		 * Let's generate the image and save it to the uploads folder
		 */
		$format     = isset( $data['format'] ) ? mb_strtolower( $data['format'] ) : 'svg';
		$size       = isset( $data['size'] ) ? intval( $data['size'] ) : 2;
		$font_size  = isset( $data['font-size'] ) ? $data['font-size'] : abs( $size ) / 2;
		$text       = isset( $data['name'] ) ? $data['name'] : true;
		$background = isset( $data['background'] ) ? $data['background'] : true;
		$color      = isset( $data['color'] ) ? $data['color'] : true;
		$rounded    = isset( $data['rounded'] ) ? $data['rounded'] : true;
		$bold       = isset( $data['bold'] ) ? $data['bold'] : true;

		$avatar = false;

		if ( $format != 'svg' ) {
			/**
			 * We're going to create an image 5 times the size requested and at the end
			 * resize it to the fit the size.
			 * This to have an image with a bit better quality
			 */
			$big_size = $size * 5;
			$image    = @imagecreatetruecolor( $big_size, $big_size );
			imagealphablending( $image, true );
			imagesavealpha( $image, true );
			imagefill( $image, 0, 0, imagecolorallocatealpha( $image, 0, 0, 0, 127 ) );
			list( $r, $g, $b ) = $this->rgb_from_hex( $background );
			if ( $rounded ) {
				$circle_size  = $big_size;
				$circle_color = imagecolorallocate( $image, $r, $g, $b );
				imagefilledellipse( $image, $circle_size / 2, $circle_size / 2, $circle_size - 2, $circle_size - 2, $circle_color );
			} else {
				imagefill( $image, 0, 0, imagecolorallocate( $image, $r, $g, $b ) );
			}

			list( $r, $g, $b ) = $this->rgb_from_hex( $color );
			$text_color = imagecolorallocate( $image, $r, $g, $b );
			//Determine font to use base on the text
			$font      = __DIR__ . ( $bold ? '/fonts/NotoSans-Bold.ttf' : '/fonts/NotoSans-Regular.ttf' );
			$fonts_arr = array(
				'/\p{Arabic}/u'    => 'Arabic',
				'/\p{Armenian}/u'  => 'Armenian',
				'/\p{Bengali}/u'   => 'Bengali',
				'/\p{Georgian}/u'  => 'Georgian',
				'/\p{Hebrew}/u'    => 'Hebrew',
				'/\p{Mongolian}/u' => 'Mongolian',
				'/\p{Thai}/u'      => 'Thai',
				'/\p{Tibetan}/u'   => 'Tibetan',
				'/\p{Han}/u'       => 'CJKjp', //Chinese
				'/\p{Hiragana}/u'  => 'CJKjp', //Japanese
				'/\p{Katakana}/u'  => 'CJKjp', //Japanese
			);
			foreach ( $fonts_arr as $regex => $font_name ) {
				if ( preg_match( $regex, $text ) > 0 ) {
					$font = __DIR__ . '/fonts/NotoSans' . $font_name . '-';
					$font .= ( $bold || $font_name == 'Mongolian' ) ? 'Regular' : 'Bold';
					$font .= '.' . ( ( $font_name == 'CJKjp' ) ? 'otf' : 'ttf' );
					break;
				}
			}

			/**
			 * Filter font to use to write the text
			 *
			 * @since 1.3.4
			 */
			$font = apply_filters( 'leira_letter_avatar_image_font', $font, $data );

			$font_size  = abs( $big_size / 3 );
			$text_width = imagettfbbox( $font_size, 0, $font, $text );
			$text_width = isset( $text_width[2] ) ? $text_width[2] : 0;
			imagettftext( $image, $font_size, 0, (int)(( $big_size - $text_width ) / 2), (int)($font_size + ( ( $big_size - $font_size ) / 2 )), $text_color, $font, $text );

			//resize
			$final = @imagecreatetruecolor( $size, $size );
			imagealphablending( $final, true );
			imagesavealpha( $final, true );
			imagefill( $final, 0, 0, imagecolorallocatealpha( $image, 0, 0, 0, 127 ) );
			imagecopyresampled( $final, $image, 0, 0, 0, 0, $size, $size, $big_size, $big_size );
			$image = $final;

			//Capture generated image
			ob_start();
			$format == 'png' ? imagepng( $image, null, 0 ) : imagejpeg( $image, null, 100 );
			$avatar = ob_get_contents();
			imagedestroy( $image );
			ob_end_clean();
		}

		if ( empty( $avatar ) ) {
			$avatar = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="' . $size . 'px" height="' . $size . 'px" viewBox="0 0 ' . $size . ' ' . $size . '" style="user-select: none;" version="1.1"><' . ( $rounded ? 'circle' : 'rect' ) . ' fill="#' . trim( $background, '#' ) . '" cx="' . ( $size / 2 ) . '" width="' . $size . '" height="' . $size . '" cy="' . ( $size / 2 ) . '" r="' . ( $size / 2 ) . '"/><text x="50%" y="50%" style="color: #' . trim( $color, '#' ) . '; line-height: 1;font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\', \'Ubuntu\', \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;" alignment-baseline="middle" text-anchor="middle" font-size="' . $font_size . '" font-weight="' . ( $bold ? 600 : 400 ) . '" dy=".1em" dominant-baseline="middle" fill="#' . trim( $color, '#' ) . '">' . $text . '</text></svg>';
		}

		/**
		 * This filter was added to provide developers a way to handle custom image generation
		 *
		 * @since 1.3.3
		 */
		$avatar = apply_filters( 'leira_letter_avatar_image_content', $avatar, $data );

		return $avatar;
	}

	/**
	 * Check if a gravatar exists given an email hash value
	 * https://codex.wordpress.org/Using_Gravatars#Checking_for_the_Existence_of_a_Gravatar
	 * This method uses the default Wordpress cache system.
	 * Additional plugin is required for better performance.
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

		$uri     = sprintf( 'https://%d.gravatar.com/avatar/%s?d=404', wp_rand( 0, 2 ), $hash );
		$context = stream_context_create();
		stream_context_set_option( $context, 'http', 'timeout', 2 ); //timeout in seconds
		$headers          = @get_headers( $uri, false, $context );
		$has_valid_avatar = isset( $headers[0] ) && preg_match( "|200|", $headers[0] );

		$expire = 60 * 60 * 3;//3 hours
		wp_cache_set( $hash, $has_valid_avatar, 'leira_letter_avatar_gravatar', $expire );

		return $has_valid_avatar;
	}

	/**
	 * Return the RGB components from Hex color
	 *
	 * @param string $hex Color in hex format
	 *
	 * @return array|int[]
	 * @since 1.3.0
	 */
	protected function rgb_from_hex( $hex ) {
		return array_map( function( $c ) {
			return hexdec( str_pad( $c, 2, $c ) );
		}, str_split( ltrim( $hex, '#' ), strlen( $hex ) > 4 ? 2 : 1 ) );
	}

	/**
	 * Return the wp file system manager
	 *
	 * @return WP_Filesystem_Base
	 * @since 1.3.9
	 */
	protected function get_filesystem() {
		// Load the WordPress filesystem.
		global $wp_filesystem;

		if ( $wp_filesystem ) {
			return $wp_filesystem;
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
		}

		// Initialize the WordPress filesystem.
		if ( ! WP_Filesystem( request_filesystem_credentials( site_url() ) ) ) {
			wp_die( esc_html__( 'Filesystem cannot enabled', 'leira-letter-avatar' ) );
		}

		return $wp_filesystem;
	}
}
