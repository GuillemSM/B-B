<?php
/**
 * Plugin Name: Cherry Testimonials
 * Plugin URI:  http://www.cherryframework.com/
 * Description: A testimonials management plugin for WordPress.
 * Version:     1.1.3
 * Author:      Cherry Team
 * Author URI:  http://www.cherryframework.com/
 * Text Domain: cherry-testimonials
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 *
 * @package  Cherry Testimonials
 * @category Core
 * @author   Cherry Team
 * @license  GPL-3.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class 'Cherry_Testimonials' not exists.
if ( ! class_exists( 'Cherry_Testimonials' ) ) {

	/**
	 * Sets up and initializes the Cherry Testimonials plugin.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Testimonials {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Set the constants needed by the plugin.
			$this->constants();

			// Load the functions files.
			$this->includes();

			// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'lang' ), 1 );

			// Load the admin files.
			add_action( 'plugins_loaded', array( $this, 'admin' ), 2 );

			// Load public-facing stylesheet.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_filter( 'cherry_compiler_static_css', array( $this, 'add_style_to_compiler' ) );

			// Adds options.
			add_filter( 'cherry_layouts_options_list', array( $this, 'add_cherry_options' ), 11 );
			add_filter( 'cherry_get_single_post_layout', array( $this, 'get_single_option' ), 11, 2 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Defines constants for the plugin.
		 *
		 * @since 1.0.0
		 */
		function constants() {

			/**
			 * Set constant name for the post type name.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_TESTI_NAME', 'testimonial' );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_TESTI_VERSION', '1.1.3' );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_TESTI_SLUG', basename( dirname( __FILE__ ) ) );

			/**
			 * Set the name for the 'meta_key' value in the 'wp_postmeta' table.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_TESTI_POSTMETA', '_cherry_testimonial' );

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_TESTI_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_TESTI_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}

		/**
		 * Loads files from the 'public/includes' folder.
		 *
		 * @since 1.0.0
		 */
		function includes() {
			require_once( CHERRY_TESTI_DIR . 'public/includes/class-cherry-testimonials-registration.php' );
			require_once( CHERRY_TESTI_DIR . 'public/includes/class-cherry-testimonials-page-template.php' );
			require_once( CHERRY_TESTI_DIR . 'public/includes/class-cherry-testimonials-data.php' );
			require_once( CHERRY_TESTI_DIR . 'public/includes/class-cherry-testimonials-shortcode.php' );
			require_once( CHERRY_TESTI_DIR . 'public/includes/class-cherry-testimonials-widget.php' );
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		function lang() {
			load_plugin_textdomain( 'cherry-testimonials', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Loads admin files.
		 *
		 * @since 1.0.0
		 */
		function admin() {

			if ( is_admin() ) {
				require_once( CHERRY_TESTI_DIR . 'admin/includes/class-cherry-testimonials-admin.php' );
				require_once( CHERRY_TESTI_DIR . 'admin/includes/class-cherry-update/class-cherry-plugin-update.php' );

				$Cherry_Plugin_Update = new Cherry_Plugin_Update();
				$Cherry_Plugin_Update->init( array(
					'version'         => CHERRY_TESTI_VERSION,
					'slug'            => CHERRY_TESTI_SLUG,
					'repository_name' => CHERRY_TESTI_SLUG,
				) );
			}
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_styles() {
			wp_enqueue_style( 'cherry-testimonials', plugins_url( 'public/assets/css/style.css', __FILE__ ), array(), CHERRY_TESTI_VERSION );
		}

		/**
		 * Pass style handle to CSS compiler.
		 *
		 * @since 1.0.0
		 * @param array $handles CSS handles to compile.
		 */
		function add_style_to_compiler( $handles ) {
			$handles = array_merge(
				array( 'cherry-testimonials' => plugins_url( 'public/assets/css/style.css', __FILE__ ) ),
				$handles
			);

			return $handles;
		}

		/**
		 * On plugin activation.
		 *
		 * @since 1.0.0
		 */
		function activation() {
			Cherry_Testimonials_Registration::register_post_type();
			Cherry_Testimonials_Registration::register_taxonomy();
			flush_rewrite_rules();
		}

		/**
		 * On plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		function deactivation() {
			flush_rewrite_rules();
		}

		/**
		 * Adds a option in `Grid -> Layouts` subsection.
		 *
		 * @since 1.0.0
		 * @param array $layouts_options Set of layout options.
		 */
		public function add_cherry_options( $layouts_options ) {
			$layouts_options['single-testi-layout'] = array(
				'type'        => 'radio',
				'title'       => __( 'Testimonials posts', 'cherry-testimonials' ),
				'hint'        => array(
					'type'    => 'text',
					'content' => __( 'You can choose if you want to display sidebars and how you want to display them.', 'cherry-testimonials' ),
				),
				'value'         => 'content-sidebar',
				'display_input' => false,
				'options'       => array(
					'sidebar-content' => array(
						'label'   => __( 'Left sidebar', 'cherry-testimonials' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-left-sidebar.svg',
					),
					'content-sidebar' => array(
						'label'   => __( 'Right sidebar', 'cherry-testimonials' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-right-sidebar.svg',
					),
					'sidebar-content-sidebar' => array(
						'label'   => __( 'Left and right sidebar', 'cherry-testimonials' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-both-sidebar.svg',
					),
					'sidebar-sidebar-content' => array(
						'label'   => __( 'Two sidebars on the left', 'cherry-testimonials' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-sameside-left-sidebar.svg',
					),
					'content-sidebar-sidebar' => array(
						'label'   => __( 'Two sidebars on the right', 'cherry-testimonials' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-sameside-right-sidebar.svg',
					),
					'no-sidebar' => array(
						'label'   => __( 'No sidebar', 'cherry-testimonials' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-fullwidth.svg',
					),
				),
			);

			return $layouts_options;
		}

		/**
		 * Rewrite a single option.
		 *
		 * @since 1.0.0
		 */
		public function get_single_option( $value, $object_id ) {

			if ( CHERRY_TESTI_NAME != get_post_type( $object_id ) ) {
				return $value;
			}

			return $this->get_option( 'single-testi-layout', 'content-sidebar' );
		}

		/**
		 * Return a values for a named option from the options database table.
		 *
		 * @since  1.0.0
		 * @param  string $option  Name of the option to retrieve.
		 * @param  mixed  $default The default value to return if no value is returned.
		 * @return mixed           Current value for the specified option. If the option does not exist, returns
		 *                         parameter $default if specified or boolean FALSE by default.
		 */
		public function get_option( $option, $default = false ) {

			if ( function_exists( 'cherry_get_option' ) ) {

				$result = cherry_get_option( $option, $default );

				return $result;
			}

			return $default;
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

	Cherry_Testimonials::get_instance();
}
