<?php
/**
 * Add cherry theme install sample content service methods
 *
 * @package   cherry_data_manager
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * main importer class
 *
 * @since  1.0.1
 */
class Cherry_Data_Manager_Install_Tools {

	/**
	 * Holds XML file path
	 * @var string
	 */
	public $xml_file = null;

	public $html_meta = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {
		$this->xml_file = get_transient( 'cherry_xml_file_path' );
	}

	/**
	 * Set XML file path
	 *
	 * @since 1.0.1
	 * @param string $file file path
	 */
	public function add_xml_file( $file ) {
		set_transient( 'cherry_xml_file_path', $file, DAY_IN_SECONDS );
		return true;
	}

	/**
	 * parse XML file into data array
	 *
	 * @param  string $file path to XML file
	 * @return array        parsed data
	 * @since  1.0.1
	 */
	public function _parse_xml( $file ) {

		$file_content = file_get_contents($file);
		$file_content = iconv('utf-8', 'utf-8//IGNORE', $file_content);
		$file_content = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $file_content);

		if ( !$file_content ) {
			return false;
		}

		$dom = new DOMDocument('1.0');
		$dom->loadXML( $file_content );

		$xml                   = simplexml_import_dom( $dom );
		$old_upload_url        = $xml->xpath('/rss/channel/wp:base_site_url');
		$old_upload_url        = $old_upload_url[0];
		$upload_dir            = wp_upload_dir();
		$upload_url            = $upload_dir['url'] . '/';
		$upload_dir            = $upload_dir['url'];
		$cut_upload_dir        = substr($upload_dir, strpos($upload_dir, 'wp-content/uploads'), strlen($upload_dir)-1);
		$cut_date_upload_dir   = '<![CDATA[' . substr($upload_dir, strpos($upload_dir, 'wp-content/uploads') + 19, strlen( $upload_dir ) - 1 );
		$cut_date_upload_dir_2 = "\"" . substr($upload_dir, strpos($upload_dir, 'wp-content/uploads') + 19, strlen( $upload_dir ) - 1 );

		$pattern = '/[\"\']http:.{2}(?!livedemo|ld-wp).[^\'\"]*wp-content.[^\'\"]*\/(.[^\/\'\"]*\.(?:jp[e]?g|png|mp4|webm|ogv))[\"\']/i';

		$patternCDATA       = '/<!\[CDATA\[\d{4}\/\d{2}/i';
		$pattern_meta_value = '/("|\')\d{4}\/\d{2}/i';

		$file_content = str_replace( $old_upload_url, site_url(), $file_content );
		$file_content = preg_replace( $patternCDATA, $cut_date_upload_dir, $file_content );
		$file_content = preg_replace( $pattern_meta_value, $cut_date_upload_dir_2, $file_content );
		$file_content = preg_replace( $pattern, '"' . $upload_url .'$1"', $file_content );

		$parser       = new Cherry_WXR_Parser();
		$parser_array = $parser->parse( $file_content, $file );

