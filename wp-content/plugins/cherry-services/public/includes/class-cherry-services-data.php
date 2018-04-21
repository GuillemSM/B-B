<?php
/**
 * Cherry Services Data class.
 * main public class. Grab services data form database and output it
 *
 * @package   Cherry_Services
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Services data.
 *
 * @since 1.0.0
 */
class Cherry_Services_Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * The array of arguments for query.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $query_args = array();

	/**
	 * Holder for the main query object, while services query processing
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private $temp_query = null;

	/**
	 * The array of arguments for template file.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $post_data = array();

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/**
		 * Fires when you need to display services.
		 *
		 * @since 1.0.0
		 */
		add_action( 'cherry_get_services', array( $this, 'the_services' ) );

	}

	/**
	 * Display or return HTML-formatted services.
	 *
	 * @since  1.0.0
	 * @param  string|array $args Arguments.
	 * @return string
	 */
	public function the_services( $args = '' ) {

		/**
		 * Filter the array of default arguments.
		 *
		 * @since 1.0.0
		 * @param array Default arguments.
		 * @param array The 'the_services' function argument.
		 */
		$defaults = apply_filters( 'cherry_the_services_default_args', array(
			'limit'             => 3,
			'orderby'           => 'date',
			'order'             => 'DESC',
			'id'                => 0,
			'categories'        => '',
			'show_photo'        => true,
			'show_name'         => true,
			'size'              => 'thumbnail',
			'echo'              => true,
			'columns'           => 3,
			'title'             => '',
			'excerpt_length'    => 20,
			'wrap_class'        => 'cherry-services',
			'before_title'      => '<h2 class="cherry-services_title">',
			'after_title'       => '</h2>',
			'pager'             => false,
			'layout'            => 'boxed',
			'button_text'       => __( 'Read More', 'cherry-services' ),
			'order_button_text' => __( 'Order', 'cherry-services' ),
			'template'          => 'default.tmpl',
			'item_class'        => 'cherry-services_item',
			'col_xs'            => '12',
			'col_sm'            => '12',
			'col_md'            => '12',
			'col_lg'            => '12',
			'container'         => '<div class="services-listing">%s</div>',
		), $args );

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments.
		 */
		$args = apply_filters( 'cherry_the_services_args', $args );

		$output = '';

		/**
		 * Fires before the services listing.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_services_before', $args );

		// The Query.
		$query = $this->get_services( $args );

		global $wp_query;

		$this->temp_query = $wp_query;
		$wp_query = null;
		$wp_query = $query;

		// Fix boolean.
		if ( isset( $args['pager'] ) && ( ( 'true' == $args['pager'] ) || true === $args['pager'] ) ) {
			$args['pager'] = true;
		} else {
			$args['pager'] = false;
		}

		// The Display.
		if ( is_wp_error( $query ) ) {
			return;
		}

		$css_class = '';

		if ( ! empty( $args['wrap_class'] ) ) {
			$css_class .= esc_attr( $args['wrap_class'] ) . ' ';
		}

		if ( ! empty( $args['class'] ) ) {
			$css_class .= esc_attr( $args['class'] ) . ' ';
		}

		$css_class .= esc_attr( $args['layout'] ) . '-layout';

		// Open wrapper.
		$output .= sprintf( '<div class="%s">', trim( $css_class ) );

		if ( ! empty( $args['title'] ) ) {
			$output .= $args['before_title'] . $args['title'] . $args['after_title'];
		}

		$collapsed = ( 'pricing-table' == $args['layout'] ) ? ' collapse-row' : '';

		if ( false !== $args['columns'] ) {
			$output .= '<div class="row' . $collapsed . '">';
		}

		if ( false !== $args['container'] ) {
			$output .= sprintf( $args['container'], $this->get_services_loop( $query, $args ) );
		} else {
			$output .= $this->get_services_loop( $query, $args );
		}

		if ( false !== $args['columns'] ) {
			$output .= '</div>';
		}

		// Close wrapper.
		$output .= '</div>';

		if ( true == $args['pager'] ) {
			$output .= get_the_posts_pagination();
		}

		$wp_query = null;
		$wp_query = $this->temp_query;

		/**
		 * Filters HTML-formatted services before display or return.
		 *
		 * @since 1.0.0
		 * @param string $output The HTML-formatted services.
		 * @param array  $query  List of WP_Post objects.
		 * @param array  $args   The array of arguments.
		 */
		$output = apply_filters( 'cherry_services_html', $output, $query, $args );

		wp_reset_query();
		wp_reset_postdata();

		if ( true !== $args['echo'] ) {
			return $output;
		}

		// If "echo" is set to true.
		echo $output;

		/**
		 * Fires after the services listing.
		 *
		 * This hook fires only when "echo" is set to true.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_services_after', $args );
	}

	/**
	 * Get services.
	 *
	 * @since  1.0.0
	 * @param  array|string $args Arguments to be passed to the query.
	 * @return array|bool         Array if true, boolean if false.
	 */
	public function get_services( $args = '' ) {

		$defaults = array(
			'limit'   => 5,
			'orderby' => 'date',
			'order'   => 'DESC',
			'id'      => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		// The Query Arguments.
		$this->query_args['post_type']        = CHERRY_SERVICES_NAME;
		$this->query_args['posts_per_page']   = $args['limit'];
		$this->query_args['orderby']          = $args['orderby'];
		$this->query_args['order']            = $args['order'];
		$this->query_args['suppress_filters'] = false;

		if ( ! empty( $args['categories'] ) ) {
			$cats = str_replace( ' ', ',', $args['categories'] );
			$cats = explode( ',', $cats );

			if ( is_array( $cats ) ) {
				$this->query_args['tax_query'] = array(
					array(
						'taxonomy' => CHERRY_SERVICES_NAME . '_category',
						'field'    => 'slug',
						'terms'    => $cats,
					),
				);
			}
		} else {
			$this->query_args['tax_query'] = false;
		}

		if ( isset( $args['pager'] ) && ( 'true' == $args['pager'] ) ) :

			if ( get_query_var( 'paged' ) ) {
				$this->query_args['paged'] = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$this->query_args['paged'] = get_query_var( 'page' );
			} else {
				$this->query_args['paged'] = 1;
			}

		endif;

		$ids = explode( ',', $args['id'] );

		if ( 0 < intval( $args['id'] ) && 0 < count( $ids ) ) :

			$ids = array_map( 'intval', $ids );

			if ( 1 == count( $ids ) && is_numeric( $ids[0] ) && ( 0 < intval( $ids[0] ) ) ) {

				$this->query_args['p'] = intval( $args['id'] );

			} else {

				$this->query_args['ignore_sticky_posts'] = 1;
				$this->query_args['post__in'] = $ids;

			}

		endif;

		// Whitelist checks.
		if ( ! in_array( $this->query_args['orderby'], array( 'none', 'ID', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order', 'meta_value', 'meta_value_num' ) ) ) {
			$this->query_args['orderby'] = 'date';
		}

		if ( ! in_array( strtoupper( $this->query_args['order'] ), array( 'ASC', 'DESC' ) ) ) {
			$this->query_args['order'] = 'DESC';
		}

		/**
		 * Filters the query.
		 *
		 * @since 1.0.0
		 * @param array The array of query arguments.
		 * @param array The array of arguments to be passed to the query.
		 */
		$this->query_args = apply_filters( 'cherry_get_services_query_args', $this->query_args, $args );

		// The Query.
		$query = new WP_Query( $this->query_args );

		if ( ! $query->have_posts() ) {
			return false;
		}

		return $query;

	}

	/**
	 * Callback to replace macros with data
	 *
	 * @since  1.0.0
	 *
	 * @param  array $matches found macros.
	 */
	public function replace_callback( $matches ) {

		if ( ! is_array( $matches ) ) {
			return '';
		}

		if ( empty( $matches ) ) {
			return '';
		}

		$key = strtolower( $matches[1] );

		// if key not found in data -return nothing
		if ( ! isset( $this->post_data[ $key ] ) ) {
			return '';
		}

		$callback = $this->post_data[ $key ];

		if ( ! is_callable( $callback ) ) {
			return;
		}

		// if found parameters and has correct callback - process it
		if ( isset( $matches[3] ) ) {
			return call_user_func( $callback, $matches[3] );
		}

		return call_user_func( $callback );

	}

	/**
	 * Get services items.
	 *
	 * @since  1.0.0
	 * @param  array $query WP_query object.
	 * @param  array $args  The array of arguments.
	 * @return string
	 */
	public function get_services_loop( $query, $args ) {

		global $post, $more;

		// Item template.
		$template = $this->get_template_by_name( $args['template'], Cherry_Services_Shortcode::$name );

		/**
		 * Filters template for services item.
		 *
		 * @since 1.0.0
		 * @param string.
		 * @param array   Arguments.
		 */
		$template = apply_filters( 'cherry_services_item_template', $template, $args );

		$count     = 1;
		$output    = '';
		$macros    = '/%%([a-zA-Z]+[^%]{2})(=[\'\"]([a-zA-Z0-9-_\s]+)[\'\"])?%%/';
		$callbacks = $this->setup_template_data( $args );

		if ( ! $query || ! is_object( $query ) ) {
			return __( 'No services found', 'cherry-services' );
		}

		foreach ( $query->posts as $post ) {

			// Sets up global post data.
			setup_postdata( $post );

			$tpl     = $template;
			$post_id = $post->ID;

			$tpl = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $tpl );

			$item_classes   = array( $args['item_class'], 'item-' . $count, 'clearfix' );
			$item_classes[] = ( $count % 2 ) ? 'odd' : 'even';

			foreach ( array( 'col_xs', 'col_sm', 'col_md', 'col_lg' ) as $col ) {
				if ( ! $args[ $col ] || 'none' == $args[ $col ] ) {
					continue;
				}
				$item_classes[] = str_replace( '_', '-', $col ) . '-' . absint( $args[ $col ] );
				$item_classes[] = ( ( $count - 1 ) % floor( 12 / absint( $args[ $col ] ) ) ) ? '' : 'clear-' . str_replace( '_', '-', $col );
			}

			$collapsed = ( 'pricing-table' == $args['layout'] ) ? 'collapse-col' : '';
			$item_classes[] = $collapsed;

			$count++;

			$meta = get_post_meta( $post_id, CHERRY_SERVICES_POSTMETA, true );

			if ( isset( $meta['is-featured'] ) && 'true' == $meta['is-featured'] ) {
				$item_classes[] = 'featured-service';
			}

			$item_class = implode( ' ', array_filter( $item_classes ) );

			$output .= '<div id="services-' . $post_id . '" class="' . $item_class . '">';

				/**
				 * Filters services items.
				 *
				 * @since 1.0.0
				 * @param string.
				 * @param array  array of arguments.
				 */
				$tpl = apply_filters( 'cherry_get_services_loop', $tpl, $args );

				$output .= $tpl;

			$output .= '</div><!--/.services-item-->';

			$callbacks->clear_data();

		}

		// Restore the global $post variable.
		wp_reset_postdata();

		return $output;
	}

	/**
	 * Prepare template data to replace
	 *
	 * @since  1.0.0
	 * @param  array $atts output attributes.
	 * @return object
	 */
	function setup_template_data( $atts ) {

		require_once( CHERRY_SERVICES_DIR . 'public/includes/class-cherry-services-template-callbacks.php' );

		$callbacks = new Cherry_Services_Template_Callbacks( $atts );

		$data = array(
			'title'    => array( $callbacks, 'get_title' ),
			'image'    => array( $callbacks, 'get_image' ),
			'icon'     => array( $callbacks, 'get_icon' ),
			'content'  => array( $callbacks, 'get_content' ),
			'excerpt'  => array( $callbacks, 'get_excerpt' ),
			'features' => array( $callbacks, 'get_features' ),
			'price'    => array( $callbacks, 'get_price' ),
			'order'    => array( $callbacks, 'get_order_button' ),
			'more'     => array( $callbacks, 'get_more_button' ),
		);

		$this->post_data = apply_filters( 'cherry_services_shortcode_data_callbacks', $data, $atts );

		return $callbacks;

	}

	/**
	 * Read template (static).
	 *
	 * @since  1.0.0
	 * @return bool|WP_Error|string - false on failure, stored text on success.
	 */
	public static function get_contents( $template ) {

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/file.php' );
		}

		WP_Filesystem();
		global $wp_filesystem;

		if ( ! $wp_filesystem->exists( $template ) ) { // Check for existence.
			return false;
		}

		// Read the file.
		$content = $wp_filesystem->get_contents( $template );

		if ( ! $content ) {
			return new WP_Error( 'reading_error', 'Error when reading file' ); // Return error object.
		}

		return $content;
	}

	/**
	 * Get template file by name
	 *
	 * @since  1.0.0
	 *
	 * @param  string $template  template name.
	 * @param  string $shortcode shortcode name.
	 * @return string
	 */
	public function get_template_by_name( $template, $shortcode ) {

		$file       = '';
		$subdir     = 'templates/shortcodes/' . $shortcode . '/' . $template;
		$default    = CHERRY_SERVICES_DIR . 'templates/shortcodes/' . $shortcode . '/default.tmpl';
		$upload_dir = wp_upload_dir();
		$basedir    = $upload_dir['basedir'];

		$content = apply_filters(
			'cherry_services_fallback_template',
			'%%photo%%<div>%%title%%</div><div>%%excerpt%%</div>'
		);

		if ( file_exists( trailingslashit( $basedir ) . $subdir ) ) {
			$file = trailingslashit( $basedir ) . $subdir;
		} elseif ( file_exists( CHERRY_SERVICES_DIR . $subdir ) ) {
			$file = CHERRY_SERVICES_DIR . $subdir;
		} else {
			$file = $default;
		}

		if ( ! empty( $file ) ) {
			$content = self::get_contents( $file );
		}

		return $content;
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
