<?php
/**
 * Cherry Data Manager Core Functions
 *
 * General core functions
 *
 * @package   cherry_data_manager
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get max upload unit size in MB
 *
 * @since  1.0.0
 * @return array max upload size in MB and max upload size in bytes
 */
function cherry_dm_get_upload_size() {

	$upload_size_unit = $max_upload_size = wp_max_upload_size();
	$upload_size_unit = size_format($upload_size_unit, 2);

	return array( 'formated' => $upload_size_unit, 'size' => $max_upload_size );

}

/**
 * get path to current upload location
 *
 * @since  1.0.0
 * @return string absolute path to upload dir
 */
function cherry_dm_get_upload_path() {

	$upload_dir = wp_upload_dir();
	$upload_dir = $upload_dir['path'] . '/';

	return $upload_dir;
}

/**
 * Get import action URL
 *
 * @since  1.0.0
 * @return string Import action URL
 */
function cherry_dm_get_import_action_url() {
	global $cherry_data_manager;
	$upload_dir   = cherry_dm_get_upload_path();
	$upload_nonce = wp_create_nonce( 'cherry_dm_content_upload' );
	return apply_filters( 'cherry_data_manager_import_action', add_query_arg( array( 'action' => 'cherry_dm_content_handle_load', 'dir' => urlencode($upload_dir), '_wpnonce' => $upload_nonce ), admin_url( 'admin-ajax.php' ) ) );
}

/**
 * template loader for usage data manager templates in external plugins
 *
 * @since  1.0.0
 * @param  string $template template name
 * @return bool false if $template not found or not passed to function or void if all ok
 */
function cherry_dm_get_admin_template( $template = '' ) {

	if ( !$template ) {
		return false;
	}

	if ( !file_exists( trailingslashit( CHERRY_DATA_MANAGER_DIR ) . 'includes/views/' . $template ) ) {
		return false;
	}

	include_once( trailingslashit( CHERRY_DATA_MANAGER_DIR ) . 'includes/views/' . $template );

}

/**
 * Show content import type selector for import via wizard
 *
 * @since 1.0.0
 */
function cherry_dm_import_selector() {

	// this function warks only if is import from wizard
	if ( !isset( $_GET['page'] ) || ! in_array( $_GET['page'], array( 'monstroid-wizard', 'cherry-wizard' ) ) ) {
		return;
	}

	global $cherry_data_manager;

	$remote_disabled = '';

	if ( !isset( $cherry_data_manager->importer ) ) {
		return;
	}

	if ( ! $cherry_data_manager->importer->auto_upload ) {
		$remote_disabled = 'disabled';
	}

	?>

	<div class="dm-import-type-selector">
		<?php do_action( 'cherry_data_manager_import_type_before' ); ?>
		<div class="dm-selector-buttons_">
			<a href="#" class="button-primary_ <?php echo $remote_disabled; ?>" id="cherry-dm-remote-import">
				<span class="dashicons dashicons-download"></span>
				<?php _e( 'Install from cloud', $cherry_data_manager->slug ); ?>
			</a>
			or
			<a href="#" class="button-primary_" id="cherry-dm-local-import">
				<span class="dashicons dashicons-category"></span>
				<?php _e( 'Install from folder', $cherry_data_manager->slug ); ?>
			</a>
			<div class="skip-sample-data">
				<a href="<?php echo apply_filters( 'cherry_data_manager_cancel_import_url', get_admin_url() ); ?>">
					<?php _e( 'Skip this step', $cherry_data_manager->slug ); ?>
					<span class="dashicons dashicons-arrow-right-alt"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="dm-return-to-switcher hidden_">
		<a href="#" id="cherry-dm-return-to-switcher">
			<span class="dashicons dashicons-arrow-left-alt"></span>
			<?php _e( 'Return to switcher', $cherry_data_manager->slug ); ?>
		</a>
	</div>
	<?php
}