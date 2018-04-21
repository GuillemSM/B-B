<?php
/**
 * Add cherry theme import sample content controllers
 *
 * @package   cherry_data_manager
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * main importer class
 *
 * @since  1.0.0
 */
class cherry_dm_content_importer {

	/**
	 * Check if auto upload is avaliable
	 *
	 * @since  1.0.0
	 */
	public $auto_upload = false;

	/**
	 * Cherry theme key (provided only if is import via wizard)
	 *
	 * @since  1.0.0
	 */
	public $cherry_key = false;

	/**
	 * Check if auto upload is avaliable
	 *
	 * @since  1.0.0
	 */
	public $theme_folder_status = 200;

	/**
	 * Transient name to save file list in
	 *
	 * @since  1.0.0
	 */
	public $transient_key = 'cherry_data_manager_content_uploads';


	function __construct() {

		/**
		 * Setup cherry license key for import via wizard
		 * @var string or boolean
		 */
		$this->cherry_key = apply_filters( 'cherry_data_manager_import_key', $this->cherry_key );

		add_action( 'admin_enqueue_scripts', array( $this, 'prepare_js' ), 99 );

		add_action( 'wp_ajax_cherry_dm_content_handle_load', array( $this, 'process_handle_upload' ), 10 );
		add_action( 'wp_ajax_cherry_dm_content_auto_load', array( $this, 'process_auto_upload' ) );
		add_action( 'cherry_data_manager_pre_import', array( $this, 'auto_load_warnings' ) );
	}

	/**
	 * Connect to the filesystem.
	 *
	 * @since 1.0.0
	 *
	 * @param array $directories                  Optional. A list of directories. If any of these do
	 *                                            not exist, a {@see WP_Error} object will be returned.
	 *                                            Default empty array.
	 * @param bool  $allow_relaxed_file_ownership Whether to allow relaxed file ownership.
	 *                                            Default false.
	 * @return bool|WP_Error True if able to connect, false or a {@see WP_Error} otherwise.
	 */
	public function fs_connect( $directories = array(), $allow_relaxed_file_ownership = false ) {

		global $wp_filesystem;

		$url = admin_url( 'options.php' );

		if ( false === ( $credentials = request_filesystem_credentials( $url, '', false, false, array(), $allow_relaxed_file_ownership ) ) ) {
			return false;
		}

		if ( empty( $directories ) ) {
			$dirs = $directories;
		} else {
			$dirs = $directories[0];
		}

		if ( ! WP_Filesystem( $credentials, $dirs, $allow_relaxed_file_ownership ) ) {
			$error = true;
			if ( is_object($wp_filesystem) && $wp_filesystem->errors->get_error_code() ) {
				$error = $wp_filesystem->errors;
			}
			return false;
		}

		if ( ! is_object($wp_filesystem) )
			return new WP_Error('fs_unavailable', $this->strings['fs_unavailable'] );

		if ( is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code() )
			return new WP_Error('fs_error', $this->strings['fs_error'], $wp_filesystem->errors);

		foreach ( (array)$directories as $dir ) {
			switch ( $dir ) {
				case ABSPATH:
					if ( ! $wp_filesystem->abspath() )
						return new WP_Error('fs_no_root_dir', $this->strings['fs_no_root_dir']);
					break;
				case WP_CONTENT_DIR:
					if ( ! $wp_filesystem->wp_content_dir() )
						return new WP_Error('fs_no_content_dir', $this->strings['fs_no_content_dir']);
					break;
				case WP_PLUGIN_DIR:
					if ( ! $wp_filesystem->wp_plugins_dir() )
						return new WP_Error('fs_no_plugins_dir', $this->strings['fs_no_plugins_dir']);
					break;
				case get_theme_root():
					if ( ! $wp_filesystem->wp_themes_dir() )
						return new WP_Error('fs_no_themes_dir', $this->strings['fs_no_themes_dir']);
					break;
				default:
					if ( ! $wp_filesystem->find_folder($dir) )
						return new WP_Error( 'fs_no_folder', sprintf( $this->strings['fs_no_folder'], esc_html( basename( $dir ) ) ) );
					break;
			}
		}

		return true;
	}

	/**
	 * Download a package.
	 *
	 * @since 2.8.0
	 *
	 * @param string $package The URI of the package. If this is the full path to an
	 *                        existing local file, it will be returned untouched.
	 * @return string|WP_Error The full path to the downloaded package file, or a {@see WP_Error} object.
	 */
	public function download_package( $package ) {

		/**
		 * Filter whether to return the package.
		 *
		 * @since 3.7.0
		 *
		 * @param bool        $reply   Whether to bail without returning the package.
		 *                             Default false.
		 * @param string      $package The package file name.
		 * @param WP_Upgrader $this    The WP_Upgrader instance.
		 */
		$reply = apply_filters( 'upgrader_pre_download', false, $package, $this );

		if ( false !== $reply ) {
			return $reply;
		}

		if ( ! preg_match('!^(http|https|ftp)://!i', $package) && file_exists($package) ) {
			return $package; //must be a local file..
		}

		if ( empty($package) ) {
			return new WP_Error('no_package', $this->strings['no_package']);
		}

		$download_file = download_url( $package );

		if ( is_wp_error($download_file) ) {
			return new WP_Error(
				'download_failed', $this->strings['download_failed'], $download_file->get_error_message()
			);
		}

		return $download_file;

	}

