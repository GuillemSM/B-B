<?php
/**
 * Cherry Social.
 *
 * @package   Cherry_Social
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @copyright 2012 - 2015, Cherry Team
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Social_Shortcodes' ) ) {

	/**
	 * Class for Social shortcode.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Social_Shortcodes {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * A reference to an instance of this plugin.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private $plugin = null;

		/**
		 * Shortcode names prefix in compatibility mode.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private $prefix = null;

		/**
		 * Sets up our actions/filters.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->plugin = Cherry_Social::get_instance();
			$this->prefix = $this->get_prefix();

			add_action( 'init', array( $this, 'register_shortcodes' ) );
			add_filter( 'cherry_shortcodes/data/shortcodes', array( $this, 'shortcodes' ) );
		}

		/**
		 * Registers the shortcodes.
		 *
		 * @since 1.0.0
		 */
		public function register_shortcodes() {
			add_shortcode( $this->prefix . 'sharing', array( $this, 'do_shortcode_sharing' ) );
			add_shortcode( $this->prefix . 'follow',  array( $this, 'do_shortcode_follow' ) );
		}

		/**
		 * The `cherry_sharing` shortcode function.
		 *
		 * @since 1.0.0
		 */
		public function do_shortcode_sharing( $atts, $content = null, $shortcode = '' ) {

			// Set up the default arguments.
			$defaults = array(
				'networks'     => '',
				'custom_class' => '',
			);

			/**
			 * Parse the arguments.
			 *
			 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
			 */
			$atts = shortcode_atts( $defaults, $atts, $shortcode );

			$networks = sanitize_text_field( $atts['networks'] );

			if ( empty( $networks ) ) {
				return;
			}

			$networks     = explode( ',', $networks );
			$custom_class = $atts['custom_class'];
			$output       = $this->plugin->share_buttons( $networks, false, $custom_class );

			/**
			 * Filters $output before return.
			 *
			 * @since 1.0.0
			 * @param string $output
			 * @param array  $atts
			 * @param string $shortcode
			 */
			$output = apply_filters( 'cherry_shortcodes_output', $output, $atts, 'sharing' );

			return $output;
		}

		/**
		 * The `cherry_follow` shortcode function.
		 *
		 * @since  1.0.0
		 */
		public function do_shortcode_follow( $atts, $content = null, $shortcode = '' ) {

			// Set up the default arguments.
			$defaults = array(
				'networks'     => '',
				'custom_class' => '',
			);

			/**
			 * Parse the arguments.
			 *
			 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
			 */
			$atts = shortcode_atts( $defaults, $atts, $shortcode );

			$networks = sanitize_text_field( $atts['networks'] );
			$networks = explode( ',', $networks );

			if ( empty( $networks ) ) {
				return;
			}

			$custom_class = $atts['custom_class'];
			$output       = $this->plugin->get_follows( $networks, false, $custom_class );

			/**
			 * Filters $output before return.
			 *
			 * @since 1.0.0
			 * @param string $output
			 * @param array  $atts
			 * @param string $shortcode
			 */
			return apply_filters( 'cherry_shortcodes_output', $output, $atts, 'follow' );
		}

		/**
		 * Filter to modify original shortcodes data and add [$this->name] shortcode.
		 *
		 * @since  1.0.0
		 * @param  array $shortcodes Original plugin shortcodes.
		 * @return array             Modified array.
		 */
		public function shortcodes( $shortcodes ) {
			$share_btns   = $this->plugin->get_the_share_btns();
			$share_values = wp_list_pluck( $share_btns, 'name' );

			$shortcodes['sharing'] = array(
				'name'  => __( 'Sharing', 'cherry-social' ),
				'desc'  => 'This is a Sharing Shortcode',
				'type'  => 'single',
				'group' => 'content',
				'atts'  => array(
							'networks' => array(
								'type'     => 'select',
								'multiple' => true,
								'values'   => $share_values,
								'default'  => '',
								'name'     => __( 'Networks', 'cherry-social' ),
								'desc'     => __( 'Select the social networks to display', 'cherry-social' ),
							),
							'custom_class' => array(
								'default' => '',
								'name'    => __( 'Class', 'cherry-social' ),
								'desc'    => __( 'Extra CSS class', 'cherry-social' ),
							),
						),
				'icon'     => 'share-square',
				'function' => array( $this, 'do_shortcode_sharing' ),
			);

			$follows = $this->plugin->get_option( 'follow-items' );

			if ( false == $follows ) {
				return $shortcodes;
			}

			$follow_values = array();
			foreach ( $follows as $follow ) {
				if ( empty( $follow['link-label'] ) ) {
					continue;
				}

				if ( empty( $follow['network-id'] ) ) {
					$follow_values[ sanitize_key( $follow['link-label'] ) ] = $follow['link-label'];
				} else {
					$follow_values[ sanitize_key( $follow['network-id'] ) ] = $follow['link-label'];
				}
			}

			$shortcodes['follow'] = array(
				'name'  => __( 'Follow', 'cherry-social' ),
				'desc'  => 'This is a Follow Shortcode',
				'type'  => 'single',
				'group' => 'content',
				'atts'  => array(
							'networks' => array(
								'type'     => 'select',
								'multiple' => true,
								'values'   => $follow_values,
								'default'  => '',
								'name'     => __( 'Networks', 'cherry-social' ),
								'desc'     => __( 'Select the social networks to display', 'cherry-social' ),
							),
							'custom_class' => array(
								'default' => '',
								'name'    => __( 'Class', 'cherry-social' ),
								'desc'    => __( 'Extra CSS class', 'cherry-social' ),
							),
						),
				'icon'     => 'users',
				'function' => array( $this, 'do_shortcode_follow' ),
			);

			return $shortcodes;
		}

		/**
		 * Retrieve shortcode names prefix.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_prefix() {
			return apply_filters( 'cherry_social_shortcode_prefix', 'cherry_' );
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

	Cherry_Social_Shortcodes::get_instance();

}
