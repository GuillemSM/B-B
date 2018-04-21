<?php
/**
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Footer Sidebars static.
 */
class cherry_footer_sidebars_static extends cherry_register_static {

	/**
	 * Callback-method for registered static.
	 * @since 4.0.0
	 */
	public function callback() {
		echo '<div class="row">';
		echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">';
			cherry_get_sidebar( "sidebar-footer-1" );
		echo '</div>';
		echo '<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">';
			cherry_get_sidebar( "sidebar-footer-2" );
		echo '</div>';
		echo '<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">';
			cherry_get_sidebar( "sidebar-footer-3" );
		echo '</div>';
		echo '</div>';	
	}
}

/**
 * Registration for Footer Sidebars static.
 */
new cherry_footer_sidebars_static(
	array(
		'name'    => __( 'Footer Sidebars', 'child-theme-domain' ),
		'id'      => 'footer_sidebars',
		'options' => array(
			'position' => 1,
			'area'     => 'footer-top',
		)
	)
);