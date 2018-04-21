<?php
/**
 * @package   cherry_data_manager
 * @author    Cherry Team
 * @license   GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name:       Cherry Data Manager
 * Plugin URI:        http://www.cherryframework.com/
 * Description:       Import/export site content, uploads, widgets and some options
 * Version:           1.0.9
 * Author:            Cherry Team
 * Author URI:        http://www.cherryframework.com/
 * Text Domain:       cherry-content-manager
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 *
 * Import/export site content, uploads, widgets and some options
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// If class 'cherry_wizard' not exists.
if ( !class_exists('cherry_data_manager') ) {

	/**
	 * Sets up and initializes the Cherry Wizard plugin.
	 *
	 * @since 1.0.0
	 */
	class cherry_data_manager {

		/**
		 * Plugin slug (for text domains and options pages)
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $slug = 'cherry-data-manager';

		/**
		 * Import page slug
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $import_page = 'cherry-content-import';

		/**
		 * Export page slug
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $export_page = 'cherry-content-export';

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// this plugin nothing to do on frontend
			if ( !is_admin() ) {
				return;
			}

			// Set the constants needed by the plugin.
			add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );
			// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'lang' ), 2 );
			// Load the functions files.
			add_action( 'plugins_loaded', array( $this, 'includes' ), 9 );
			// Load public-facing style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 20 );

			add_action( 'init', array( $this, 'init_updater' ) );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		}

		/**
		 * Include assets
		 *
		 * @since 1.0.0
		 */
		function assets() {

			wp_enqueue_style(
				'cherry-ui-elements',
				CHERRY_DATA_MANAGER_URI . 'assets/css/cherry-ui.css', array(), '1.0.0'
			);
			wp_enqueue_style(
				$this->slug . '-style',
				CHERRY_DATA_MANAGER_URI . 'assets/css/style.css', '', CHERRY_DATA_MANAGER_VERSION
			);

			if ( $this->is_manager_page( $this->import_page ) ) {

				wp_deregister_script('heartbeat');

				wp_enqueue_script(
					$this->slug . '-importer',
					CHERRY_DATA_MANAGER_URI . 'assets/js/importer.js',
					array( 'jquery' ),
					CHERRY_DATA_MANAGER_VERSION,
					true
				);

				wp_localize_script(
					$this->slug . '-importer',
					'cherry_data_manager_ajax',
					array( 'nonce' => wp_create_nonce( 'cherry_data_manager_ajax' ) )
				);

				$autoload = ! empty( $_SESSION['cherry_data']['sample'] ) ? 'yes' : 'no';

				// setup js trigger for manual/automatic upload
				wp_localize_script(
					$this->slug . '-importer',
					'cherry_dm_import_trigger',
					array( 'autoload' => apply_filters( 'cherry_data_manager_content_autoload', $autoload ) )
				);

				// setup localized data for importer
				$import_texts = array(
					'error_upload'                 => __( 'Upload Error', $this->slug ),
					'error_size'                   => __( 'The file is too big!', $this->slug ),
					'error_type'                   => __( 'The file type is error!', $this->slug ),
					'error_folder'                 => __( 'Folder cannot be uploaded!', $this->slug ),
					'uploading'                    => __( 'Uploading', $this->slug ),
					'upload'                       => __( 'Upload', $this->slug ),
					'upload_complete'              => __( 'Upload Complete', $this->slug ),
					'item'                         => __( 'item', $this->slug ),
					'items'                        => __( 'items', $this->slug ),
					'uploaded_status_text'         => __( 'Sample data installing. Some steps may take some time depending on your server settings. Please be patient.', $this->slug ),
					'uploaded_status_text_1'       => __( 'Upload complete please click Continue Install button to proceed.', $this->slug ),
					//xml status text
					'import_xml'                   => __( 'Importing XML', $this->slug ),
					'import_options'               => __( 'Importing options', $this->slug ),
					'import_custom_tables'         => __( 'Importing custom database tables', $this->slug ),
					'import_categories'            => __( 'Importing categories', $this->slug ),
					'import_tags'                  => __( 'Importing tags', $this->slug ),
					'process_terms'                => __( 'Processing dependencies', $this->slug ),
					'import_posts'                 => __( 'Importing posts. This may take some time. Please wait.', $this->slug ),
					'attach_terms'                 => __( 'Attaching terms to imported posts. This may take some time. Please wait.', $this->slug ),
					'attach_comments'              => __( 'Attaching comments to imported posts. This may take some time. Please wait.', $this->slug ),
					'attach_postmeta'              => __( 'Attaching metadata to imported posts. This may take some time. Please wait.', $this->slug ),
					'import_menu_item'             => __( 'Importing menu items. This may take some time. Please wait.', $this->slug ),
					'import_attachment'            => __( 'Importing media library.', $this->slug ),
					'import_attachment_metadata'   => __( 'Importing attachements meta.', $this->slug ),
					'generate_attachment_metadata_step_1' => __( 'Generate attachements meta. Preparing. This may take some time. Please wait.', $this->slug ),
					'generate_attachment_metadata_step_2' => __( 'Generate attachements meta. Processing. This may take some time. Please wait.', $this->slug ),
					'generate_attachment_metadata_step_3' => __( 'Generate attachements meta. Finishing. This may take some time. Please wait.', $this->slug ),
					'generate_attachment_metadata_step_4' => __( 'Cropping attachements meta. Preparing. This may take some time. Please wait.', $this->slug ),
					'generate_attachment_metadata_step_5' => __( 'Cropping attachements meta. Processing. This may take some time. Please wait.', $this->slug ),
					'generate_attachment_metadata_step_6' => __( 'Cropping attachements meta. Finishing. This may take some time. Please wait.', $this->slug ),
					'import_parents'               => __( 'Generating content hierarchy', $this->slug ),
					'update_featured_images'       => __( 'Updating featured images', $this->slug ),
					'update_attachment'            => __( 'Updating attachments', $this->slug ),
					'import_json'                  => __( 'Importing JSON', $this->slug ),
					'import_complete'              => __( 'Installing content complete', $this->slug ),
					'instal_error'                 => __( 'Installing content error', $this->slug ),
					'confirm_load'                 => __( "ATTENTION! Installing Sample Data will overwrite your current content.\nSo we don't recommend you to install Sample Data on a live website.\n\nClick OK to proceed", $this->slug ),
				);

				wp_localize_script( $this->slug . '-importer', 'cherry_dm_import_texts', $import_texts );

				// setup additional JS varaibles for importer
				$max_size   = cherry_dm_get_upload_size();
				$upload_dir = cherry_dm_get_upload_path();
				$action_url = cherry_dm_get_import_action_url();

				if ( isset( $_GET['type'] ) ) {
					$type = $_GET['type'];
				} else {
					$type = 'demo';
				}

				$import_vars = array(
					'max_file_size' => $max_size['size'],
					'action_url'    => $action_url,
					'type'          => $type,
					'nonce'         => wp_create_nonce( 'cherry-dm-nonce' )
				);

				wp_localize_script( $this->slug . '-importer', 'cherry_dm_import_vars', $import_vars );

			}

			if ( $this->is_manager_page( $this->export_page ) ) {
				wp_enqueue_script(
					$this->slug . '-exporter',
					CHERRY_DATA_MANAGER_URI . 'assets/js/exporter.js',
					array( 'jquery' ),
					CHERRY_DATA_MANAGER_VERSION,
					true
				);
			}
		}

		/**
		 * Defines constants for the plugin.
		 *
		 * @since 1.0.0
		 */
		function constants() {

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_DATA_MANAGER_VERSION', '1.0.9' );

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_DATA_MANAGER_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_DATA_MANAGER_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

		}

		/**
		 * Loads files from the '/inc' folder.
		 *
		 * @since 1.0.0
		 */
		function includes() {

			require_once( CHERRY_DATA_MANAGER_DIR . 'includes/class-cherry-data-manager-interface.php' );
			require_once( CHERRY_DATA_MANAGER_DIR . 'includes/cherry-data-manager-core-functions.php' );

			// include next handlers only for data import page and AJAX handlers
			if ( $this->is_manager_page($this->import_page) ) {
				require_once( CHERRY_DATA_MANAGER_DIR . 'includes/class-cherry-data-manager-content-importer.php' );
				require_once( CHERRY_DATA_MANAGER_DIR . 'includes/class-cherry-data-manager-content-installer.php' );
			}

			if ( $this->is_manager_page($this->export_page) ) {
				require_once( CHERRY_DATA_MANAGER_DIR . 'includes/class-cherry-data-manager-content-exporter.php' );
			}

			// include updater
			require_once( CHERRY_DATA_MANAGER_DIR . 'admin/includes/class-cherry-update/class-cherry-plugin-update.php' );

		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		function lang() {
			load_plugin_textdomain( $this->slug, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * On plugin activation.
		 *
		 * @since 1.0.0
		 */
		function activation() {
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
		 * check if is import content page
		 *
		 * @since 1.0.0
		 */
		public function is_manager_page( $page = '' ) {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return true;
			}

			if ( isset( $_GET['page'] ) && $page === $_GET['page'] ) {
				return true;
			}

			$custom_coditions = apply_filters( 'cherry_data_manager_conditions_' . $page, false );

			if ( $custom_coditions ) {
				return true;
			}

			return false;
		}

		/**
		 * Get UI wrapper CSS class
		 *
		 * @since  1.0.0
		 */
		public function ui_wrapper_class( $classes = array() ) {

			// prevent PHP errors
			if ( ! $classes || ! is_array( $classes ) ) {
				$classes = array();
			}

			$classes = array_merge( array( 'cherry-ui-core' ), $classes );

			/**
			 * Filter UI wrapper CSS classes
			 *
			 * @since 1.0.0
			 *
			 * @param array $classes - default CSS classes array
			 */
			$classes = apply_filters( 'cherry_ui_wrapper_class', $classes, 'data-manager' );

			$classes = array_unique( $classes );

			return join( ' ', $classes );
		}

		/**
		 * Init plugin updater
		 *
		 * @since  1.0.0
		 */
		public function init_updater() {

			$updater = new Cherry_Plugin_Update();

			$updater->init(
				array(
					'slug'            => $this->slug,
					'repository_name' => $this->slug,
					'version'         => CHERRY_DATA_MANAGER_VERSION
				)
			);
		}

	}

	// create class instance
	$GLOBALS['cherry_data_manager'] = new cherry_data_manager();

}