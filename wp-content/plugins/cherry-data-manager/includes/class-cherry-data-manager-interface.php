<?php
/**
 * Add admin interface
 *
 * @package   cherry_wizard
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( !class_exists( 'cherry_data_manager_interface' ) ) {

	/**
	 * Add admin interface
	 *
	 * @since 1.0.0
	 */
	class cherry_data_manager_interface {
		
		function __construct() {
			// Add the withard page and menu item.
			add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		}
		
		/**
		 * Register the administration menu for this plugin into the WordPress Dashboard menu.
		 *
		 * @since 1.0.0
		 */
		public function add_admin_pages() {
			
			global $cherry_data_manager;
			// add content import page
			add_management_page( __( 'Cherry Content Import', $cherry_data_manager->slug ), __( 'Cherry Import', $cherry_data_manager->slug ), 'manage_options', $cherry_data_manager->import_page, array( $this, 'show_admin_pages' ) );
			// add content export page
			add_management_page( __( 'Cherry Content Export', $cherry_data_manager->slug ), __( 'Cherry Export', $cherry_data_manager->slug ), 'manage_options', $cherry_data_manager->export_page, array( $this, 'show_admin_pages' ) );
		}

		/**
		 * show wizard management page
		 * 
		 * @since 1.0.0
		 */
		public function show_admin_pages() {

			$page = isset($_GET['page']) ? $_GET['page'] : '';

			if ( !$page ) {
				return;
			}

			if ( file_exists( CHERRY_DATA_MANAGER_DIR . 'includes/views/' . $page . '.php' ) ) {
				include_once( CHERRY_DATA_MANAGER_DIR . 'includes/views/' . $page . '.php' );
			}

		}

	}

	new cherry_data_manager_interface();

}