	/**
	 * Unpack a compressed package file.
	 *
	 * @since 2.8.0
	 *
	 * @param string $package        Full path to the package file.
	 * @param bool   $delete_package Optional. Whether to delete the package file after attempting
	 *                               to unpack it. Default true.
	 * @return string|WP_Error The path to the unpacked contents, or a {@see WP_Error} on failure.
	 */
	public function unpack_package( $package, $delete_package = true ) {

		global $wp_filesystem;

		$upload_folder = str_replace( ABSPATH, $wp_filesystem->abspath(), cherry_dm_get_upload_path() );

		// We need a working directory - Strip off any .tmp or .zip suffixes
		$working_dir = $upload_folder;

		// Unzip package to working directory
		$zip    = new PclZip( $package );
		$result = $zip->extract(
			PCLZIP_OPT_BY_NAME, "sample_data/",
			PCLZIP_OPT_ADD_PATH, $working_dir,
			PCLZIP_OPT_REMOVE_PATH, "sample_data/"
		);
		//$result = unzip_file( $package, $working_dir );

		// Once extracted, delete the package if required.
		if ( $delete_package ) {
			unlink( $package );
		}

		if ( 0 == $result ) {
			return $zip->errorInfo( true );
		}

		return $working_dir;
	}

	/**
	 * Process handle file upolads from local drive
	 *
	 * @since 1.0.0
	 */
	public function process_handle_upload() {

		if ( !isset( $_REQUEST['action'] ) || 'upload_files' != $_REQUEST['action'] && !isset( $_REQUEST['dir'] ) ) {
			return;
		}

		// check user caps
		if ( !current_user_can( 'upload_files' ) ) {
			wp_die( 'You don\'t have permissions to do this', 'Error' );
		}

		if ( strtolower($_SERVER['REQUEST_METHOD']) != 'post' ) {
			wp_die( 'You don\'t have permissions to do this', 'Error' );
		}

		if ( array_key_exists( 'file', $_FILES ) ) {
			$upload_dir  = urldecode( $_REQUEST['dir'] );
			$file_name   = basename($_FILES['file']['name']);
			$upload_file = $upload_dir . $file_name;
			$result      = move_uploaded_file($_FILES['file']['tmp_name'], $upload_file);
		}

		die();
	}

	/**
	 * Show warning notices if auto load not avaliable
	 *
	 * @since  1.0.0
	 */
	function auto_load_warnings() {

		if ( $this->auto_upload || !$this->cherry_key ) {
			return;
		}

		//echo $this->theme_folder_status;
	}

	/**
	 * process automatic file upload from TM cloud
	 *
	 * @since  1.0.0
	 */
	public function process_auto_upload() {

		//make sure request is comming from Ajax
		$xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
		if (!$xhr) {
			header('HTTP/1.1 500 Error: Request must come from Ajax!');
			exit();
		}

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'cherry-dm-nonce' ) );

		global $cherry_data_manager;

		ob_start();

		$response = array(
			'type' => 'error'
		);

		/**
		 * Filter sample data remote link
		 *
		 * @since 1.0.9
		 * @param string $url remote sample data URL
		 */
		$url = apply_filters( 'cherry_data_manager_cloud_sample_data_url', false );

		if ( ! $url ) {

			if ( ! isset( $_SESSION['cherry_data']['sample'] ) ) {
				$response['message'] = __( 'Can\'t find link to sample data', $cherry_data_manager->slug );
				ob_clean();
				wp_send_json( $response );
			}

			$url = $_SESSION['cherry_data']['sample'];

		}

		$res = $this->fs_connect( array( cherry_dm_get_upload_path() ) );

		if ( ! $res ) {
			$response['message'] = __( 'Can\'t connect to file system', $cherry_data_manager->slug );
			ob_clean();
			wp_send_json( $response );
		}

		$download    = $this->download_package( $url );
		$working_dir = $this->unpack_package( $download, true );

		$response['type']    = 'success';
		$response['message'] = __( 'File imported', $cherry_data_manager->slug );
		ob_clean();
		wp_send_json( $response );

	}

	public function is_xml( $filename ) {
		$pathinfo = pathinfo($filename);
		return $pathinfo['extension'] == 'xml';
	}

	/**
	 * Auto file uploader - response content markup
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $file       uploaded file name
	 * @param  string  $result     file uploading result
	 * @param  string  $file_size  uploaded file size
	 */
	public function response_message( $file = 'filename', $result = 'error', $file_size = null ) {

		if ( $file_size ) {
			$file_size = '<div class="dm-uploaded-file-size">' . $file_size . '</div>';
		}

		$output = '<div class="row row-' . $result . '" data-file="' . $file . '"><div class="column_1">' . $file . '</div><div class="column_2">' . $file_size . '</div><div class="column_3"><span class="dm-load-status"></span></div></div>';

		return $output;
	}

	/**
	 * Prepare JS variables
	 *
	 * @since 1.0.0
	 */
	function prepare_js() {

		global $cherry_data_manager;

		$import_row = $this->response_message( '%file%', 'preload', '' );
		wp_localize_script( $cherry_data_manager->slug . '-importer', 'cherry_dm_import_row', $import_row );
	}

}

global $cherry_data_manager;
$cherry_data_manager->importer = new cherry_dm_content_importer();