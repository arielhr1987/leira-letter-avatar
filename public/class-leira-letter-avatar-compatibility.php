<?php

/**
 * This class handles all compatibility with third party plugins
 *
 * @link       https://leira.dev
 * @since      1.0.0
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/public
 * @author     Ariel <arielhr1987@gmail.com>
 */
class Leira_Letter_Avatar_Compatibility
{

    /**
     * BuddyBoss compatibility
     * This function ensures BuddyBoss uses Letter Avatars.
     *
     * @param  string  $avatar_image_url  URL of the avatar image.
     * @param  array  $params  Parameters
     *
     * @return bool
     * @since 1.3.8
     */
    public function bb_attachments_get_default_profile_group_avatar_image($avatar_image_url, $params)
    {
        /**
         * BuddyBoss seems to use bb_attachments_get_default_profile_group_avatar_image() to load avatars.
         *
         * The above function, if there is no custom avatar, and WordPress avatars are turned on, will call
         * get_avatar_url() without any user data, so every user ends up with the same avatar.
         *
         * This function injects a properly formed avatar into the filter, so BuddyBoss displays letter avatars for users.
         */

        if (!function_exists('bp_get_option') || !function_exists('bb_get_profile_avatar_type')) {
            return $avatar_image_url;
        }

        $show_avatar         = bp_get_option('show_avatars');
        $profile_avatar_type = bb_get_profile_avatar_type();

        // Only override if the custom avatar option is enabled.
        if ($show_avatar && 'WordPress' === $profile_avatar_type && 'blank' !== bp_get_option('avatar_default',
                'mystery')) {
            return $this->bp_core_default_avatar($avatar_image_url, $params);
        }else {
            return $avatar_image_url;
        }

    }

    /**
     * BuddyPress compatibility
     * Check if buddy press should use gravatar as the default image or letter avatar.
     * If the user didn't upload a profile picture, this method is executed
     *
     * @param  bool  $no_gravatar  Do not use Gravatar
     * @param  array  $params  Parameters
     *
     * @return bool
     * @since 1.2.0
     */
    public function bp_core_fetch_avatar_no_grav($no_gravatar, $params)
    {
        /**
         * System forces to generate avatar with a specific format.
         * This fixes discussion settings repeated images
         */
        $default       = isset($params['default']) ? $params['default'] : false;
        $force_default = isset($params['force_default']) ? $params['force_default'] : false;
        if ($force_default && $default !== 'leira_letter_avatar') {
            return $no_gravatar;
        }

        /**
         * Do not use gravatar in favor of letter avatar
         */
        if ($default == 'leira_letter_avatar' || leira_letter_avatar()->is_active()) {
            $object = $params['object'];
            if (in_array($object, array('user'))) { //, 'group', 'site'
                $no_gravatar = true;
            }
        }

        return $no_gravatar;
    }

    /**
     * BuddyPress compatibility
     * Generate image avatar for buddy press.
     *
     * @param  string  $avatar_default
     * @param  array  $params
     *
     * @return string
     * @since 1.2.0
     */
    public function bp_core_default_avatar($avatar_default, $params)
    {

        $default = isset($params['default']) ? $params['default'] : false;
        /**
         * Generate letter avatar for a specific user, group or site
         */
        if ($default == 'leira_letter_avatar' || leira_letter_avatar()->is_active()) {
            /**
             * Our avatar method is enable
             */
            if (isset($params['object'])) {
                $object = $params['object'];
            }else {
                $object            = 'user';
                $params['item_id'] = bp_displayed_user_id();//bp_loggedin_user_id()
                /**
                 * We use "bp_displayed_user_id()" as in "bp_get_user_has_avatar" method in file bp-core-avatars.php
                 */
            }
            if ($object == 'user') {
                if (isset($params['item_id'])) {
                    $user = get_user_by('id', $params['item_id']);

                    if (isset($params['width'])) {
                        $size = $params['width'];
                    }else {
                        if (isset($params['type']) && 'thumb' === $params['type']) {
                            $size = bp_core_avatar_thumb_width();
                        }else {
                            $size = bp_core_avatar_full_width();
                        }
                    }


                    $args = array(
                        'size' => $size
                    );
                    $url  = leira_letter_avatar()->public->generate_letter_avatar_url($user, $args);
                    if ($url) {
                        /**
                         * If it was generated correctly, use this avatar url
                         */
                        $avatar_default = $url;
                    }
                }
            }elseif ($object == 'group') {
                if (isset($params['item_id'])) {
                    /**
                     * do nothing. we do not generate avatars for groups
                     */
                }
            }
        }

        return $avatar_default;
    }

