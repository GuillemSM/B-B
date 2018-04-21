<?php
/**
 * New post type and taxonomy registration.
 *
 * @package   Cherry_Testimonials
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2012 - 2015, Cherry Team
 */

/**
 * Class for register post types.
 *
 * @since 1.0.0
 */
class Cherry_Testimonials_Registration {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Adds the testimonials post type.
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );
	}

	/**
	 * Register the custom post type.
	 *
	 * @since 1.0.0
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function register_post_type() {
		$labels = array(
			'name'               => __( 'Testimonials', 'cherry-testimonials' ),
			'singular_name'      => __( 'Testimonial', 'cherry-testimonials' ),
			'add_new'            => __( 'Add New', 'cherry-testimonials' ),
			'add_new_item'       => __( 'Add New Testimonial', 'cherry-testimonials' ),
			'edit_item'          => __( 'Edit Testimonial', 'cherry-testimonials' ),
			'new_item'           => __( 'New Testimonial', 'cherry-testimonials' ),
			'view_item'          => __( 'View Testimonial', 'cherry-testimonials' ),
			'search_items'       => __( 'Search Testimonials', 'cherry-testimonials' ),
			'not_found'          => __( 'No testimonials found', 'cherry-testimonials' ),
			'not_found_in_trash' => __( 'No testimonials found in trash', 'cherry-testimonials' ),
		);

		$supports = array(
			'title',
			'editor',
			'thumbnail',
			'revisions',
			'page-attributes',
			'cherry-grid-type',
			'cherry-layouts',
		);

		$args = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'capability_type' => 'post',
			'hierarchical'    => false, // Hierarchical causes memory issues - WP loads all records!
			'rewrite'         => array(
				'slug'       => 'testimonial-view',
				'with_front' => false,
				'feeds'      => true,
			),
			'query_var'       => true,
			'menu_position'   => null,
			'menu_icon'       => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-testimonial' : '',
			'can_export'      => true,
			'has_archive'     => true,
		);

		$args = apply_filters( 'cherry_testimonials_post_type_args', $args );

		register_post_type( CHERRY_TESTI_NAME, $args );
	}

	/**
	 * Register the custom taxonomy.
	 *
	 * @since 1.0.0
	 * @link  https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public static function register_taxonomy() {
		$labels = array(
			'name'                       => __( 'Testimonials Categories', 'cherry-testimonials' ),
			'singular_name'              => __( 'Edit Category', 'cherry-testimonials' ),
			'search_items'               => __( 'Search Categories', 'cherry-testimonials' ),
			'popular_items'              => __( 'Popular Categories', 'cherry-testimonials' ),
			'all_items'                  => __( 'All Categories', 'cherry-testimonials' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Category', 'cherry-testimonials' ),
			'update_item'                => __( 'Update Category', 'cherry-testimonials' ),
			'add_new_item'               => __( 'Add New Category', 'cherry-testimonials' ),
			'new_item_name'              => __( 'New Category Name', 'cherry-testimonials' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'cherry-testimonials' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'cherry-testimonials' ),
			'choose_from_most_used'      => __( 'Choose from the most used categories', 'cherry-testimonials' ),
			'not_found'                  => __( 'No categories found.', 'cherry-testimonials' ),
			'menu_name'                  => __( 'Categories', 'cherry-testimonials' ),
		);

		$args = array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => CHERRY_TESTI_NAME . '_category' ),
		);

		register_taxonomy( CHERRY_TESTI_NAME . '_category', CHERRY_TESTI_NAME, $args );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

Cherry_Testimonials_Registration::get_instance();
