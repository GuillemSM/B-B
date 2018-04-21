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
 * Search Form static.
 */
class cherry_search_form_static extends cherry_register_static {

	/**
	 * Callback-method for registered static.
	 *
	 * @since 4.0.0
	 */
	public function callback() {
		//get_search_form( true );
		?><form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ) ?>">
            <label>
                <span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'child-theme-domain' ) ?></span>
                <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'child-theme-domain' ) ?>" value="<?php echo get_search_query()  ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'child-theme-domain' ) ?>" />
            </label>
            <input type="submit" class="search-submit" value="" />
        </form><?php
	}
}

/**
 * Registration for Search Form static.
 */
new cherry_search_form_static(
	array(
		'id'      => 'search-form',
		'name'    => __( 'Search Form', 'child-theme-domain' ),
		'options' => array(
			'col-lg'   => 'none',
			'col-md'   => 'none',
			'col-sm'   => 'none',
			'col-xs'   => 'none',
			'class'    => 'pull-left',
			'position' => 3,
			'area'     => 'header-top',
		)
	)
);