		return $parser_array;

	}

	/**
	 * Get parsed XML data
	 *
	 * @since  1.0.1
	 * @param  string $key
	 * @return mixed
	 */
	public function get_xml_data( $key ) {

		if ( ! $this->xml_file ) {
			return false;
		}

		$data = $this->_parse_xml( $this->xml_file );

		if ( isset( $data[$key] ) ) {
			return $data[$key];
		}

	}

	/**
	 * Add unserialized meta to prevent dropping HTML markup
	 *
	 * @param int    $post_id post ID
	 * @param string $key     meta key
	 * @param string $value   meta value
	 */
	public function fix_html_meta( $post_id, $key, $value ) {

		// esc HTML meta
		$value = preg_replace_callback(
			'/(\"fetures-text\";s:)(\d+)(:\")(.*)(\";s:5:\"price\")/s',
			array( $this, 'replace_html_meta' ),
			$value
		);

		$value = maybe_unserialize( $value );
		add_post_meta( $post_id, $key, $value );

		if ( null != $this->html_meta ) {
			$meta = get_post_meta( $post_id, $key, true );
			$meta['fetures-text'] = $this->html_meta;
			update_post_meta( $post_id, $key, $meta );
		}

	}

	/**
	 * Replace callback for HTML meta search
	 *
	 * @since  1.0.4
	 * @param  array $matches
	 * @return string
	 */
	public function replace_html_meta( $matches ) {

		if ( empty( $matches[4] ) ) {
			$this->html_meta = null;
			return $matches[0];
		}

		$this->html_meta = $matches[4];

		return $matches[1] . '0' . $matches[3] . $matches[5];
	}

	/**
	 * Get file content via WP Filesystem API
	 *
	 * @since  1.0.4
	 *
	 * @param  string $file   file path
	 * @param  bool   $remove remove file after installation finished
	 * @return mixed
	 */
	public function get_contents( $file, $remove = true ) {

		if ( ! $file ) {
			return false;
		}

		global $cherry_data_manager;

		$res = $cherry_data_manager->importer->fs_connect();

		if ( ! $res ) {
			return false;
		}

		global $wp_filesystem;

		$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );

		if ( ! $wp_filesystem->exists( $file ) ) {
			return false;
		}

		if ( true == $remove ) {
			$_SESSION['files_to_remove'][] = $file;
		}

		return $wp_filesystem->get_contents( $file );

	}

	/**
	 * Remove service files after installation finished
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function clean_files() {

		if ( empty( $_SESSION['files_to_remove'] ) ) {
			return false;
		}

		global $cherry_data_manager;

		$res = $cherry_data_manager->importer->fs_connect();

		if ( ! $res ) {
			return false;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			return false;
		}

		foreach ( $_SESSION['files_to_remove'] as $file ) {
			if ( $wp_filesystem->exists( $file ) ) {
				$wp_filesystem->delete( $file );
			}
		}

		unset( $_SESSION['files_to_remove'] );
	}

	/**
	 * unpack ziped folders
	 *
	 * @since 1.0.6
	 */
	public function unpack_dirs( $upload_dir ) {

		$unpack_dirs = apply_filters(
			'cherry_data_manager_packed_dirs',
			array( 'templates', 'cherry-style-switcher' )
		);

		include_once( ABSPATH . '/wp-admin/includes/class-pclzip.php' );

		foreach ( $unpack_dirs as $dir ) {
			$zip_file        = $upload_dir . $dir . '.zip';
			$wp_upload       = wp_upload_dir();
			$upload_base_dir = $wp_upload['basedir'];

			if ( ! file_exists( $zip_file ) ) {
				continue;
			}

			$_SESSION['files_to_remove'][] = $zip_file;

			$zip = new PclZip( $zip_file );
			$zip->extract( PCLZIP_OPT_ADD_PATH, $upload_base_dir . '/' . $dir );
		}

	}

	/**
	 * Set meta to rewitten ID values
	 *
	 * @since  1.0.8
	 * @param  array  $meta       rewritten meta field data.
	 * @param  string $new_values rewritten id's values.
	 * @param  array  $old_meta   existing meta array.
	 * @return array
	 */
	public function update_rewritten_meta( $meta, $new_values, $old_meta ) {

		if ( ! is_array( $meta['inner_key'] ) ) {
			return array_merge( $old_meta, array( $meta['inner_key'] => $new_values ) );
		}

		reset( $meta['inner_key'] );
		$new_key        = key( $meta['inner_key'] );
		$new_key_nested = $meta['inner_key'][ $new_key ];

		if ( isset( $old_meta[ $new_key ][ $new_key_nested ] ) ) {
			$old_meta[ $new_key ][ $new_key_nested ] = $new_values;
		}

		return $old_meta;
	}

	/**
	 * Prepare meta field to rewrite ids
	 *
	 * @since  1.0.8
	 * @param  array $meta                current meta field.
	 * @param  array $rewrite_meta_fields array of meta fields to rewrite
	 * @return array
	 */
	public function prepare_meta_to_rewrite( $meta, $rewrite_meta_fields ) {

		$defaults = array(
			'key'       => '',
			'inner_key' => false,
			'val'       => '',
		);

		if ( false === $rewrite_meta_fields[ $meta['key'] ] ) {
			return array_merge(
				$defaults,
				array(
					'key' => $meta['key'],
					'val' => $meta['value'],
				)
			);
		}

		$value = maybe_unserialize( $meta['value'] );

		return array_merge(
			$defaults,
			array(
				'key'       => $meta['key'],
				'inner_key' => $rewrite_meta_fields[ $meta['key'] ],
				'val'       => $this->get_val_by_key( $value, $rewrite_meta_fields[ $meta['key'] ] ),
			)
		);

	}

	/**
	 * Recursive get inner meta value by key
	 *
	 * @since  1.0.8
	 * @param  array $value meta value.
	 * @param  mixed $key   string or array nnested key.
	 * @return mixed
	 */
	public function get_val_by_key( $value, $key ) {

		if ( ! is_array( $key ) ) {
			return isset( $value[ $key ] ) ? $value[ $key ] : false;
		}

		reset( $key );
		$new_key    = key( $key );
		$new_value  = isset( $new_value[ $key ] ) ? $new_value[ $key ] : array();
		$search_key = $search_key[ $new_key ];

		return $this->get_val_by_key( $new_value, $search_key );

	}

	/**
	 * Get attachment ID by URL
	 *
	 * @since  1.0.8
	 * @param  string $url image URL
	 * @return int|bool false
	 */
	public function get_id_by_url( $url ) {

		global $wpdb;
		$url = esc_url( stripslashes( $url ) );
		$query = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $url );
		$id = $wpdb->get_var( $query );

		if ( ! $id ) {
			return false;
		}

		return $id;
	}

	/**
	 * Check if is content package installation
	 *
	 * @since  1.0.9
	 * @return boolean
	 */
	public function is_package_install() {

		if ( empty( $_SESSION['monstroid_install_type'] ) ) {
			return false;
		}

		$type = esc_attr( $_SESSION['monstroid_install_type'] );

		if ( in_array( $type, array( 'advanced', 'full' ) ) ) {
			return false;
		}

		return true;

	}

}
