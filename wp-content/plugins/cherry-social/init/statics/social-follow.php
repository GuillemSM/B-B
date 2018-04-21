<?php
/**
 * Sets up a `Social Follow` static functionality.
 *
 * @package   Cherry_Social
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @copyright 2012 - 2015, Cherry Team
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'cherry_register_static' ) ) {
	return;
}

/**
 * Declare a `Social Follow` static php-class.
 *
 * @since 1.0.0
 */
class Cherry_Social_Follow_Static extends cherry_register_static {

	/**
	 * Callbck method for register static.
	 *
	 * @since 1.0.0
	 */
	public function callback() {
		$plugin = Cherry_Social::get_instance();
		$title  = $plugin->get_option( 'follow-title' );

		if ( ! empty( $title ) ) {
			$title_wrap = sprintf( '<h3 class="%1$s">%2$s</h3>', 'cherry-follow_title', $title );
			$title_wrap = apply_filters( 'cherry_social_follow_static_title', $title_wrap );
			echo $title_wrap;
		}

		$plugin->get_follows( -1 );
	}
}

new Cherry_Social_Follow_Static( array(
	'name'     => __( 'Follow Us', 'cherry-social' ),
	'id'       => 'social-follow',
	'options'  => array(
		'col-lg'   => 'col-lg-6',  // (optional) Column class for a large devices (≥1200px)
		'col-md'   => 'col-md-6',  // (optional) Column class for a medium devices (≥992px)
		'col-sm'   => 'col-sm-12', // (optional) Column class for a tablets (≥768px)
		'col-xs'   => 'col-xs-12', // (optional) Column class for a phones (<768px)
		'position' => 1, // (optional) Position in static area (1 - first static, 2 - second static, etc.)
		'area'     => 'available-statics', // (required) ID for static area
		'collapse' => false, // (required) Collapse column paddings?
		),
	)
);