    /**
     * This method is different from "bp_core_default_avatar".
     * With this filter we make sure that any call to "bp_core_avatar_default" returns our letter avatar
     *
     * @param $avatar_default
     * @param $params
     *
     * @return string
     * @since 1.2.1
     */
    public function bp_core_avatar_default($avatar_default, $params)
    {
        return $this->bp_core_default_avatar($avatar_default, $params);
    }

    /**
     * Ultimate Member compatibility
     * Determine default avatar tu use if the user did not upload his own avatar
     *
     * @param  string  $url  Current avatar url
     * @param  int  $user_id  User id of the associated avatar
     * @param  array  $data  Array of setting to build the avatar.
     *                        Some plugins do not send this parameter that's why we set a default value
     *
     * @return string
     * @since 1.2.0
     */
    public function um_user_avatar_url_filter($url, $user_id, $data = array('size' => 96))
    {
        if (empty($user_id)) {
            $user_id = um_user('ID');
        }else {
            um_fetch_user($user_id);
        }

        if (!um_profile('profile_photo') && !um_user('synced_profile_photo') && !UM()->options()->get('use_gravatars')) {

            $args   = array(
                'size' => isset($data['size']) ? $data['size'] : 96 //default Ultimate Membership value
            );
            $avatar = leira_letter_avatar()->public->generate_letter_avatar_url($user_id, $args);
            if ($avatar) {
                $url = $avatar;
            }
        }

        return $url;
    }

    /**
     * wpdiscuz compatibility
     * Fix problem with comments
     *
     * @param  string  $url  The URL of the avatar.
     * @param  mixed  $id_or_email  The Gravatar to retrieve. Accepts a user ID, Gravatar MD5 hash, user email, WP_User object, WP_Post object, or WP_Comment object.
     * @param  array  $args  Arguments passed to get_avatar_data(), after processing.
     *
     * @return string
     * @since 1.2.0
     */
    public function wpdiscuz_get_avatar_url($url, $id_or_email, $args)
    {

        /**
         * Capture social avatars
         * wp-content/plugins/wpdiscuz/forms/wpdFormAttr/Login/SocialLogin.php#1576
         */
        if (interface_exists('wpdFormAttr\FormConst\wpdFormConst')) {
            /**
             * We make sure wpDiscuz is installed
             */
            $userID = false;
            if (isset($args["wpdiscuz_current_user"])) {
                if ($args["wpdiscuz_current_user"]) {
                    $userID = $args["wpdiscuz_current_user"]->ID;
                }
            }else {
                if (is_numeric($id_or_email)) {
                    $userID = (int) $id_or_email;
                }elseif ($id_or_email instanceof WP_User) {
                    $userID = $id_or_email->ID;
                }elseif ($id_or_email instanceof WP_Post) {
                    $userID = (int) $id_or_email->post_author;
                }elseif ($id_or_email instanceof WP_Comment) {
                    if (!empty($id_or_email->user_id)) {
                        $userID = (int) $id_or_email->user_id;
                    }
                }else {
                    $user   = get_user_by("email", $id_or_email);
                    $userID = isset($user->ID) ? $user->ID : 0;
                }
            }

            if ($userID && $avatar_url = get_user_meta($userID,
                    wpdFormAttr\FormConst\wpdFormConst::WPDISCUZ_SOCIAL_AVATAR_KEY, true)) {
                return $avatar_url;
            }
        }

        /**
         * Default logic for incorrectly avatar requests
         */
        if (isset($args['wpdiscuz_current_user'])) {
            $current_user = $args['wpdiscuz_current_user'];
            $email        = isset($args['wpdiscuz_gravatar_user_email']) ? $args['wpdiscuz_gravatar_user_email'] : 'unknown@example.com';
            /**
             * Check if we have a user
             */
            if (!$current_user instanceof WP_User) {
                $current_user = new WP_User();
            }

            /**
             * This is a valid user, no need to continue
             */
            if ($current_user->ID > 0) {
                return $url;
            }

            /**
             * Populate user info
             */
            $current_user->user_email = $email;
            $current_user->nickname   = isset($args['alt']) ? $args['alt'] : '';

            $avatar = leira_letter_avatar()->public->get_avatar_url($url, $current_user, $args);
            if ($avatar) {
                $url = $avatar;
            }
        }

        return $url;
    }

}
