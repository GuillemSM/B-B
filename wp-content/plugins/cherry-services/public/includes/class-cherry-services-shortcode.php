<?php
/**
 * Cherry Services Shortcode.
 *
 * @package   Cherry_Services
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Services shortcode.
 *
 * @since 1.0.0
 */
class Cherry_Services_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'services';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Storage for data object
	 * @since 1.0.0
	 * @var   null|object
	 */
	public $data = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register shortcode on 'init'.
		add_action( 'init', array( $this, 'register_shortcode' ) );

		// Register shortcode and add it to the dialog.
		add_filter( 'cherry_shortcodes/data/shortcodes', array( $this, 'shortcodes' ) );
		add_filter( 'cherry_templater/data/shortcodes',  array( $this, 'shortcodes' ) );

		add_filter( 'cherry_templater_target_dirs', array( $this, 'add_target_dir' ), 11 );
		add_filter( 'cherry_templater_macros_buttons', array( $this, 'add_macros_buttons' ), 11, 2 );

		// Modify swiper_carousel shortcode to allow it process services
		add_filter( 'cherry_shortcodes_add_carousel_macros', array( $this, 'extend_carousel_macros' ) );
		add_filter( 'cherry-shortcode-swiper-carousel-postdata', array( $this, 'add_carousel_data' ), 10, 3 );

		$this->data = Cherry_Services_Data::get_instance();
	}

	/**
	 * Registers the [$this->name] shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {

		/**
		 * Filters a shortcode name.
		 *
		 * @since 1.0.0
		 * @param string $this->name Shortcode name.
		 */
		$tag = apply_filters( self::$name . '_shortcode_name', self::$name );

		add_shortcode( $tag, array( $this, 'do_shortcode' ) );
	}

	/**
	 * Filter to modify original shortcodes data and add [$this->name] shortcode.
	 *
	 * @since  1.0.0
	 * @param  array $shortcodes Original plugin shortcodes.
	 * @return array             Modified array.
	 */
	public function shortcodes( $shortcodes ) {
		$terms_list = array();

		if ( did_action( 'wp_ajax_cherry_shortcodes_generator_settings' ) ) {
			$terms = get_terms( CHERRY_SERVICES_NAME . '_category' );

			if ( ! is_wp_error( $terms ) ) {
				$terms_list = wp_list_pluck( $terms, 'name', 'slug' );
			}
		}

		$sizes_list = array();
		if ( class_exists( 'Cherry_Shortcodes_Tools' ) && method_exists( 'Cherry_Shortcodes_Tools', 'image_sizes' ) ) {
			$sizes_list = Cherry_Shortcodes_Tools::image_sizes();
		}

		$shortcodes[ self::$name ] = array(
			'name'  => __( 'Services', 'cherry-services' ), // Shortcode name.
			'desc'  => __( 'Cherry services shortcode', 'cherry-services' ),
			'type'  => 'single', // Can be 'wrap' or 'single'. Example: [b]this is wrapped[/b], [this_is_single]
			'group' => 'content', // Can be 'content', 'box', 'media' or 'other'. Groups can be mixed
			'atts'  => array( // List of shortcode params (attributes).
				'limit' => array(
					'type'    => 'slider',
					'min'     => -1,
					'max'     => 100,
					'step'    => 1,
					'default' => 3,
					'name'    => __( 'Limit', 'cherry-services' ),
					'desc'    => __( 'Maximum number of services.', 'cherry-services' ),
				),
				'order' => array(
					'type' => 'select',
					'values' => array(
						'desc' => __( 'Descending', 'cherry-services' ),
						'asc'  => __( 'Ascending', 'cherry-services' ),
					),
					'default' => 'DESC',
					'name' => __( 'Order', 'cherry-services' ),
					'desc' => __( 'Posts order', 'cherry-services' ),
				),
				'orderby' => array(
					'type' => 'select',
					'values' => array(
						'none'          => __( 'None', 'cherry-services' ),
						'id'            => __( 'Post ID', 'cherry-services' ),
						'author'        => __( 'Post author', 'cherry-services' ),
						'title'         => __( 'Post title', 'cherry-services' ),
						'name'          => __( 'Post slug', 'cherry-services' ),
						'date'          => __( 'Date', 'cherry-services' ),
						'modified'      => __( 'Last modified date', 'cherry-services' ),
						'rand'          => __( 'Random', 'cherry-services' ),
						'comment_count' => __( 'Comments number', 'cherry-services' ),
						'menu_order'    => __( 'Menu order', 'cherry-services' ),
					),
					'default' => 'date',
					'name'    => __( 'Order by', 'cherry-services' ),
					'desc'    => __( 'Order posts by', 'cherry-services' ),
				),
				'id' => array(
					'default' => 0,
					'name'    => __( 'Post ID\'s', 'cherry-services' ),
					'desc'    => __( 'Enter comma separated ID\'s of the posts that you want to show', 'cherry-services' ),
				),
				'categories' => array(
					'type'     => 'select',
					'multiple' => true,
					'values'   => $terms_list,
					'default'  => '',
					'name'     => __( 'Categories', 'cherry-services' ),
					'desc'     => __( 'Select categories to show services from', 'cherry-services' ),
				),
				'linked_title' => array(
					'type'    => 'bool',
					'default' => 'yes',
					'name'    => __( 'Title as a link to post', 'cherry-shortcodes' ),
					'desc'    => __( 'Linked title or plain text', 'cherry-shortcodes' ),
				),
				'show_media' => array(
					'type' => 'select',
					'values' => array(
						'none'  => __( 'None', 'cherry-services' ),
						'image' => __( 'Image', 'cherry-services' ),
						'icon'  => __( 'Icon', 'cherry-services' ),
					),
					'default' => 'icon',
					'name'    => __( 'Show media', 'cherry-services' ),
					'desc'    => __( 'Select what media attachment to show', 'cherry-services' ),
				),
				'size' => array(
					'type'    => 'select',
					'values'  => $sizes_list,
					'default' => 'thumbnail',
					'name'    => __( 'Featured image size', 'cherry-team' ),
					'desc'    => __( 'Select size for a Featured image', 'cherry-team' ),
				),
				'excerpt_length' => array(
					'type'    => 'slider',
					'min'     => 5,
					'max'     => 150,
					'step'    => 1,
					'default' => 20,
					'name'    => __( 'Excerpt Length', 'cherry-services' ),
					'desc'    => __( 'Excerpt length (if used in template)', 'cherry-services' ),
				),
				'button_text' => array(
					'default' => __( 'Read More', 'cherry-services' ),
					'name'    => __( 'More Button text', 'cherry-services' ),
					'desc'    => __( 'Enter read more button text', 'cherry-services' ),
				),
				'order_button_text' => array(
					'default' => __( 'Order', 'cherry-services' ),
					'name'    => __( 'Order Button text', 'cherry-services' ),
					'desc'    => __( 'Enter order button text', 'cherry-services' ),
				),
				'layout' => array(
					'type' => 'select',
					'values' => array(
						'boxes'         => __( 'Boxes', 'cherry-services' ),
						'pricing-table' => __( 'Pricing Table', 'cherry-services' ),
					),
					'default' => 'boxes',
					'name'    => __( 'Layout type', 'cherry-services' ),
					'desc'    => __( 'Select layout type', 'cherry-services' ),
				),
				'col' => array(
					'type'    => 'responsive',
					'default' => array(
						'col_xs' => 'none',
						'col_sm' => 'none',
						'col_md' => 'none',
						'col_lg' => 'none',
					),
					'name'    => __( 'Column class', 'cherry-team' ),
					'desc'    => __( 'Column class for each item.', 'cherry-team' ),
				),
				'template' => array(
					'type'   => 'select',
					'values' => array(
						'default.tmpl' => 'default.tmpl',
					),
					'default' => 'default.tmpl',
					'name'    => __( 'Template', 'cherry-services' ),
					'desc'    => __( 'Shortcode template', 'cherry-services' ),
				),
				'class' => array(
					'default' => '',
					'name'    => __( 'Class', 'cherry-services' ),
					'desc'    => __( 'Extra CSS class', 'cherry-services' ),
				),
			),
			'icon'     => 'cogs',
			'function' => array( $this, 'do_shortcode' ),
		);

		return $shortcodes;
	}

	/**
	 * Add services specific macros buttons into caousel shortcode
	 *
	 * @since  1.0.0
	 * @param  array $macros_buttons default macros buttons.
	 * @return array
	 */
	public function extend_carousel_macros( $macros_buttons ) {

		$macros_buttons['icon'] = array(
			'id'    => 'cherry_icon',
			'value' => __( 'Icon (Services only)', 'cherry-services' ),
			'open'  => '%%ICON%%',
			'close' => '',
		);
		$macros_buttons['features'] = array(
			'id'    => 'cherry_features',
			'value' => __( 'Features (Services only)', 'cherry-services' ),
			'open'  => '%%FEATURES%%',
			'close' => '',
		);
		$macros_buttons['price'] = array(
			'id'    => 'cherry_price',
			'value' => __( 'Price (Services only)', 'cherry-services' ),
			'open'  => '%%PRICE%%',
			'close' => '',
		);
		$macros_buttons['order'] = array(
			'id'    => 'cherry_order',
			'value' => __( 'Order button (Services only)', 'cherry-services' ),
			'open'  => '%%ORDER%%',
			'close' => '',
		);

		return $macros_buttons;
	}

	/**
	 * Add services macros data to process it in carousel shortcode
	 *
	 * @since  1.0.0
	 *
	 * @param  array $postdata  default data.
	 * @param  array $post_id   processed post ID.
	 * @param  array $atts      shortcode attributes.
	 * @return array
	 */
	public function add_carousel_data( $postdata, $post_id, $atts ) {

		require_once( CHERRY_SERVICES_DIR . 'public/includes/class-cherry-services-template-callbacks.php' );
		$callbacks = new Cherry_Services_Template_Callbacks( $atts );

		$postdata['icon']     = $callbacks->get_icon();
		$postdata['features'] = $callbacks->get_features();
		$postdata['price']    = $callbacks->get_price();
		$postdata['order']    = $callbacks->get_order_button();

		return $postdata;

	}

	/**
	 * Adds services template directory to shortcodes templater
	 *
	 * @since  1.0.0
	 * @param  array $target_dirs existing target dirs.
	 * @return array
	 */
	public function add_target_dir( $target_dirs ) {

		array_push( $target_dirs, CHERRY_SERVICES_DIR );
		return $target_dirs;

	}

	/**
	 * Add services shortcode macros buttons to templater
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $macros_buttons current buttons array.
	 * @param  string $shortcode      shortcode name.
	 * @return array
	 */
	public function add_macros_buttons( $macros_buttons, $shortcode ) {

		if ( self::$name != $shortcode ) {
			return $macros_buttons;
		}

		$macros_buttons = array(
			'title' => array(
				'id'    => 'cherry_title',
				'value' => __( 'Title', 'cherry-services' ),
				'open'  => '%%TITLE%%',
				'close' => '',
			),
			'image' => array(
				'id'    => 'cherry_image',
				'value' => __( 'Image', 'cherry-services' ),
				'open'  => '%%IMAGE%%',
				'close' => '',
			),
			'icon' => array(
				'id'    => 'cherry_icon',
				'value' => __( 'Icon', 'cherry-services' ),
				'open'  => '%%ICON%%',
				'close' => '',
			),
			'content' => array(
				'id'    => 'cherry_content',
				'value' => __( 'Content', 'cherry-services' ),
				'open'  => '%%CONTENT%%',
				'close' => '',
			),
			'excerpt' => array(
				'id'    => 'cherry_excerpt',
				'value' => __( 'Short description', 'cherry-services' ),
				'open'  => '%%EXCERPT%%',
				'close' => '',
			),
			'features' => array(
				'id'    => 'cherry_features',
				'value' => __( 'Features', 'cherry-services' ),
				'open'  => '%%FEATURES%%',
				'close' => '',
			),
			'price' => array(
				'id'    => 'cherry_price',
				'value' => __( 'Price', 'cherry-services' ),
				'open'  => '%%PRICE%%',
				'close' => '',
			),
			'order' => array(
				'id'    => 'cherry_order',
				'value' => __( 'Order button', 'cherry-services' ),
				'open'  => '%%ORDER%%',
				'close' => '',
			),
			'more' => array(
				'id'    => 'cherry_more',
				'value' => __( 'More button', 'cherry-services' ),
				'open'  => '%%MORE%%',
				'close' => '',
			),
		);

		return $macros_buttons;

	}

	/**
	 * The shortcode function.
	 *
	 * @since  1.0.0
	 * @param  array  $atts      The user-inputted arguments.
	 * @param  string $content   The enclosed content (if the shortcode is used in its enclosing form).
	 * @param  string $shortcode The shortcode tag, useful for shared callback functions.
	 * @return string
	 */
	public function do_shortcode( $atts, $content = null, $shortcode = 'services' ) {

		// Set up the default arguments.
		$defaults = array(
			'limit'             => 3,
			'orderby'           => 'date',
			'order'             => 'DESC',
			'categories'        => '',
			'id'                => 0,
			'show_media'        => 'icon',
			'size'              => 'thumbnail',
			'linked_title'      => 'yes',
			'excerpt_length'    => 20,
			'button_text'       => __( 'Read More', 'cherry-services' ),
			'order_button_text' => __( 'Order', 'cherry-services' ),
			'layout'            => 'boxed',
			'echo'              => false,
			'template'          => 'default.tmpl',
			'col_xs'            => '12',
			'col_sm'            => '6',
			'col_md'            => '3',
			'col_lg'            => 'none',
			'class'             => '',
		);

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */
		$atts = shortcode_atts( $defaults, $atts, $shortcode );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		// Fix integers.
		if ( isset( $atts['limit'] ) ) {
			$atts['limit'] = intval( $atts['limit'] );
		}

		if ( isset( $atts['size'] ) &&  ( 0 < intval( $atts['size'] ) ) ) {
			$atts['size'] = intval( $atts['size'] );
		} else {
			$atts['size'] = esc_attr( $atts['size'] );
		}

		$atts['before_title'] = '<h3 class="cherry-services_title">';
		$atts['after_title']  = '</h3>';

		$atts['layout'] = in_array( $atts['layout'], array( 'boxed', 'pricing-table' ) ) ? $atts['layout'] : 'boxed';

		return $this->data->the_services( $atts );
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

Cherry_Services_Shortcode::get_instance();
