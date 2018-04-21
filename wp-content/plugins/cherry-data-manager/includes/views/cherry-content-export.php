<?php
/**
 * Represents the view for the administration dashboard.
 *
 * Export sample content
 *
 * @package   cherry_data_manager
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $cherry_data_manager;

?>
<div class="wrap">
	<div class="<?php echo $cherry_data_manager->ui_wrapper_class(); ?>">
		<h2 class="main-title_"><?php echo esc_html( get_admin_page_title() ); ?></h2>

		<div class="content-box">
			<p>
				<?php _e( "Export allows you to create a backup of your website content in one click. You'll get a downloaded archive containing all website data: images, audio, video and other files from your media library. XML file with your posts and categories data, JSON file with widget settings. You can use downloaded archive to move your website to other hosting server or restore website data. ", $cherry_data_manager->slug ); ?>
			</p>
			<p>
				<b><?php _e( "Please note! ", $cherry_data_manager->slug);  ?></b>
				<?php _e("XML file doesn't contain any user data except name and email of the website administrator.", $cherry_data_manager->slug ); ?>
			</p>
			<a id="export-content" class="button-primary_" href="#">
				<?php _e('Export Content', $cherry_data_manager->slug); ?>
			</a><span class="spinner"></span>
			<?php do_action( 'cherry_data_manager_export_page_after' ); ?>
		</div>
	</div>
</div>