<?php
/**
 * Represents the view for the administration dashboard.
 *
 * Import sample content
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
	<div class="<?php echo $cherry_data_manager->ui_wrapper_class( array( 'cherry-wizard_' ) ); ?>">
		<?php
			/**
			 * hook cherry_data_manager_pre_import
			 *
			 * do anything before content import starts
			 */
			do_action( 'cherry_data_manager_pre_import' );

			$action_url      = cherry_dm_get_import_action_url();
			$max_upload_size = cherry_dm_get_upload_size();

		?>
		<div class="box-default_ content-wrap_">
			<?php do_action( 'cherry_data_manager_before_title' ); ?>
			<h2 class="main-title_">
				<?php _e( 'Import &amp; install content', $cherry_data_manager->slug ); ?>
			</h2>
			<?php _e( 'Please, install Sample data in order to make your webste looks exactly as our live demo including all images, pages, layout options etc.', $cherry_data_manager->slug ); ?>
		</div>
		<div class="box-default_ alt-box_ content-wrap_">
			<div class="dm-import-wrap">
				<?php cherry_dm_import_selector(); ?>
				<div class="dm-loaders">
					<div id='upload_status'>
						<div class="progress-bar-counter_"><span>0</span>% <?php _e( 'complete', $cherry_data_manager->slug ); ?></div>
						<div class="progress-bar_ loader_bar"><span class="progress-bar-load_ transition_2"></span></div>
					</div>
					<div id='import_data' class="hidden_">
						<div class="progress-bar-counter_"><span>0</span>% <?php _e( 'complete', $cherry_data_manager->slug ); ?></div>
						<div class="progress-bar_ loader_bar"><span class="progress-bar-load_ transition"></span></div>
					</div>
				</div>
				<div class="box-inner_">
					<!-- drag drop form -->
					<form enctype="multipart/form-data" method="post" action="<?php echo $action_url; ?>" id="upload_files">
						<div id="area-drag-drop">
							<div class="drag-drop-inside">
								<p class="drag-drop-info"><?php _e('Please Drop all needed files here <br> to import sample data', $cherry_data_manager->slug); ?></p>
								<p><?php _e('or', $cherry_data_manager->slug); ?></p>
								<p class="drag-drop-buttons">
									<input type="button" value="<?php _e('Browse local files', $cherry_data_manager->slug); ?>" class="wizard-upload-files button-primary_" >
									<input id="upload_files_html5" style="visibility: hidden; width: 0; height: 0; overflow: hidden; margin:0;" type="file" multiple>
								</p>
								<p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %s.', $cherry_data_manager->slug), esc_html($max_upload_size['formated']) ); ?></p>
							</div>
						</div>
					</form>
					<!-- end drag drop form -->
					<div id="import_step_2" class="hidden_">
					<!-- file_list -->
						<div id="file_list_holder">
							<div id="file_list" class="dm-files-list">
								<div id="file_list_header" class="dm-files-list-header">
									<div class='row'>
										<div class="column_1"><?php _e( "File name", $cherry_data_manager->slug) ?></div><div class="column_2"><?php _e( "File size", $cherry_data_manager->slug) ?></div><div class="column_3"><?php _e('Uploaded file:', $cherry_data_manager->slug); ?> <span id="upload_counter"><b>0</b></span> <span class="items_name"><?php _e( "item", $cherry_data_manager->slug) ?></span></div>
									</div>
								</div>
								<div id="file_list_body" class="dm-files-list-body"></div>
							</div>
						</div>
					<!-- end file_list -->
					<!-- log -->
						<div id="import_xml_status" class="hidden_">
							<div id="status_log">
								<div class="dm-install-item"><?php _e('Installing content started.', $cherry_data_manager->slug); ?></div>
							</div>
						</div>
					<!--end log -->
						<div id="import_status" class="clearfix">
							<div id="info_holder" class="hidden_">
								<p>
									<span class="upload_status_text"><?php _e( "Files successfully uploaded. Please make sure you have uploaded <b>.JSON</b> and <b>.XML</b> files to install theme sample data.", $cherry_data_manager->slug) ?><a href="http://info.template-help.com/help/quick-start-guide/wordpress-themes/master/index_en.html#theme_sample_data" target="_blank" id="info_link"><i class="icon-info-sign"></i></a></span>
									<br>
									<a class="wizard-upload-files" href="#"><?php _e('Add More Files', $cherry_data_manager->slug); ?></a>
								</p>
								<a class="button-primary_ not_active next_step" href="#"><?php _e('Continue Install', $cherry_data_manager->slug); ?></a><span id="not_load_file" class="hidden_"><?php _e('Missing .XML or .JSON file', $cherry_data_manager->slug); ?></span>
							</div>
						</div>
					</div>
					<div id="import_step_3" class="hidden_">
						<div class="box-default_ box-default_ content-box install-finished_">
							<h3><?php _e( 'Congratulations', $cherry_data_manager->slug ); ?></h3>
							<p class="install-finished-text_">
								<?php _e( 'Content has been successfully installed', $cherry_data_manager->slug ); ?>
							</p>
							<div class="install-finished-actions_">
								<a class="button-default_" href="<?php echo home_url(); ?>">
									<?php _e( 'View your site', $cherry_data_manager->slug ); ?>
								</a>
								<a class="button-primary_" href="<?php echo menu_page_url( 'options', false ); ?>" target="_parent">
									<?php _e( 'Go to Cherry Options', $cherry_data_manager->slug ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>