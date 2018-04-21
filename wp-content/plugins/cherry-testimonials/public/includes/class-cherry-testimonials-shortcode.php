<?php
/**
 * New shortcode registration.
 *
 * @package   Cherry_Testimonials
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2012 - 2015, Cherry Team
 */

/**
 * Class for Testimonials shortcode.
 *
 * @since 1.0.0
 */
class Cherry_Testimonials_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'testimonials';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Storage for data object
	 *
	 * @since 1.0.2
	 * @var   null|object
	 */
	public $data = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_shortcode' ) );

		// Register shortcode and add it to the dialog.
		add_filter( 'cherry_shortcodes/data/shortcodes', array( $this, 'shortcodes' ) );
		add_filter( 'cherry_templater/data/shortcodes',  array( $this, 'shortcodes' ) );

		add_filter( 'cherry_templater_target_dirs',      array( $this, 'add_target_dir' ),     11 );
		add_filter( 'cherry_templater_macros_buttons',   array( $this, 'add_macros_buttons' ), 11, 2 );

		// Modify `swiper_carousel` shortcode to allow it process testimonials.
		add_filter( 'cherry_shortcodes_add_carousel_macros',     array( $this, 'extend_carousel_macros' ) );
		add_filter( 'cherry-shortcode-swiper-carousel-postdata', array( $this, 'add_carousel_data' ), 10, 3 );

		$this->data = Cherry_Testimonials_Data::get_instance();
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

		add_shortcode( 'cherry_' . $tag, array( $this, 'do_shortcode' ) );
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
			$terms = get_terms( CHERRY_TESTI_NAME . '_category' );

			if ( ! is_wp_error( $terms ) ) {
				$terms_list = wp_list_pluck( $terms, 'name', 'slug' );
			}
		}

		$shortcodes[ self::$name ] = array(
			'name'  => __( 'Testimonials', 'cherry-testimonials' ), // Shortcode name.
			'desc'  => __( 'This is a Testimonials Shortcode', 'cherry-testimonials' ),
			'type'  => 'single', // Can be 'wrap' or 'single'.
			'group' => 'content', // Can be 'content', 'box', 'media' or 'other'.
			'atts'  => array( // List of shortcode params (attributes).
				'limit' => array(
					'type'    => 'slider',
					'min'     => -1,
					'max'     => 100,
					'step'    => 1,
					'default' => 3,
					'name'    => __( 'Limit', 'cherry-testimonials' ),
					'desc'    => __( 'Maximum number of posts.', 'cherry-testimonials' ),
				),
				'order' => array(
					'type'   => 'select',
					'values' => array(
						'desc' => __( 'Descending', 'cherry-testimonials' ),
						'asc'  => __( 'Ascending', 'cherry-testimonials' ),
					),
					'default' => 'DESC',
					'name'    => __( 'Order', 'cherry-testimonials' ),
					'desc'    => __( 'Posts order', 'cherry-testimonials' ),
				),
				'orderby' => array(
					'type'   => 'select',
					'values' => array(
						'none'          => __( 'None', 'cherry-testimonials' ),
						'id'            => __( 'Post ID', 'cherry-testimonials' ),
						'author'        => __( 'Post author', 'cherry-testimonials' ),
						'title'         => __( 'Post title', 'cherry-testimonials' ),
						'name'          => __( 'Post slug', 'cherry-testimonials' ),
						'date'          => __( 'Date', 'cherry-testimonials' ),
						'modified'      => __( 'Last modified date', 'cherry-testimonials' ),
						'parent'        => __( 'Post parent', 'cherry-testimonials' ),
						'rand'          => __( 'Random', 'cherry-testimonials' ),
						'comment_count' => __( 'Comments number', 'cherry-testimonials' ),
						'menu_order'    => __( 'Menu order', 'cherry-testimonials' ),
						'meta_value'    => __( 'Meta key values', 'cherry-testimonials' ),
					),
					'default' => 'date',
					'name'    => __( 'Order by', 'cherry-testimonials' ),
					'desc'    => __( 'Order posts by', 'cherry-testimonials' ),
				),
				'category' => array(
					'type'     => 'select',
					'multiple' => true,
					'values'   => $terms_list,
					'default'  => '',
					'name'     => __( 'Category', 'cherry-testimonials' ),
					'desc'     => __( 'Select category to show testimonials from', 'cherry-testimonials' ),
				),
				'id' => array(
					'default' => '',
					'name'    => __( 'Post ID\'s', 'cherry-testimonials' ),
					'desc'    => __( 'Enter comma separated ID\'s of the posts that you want to show', 'cherry-testimonials' ),
				),
				'display_author' => array(
					'type'    => 'bool',
					'default' => 'yes',
					'name'    => __( 'Display author?', 'cherry-testimonials' ),
					'desc'    => __( 'Display author?', 'cherry-testimonials' ),
				),
				'display_avatar' => array(
					'type'    => 'bool',
					'default' => 'yes',
					'name'    => __( 'Display avatar?', 'cherry-testimonials' ),
					'desc'    => __( 'Display avatar?', 'cherry-testimonials' ),
				),
				'clickable_url' => array(
					'type'    => 'bool',
					'default' => 'no',
					'name'    => __( 'Clickable URL?', 'cherry-testimonials' ),
					'desc'    => __( 'Make clickable URL', 'cherry-testimonials' ),
				),
				'size' => array(
					'type'    => 'slider',
					'min'     => 10,
					'max'     => 500,
					'step'    => 1,
					'default' => 50,
					'name'    => __( 'Avatar size', 'cherry-testimonials' ),
					'desc'    => __( 'Avatar size (in pixels)', 'cherry-testimonials' ),
				),
				'content_type' => array(
					'type' => 'select',
					'values' => array(
						'part'    => __( 'Part of content', 'cherry-testimonials' ),
						'full'    => __( 'Full content', 'cherry-testimonials' ),
					),
					'default' => 'full',
					'name'    => __( 'Post content', 'cherry-testimonials' ),
					'desc'    => __( 'Choose to display a part or full content', 'cherry-testimonials' ),
				),
				'content_length' => array(
					'type'    => 'number',
					'min'     => 1,
					'max'     => 10000,
					'step'    => 1,
					'default' => 55,
					'name'    => __( 'Content Length', 'cherry-testimonials' ),
					'desc'    => __( 'Insert the number of words you want to show in the post content.', 'cherry-testimonials' ),
				),
				'template' => array(
					'type'   => 'select',
					'values' => array(
						'default.tmpl' => 'default.tmpl',
					),
					'default' => 'default.tmpl',
					'name'    => __( 'Template', 'cherry-testimonials' ),
					'desc'    => __( 'Shortcode template', 'cherry-testimonials' ),
				),
				'custom_class' => array(
					'default' => '',
					'name'    => __( 'Class', 'cherry-testimonials' ),
					'desc'    => __( 'Extra CSS class', 'cherry-testimonials' ),
				),
			),
			'icon'     => 'quote-left', // Custom icon (font-awesome).
			'function' => array( $this, 'do_shortcode' ), // Name of shortcode function.
		);

		return $shortcodes;
	}

	/**
	 * Register a directory with *.tmpl files.
	 *
	 * @since 1.0.0
	 * @param array $target_dirs Set of directories.
	 */
	public function add_target_dir( $target_dirs ) {
		array_push( $target_dirs, CHERRY_TESTI_DIR );

		return $target_dirs;
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
	public function do_shortcode( $atts, $content = null, $shortcode = 'testimonials' ) {

		// Set up the default arguments.
		$defaults = array(
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
			'echo'           => false,
			'custom_class'   => '',
			'template'       => 'default.tmpl',
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

		// Fix booleans.
		foreach ( array( 'display_author', 'display_avatar', 'clickable_url' ) as $k => $v ) :

			if ( isset( $atts[ $v ] ) && ( 'yes' == $atts[ $v ] ) ) {
				$atts[ $v ] = true;
			} else {
				$atts[ $v ] = false;
			}

		endforeach;

		$atts['content_type']   = sanitize_key( $atts['content_type'] );
		$atts['content_length'] = intval( $atts['content_length'] );

		return $this->data->the_testimonials( $atts );
	}

	/**
	 * Adds a specific macros buttons.
	 *
	 * @since  1.0.0
	 * @param  array  $macros_buttons Array with macros buttons.
	 * @param  string $shortcode      Shortcode's name.
	 * @return array
	 */
	public function add_macros_buttons( $macros_buttons, $shortcode ) {

		if ( self::$name != $shortcode ) {
			return $macros_buttons;
		}

		$macros_buttons = array();

		$macros_buttons['name'] = array(
			'id'    => 'cherry_author_name',
			'value' => __( 'Name', 'cherry-testimonials' ),
			'open'  => '%%NAME%%',
			'close' => '',
		);
		$macros_buttons['email'] = array(
			'id'    => 'cherry_email',
			'value' => __( 'Email', 'cherry-testimonials' ),
			'open'  => '%%EMAIL%%',
			'close' => '',
		);
		$macros_buttons['url'] = array(
			'id'    => 'cherry_url',
			'value' => __( 'URL', 'cherry-testimonials' ),
			'open'  => '%%URL%%',
			'close' => '',
		);
		$macros_buttons['avatar'] = array(
			'id'    => 'cherry_avatar',
			'value' => __( 'Avatar', 'cherry-testimonials' ),
			'open'  => '%%AVATAR%%',
			'close' => '',
		);
		$macros_buttons['position'] = array(
			'id'    => 'cherry_position',
			'value' => __( 'Position', 'cherry-testimonials' ),
			'open'  => '%%POSITION%%',
			'close' => '',
		);
		$macros_buttons['company'] = array(
			'id'    => 'cherry_company',
			'value' => __( 'Company', 'cherry-testimonials' ),
			'open'  => '%%COMPANY%%',
			'close' => '',
		);
		$macros_buttons['content'] = array(
			'id'    => 'cherry_content',
			'value' => __( 'Content', 'cherry-testimonials' ),
			'open'  => '%%CONTENT%%',
			'close' => '',
		);
		$macros_buttons['author'] = array(
			'id'    => 'cherry_author',
			'value' => __( 'Name + URL (HTML-formatted)', 'cherry-testimonials' ),
			'open'  => '%%AUTHOR%%',
			'close' => '',
		);

		return $macros_buttons;
	}

	/**
	 * Adds a specific macros buttons into `swiper_carousel` shortcode.
	 *
	 * @since 1.0.2
	 * @param array $macros_buttons Default macros buttons.
	 */
	public function extend_carousel_macros( $macros_buttons ) {
		$macros_buttons['testi_name'] = array(
			'id'    => 'testi_name',
			'value' => __( 'Author Name (Testimonials only)', 'cherry-testimonials' ),
			'open'  => '%%TESTINAME%%',
			'close' => '',
		);
		$macros_buttons['testi_email'] = array(
			'id'    => 'testi_email',
			'value' => __( 'Email (Testimonials only)', 'cherry-testimonials' ),
			'open'  => '%%TESTIEMAIL%%',
			'close' => '',
		);
		$macros_buttons['testi_url'] = array(
			'id'    => 'testi_url',
			'value' => __( 'URL (Testimonials only)', 'cherry-testimonials' ),
			'open'  => '%%TESTIURL%%',
			'close' => '',
		);
		$macros_buttons['testi_author'] = array(
			'id'    => 'testi_author',
			'value' => __( 'Name + URL (Testimonials only)', 'cherry-testimonials' ),
			'open'  => '%%TESTIAUTHOR%%',
			'close' => '',
		);
		$macros_buttons['testi_position'] = array(
			'id'    => 'testi_position',
			'value' => __( 'Position (Testimonials only)', 'cherry-testimonials' ),
			'open'  => '%%TESTIPOSITION%%',
			'close' => '',
		);
		$macros_buttons['testi_company'] = array(
			'id'    => 'testi_company',
			'value' => __( 'Company (Testimonials only)', 'cherry-testimonials' ),
			'open'  => '%%TESTICOMPANY%%',
			'close' => '',
		);

		return $macros_buttons;
	}

	/**
	 * Add testimonials macros data to process it in carousel shortcode.
	 *
	 * @since 1.0.2
	 * @param array $postdata Default data.
	 * @param int   $post_id  Processed post ID.
	 * @param array $atts     Shortcode attributes.
	 */
	public function add_carousel_data( $postdata, $post_id, $atts ) {

		require_once( CHERRY_TESTI_DIR . 'public/includes/class-cherry-testimonials-template-callbacks.php' );
		$callbacks = new Cherry_Testimonials_Template_Callbacks( $atts );

		$postdata['testiavatar']   = $callbacks->get_avatar();
		$postdata['testicontent']  = $callbacks->get_content();
		$postdata['testiauthor']   = $callbacks->get_author();
		$postdata['testiemail']    = $callbacks->get_email();
		$postdata['testiname']     = $callbacks->get_name();
		$postdata['testiurl']      = $callbacks->get_url();
		$postdata['testiposition'] = $callbacks->get_position();
		$postdata['testicompany']  = $callbacks->get_company();

		/**
		 * Filters a fallback for old themes.
		 *
		 * @since 1.1.0
		 */
		if ( false === apply_filters( 'cherry_testimonials_remove_carousel_data_fallback', false ) ) {
			$postdata['author'] = $callbacks->get_author();
			$postdata['email']  = $callbacks->get_email();
			$postdata['url']    = $callbacks->get_url();
		}

		return $postdata;
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

Cherry_Testimonials_Shortcode::get_instance();
