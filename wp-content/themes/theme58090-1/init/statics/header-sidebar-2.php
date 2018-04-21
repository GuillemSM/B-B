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
 * Header Sidebar static.
 */
class cherry_header_sidebar_static_2 extends cherry_register_static {

	/**
	 * Callback-method for registered static.
	 *
	 * @since 4.0.0
	 */
	public function callback() {
		cherry_get_sidebar( 'sidebar-header-2' );
	}
}

/**
 * Registration for Header Sidebar static.
 */
new cherry_header_sidebar_static_2(
	array(
		'id'      => 'header_sidebar_2',
		'name'    => __( 'Header Sidebar 2', 'child-theme-domain' ),
		'options' => array(
			'col-lg'   => 'col-lg-12',
			'col-md'   => 'col-md-12',
			'col-sm'   => 'col-sm-12',
			'col-xs'   => 'col-xs-12',
			'area'     => 'available-statics',
		)
	)
);