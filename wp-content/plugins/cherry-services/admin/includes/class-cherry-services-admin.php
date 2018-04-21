<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package   Cherry_Services_Admin
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Define admin-area realted hooks
 */
class Cherry_Services_Admin {

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
		add_action( 'load-post.php',     array( $this, 'load_post_meta_boxes' ) );
		add_action( 'load-post-new.php', array( $this, 'load_post_meta_boxes' ) );

		// Only run our customization on the 'edit.php' page in the admin.
		add_action( 'load-edit.php', array( $this, 'load_edit' ) );

		// Modify the columns on the "Services" screen.
		add_filter( 'manage_edit-' . CHERRY_SERVICES_NAME . '_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_' . CHERRY_SERVICES_NAME . '_posts_custom_column', array( $this, 'manage_columns' ), 10, 2 );
	}

	/**
	 * Loads custom meta boxes on the "Add New Service" and "Edit Service" screens.
	 *
	 * @since 1.0.0
	 */
	public function load_post_meta_boxes() {

		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && CHERRY_SERVICES_NAME === $screen->post_type ) {
			require_once( CHERRY_SERVICES_DIR . 'admin/includes/class-cherry-services-meta-boxes.php' );
		}

	}

	/**
	 * Adds a custom filter on 'request' when viewing the "Services" screen in the admin.
	 *
	 * @since 1.0.0
	 */
	public function load_edit() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && CHERRY_SERVICES_NAME === $screen->post_type ) {
			add_action( 'admin_head', array( $this, 'print_styles' ) );
		}
	}

	/**
	 * Style adjustments for the manage menu items screen.
	 *
	 * @since 1.0.0
	 */
	public function print_styles() {
		?>
		<style type="text/css">
		.edit-php .wp-list-table td.thumbnail.column-thumbnail,
		.edit-php .wp-list-table th.manage-column.column-thumbnail,
		.edit-php .wp-list-table td.author_name.column-price {
			text-align: center;
		}
		</style>
		<?php
	}

	/**
	 * Filters the columns on the "Services" screen.
	 *
	 * @since  1.0.0
	 * @param  array $post_columns current post columns list.
	 * @return array
	 */
	public function add_columns( $post_columns ) {

		unset(
			$post_columns[ 'taxonomy-' . CHERRY_SERVICES_NAME . '_category' ],
			$post_columns['date']
		);

		// Add custom columns.
		$post_columns['title']     = __( 'Name', 'cherry-services' );
		$post_columns['thumbnail'] = __( 'Photo', 'cherry-services' );
		$post_columns['price']     = __( 'Price', 'cherry-services' );
		$post_columns['date']      = __( 'Added', 'cherry-services' );

		// Return the columns.
		return $post_columns;
	}

	/**
	 * Add output for custom columns on the "menu items" screen.
	 *
	 * @since  1.0.0
	 * @param  string $column  current column name.
	 * @param  int    $post_id current post ID.
	 * @return void
	 */
	public function manage_columns( $column, $post_id ) {

		switch ( $column ) {

			case 'price' :

				$post_meta = get_post_meta( $post_id, CHERRY_SERVICES_POSTMETA, true );

				if ( ! empty( $post_meta ) ) {
					echo ( ! empty( $post_meta['price'] ) ) ? strip_tags( htmlspecialchars_decode( $post_meta['price'] ) ) : '&mdash;';
				}

				break;

			case 'thumbnail' :

				$thumb = get_the_post_thumbnail( $post_id, array( 50, 50 ) );

				echo ! empty( $thumb ) ? $thumb : '&mdash;';

				break;

			default:
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

Cherry_Services_Admin::get_instance();
