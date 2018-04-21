<?php
/**
 * Cherry Services templates loader
 *
 * @package   Cherry_Services
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

/**
 * Class for including page templates.
 *
 * @since 1.0.0
 */
class Cherry_Services_Templater {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Posts number per team archive and page template
	 *
	 * @since 1.0.0
	 * @var   integer
	 */
	public static $posts_per_archive_page = null;

	/**
	 * The array of templates that this plugin tracks.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	protected $templates;

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->templates = array();

		// Set posts per archive services page
		add_action( 'pre_get_posts', array( $this, 'set_posts_per_archive_page' ) );

		// Add our template into the page template cache.
		add_filter( 'admin_head', array( $this, 'register_custom_template' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'register_custom_template' ) );

		// Add a filter to the template include in order to determine if the page has our template assigned and return it's path.
		add_filter( 'template_include', array( $this, 'view_template' ) );

		// Add a filter to load a custom template for a given post.
		add_filter( 'single_template', array( $this, 'get_single_template' ) );

		add_filter( 'cherry_attr_post', array( $this, 'page_template_classes' ), 10, 2 );

		add_filter( 'theme_page_templates', array( $this, 'add_templates' ) );

		// Add your templates to this array.
		$this->templates = array(
			'template-services.php' => __( 'Services Page', 'cherry-services' ),
		);

	}

	/**
	 * Add services page templates.
	 *
	 * @param  array $templates Existing templates array.
	 * @return array
	 */
	public function add_templates( $templates = array() ) {
		return array_merge( $templates, $this->templates );
	}

	/**
	 * Register custom page tamplate for Services page
	 *
	 * @since  1.0.4
	 * @param  array $data if function is called from wp_insert_post_data filter - array with post data to save.
	 * @return void|bool
	 */
	public function register_custom_template( $data = array() ) {

		global $current_screen;

		if ( isset( $current_screen->id ) && ! in_array( $current_screen->id, array( 'edit-page', 'page' ) ) ) {
			return $data;
		}

		if ( isset( $data['post_type'] ) && 'page' !== $data['post_type'] ) {
			return $data;
		}

		// Create default cache
		$page_templates = wp_get_theme()->get_page_templates();

		// Generate cache key to rewite
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		$page_templates = array_merge( $page_templates, $this->templates );

		wp_cache_delete( $cache_key , 'themes' );
		wp_cache_add( $cache_key, $page_templates, 'themes', 1800 );

		return $data;

	}

	/**
	 * Set posts number per services archive page
	 *
	 * @since  1.0.0
	 * @param  object $query current query object.
	 * @return void|bool false
	 */
	public function set_posts_per_archive_page( $query ) {

		// Must work only for public.
		if ( is_admin() ) {
			return $query;
		}

		// And only for main query
		if ( ! $query->is_main_query() ) {
			return $query;
		}

		$is_archive = $query->is_post_type_archive( CHERRY_SERVICES_NAME );

		if ( $is_archive || $this->is_services_tax( $query ) ) {
			$query->set( 'posts_per_page', self::get_posts_per_archive_page() );
		}
	}

	/**
	 * Check if passed query is services taxonomy
	 *
	 * @since  1.0.4
	 * @param  object $query current query object.
	 * @return boolean
	 */
	public function is_services_tax( $query ) {

		$tax = CHERRY_SERVICES_NAME . '_category';
		return ! empty( $query->query_vars[ $tax ] );;
	}

	/**
	 * Get number of posts per archive page
	 *
	 * @since  1.0.4
	 * @return int
	 */
	public static function get_posts_per_archive_page() {

		if ( null !== self::$posts_per_archive_page ) {
			self::$posts_per_archive_page;
		}

		/**
		 * Filter posts per archive page value
		 * @var int
		 */
		self::$posts_per_archive_page = apply_filters( 'cherry_services_posts_per_archive_page', 6 );

		return self::$posts_per_archive_page;
	}

	/**
	 * Checks if the template is assigned to the page.
	 *
	 * @since 1.0.0
	 */
	public function view_template( $template ) {

		global $post;

		// check if we need archive template to include
		if ( is_post_type_archive( CHERRY_SERVICES_NAME ) || is_tax( CHERRY_SERVICES_NAME . '_category' ) ) {

			$file = trailingslashit( CHERRY_SERVICES_DIR ) . 'templates/archive-services.php';

			if ( file_exists( $file ) ) {
				return $file;
			}
		}

		if ( ! is_page( $post ) ) {
			return $template;
		}

		$page_template_meta = get_post_meta( $post->ID, '_wp_page_template', true );

		if ( ! isset( $this->templates[ $page_template_meta ] ) ) {
			return $template;
		}

		$file = trailingslashit( CHERRY_SERVICES_DIR ) . 'templates/' . $page_template_meta;

		// Just to be safe, we check if the file exist first.
		if ( file_exists( $file ) ) {
			return $file;
		}

		return $template;
	}

	/**
	 * Adds a custom single template for a 'Team' post.
	 *
	 * @since 1.0.0
	 */
	public function get_single_template( $template ) {
		global $post;

		if ( $post->post_type == CHERRY_SERVICES_NAME ) {
			$template = trailingslashit( CHERRY_SERVICES_DIR ) . 'templates/single-services.php';
		}

		return $template;
	}

	/**
	 * Add custom sclasses post wrapper for services page.
	 *
	 * @since  1.0.0
	 * @param  array  $atts    default classes array.
	 * @param  string $context current post context.
	 * @return array
	 */
	public function page_template_classes( $atts, $context ) {

		if ( 'services-template' != $context ) {
			return $atts;
		}

		$atts['class'] .= ' services-page';

		return $atts;
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

Cherry_Services_Templater::get_instance();
