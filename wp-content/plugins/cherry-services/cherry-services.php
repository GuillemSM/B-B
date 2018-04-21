<?php
/**
 * Plugin Name: Cherry Services
 * Plugin URI:  http://www.cherryframework.com/
 * Description: A services management plugin for WordPress.
 * Version:     1.0.5
 * Author:      Cherry Team
 * Author URI:  http://www.cherryframework.com/
 * Text Domain: cherry-services
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 *
 * @package  Cherry Team
 * @category Core
 * @author   Cherry Team
 * @license  GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class 'Cherry_Services' not exists.
if ( ! class_exists( 'Cherry_Services' ) ) {

	/**
	 * Sets up and initializes the Cherry Services plugin.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Services {

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

			$this->constants();
			$this->includes();

			// Attach service hooks
			add_action( 'plugins_loaded', array( $this, 'lang' ),      2 );
			add_action( 'plugins_loaded', array( $this, 'admin' ),     5 );

			// Load public-facing style sheet and JavaScript.
			add_action( 'wp_enqueue_scripts',         array( $this, 'enqueue_assets' ), 20 );
			add_filter( 'cherry_compiler_static_css', array( $this, 'add_style_to_compiler' ) );

			// Adds options.
			add_filter( 'cherry_layouts_options_list',   array( $this, 'add_cherry_options' ), 11 );
			add_filter( 'cherry_get_single_post_layout', array( $this, 'get_single_option' ),  11, 2 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
			register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );
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
			define( 'CHERRY_SERVICES_NAME', 'service' );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SERVICES_VERSION', '1.0.5' );

			/**
			 * Set the slug of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SERVICES_SLUG', basename( dirname( __FILE__ ) ) );

			/**
			 * Set the name for the 'meta_key' value in the 'wp_postmeta' table.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SERVICES_POSTMETA', '_cherry_services' );

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SERVICES_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_SERVICES_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}

		/**
		 * Loads files from the '/inc' folder.
		 *
		 * @since 1.0.0
		 */
		function includes() {
			require_once( CHERRY_SERVICES_DIR . 'public/includes/class-cherry-services-registration.php' );
			require_once( CHERRY_SERVICES_DIR . 'public/includes/class-cherry-services-templates.php' );
			require_once( CHERRY_SERVICES_DIR . 'public/includes/class-cherry-services-data.php' );
			require_once( CHERRY_SERVICES_DIR . 'public/includes/class-cherry-services-shortcode.php' );
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		function lang() {
			load_plugin_textdomain( 'cherry-services', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Loads admin files.
		 *
		 * @since 1.0.0
		 */
		function admin() {

			if ( is_admin() ) {
				require_once( CHERRY_SERVICES_DIR . 'admin/includes/class-cherry-services-admin.php' );
				require_once( CHERRY_SERVICES_DIR . 'admin/includes/class-cherry-update/class-cherry-plugin-update.php' );

				$Cherry_Plugin_Update = new Cherry_Plugin_Update();
				$Cherry_Plugin_Update -> init( array(
						'version'			=> CHERRY_SERVICES_VERSION,
						'slug'				=> CHERRY_SERVICES_SLUG,
						'repository_name'	=> CHERRY_SERVICES_SLUG,
				));
			}
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_assets() {

			wp_enqueue_style(
				'cherry-services',
				plugins_url( 'public/assets/css/style.css', __FILE__ ), array(), CHERRY_SERVICES_VERSION
			);

		}

		/**
		 * Pass style handle to CSS compiler.
		 *
		 * @since 1.0.0
		 *
		 * @param array $handles CSS handles to compile.
		 */
		function add_style_to_compiler( $handles ) {
			$handles = array_merge(
				array( 'cherry-services' => plugins_url( 'public/assets/css/style.css', __FILE__ ) ),
				$handles
			);

			return $handles;
		}

		/**
		 * On plugin activation.
		 *
		 * @since 1.0.0
		 */
		public static function activation() {
			Cherry_Services_Registration::register_post();
			Cherry_Services_Registration::register_tax();
			flush_rewrite_rules();
		}

		/**
		 * On plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		public static function deactivation() {
			flush_rewrite_rules();
		}

		/**
		 * Adds a option in `Grid -> Layouts` subsection.
		 *
		 * @since  1.0.0
		 * @param  array $layouts_options default options list.
		 * @return array
		 */
		public function add_cherry_options( $layouts_options ) {
			$layouts_options['single-service-layout'] = array(
				'type'        => 'radio',
				'title'       => __( 'Services posts', 'cherry-services' ),
				'hint'        => array(
					'type'    => 'text',
					'content' => __( 'You can choose if you want to display sidebars and how you want to display them.', 'cherry-services' ),
				),
				'value'         => 'no-sidebar',
				'display_input' => false,
				'options'       => array(
					'sidebar-content' => array(
						'label'   => __( 'Left sidebar', 'cherry-services' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-left-sidebar.svg',
					),
					'content-sidebar' => array(
						'label'   => __( 'Right sidebar', 'cherry-services' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-right-sidebar.svg',
					),
					'sidebar-content-sidebar' => array(
						'label'   => __( 'Left and right sidebar', 'cherry-services' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-both-sidebar.svg',
					),
					'sidebar-sidebar-content' => array(
						'label'   => __( 'Two sidebars on the left', 'cherry-services' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-sameside-left-sidebar.svg',
					),
					'content-sidebar-sidebar' => array(
						'label'   => __( 'Two sidebars on the right', 'cherry-services' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-sameside-right-sidebar.svg',
					),
					'no-sidebar' => array(
						'label'   => __( 'No sidebar', 'cherry-services' ),
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

			if ( CHERRY_SERVICES_NAME != get_post_type( $object_id ) ) {
				return $value;
			}

			return $this->get_option( 'single-service-layout', 'no-sidebar' );
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

	Cherry_Services::get_instance();
}
