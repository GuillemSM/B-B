<?php
/**
 * Cherry Testimonials Data class.
 *
 * @package   Cherry_Testimonials
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2012 - 2015, Cherry Team
 */

/**
 * Class for Testimonials data.
 *
 * @since 1.0.0
 */
class Cherry_Testimonials_Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.2
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * The array of arguments for query.
	 *
	 * @since 1.0.2
	 * @var   array
	 */
	private $query_args = array();

	/**
	 * Holder for the main query object, while team query processing
	 *
	 * @since 1.0.2
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
		 * Fires when you need to display testimonials.
		 *
		 * @since 1.0.0
		 */
		add_action( 'cherry_get_testimonials', array( $this, 'the_testimonials' ) );
	}

	/**
	 * Display or return HTML-formatted testimonials.
	 *
	 * @since  1.0.0
	 * @param  string|array $args Arguments.
	 * @return string
	 */
	public function the_testimonials( $args = '' ) {
		/**
		 * Filter the array of default arguments.
		 *
		 * @since 1.0.0
		 * @param array Default arguments.
		 * @param array The 'the_testimonials' function argument.
		 */
		$defaults = apply_filters( 'cherry_the_testimonials_default_args', array(
			'limit'          => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'category'       => '',
			'id'             => 0,
			'display_author' => true,
			'display_avatar' => true,
			'clickable_url'  => false,
			'size'           => 50,
			'content_type'   => 'full',
			'content_length' => 55,
			'echo'           => true,
			'title'          => '',
			'container'      => '<div class="testimonials-list">%s</div>',
			'wrap_class'     => 'testimonials-wrap',
			'before_title'   => '<h2>',
			'after_title'    => '</h2>',
			'pager'          => false,
			'template'       => 'default.tmpl',
			'custom_class'   => '',
		), $args );

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments.
		 */
		$args = apply_filters( 'cherry_the_testimonials_args', $args );
		$output = '';

		/**
		 * Fires before the Testimonials.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_testimonials_before', $args );

		// The Query.
		$query = $this->get_testimonials( $args );

		global $wp_query;

		$this->temp_query = $wp_query;
		$wp_query = null;
		$wp_query = $query;

		$all_posts = '';

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

		if ( ! empty( $args['template'] ) ) {
			$css_class .= $this->get_template_class( $args['template'] ) . ' ';
		}

		if ( ! empty( $args['custom_class'] ) ) {
			$css_class .= esc_attr( $args['custom_class'] );
		}

		// Open wrapper.
		$output .= sprintf( '<div class="%s">', trim( $css_class ) );

		if ( ! empty( $args['title'] ) ) {
			$output .= $args['before_title'] . $args['title'] . $args['after_title'];
		}

		if ( false !== $args['container'] ) {
			$output .= sprintf( $args['container'], $this->get_testimonials_loop( $query, $args ) );
		} else {
			$output .= $this->get_team_loop( $query, $args );
		}

		// Close wrapper.
		$output .= '</div>';

		if ( true == $args['pager'] ) {
			$output .= get_the_posts_pagination();
		}

		$wp_query = null;
		$wp_query = $this->temp_query;

		/**
		 * Filters HTML-formatted testimonials before display or return.
		 *
		 * @since 1.0.0
		 * @param string $output The HTML-formatted testimonials.
		 * @param array  $query  List of WP_Post objects.
		 * @param array  $args   The array of arguments.
		 */
		$output = apply_filters( 'cherry_testimonials_html', $output, $query, $args );

		wp_reset_query();
		wp_reset_postdata();

		if ( true != $args['echo'] ) {
			return $output;
		}

		// If "echo" is set to true.
		echo $output;

		/**
		 * Fires after the Testimonials.
		 *
		 * This hook fires only when "echo" is set to true.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_testimonials_after', $args );
	}

	/**
	 * Get testimonials.
	 *
	 * @since  1.0.0
	 * @param  array|string $args Arguments to be passed to the query.
	 * @return array|bool         Array if true, boolean if false.
	 */
	public function get_testimonials( $args = '' ) {

		$defaults = array(
			'limit'   => 5,
			'orderby' => 'date',
			'order'   => 'DESC',
			'id'      => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments to be passed to the query.
		 */
		$args = apply_filters( 'cherry_get_testimonials_args', $args );

		// The Query Arguments.
		$this->query_args['post_type']        = CHERRY_TESTI_NAME;
		$this->query_args['posts_per_page']   = $args['limit'];
		$this->query_args['orderby']          = $args['orderby'];
		$this->query_args['order']            = $args['order'];
		$this->query_args['suppress_filters'] = false;

		if ( ! empty( $args['category'] ) ) {
			$category = str_replace( ' ', ',', $args['category'] );
			$category = explode( ',', $category );

			if ( is_array( $category ) ) {
				$this->query_args['tax_query'] = array(
					array(
						'taxonomy' => CHERRY_TESTI_NAME . '_category',
						'field'    => 'slug',
						'terms'    => $category,
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
				$this->query_args['post__in']            = $ids;

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
		$this->query_args = apply_filters( 'cherry_get_testimonials_query_args', $this->query_args, $args );

		// The Query.
		$query = new WP_Query( $this->query_args );

		if ( ! $query->have_posts() ) {
			return false;
		}

		return $query;
	}

	/**
	 * Get the image for the given ID. If no featured image, check for Gravatar e-mail.
	 *
	 * @since  1.0.0
	 * @param  int              $id   The post ID.
	 * @param  string|array|int $size The image dimension.
	 * @return string
	 */
	public static function get_image( $id, $size ) {
		$image = '';

		if ( has_post_thumbnail( $id ) ) {

			// If not a string or an array, and not an integer, default to 150x9999.
			if ( ( is_int( $size ) || ( 0 < intval( $size ) ) ) && ! is_array( $size ) ) {
				$size = array( intval( $size ), intval( $size ) );
			} elseif ( ! is_string( $size ) && ! is_array( $size ) ) {
				$size = array( 50, 50 );
			}

			$image = get_the_post_thumbnail( intval( $id ), $size, array( 'class' => 'avatar' ) );

			return $image;
		}

		$post_meta = get_post_meta( $id, CHERRY_TESTI_POSTMETA, true );

		if ( empty( $post_meta ) ) {
			return;
		}

		if ( empty( $post_meta['email'] ) ) {
			return;
		}

		$email = $post_meta['email'];

		if ( ! is_email( $email ) ) {
			return;
		}

		$image = get_avatar( $email, $size );

		return $image;
	}

	/**
	 * Callback to replace macros with data.
	 *
	 * @since 1.0.0
	 * @param array $matches Founded macros.
	 */
	public function replace_callback( $matches ) {

		if ( ! is_array( $matches ) ) {
			return;
		}

		if ( empty( $matches ) ) {
			return;
		}

		$key = strtolower( $matches[1] );

		// If key not found in data - return nothing.
		if ( ! isset( $this->post_data[ $key ] ) ) {
			return;
		}

		$callback = $this->post_data[ $key ];

		if ( ! is_callable( $callback ) ) {
			return;
		}

		// If found parameters and has correct callback - process it.
		if ( isset( $matches[3] ) ) {
			return call_user_func( $callback, $matches[3] );
		}

		return call_user_func( $callback );
	}

	/**
	 * Get testimonials items.
	 *
	 * @since  1.0.0
	 * @param  array $query WP_query object.
	 * @param  array $args  The array of arguments.
	 * @return string
	 */
	public function get_testimonials_loop( $query, $args ) {
		global $post, $more;

		// Item template.
		$template = $this->get_template_by_name( $args['template'], Cherry_Testimonials_Shortcode::$name );

		/**
		 * Filters template for testimonials item.
		 *
		 * @since 1.0.0
		 * @param string $template.
		 * @param array  $args.
		 */
		$template = apply_filters( 'cherry_testimonials_item_template', $template, $args );

		$count  = 1;
		$output = '';

		if ( ! is_object( $query ) || ! is_array( $query->posts ) ) {
			return false;
		}

		$macros    = '/%%([a-zA-Z]+[^%]{2})(=[\'\"]([a-zA-Z0-9-_\s]+)[\'\"])?%%/';
		$callbacks = $this->setup_template_data( $args );

		foreach ( $query->posts as $post ) {

			// Sets up global post data.
			setup_postdata( $post );

			$tpl     = $template;
			$post_id = $post->ID;
			$tpl     = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $tpl );

			$output .= '<div id="quote-' . $post_id . '" class="testimonials-item item-' . $count . ( ( $count++ % 2 ) ? ' odd' : ' even' ) . ' clearfix">';

				/**
				 * Filters testimonails item.
				 *
				 * @since 1.0.0
				 * @param string $tpl.
				 */
				$tpl = apply_filters( 'cherry_get_testimonails_loop', $tpl );

				$output .= $tpl;

			$output .= '</div>';

			$callbacks->clear_data();
		}

		// Restore the global $post variable.
		wp_reset_postdata();

		return $output;
	}

	/**
	 * Prepare template data to replace.
	 *
	 * @since 1.0.2
	 * @param array $atts Output attributes.
	 */
	function setup_template_data( $atts ) {
		require_once( CHERRY_TESTI_DIR . 'public/includes/class-cherry-testimonials-template-callbacks.php' );

		$callbacks = new Cherry_Testimonials_Template_Callbacks( $atts );

		$data = array(
			'avatar'   => array( $callbacks, 'get_avatar' ),
			'content'  => array( $callbacks, 'get_content' ),
			'author'   => array( $callbacks, 'get_author' ),
			'email'    => array( $callbacks, 'get_email' ),
			'name'     => array( $callbacks, 'get_name' ),
			'url'      => array( $callbacks, 'get_url' ),
			'position' => array( $callbacks, 'get_position' ),
			'company'  => array( $callbacks, 'get_company' ),
		);

		/**
		 * Filters item data.
		 *
		 * @since 1.0.2
		 * @param array $data Item data.
		 * @param array $atts Attributes.
		 */
		$this->post_data = apply_filters( 'cherry_testimonials_data_callbacks', $data, $atts );

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

		// Check for existence.
		if ( ! $wp_filesystem->exists( $template ) ) {
			return false;
		}

		// Read the file.
		$content = $wp_filesystem->get_contents( $template );

		if ( ! $content ) {
			// Return error object.
			return new WP_Error( 'reading_error', 'Error when reading file' );
		}

		return $content;
	}

	/**
	 * Retrieve a *.tmpl file content.
	 *
	 * @since  1.0.0
	 * @param  string $template  File name.
	 * @param  string $shortcode Shortcode name.
	 * @return string
	 */
	public function get_template_by_name( $template, $shortcode ) {
		$file       = '';
		$default    = CHERRY_TESTI_DIR . 'templates/shortcodes/' . $shortcode . '/default.tmpl';
		$upload_dir = wp_upload_dir();
		$upload_dir = trailingslashit( $upload_dir['basedir'] );
		$subdir     = 'templates/shortcodes/' . $shortcode . '/' . $template;

		/**
		 * Filters a default fallback-template.
		 *
		 * @since 1.0.0
		 * @param string $content.
		 */
		$content = apply_filters( 'cherry_testimonials_fallback_template', '%%avatar%%<blockquote>%%content%% %%author%%</blockquote>' );

		if ( file_exists( $upload_dir . $subdir ) ) {
			$file = $upload_dir . $subdir;
		} elseif ( file_exists( CHERRY_TESTI_DIR . $subdir ) ) {
			$file = CHERRY_TESTI_DIR . $subdir;
		} else {
			$file = $default;
		}

		if ( ! empty( $file ) ) {
			$content = self::get_contents( $file );
		}

		return $content;
	}

	/**
	 * Get CSS class name for shortcode by template name.
	 *
	 * @since  1.1.0
	 * @param  string $template Template name.
	 * @return string|bool
	 */
	public function get_template_class( $template ) {

		if ( ! $template ) {
			return false;
		}

		/**
		 * Filters a CSS-class prefix.
		 *
		 * Use the same filter for all cherry-related shortcodes.
		 *
		 * @since 1.1.0
		 * @param string $prefix.
		 */
		$prefix = apply_filters( 'cherry_shortcodes_template_class_prefix', 'template' );
		$class  = sprintf( '%s-%s', esc_attr( $prefix ), esc_attr( str_replace( '.tmpl', '', $template ) ) );

		return $class;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.2
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
