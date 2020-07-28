<?php

/**
 * This class handle all compatibility with third party plugins
 *
 * @link       https://leira.dev
 * @since      1.0.0
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/public
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Letter_Avatar_Compatibility{

	/**
	 * BuddyPress compatibility
	 * Determine if buddy press should use gravatar as default image or letter avatar.
	 * If user didnt upload profile picture this method is executed
	 *
	 * @param bool  $no_gravatar Do not use Gravatar
	 * @param array $params      Parameters
	 *
	 * @return bool
	 * @since 1.2.0
	 */
	public function bp_core_fetch_avatar_no_grav( $no_gravatar, $params ) {
		/**
		 * System forces to generate avatar with specific format.
		 * This fix discussion settings repeated images
		 */
		$default       = isset( $params['default'] ) ? $params['default'] : false;
		$force_default = isset( $params['force_default'] ) ? $params['force_default'] : false;
		if ( $force_default && $default !== 'leira_letter_avatar' ) {
			return $no_gravatar;
		}

		/**
		 * Do not use gravatar in favor of letter avatar
		 */
		if ( $default == 'leira_letter_avatar' || leira_letter_avatar()->is_active() ) {
			$object = $params['object'];
			if ( in_array( $object, array( 'user' ) ) ) { //, 'group' , 'site'
				$no_gravatar = true;
			}
		}

		return $no_gravatar;
	}

	/**
	 * BuddyPress compatibility
	 * Generate image avatar for buddy press.
	 *
	 * @param string $avatar_default
	 * @param array  $params
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public function bp_core_default_avatar( $avatar_default, $params ) {

		$default = isset( $params['default'] ) ? $params['default'] : false;
		/**
		 * Generate letter avatar for specific user, group or site
		 */
		if ( $default == 'leira_letter_avatar' || leira_letter_avatar()->is_active() ) {
			/**
			 * Our avatar method is enable
			 */
			$object = $params['object'];
			if ( $object == 'user' ) {
				if ( isset( $params['item_id'] ) ) {
					$user = get_user_by( 'id', $params['item_id'] );

					$args = array(
						'size' => $params['width']
					);
					$url  = leira_letter_avatar()->public->generate_letter_avatar_url( $user, $args );
					if ( $url ) {
						/**
						 * If it was generated correctly use this avatar url
						 */
						$avatar_default = $url;
					}
				}
			} elseif ( $object == 'group' ) {
				if ( isset( $params['item_id'] ) ) {
					/**
					 * do nothing. we do not generate avatars for groups
					 */
				}
			}
		}

		return $avatar_default;
	}

	/**
	 * Ultimate Member compatibility
	 * Determine default avatar tu use if user did not uploaded his own avatar
	 *
	 * @param string $url     Current avatar url
	 * @param int    $user_id User id of the associated avatar
	 * @param array  $data    Array of setting to build the avatar
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public function um_user_avatar_url_filter( $url, $user_id, $data ) {
		if ( empty( $user_id ) ) {
			$user_id = um_user( 'ID' );
		} else {
			um_fetch_user( $user_id );
		}

		if ( ! um_profile( 'profile_photo' ) && ! um_user( 'synced_profile_photo' ) && ! UM()->options()->get( 'use_gravatars' ) ) {

			$args   = array(
				'size' => $data['size']
			);
			$avatar = leira_letter_avatar()->public->generate_letter_avatar_url( $user_id, $args );
			if ( $avatar ) {
				$url = $avatar;
			}
		}

		return $url;
	}

	/**
	 * wpdiscuz compatibility
	 * Fix problem with comments
	 *
	 * @param string $url         The URL of the avatar.
	 * @param mixed  $id_or_email The Gravatar to retrieve. Accepts a user ID, Gravatar MD5 hash,
	 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
	 *
	 * @return string
	 * @since 1.2.0
	 */
	public function wpdiscuz_get_avatar_url( $url, $id_or_email, $args ) {

		if ( isset( $args['wpdiscuz_current_user'] ) ) {
			$current_user = $args['wpdiscuz_current_user'];
			$email        = isset( $args['wpdiscuz_gravatar_user_email'] ) ? $args['wpdiscuz_gravatar_user_email'] : 'unknown@example.com';
			/**
			 * Check if we have a user
			 */
			if ( ! $current_user instanceof WP_User ) {
				$current_user = new WP_User();
			}

			/**
			 * This is a valid user, no need to continue
			 */
			if ( $current_user->ID > 0 ) {
				return $url;
			}

			/**
			 * Populate user info
			 */
			$current_user->user_email = $email;
			$current_user->nickname   = isset( $args['alt'] ) ? $args['alt'] : '';

			$avatar = leira_letter_avatar()->public->get_avatar_url( $url, $current_user, $args );
			if ( $avatar ) {
				$url = $avatar;
			}
		}

		return $url;
	}

}
