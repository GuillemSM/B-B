<?php
/**
 * Static-areas configuration.
 *
 * @package    Cherry_Framework
 * @subpackage Config
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

// Register a static areas.
add_action( 'init', 'cherry_register_static_areas' );
function cherry_register_static_areas() {

	cherry_register_static_area( array(
		'id'    => 'header-top',
		'name'  => __( 'Header Top', 'child-theme-domain' ),
		'fluid' => true,
	) );

	cherry_register_static_area( array(
		'id'    => 'header-bottom',
		'name'  => __( 'Header Bottom', 'child-theme-domain' ),
		'fluid' => false,
	) );

	cherry_register_static_area( array(
		'id'    => 'showcase-area',
		'name'  => __( 'Showcase Area', 'child-theme-domain' ),
		'fluid' => true,
	) );

	cherry_register_static_area( array(
		'id'    => 'footer-top',
		'name'  => __( 'Footer Top', 'child-theme-domain' ),
		'fluid' => false,
	) );

	cherry_register_static_area( array(
		'id'    => 'footer-bottom',
		'name'  => __( 'Footer Bottom', 'child-theme-domain' ),
		'fluid' => false,
	) );
}