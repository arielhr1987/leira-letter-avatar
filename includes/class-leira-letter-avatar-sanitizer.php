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
 * Common sanitizing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Leira_Letter_Avatar
 * @subpackage Leira_Letter_Avatar/includes
 * @author     Ariel <arielhr1987@gmail.com>
 * @since      1.0.0
 */
class Leira_Letter_Avatar_Sanitizer{

	/**
	 * @var string
	 * @since 1.0.0
	 */
	protected $default_bg = 'fc91ad';

	/**
	 * @var string[]
	 * @since 1.3.0
	 */
	protected $formats = array( 'svg', 'png', 'jpg' );

	/**
	 * Get all available formats
	 *
	 * @return string[]
	 * @since 1.3.0
	 */
	public function get_formats() {
		return $this->formats;
	}

	/**
	 * Get default background color
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function getDefaultBackground() {
		return $this->default_bg;
	}

	/**
	 * Sanitize defaults
	 *
	 * @param $value
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function avatar_default( $value ) {
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
		$value           = sanitize_text_field( $value );
		$value           = isset( $avatar_defaults[ $value ] ) ? $value : 'mystery';

		return $value;
	}

	/**
	 * Sanitize boolean values
	 *
	 * @param $value
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function boolean( $value ) {
		$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );

		return $value;
	}

	/**
	 * Sanitize letters count
	 *
	 * @param $value
	 *
	 * @return int|mixed
	 * @since 1.0.0
	 */
	public function letters( $value ) {
		$value = filter_var( $value, FILTER_VALIDATE_INT );
		$value = $value > 2 ? 2 : $value;
		$value = $value < 1 ? 1 : $value;

		return $value;
	}

	/**
	 * Sanitize color method
	 *
	 * @param $value
	 *
	 * @return string
	 * @since 1.2.2
	 */
	public function color_method( $value ) {
		$value   = sanitize_text_field( $value );
		$methods = array( 'auto', 'fixed' );
		$value   = in_array( $value, $methods ) ? $value : 'auto';

		return $value;
	}

	/**
	 * Sanitize method
	 *
	 * @param $value
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function method( $value ) {
		$value   = sanitize_text_field( $value );
		$methods = array( 'auto', 'fixed', 'random' );
		$value   = in_array( $value, $methods ) ? $value : 'auto';

		return $value;
	}

	/**
	 * Sanitize background
	 *
	 * @param $value
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function background( $value ) {
		$value = ltrim( $value, '#' );
		$value = sanitize_hex_color_no_hash( $value );
		$value = ltrim( $value, '#' );

		return empty( $value ) ? $this->default_bg : $value;
	}

	/**
	 * Sanitize backgrounds
	 *
	 * @param $value
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function backgrounds( $value ) {
		$value  = sanitize_text_field( $value );
		$values = explode( ',', $value );
		$values = array_map( function( $value ) {
			return trim( trim( $value ), '#' );
		}, $values );
		$values = array_filter( $values, 'sanitize_hex_color_no_hash' );
		$value  = implode( ',', $values );

		return $value;
	}

	/**
	 * Sanitize avatar format
	 *
	 * @param $value
	 *
	 * @return mixed|string
	 *
	 * @since 1.3.0
	 */
	public function format( $value ) {
		$value = sanitize_text_field( $value );
		$value = in_array( $value, $this->get_formats() ) ? $value : 'svg';

		return $value;
	}

}
