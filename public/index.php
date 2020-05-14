<?php // Silence is golden

// if this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Sanitizes a hex color.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or nothing.
 *
 * @param string $color
 *
 * @return string|void
 */
function leira_letter_avatar_sanitize_hex_color( $color ) {
	if ( '' === $color ) {
		return '';
	}
	$hex_regex = '|^#?([A-Fa-f0-9]{3}){1,2}$|';
	// 3 or 6 hex digits, or the empty string.
	if ( preg_match( $hex_regex, $color ) ) {
		return $color;
	}
}

/**
 * Generate an svg image from query string parameters
 * Parameters:
 *  s    => size [16 , 512] default 64
 *  f    => font_size default
 *  l    => characters length [1|2] default 2
 *  t    => text to show. default ''
 *  r    => make a circle or square icon. boolean default true
 *  b    => text bold. boolean default false
 *  u    => text uppercase. boolean default false
 *  bg   => background color. default fc91ad
 *  c    => text color. default calculated from background
 */

/**
 * Headers
 */
header( 'Pragma: public' );
header( 'Access-Control-Allow-Origin: *' );
header( 'Access-Control-Allow-Credentials: true' );
header( 'Access-Control-Allow-Methods: GET, OPTIONS' );
header( 'Access-Control-Max-Age: 1814400' );
header( 'Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, remember-me' );
header( 'Cache-Control: max-age=1814400' );
header( 'Content-type: image/svg+xml' );

/**
 * Size (16 >= $size <= 512)
 */
$size = isset( $_GET['s'] ) ? abs( intval( filter_var( $_GET['s'], FILTER_VALIDATE_INT ) ) ) : 64;
$size = $size < 16 ? 16 : $size;
$size = $size > 512 ? 512 : $size;

/**
 * Font size
 */
$font_size = isset( $_GET['f'] ) ? abs( intval( filter_var( $_GET['f'], FILTER_VALIDATE_INT ) ) ) : ( $size / 2 );

/**
 * Characters Length
 */
$length = isset( $_GET['l'] ) ? abs( intval( filter_var( $_GET['l'], FILTER_VALIDATE_INT ) ) ) : 2;
$length = $length < 1 ? 1 : $length;
$length = $length > 2 ? 2 : $length;

/**
 * Text
 */
$text = isset( $_GET['t'] ) ? filter_var( $_GET['t'], FILTER_SANITIZE_STRING ) : '';
$text = substr( $text, 0, $length );

/**
 * Rounded
 */
$rounded = isset( $_GET['r'] ) ? filter_var( $_GET['r'], FILTER_VALIDATE_BOOLEAN ) : true;

/**
 * Bold
 */
$bold = isset( $_GET['b'] ) ? filter_var( $_GET['b'], FILTER_VALIDATE_BOOLEAN ) : false;

/**
 * Uppercase
 */
$uppercase = isset( $_GET['u'] ) ? filter_var( $_GET['u'], FILTER_VALIDATE_BOOLEAN ) : false;
if ( $uppercase ) {
	$text = strtoupper( $text );
}

/**
 * Background color
 */
$background = ( isset( $_GET['bg'] ) && leira_letter_avatar_sanitize_hex_color( $_GET['bg'] ) ) ? leira_letter_avatar_sanitize_hex_color( trim( $_GET['bg'], '#' ) ) : 'fc91ad';

/**
 * Text color
 */
$color = ( hexdec( $background ) > 0xffffff / 2 ) ? '000' : 'fff';
$color = isset( $_GET['c'] ) && leira_letter_avatar_sanitize_hex_color( $_GET['c'] ) ? leira_letter_avatar_sanitize_hex_color( trim( $_GET['c'], '#' ) ) : $color;

$avatar = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="' . $size . 'px" height="' . $size . 'px" viewBox="0 0 ' . $size . ' ' . $size . '" style="user-select: none;" version="1.1"><' . ( $rounded ? 'circle' : 'rect' ) . ' fill="#' . trim( $background, '#' ) . '" cx="' . ( $size / 2 ) . '" width="' . $size . '" height="' . $size . '" cy="' . ( $size / 2 ) . '" r="' . ( $size / 2 ) . '"/><text x="50%" y="50%" style="color: #' . trim( $color, '#' ) . '; line-height: 1;font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\', \'Ubuntu\', \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;" alignment-baseline="middle" text-anchor="middle" font-size="' . $font_size . '" font-weight="' . ( $bold ? 600 : 400 ) . '" dy=".1em" dominant-baseline="middle" fill="#' . trim( $color, '#' ) . '">' . $text . '</text></svg>';

echo $avatar;