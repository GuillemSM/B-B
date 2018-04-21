<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package   Cherry_Testimonials_Admin
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2012 - 2015, Cherry Team
 */

/**
 * Class for Testimonials admin functionality.
 *
 * @since 1.0.0
 */
class Cherry_Testimonials_Admin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {
		// Load post meta boxes on the post editing screen.
		add_action( 'load-post.php', array( $this, 'load_post_meta_boxes' ) );
		add_action( 'load-post-new.php', array( $this, 'load_post_meta_boxes' ) );

		// Only run our customization on the 'edit.php' page in the admin.
		add_action( 'load-edit.php', array( $this, 'load_edit' ) );

		// Modify the columns on the "Testimonials" screen.
		add_filter( 'manage_edit-testimonial_columns', array( $this, 'edit_testimonial_columns' ) );
		add_action( 'manage_testimonial_posts_custom_column', array( $this, 'manage_testimonial_columns' ), 10, 2 );
	}

	/**
	 * Loads custom meta boxes on the "Add New Testimonial" and "Edit Testimonial" screens.
	 *
	 * @since 1.0.0
	 */
	public function load_post_meta_boxes() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'testimonial' === $screen->post_type ) {
			require_once( trailingslashit( CHERRY_TESTI_DIR ) . 'admin/includes/class-cherry-testimonials-meta-boxes.php' );
		}
	}

	/**
	 * Adds a custom filter on 'request' when viewing the "Testimonials" screen in the admin.
	 *
	 * @since 1.0.0
	 */
	public function load_edit() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'testimonial' === $screen->post_type ) {
			add_action( 'admin_head', array( $this, 'print_styles' ) );
		}
	}

	/**
	 * Style adjustments for the manage menu items screen.
	 *
	 * @since 1.0.0
	 */
	public function print_styles() {
		?><style type="text/css">
		.edit-php .wp-list-table td.thumbnail.column-thumbnail,
		.edit-php .wp-list-table th.manage-column.column-thumbnail {
			text-align: center;
		}
		</style>
	<?php }

	/**
	 * Filters the columns on the `Testimonials` screen.
	 *
	 * @since  1.0.0
	 * @param  array $post_columns An array of column name => label.
	 * @return array
	 */
	public function edit_testimonial_columns( $post_columns ) {

		unset(
			$post_columns['author'],
			$post_columns[ 'taxonomy-' . CHERRY_TESTI_NAME . '_category' ],
			$post_columns['date']
		);

		// Add custom columns and overwrite the 'date' column.
		$post_columns['thumbnail']    = __( 'Avatar', 'cherry-testimonials' );
		$post_columns['author_name']  = __( 'Author', 'cherry-testimonials' );
		$post_columns['position']     = __( 'Position', 'cherry-testimonials' );
		$post_columns['company_name'] = __( 'Company Name', 'cherry-testimonials' );
		$post_columns['date']         = __( 'Date', 'cherry-testimonials' );

		// Return the columns.
		return $post_columns;
	}

	/**
	 * Add output for custom columns on the "menu items" screen.
	 *
	 * @since  1.0.0
	 * @param  string $column  The name of the column to display.
	 * @param  int    $post_id The ID of the current post.
	 */
	public function manage_testimonial_columns( $column, $post_id ) {
		require_once( CHERRY_TESTI_DIR . 'public/includes/class-cherry-testimonials-template-callbacks.php' );

		$callbacks = new Cherry_Testimonials_Template_Callbacks( null );

		switch ( $column ) {
			case 'author_name':
				$name = $callbacks->get_name();
				echo empty( $name ) ? '&mdash;' : $name;
				break;

			case 'thumbnail':
				$avatar = $callbacks->get_avatar();
				echo empty( $avatar ) ? '&mdash;' : $avatar;
				break;

			case 'position':
				$position = $callbacks->get_position();
				echo empty( $position ) ? '&mdash;' : $position;
				break;

			case 'company_name':
				$company_name = $callbacks->get_company();
				echo empty( $company_name ) ? '&mdash;' : $company_name;
				break;

			default :
				break;
		}
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

Cherry_Testimonials_Admin::get_instance();
