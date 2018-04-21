<?php
/**
 * Add cherry theme install sample content controllers
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
 * @since  1.0.0
 */
class cherry_data_manager_content_installer {

	/**
	 * Holder for tools class instance
	 *
	 * @since 1.1.0
	 */
	public $tools = false;

	/**
	 * Set transients prefix
	 *
	 * @since 1.0.0
	 */
	public $transients_prefix = '_cherry_content_import_';

	function __construct() {

		include_once ( 'libs/parsers.php' );
		include_once ( 'class-cherry-data-manager-install-tools.php' );

		$this->tools = new Cherry_Data_Manager_Install_Tools();

		if ( ! session_id() ) {
			session_start();
		}

		if ( ! isset( $_SESSION['files_to_remove'] ) ) {
			$_SESSION['files_to_remove'] = array();
		}

		add_action( 'wp_ajax_import_xml',                          array( $this, 'import_xml' ) );
		add_action( 'wp_ajax_import_options',                      array( $this, 'import_options' ) );
		add_action( 'wp_ajax_import_custom_tables',                array( $this, 'import_custom_tables' ) );
		add_action( 'wp_ajax_import_categories',                   array( $this, 'import_categories' ) );
		add_action( 'wp_ajax_import_tags',                         array( $this, 'import_tags' ) );
		add_action( 'wp_ajax_process_terms',                       array( $this, 'process_terms' ) );
		add_action( 'wp_ajax_import_posts',                        array( $this, 'import_posts' ) );
		add_action( 'wp_ajax_attach_terms',                        array( $this, 'attach_terms' ) );
		add_action( 'wp_ajax_attach_comments',                     array( $this, 'attach_comments' ) );
		add_action( 'wp_ajax_attach_postmeta',                     array( $this, 'attach_postmeta' ) );
		add_action( 'wp_ajax_import_menu_item',                    array( $this, 'import_menu_item' ) );
		add_action( 'wp_ajax_import_attachment',                   array( $this, 'import_attachment' ) );
		add_action( 'wp_ajax_generate_attachment_metadata_step_1', array( $this, 'generate_attachment_metadata_1' ) );
		add_action( 'wp_ajax_generate_attachment_metadata_step_2', array( $this, 'generate_attachment_metadata_2' ) );
		add_action( 'wp_ajax_generate_attachment_metadata_step_3', array( $this, 'generate_attachment_metadata_3' ) );
		add_action( 'wp_ajax_generate_attachment_metadata_step_4', array( $this, 'generate_attachment_metadata_4' ) );
		add_action( 'wp_ajax_generate_attachment_metadata_step_5', array( $this, 'generate_attachment_metadata_5' ) );
		add_action( 'wp_ajax_generate_attachment_metadata_step_6', array( $this, 'generate_attachment_metadata_6' ) );
		add_action( 'wp_ajax_import_attachment_metadata',          array( $this, 'import_attachment_metadata' ) );
		add_action( 'wp_ajax_import_parents',                      array( $this, 'import_parents' ) );
		add_action( 'wp_ajax_update_featured_images',              array( $this, 'update_featured_images' ) );
		add_action( 'wp_ajax_update_attachment',                   array( $this, 'update_attachment' ) );
		add_action( 'wp_ajax_import_json',                         array( $this, 'import_json' ) );

	}

	/**
	 * Import and parse XML file
	 *
	 * Import Step#1
	 *
	 * @since 1.0.0
	 */
	public function import_xml(){

		$this->verify_nonce();

		do_action( 'cherry_data_manager_import_xml' );

		add_filter( 'import_post_meta_key', 'cherry_plugin_is_valid_meta_key' );

		$xml_file = isset($_POST['file']) ? $_POST['file'] : get_transient( 'cherry_content_xml_file' );

		if ( !$xml_file ) {
			$xml_file = 'sample_data.xml';
		}

		$upload_dir = cherry_dm_get_upload_path();

		if ( ! file_exists( $upload_dir . $xml_file ) ) {
			exit('error');
		}

		$_SESSION['files_to_remove'][] = $upload_dir . $xml_file;

		$this->tools->add_xml_file( $upload_dir . $xml_file );

		$ids_file = $upload_dir . 'rewrite-ids.json';

		$ids_data = $this->tools->get_contents( $ids_file );

		if ( false !== $ids_data ) {

			$ids_data = json_decode( $ids_data, true );
			set_transient( $this->transients_prefix . 'ids_data', $ids_data, DAY_IN_SECONDS );

			if ( isset( $ids_data['menus'] ) && !empty( $ids_data['menus'] ) ) {
				$menus = array_values( $ids_data['menus'] );
				set_transient( $this->transients_prefix . 'menus', $menus, DAY_IN_SECONDS );
			}

		}

		/**
		 * Hook fires after successfull XML import
		 */
		do_action( 'cherry_data_manager_start_import' );

		$this->import_start();

		exit( 'import_options' );
	}

	/**
	 * Import site options and unpack template files to uploads dir
	 *
	 * Import Step#2
	 *
	 * @since 1.0.0
	 */
	public function import_options(){

		$this->verify_nonce();

		ob_start();

		$options_file = 'options.json';
		$upload_dir   = cherry_dm_get_upload_path();
		$json         = $this->tools->get_contents( $upload_dir . $options_file );

		if ( false == $json ) {
			exit('import_custom_tables');
		}

		$options = json_decode( $json, true );

		if ( ! is_array( $options ) ) {
			ob_clean();
			exit( 'import_custom_tables' );
		}

		$theme = get_option( 'stylesheet' );

		$patterns = array(
			'/(theme\d+|monstroid)/',
			'/(theme\d+|monstroid)_defaults/',
			'/(theme\d+|monstroid)_statics/',
			'/(theme\d+|monstroid)_statics_defaults/',
		);

		$replace = array(
			$theme,
			$theme . '_defaults',
			$theme . '_statics',
			$theme  . '_statics_defaults',
		);

		foreach ( $options as $option_name => $option_val ) {
			$option_name = preg_replace( $patterns, $replace, $option_name );

			if ( 'cherry-options' == $option_name ) {
				$option_val['id'] = $theme;
			}

			update_option( $option_name, $option_val );
		}

		/**
		 * Hook fires after succesfull options import
		 */
		do_action( 'cherry_data_manager_import_options' );

		/**
		 * Also we unpack separate folders on this step (if templates.zip was uploaded)
		 */
		$this->tools->unpack_dirs( $upload_dir );

		ob_clean();
		exit( 'import_custom_tables' );
	}

	/**
	 * Import custom tables into database
	 * @since  1.0.0
	 */
	function import_custom_tables() {
		$this->verify_nonce();

		$tables_file = 'tables.json';
		$upload_dir  = cherry_dm_get_upload_path();
		$json        = $this->tools->get_contents( $upload_dir . $tables_file );

		if ( false == $json ) {
			exit('import_categories');
		}

		$tables = json_decode( $json, true );
		if ( ! is_array( $tables ) || empty( $tables ) ) {
			exit( 'import_categories' );
		}

		global $wpdb;
		foreach ( $tables as $table => $table_data ) {

			$table_name = $wpdb->prefix . $table;

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				continue;
			}

			foreach ( $table_data as $row ) {
				if ( 'mpsl_slides' === $table ) {
					$row = $this->remap_slider_urls( $row );
				}
				$wpdb->replace( $table_name, $row );
			}

		}

		exit( 'import_categories' );
	}

	/**
	 * Replace URL for moto slider
	 *
	 * @since  1.0.0
	 *
	 * @param  array $data_row array with imported row
	 * @return array           parsed array
	 */
	public function remap_slider_urls( $data_row ) {

		if ( ! is_array( $data_row  ) ) {
			return $data_row;
		}

		$maybe_has_image = array( 'options', 'layers' );
		$upload_dir      = wp_upload_dir();
		$upload_url      = $upload_dir['url'] . '/';

		foreach ( $maybe_has_image as $key ) {

			if ( empty( $data_row[ $key ] ) ) {
				continue;
			}

			$json_string = preg_replace(
				'/[\"\']http:.{4}(?!livedemo|ld-wp).[^\'\"]*wp-content.[^\'\"]*\/(.[^\/\'\"]*\.(?:jp[e]?g|png))[\"\']/',
				json_encode( $upload_url .'$1' ),
				$data_row[ $key ]
			);

			$data_row[ $key ] = $json_string;

		}

		return $data_row;

	}

	/**
	 * Import post categories
	 *
	 * Import Step#3
	 *
	 * @since 1.0.0
	 */
	public function import_categories(){

		$this->verify_nonce();

		$categories_array = $this->tools->get_xml_data( 'categories' );
		$categories_array = apply_filters( 'wp_import_categories', $categories_array );

		if ( empty( $categories_array ) ) {
			exit('import_tags');
		}

		foreach ( $categories_array as $cat ) {

			// if the category already exists leave it alone
			$term_id = term_exists( $cat['category_nicename'], 'category' );

			if ( $term_id ) {

				if ( is_array($term_id) ) $term_id = $term_id['term_id'];

				if ( isset($cat['term_id']) ) {
					$_SESSION['processed_terms'][intval($cat['term_id'])] = (int) $term_id;
				}

				continue;
			}

			$category_parent      = empty( $cat['category_parent'] ) ? 0 : $this->_category_exists( $cat['category_parent'] );
			$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';

			$catarr = array(
				'category_nicename'    => $cat['category_nicename'],
				'category_parent'      => $category_parent,
				'cat_name'             => $cat['cat_name'],
				'category_description' => $category_description
			);

			$id = wp_insert_category( $catarr );

			if ( ! is_wp_error( $id ) ) {
				if ( isset($cat['term_id']) ) {
					$_SESSION['processed_terms'][intval($cat['term_id'])] = $id;
				}
			} else {
				continue;
			}
		}

		/**
		 * Hook fires after succesfull categories import
		 */
		do_action( 'cherry_data_manager_import_categories' );

		exit('import_tags');

	}

	/**
	 * Import post tags
	 *
	 * Import Step#4
	 *
	 * @since 1.0.0
	 */
	function import_tags() {

		$this->verify_nonce();

		$tag_array = $this->tools->get_xml_data( 'tags' );
		$tag_array = apply_filters( 'wp_import_tags', $tag_array );

		if ( empty( $tag_array ) ) {
			exit('process_terms');
		}

		foreach ( $tag_array as $tag ) {

			// if the tag already exists leave it alone
			$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
			if ( $term_id ) {

				if ( is_array($term_id) ) $term_id = $term_id['term_id'];

				if ( isset($tag['term_id']) ) {
					$_SESSION['processed_terms'][intval($tag['term_id'])] = (int) $term_id;
				}
				continue;
			}

			$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
			$tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

			$id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($tag['term_id']) ) {
					$_SESSION['processed_terms'][intval($tag['term_id'])] = $id['term_id'];
				}
			} else {
				continue;
			}
		}

		/**
		 * Hook fires after sucessfull tags import
		 */
		do_action( 'cherry_data_manager_import_tags' );

		exit('process_terms');
	}

	/**
	 * Process custom terms
	 *
	 * Import Step#5
	 *
	 * @since 1.0.0
	 */
	function process_terms() {

		$this->verify_nonce();


		$terms = $this->tools->get_xml_data( 'terms' );
		$terms = apply_filters( 'wp_import_terms', $terms );

		$_SESSION['processed_menus'] = array();

		if ( empty( $terms ) ) {
			exit('import_posts');
		}

		foreach ( $terms as $term ) {

			// if the term already exists in the correct taxonomy leave it alone
			$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
			if ( $term_id ) {
				if ( is_array($term_id) ) {
					$term_id = $term_id['term_id'];
				}
				if ( isset($term['term_id']) ){
					$_SESSION['processed_terms'][intval($term['term_id'])] = (int) $term_id;
					if ( 'nav_menu' == $term['term_taxonomy'] ) {
						$_SESSION['processed_menus'][$term_id] = $term['term_id'];
					}
					continue;
				}
			}
			if ( empty( $term['term_parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
				if ( is_array( $parent ) ) $parent = $parent['term_id'];
			}

			$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
			$termarr     = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent) );

			$id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );

			if ( is_wp_error( $id ) ) {
				continue;
			}

			if ( !isset($term['term_id']) ) {
				continue;
			}

			$_SESSION['processed_terms'][intval($term['term_id'])] = $id['term_id'];

			if ( 'nav_menu' == $term['term_taxonomy'] ) {
				$_SESSION['processed_menus'][$id['term_id']] = $term['term_id'];
			}
		}

		/**
		 * Hook fires after successfull terms processing
		 */
		do_action( 'cherry_data_manager_process_terms' );

		exit('import_posts');
	}

	/**
	 * Process all posts
	 *
	 * Import Step#6
	 *
	 * @since 1.0.0
	 */
	function import_posts() {

		$this->verify_nonce();

		$_SESSION['url_remap']            = array();
		$_SESSION['featured_images']      = array();
		$_SESSION['attachment_posts']     = array();
		$_SESSION['processed_posts']      = array();
		$_SESSION['menu_items']           = array();
		$_SESSION['post_orphans']         = array();

		$posts_array      = $this->tools->get_xml_data( 'posts' );
		$posts_array      = apply_filters( 'wp_import_posts', $posts_array );
		$attachment_posts = array();

		$ids_data = get_transient( $this->transients_prefix . 'ids_data' );
		$ids_data = isset($ids_data['posts']) ? $ids_data['posts'] : array();

		$author = (int) get_current_user_id();

		$default_postdata = array(
			'import_id'      => '',
			'post_author'    => $author,
			'post_date'      => '',
			'post_date_gmt'  => '',
			'post_content'   => '',
			'post_excerpt'   => '',
			'post_title'     => '',
			'post_status'    => '',
			'post_name'      => '',
			'comment_status' => '',
			'ping_status'    => '',
			'guid'           => '',
			'post_parent'    => '',
			'menu_order'     => '',
			'post_type'      => '',
			'post_password'  => ''
		);

		$i = 0;

		foreach ( $posts_array as $post ) {

			$i++;

			$post = apply_filters( 'wp_import_post_data_raw', $post );

			if ( ! post_type_exists( $post['post_type'] ) ) {
				// Failed to import
				do_action( 'wp_import_post_exists', $post );
				continue;
			}

			// do nothing if already process
			if ( isset( $_SESSION['processed_posts'][$post['post_id']] ) && ! empty( $post['post_id'] ) ) {
				continue;
			}

			// do nothing if this post is auto draft
			if ( $post['status'] == 'auto-draft' ) {
				continue;
			}

			// do nothing if this is nav menu item
			if ( 'nav_menu_item' == $post['post_type'] ) {
				$_SESSION['menu_items'][$post['post_id']] = $post;
				continue;
			}

			//!!!!$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );

			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				// already exists
				$comment_post_ID = $post_id = $post_exists;
				continue;
			}

			$post_parent = (int)$post['post_parent'];

			if ( $post_parent ) {

				// if we already know the parent, map it to the new local ID
				if ( isset( $_SESSION['processed_posts'][$post_parent] ) ) {
					$post_parent = $_SESSION['processed_posts'][$post_parent];
				// otherwise record the parent for later
				} else {
					$_SESSION['post_orphans'][intval($post['post_id'])] = $post_parent;
					$post_parent = 0;
				}
			}

			if ( 'attachment' == $post['post_type'] ) {
				array_push($attachment_posts, $post);
				continue;
			}

			$postdata = wp_parse_args( array(
				'import_id'      => $post['post_id'],
				'post_date'      => $post['post_date'],
				'post_date_gmt'  => $post['post_date_gmt'],
				'post_content'   => $post['post_content'],
				'post_excerpt'   => $post['post_excerpt'],
				'post_title'     => $post['post_title'],
				'post_status'    => $post['status'],
				'post_name'      => $post['post_name'],
				'comment_status' => $post['comment_status'],
				'ping_status'    => $post['ping_status'],
				'guid'           => $post['guid'],
				'post_parent'    => $post_parent,
				'menu_order'     => $post['menu_order'],
				'post_type'      => $post['post_type'],
				'post_password'  => $post['post_password']
			), $default_postdata );

			$original_post_ID = $post['post_id'];
			$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

			ini_set('max_execution_time', 300);
			set_time_limit(0);

			$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
			do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );

			if ( is_wp_error( $post_id ) ) {
				// Failed to import
				continue;
			}

			$this->new_id_to_option( $original_post_ID, $post_id, $ids_data );

			if ( $post['is_sticky'] == 1 ) stick_post( $post_id );

			// map preimport post ID's to new
			$_SESSION['processed_posts'][intval($post['post_id'])]      = intval($post_id);

		}

		$_SESSION['attachment_posts'] = $attachment_posts;

		/**
		 * Hook fires after succesfull posts import
		 */
		do_action( 'cherry_data_manager_import_posts' );

		exit('attach_terms');
	}

	/**
	 * Attaching terms to imported posts
	 *
	 * Import Step#7
	 *
	 * @since 1.0.0
	 */
	function attach_terms() {

		$this->verify_nonce();

		$posts_array = $this->tools->get_xml_data( 'posts' );

		// go to next step if no posts was processed on prev step
		if ( !is_array( $_SESSION['processed_posts'] ) ) {
			exit('attach_comments');
		}

		foreach ( $_SESSION['processed_posts'] as $old_post_id => $new_post_id ) {

			if ( !isset($posts_array[$old_post_id]) ) {
				continue;
			}

			$postdata = $posts_array[$old_post_id];

			if ( !isset( $postdata['terms'] ) || empty( $postdata['terms'] ) ) {
				unset($postdata);
				continue;
			}

			$terms_to_set = array();

			foreach ( $postdata['terms'] as $term ) {
				// back compat with WXR 1.0 map 'tag' to 'post_tag'
				$taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];

				$term_exists = term_exists( $term['slug'], $taxonomy );
				$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;

				if ( ! $term_id ) {
					$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
					if ( ! is_wp_error( $t ) ) {
						$term_id = $t['term_id'];
						do_action( 'cherry_data_manager_import_insert_term', $t, $term, $new_post_id, $postdata );
					} else {
						// Failed to import
						do_action( 'cherry_data_manager_import_insert_term_failed', $t, $term, $new_post_id, $postdata );
						continue;
					}
				}

				$terms_to_set[$taxonomy][] = intval( $term_id );
			}

			foreach ( $terms_to_set as $tax => $ids ) {
				$tt_ids = wp_set_post_terms( $new_post_id, $ids, $tax );
				do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $new_post_id, $postdata );
			}

			unset( $postdata, $terms_to_set );

		}

		/**
		 * Hook fires after succesfull attaching terms to posts
		 */
		do_action( 'cherry_data_manager_attach_terms' );

		exit('attach_comments');

	}

	/**
	 * Attaching comments to imported posts
	 *
	 * Import Step#8
	 *
	 * @since 1.0.0
	 */
	function attach_comments() {

		$this->verify_nonce();

		$posts_array = $this->tools->get_xml_data( 'posts' );

		// go to next step if no posts was processed on prev step
		if ( !is_array( $_SESSION['processed_posts'] ) ) {
			exit('attach_postmeta');
		}

		foreach ( $_SESSION['processed_posts'] as $old_post_id => $new_post_id ) {

			if ( !isset($posts_array[$old_post_id]) ) {
				continue;
			}

			$postdata = $posts_array[$old_post_id];

			if ( !isset( $postdata['comments'] ) || empty( $postdata['comments'] ) ) {
				unset($postdata);
				continue;
			}

			$num_comments      = 0;
			$inserted_comments = array();
			$comment_post_ID   = $new_post_id;

			foreach ( $postdata['comments'] as $comment ) {

				$comment_id = $comment['comment_id'];

				$newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
				$newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
				$newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
				$newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
				$newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
				$newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
				$newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
				$newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
				$newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
				$newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
				$newcomments[$comment_id]['comment_parent']       = $comment['comment_parent'];
				$newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();

				if ( isset( $processed_authors[$comment['comment_user_id']] ) ) {
					$newcomments[$comment_id]['user_id'] = $processed_authors[$comment['comment_user_id']];
				}
			}

			ksort( $newcomments );

			foreach ( $newcomments as $key => $comment ) {

				// if this is a new post we can skip the comment_exists() check
				if ( comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
					continue;
				}

				if ( isset( $inserted_comments[$comment['comment_parent']] ) ) {
					$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
				}

				$comment                 = wp_filter_comment( $comment );
				$inserted_comments[$key] = wp_insert_comment( $comment );

				do_action( 'cherry_data_manager_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $postdata );

				foreach( $comment['commentmeta'] as $meta ) {
					$value = maybe_unserialize( $meta['value'] );
					add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
				}

				$num_comments++;


			}

			unset( $newcomments, $inserted_comments, $postdata );

		}

		/**
		 * Hook fires after attaching comments to posts
		 */
		do_action( 'cherry_data_manager_attach_comments' );

		exit('attach_postmeta');

	}

	/**
	 * Attaching post meta to imported posts
	 *
	 * Import Step#9
	 *
	 * @since 1.0.0
	 */
	function attach_postmeta() {

		$this->verify_nonce();

		$posts_array = $this->tools->get_xml_data( 'posts' );

		// go to next step if no posts was processed on prev step
		if ( ! is_array( $_SESSION['processed_posts'] ) ) {
			exit('import_menu_item');
		}

		$_SESSION['meta_to_rewrite'] = array();

		$rewrite_meta_fields = apply_filters(
			'cherry_data_manager_rewrite_meta',
			array( '_cherry_portfolio' => 'portfolio-gallery-attachments-ids' )
		);

		foreach ( $_SESSION['processed_posts'] as $old_post_id => $new_post_id ) {

			if ( ! isset( $posts_array[$old_post_id] ) ) {
				continue;
			}

			$postdata = $posts_array[$old_post_id];

			if ( !isset( $postdata['postmeta'] ) || empty( $postdata['postmeta'] ) ) {
				unset( $postdata );
				continue;
			}

			foreach ( $postdata['postmeta'] as $meta ) {

				$key   = apply_filters( 'import_post_meta_key', $meta['key'] );
				$value = false;

				if ( is_array( $rewrite_meta_fields ) && array_key_exists( $key, $rewrite_meta_fields ) ) {

					$_SESSION['meta_to_rewrite'][ $new_post_id ] = $this->tools->prepare_meta_to_rewrite(
						$meta,
						$rewrite_meta_fields
					);

				}

				if ( ! $key || '_edit_last' == $key || '_wp_attachment_metadata' == $key ) {
					continue;
				}

				if ( '_cherry_services' == $key ) {
					$this->tools->fix_html_meta( $new_post_id, $key, $meta['value'] );
					continue;
				}

				// export gets meta straight from the DB so could have a serialized string
				if ( ! $value ) {
					$value = maybe_unserialize( $meta['value'] );
				}

				ini_set('max_execution_time', 300);
				set_time_limit(0);

				add_post_meta( $new_post_id, $key, $value );

				/**
				 * Hook fires when single meta added to imported posts
				 *
				 * @since  1.0.0
				 *
				 * @param  $new_post_id imported post ID
				 * @param  $key         imported meta key
				 * @param  $value       imported meta value
				 */
				do_action( 'cherry_plugin_import_post_meta', $new_post_id, $key, $value );

				// if the post has a featured image, take note of this in case of remap
				if ( '_thumbnail_id' == $key ){
					$_SESSION['featured_images'][$new_post_id] = (int) $value;
				}

			}

		}

		/**
		 * Hook fires after attaching posts meta
		 */
		do_action( 'cherry_data_manager_attach_comments' );

		exit('import_menu_item');

	}

	/**
	 * Check if category exists & return category ID if use
	 *
	 * Duplicate standard WP category_exists internal function
	 * to prevent errors if function standrd fuction will changed in core
	 *
	 * @since  1.0.0
	 * @param  string  $cat_name category name to check
	 * @param  int     $parent   parent category ID
	 * @return int               category ID if exists
	 */
	function _category_exists( $cat_name, $parent = 0 ) {

		$id = term_exists($cat_name, 'category', $parent);

		if ( is_array($id) ) {
			$id = $id['term_id'];
		}

		return $id;
	}

	/**
	 * Process menu items
	 *
	 * Import Step#10
	 *
	 * @since 1.0.0
	 */
	function import_menu_item() {

		$this->verify_nonce();

		$_SESSION['missing_menu_items'] = array();

		$menu_items = $_SESSION['menu_items'];

		if ( empty($menu_items) ) {
			exit('import_attachment');
		}

		$menus = get_transient( $this->transients_prefix . 'menus' );

		/**
		 * Get menu meta keys to update on import from theme and plugins
		 * @var array
		 */
		$menu_extra_meta = apply_filters( 'cherry_data_manager_menu_meta', array() );

		if ( $menus && is_array( $menus ) ) {
			foreach ( $menus as $location ) {
				unregister_nav_menu( $location );
			}
		}

		foreach ( $menu_items as $post ) {
			$post = apply_filters( 'wp_import_post_data_raw', $post );
			$this->add_menu_item( $post, $menu_extra_meta );
		}

		unset( $_SESSION['posts'] );

		/**
		 * Hook fires after successfull menu items import
		 */
		do_action( 'cherry_data_manager_import_menu_item' );

		exit( 'import_attachment' );
	}

	/**
	 * Process attachments
	 *
	 * Import Step#11
	 *
	 * @since 1.0.0
	 */
	function import_attachment() {

		$this->verify_nonce();

		if( empty($_SESSION['attachment_posts']) ) {
			exit('generate_attachment_metadata_step_1');
		}

		$_SESSION['missing_menu_items']  = array();
		$_SESSION['attachment_metapost'] = array();
		$posts_array                     = $_SESSION['attachment_posts'];
		$posts_array                     = apply_filters( 'wp_import_posts', $posts_array );
		$author                          = (int) get_current_user_id();

		foreach ( $posts_array as $post ) {
			$post = apply_filters( 'wp_import_post_data_raw', $post );

			$postdata = array(
				'import_id'      => $post['post_id'],
				'post_author'    => $author,
				'post_date'      => $post['post_date'],
				'post_date_gmt'  => $post['post_date_gmt'],
				'post_content'   => $post['post_content'],
				'post_excerpt'   => $post['post_excerpt'],
				'post_title'     => $post['post_title'],
				'post_status'    => $post['status'],
				'post_name'      => $post['post_name'],
				'comment_status' => $post['comment_status'],
				'ping_status'    => $post['ping_status'],
				'guid'           => $post['guid'],
				/*'post_parent'  => $post_parent,*/
				'menu_order'     => $post['menu_order'],
				'post_type'      => $post['post_type'],
				'post_password'  => $post['post_password']
			);

			$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

			$remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];
			// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
			// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
			$postdata['upload_date'] = $post['post_date'];

			$upload_dir = cherry_dm_get_upload_path();

			$file_url = $upload_dir . basename( $remote_url );

			if ( file_exists( $file_url ) ) {
				$this->add_attachment( $postdata, $remote_url, $upload_dir );
			}
		}

		wp_suspend_cache_invalidation( false );

		/**
		 * Hook fires import attachment
		 */
		do_action( 'cherry_data_manager_import_attachment' );

		exit('generate_attachment_metadata_step_1');

	}

	/**
	 * Generate attachment meta. Step 1
	 *
	 * @since 1.0.0
	 */
	function generate_attachment_metadata_1() {
		$this->verify_nonce();

		do_action( 'cherry_data_manager_generate_attachment_metadata' );

		if(empty($_SESSION['attachment_posts'])){
			exit('import_attachment_metadata');
		}

		$range = $this->get_step_range( 1 );
		$this->generate_attachment_meta_step( $range['from'], $range['to'] );

		exit('generate_attachment_metadata_step_2');
	}

	/**
	 * Generate attachment meta. Step 2
	 *
	 * @since 1.0.0
	 */
	function generate_attachment_metadata_2() {
		$this->verify_nonce();

		do_action( 'cherry_data_manager_generate_attachment_metadata' );

		if(empty($_SESSION['attachment_posts'])){
			exit('import_attachment_metadata');
		}

		$range = $this->get_step_range( 2 );
		$this->generate_attachment_meta_step( $range['from'], $range['to'] );

		exit('generate_attachment_metadata_step_3');
	}

	/**
	 * Generate attachment meta. Step 3
	 *
	 * @since 1.0.0
	 */
	function generate_attachment_metadata_3() {
		$this->verify_nonce();

		do_action( 'cherry_data_manager_generate_attachment_metadata' );

		if(empty($_SESSION['attachment_posts'])){
			exit('import_attachment_metadata');
		}

		$range = $this->get_step_range( 3 );
		$this->generate_attachment_meta_step( $range['from'], $range['to'] );

		exit('generate_attachment_metadata_step_4');
	}

	/**
	 * Generate attachment meta. Step 4
	 *
	 * @since 1.0.0
	 */
	function generate_attachment_metadata_4() {
		$this->verify_nonce();

		do_action( 'cherry_data_manager_generate_attachment_metadata' );

		if(empty($_SESSION['attachment_posts'])){
			exit('import_attachment_metadata');
		}

		$range = $this->get_step_range( 4 );
		$this->generate_attachment_meta_step( $range['from'], $range['to'] );

		exit('generate_attachment_metadata_step_5');
	}

	/**
	 * Generate attachment meta. Step 5
	 *
	 * @since 1.0.0
	 */
	function generate_attachment_metadata_5() {
		$this->verify_nonce();

		do_action( 'cherry_data_manager_generate_attachment_metadata' );

		if(empty($_SESSION['attachment_posts'])){
			exit('import_attachment_metadata');
		}

		$range = $this->get_step_range( 5 );
		$this->generate_attachment_meta_step( $range['from'], $range['to'] );

		exit('generate_attachment_metadata_step_6');
	}

	/**
	 * Generate attachment meta. Step 4
	 *
	 * @since 1.0.0
	 */
	function generate_attachment_metadata_6() {
		$this->verify_nonce();

		do_action( 'cherry_data_manager_generate_attachment_metadata' );

		if(empty($_SESSION['attachment_posts'])){
			exit('import_attachment_metadata');
		}

		$range = $this->get_step_range( 6 );
		$this->generate_attachment_meta_step( $range['from'], $range['to'] );

		exit('import_attachment_metadata');
	}

	/**
	 * Get current step index range
	 *
	 * @since 1.0.0
	 */
	function get_step_range( $step = 1 ) {

		$metadata = $_SESSION['attachment_metapost'];
		$count    = count( $metadata );
		$by_step  = intval( $count/4 );

		$range = array(
			'from' => 0,
			'to'   => $count
		);

		switch ( $step ) {

			case 1:
				$range['to'] = $by_step;
				if ( $by_step > 3 ) {
					$range['to'] = $range['to'] - 2;
				}
				break;

			case 5:
				$range['from'] = $by_step*($step - 1) + 1;
				break;

			default:
				$range['from'] = $by_step*($step - 1) + 1;
				$range['to']   = $by_step*$step;
				if ( $by_step > 3 && 2 == $step ) {
					$range['from'] = $range['from'] - 2;
				}
				break;

		}

		return $range;
	}

	/**
	 * Process attachment regenerate by step
	 *
	 * @since 1.0.0
	 */
	function generate_attachment_meta_step( $from = 0, $to = 20 ) {

		$metadata = $_SESSION['attachment_metapost'];

		$values = array_values( $metadata );
		$keys   = array_keys( $metadata );

		for ( $i = $from; $i <= $to; $i++ ) {

			$key   = isset( $keys[$i] ) ? $keys[$i] : false;
			$value = isset( $values[$i] ) ? $values[$i] : false;

			if ( ! $key && ! $value ) {
				continue;
			}

			ini_set( 'max_execution_time', -1 );
			set_time_limit( 0 );

			$_SESSION['attachment_metapost'][$key]['file'] = wp_generate_attachment_metadata( $value['post_id'], $value['file'] );
		}

	}

	/**
	 * Update attachment metadata
	 *
	 * Import another step
	 *
	 * @since 1.0.0
	 */
	function import_attachment_metadata() {

		$this->verify_nonce();

		do_action( 'cherry_data_manager_update_attachment_metadata' );

		if ( empty($_SESSION['attachment_posts']) ) {
			exit('import_parents');
		}

		$generate_metadata = $_SESSION['attachment_metapost'];
		foreach ($generate_metadata as $key => $value) {
			ini_set('max_execution_time', -1);
			set_time_limit(0);
			wp_update_attachment_metadata($value['post_id'], $value['file']);
		}

		unset( $_SESSION['attachment_metapost'] );
		exit('import_parents');

	}

	/**
	 * Update content hierarchy
	 *
	 * Import Step#14
	 *
	 * @since 1.0.0
	 */
	function import_parents() {

		$this->verify_nonce();

		global $wpdb;

		do_action( 'cherry_data_manager_import_parents' );

		// find parents for post orphans
		$post_orphans = isset($_SESSION['post_orphans']) ? $_SESSION['post_orphans'] : array();
		foreach ( $post_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = false;
			if ( isset( $_SESSION['processed_posts'][$child_id] ) ) {
				$local_child_id = $_SESSION['processed_posts'][$child_id];
			}
			if ( isset( $_SESSION['processed_posts'][$parent_id] ) ) {
				$local_parent_id = $_SESSION['processed_posts'][$parent_id];
			}

			if ( $local_child_id && $local_parent_id ) {
				$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
			}
		}

		// all other posts/terms are imported, retry menu items with missing associated object
		$missing_menu_items_arrary = $_SESSION['missing_menu_items'];

		/**
		 * Get menu meta keys to update on import from theme and plugins
		 * @var array
		 */
		$menu_extra_meta = apply_filters( 'cherry_data_manager_menu_meta', array() );

		if ( is_array( $missing_menu_items_arrary ) ) {
			foreach ($missing_menu_items_arrary as $item ) {
				$this->add_menu_item( $item, $menu_extra_meta );
			}
		}

		// find parents for menu item orphans
		$menu_item_orphans = isset($_SESSION['menu_item_orphans']) ? $_SESSION['menu_item_orphans'] : array();

		foreach ( $menu_item_orphans as $child_id => $parent_id ) {

			$local_child_id = $local_parent_id = 0;
			if ( isset( $_SESSION['processed_menu_items'][$child_id] ) ) {
				$local_child_id = $_SESSION['processed_menu_items'][$child_id];
			}
			if ( isset( $_SESSION['processed_menu_items'][$parent_id] ) ) {
				$local_parent_id = $_SESSION['processed_menu_items'][$parent_id];
			}

			if ( $local_child_id && $local_parent_id ) {
				update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
			}
		}

		exit( 'update_featured_images' );
	}

	/**
	 * Update featured images
	 *
	 * Import Step#15
	 *
	 * @since 1.0.0
	 */
	function update_featured_images() {

		$this->verify_nonce();

		do_action( 'cherry_data_manager_update_featured_images' );

		$featured_images = $_SESSION['featured_images'];

		// update meta fields with images if needed
		if ( is_array( $_SESSION['meta_to_rewrite'] ) ) {
			foreach ( $_SESSION['meta_to_rewrite'] as $post_id => $meta ) {
				$values     = isset( $meta['val'] ) ? explode( ',', $meta['val'] ) : array();
				$new_values = $this->remap_img_ids($values);
				$new_values = implode( ',', $new_values );

				if ( false !== $meta['inner_key'] ) {
					$old_meta = get_post_meta( $post_id, $meta['key'], true );
					$new_meta = $this->tools->update_rewritten_meta( $meta, $new_values, $old_meta );
					//$new_meta = array_merge( $old_meta, array( $meta['inner_key'] => $new_values ) );
					update_post_meta( $post_id, $meta['key'], $new_meta );
				} else {
					update_post_meta( $post_id, $meta['key'], $new_values );
				}

			}
		}

		if ( empty( $featured_images ) ) {
			exit('update_attachment');
		}

		// cycle through posts that have a featured image
		foreach ( $featured_images as $post_id => $value ) {

			if ( ! isset( $_SESSION['processed_posts'][$value] ) ) {
				continue;
			}

			$new_id = $_SESSION['processed_posts'][$value];
			// only update if there's a difference
			if ( $new_id != $value ) {
				update_post_meta( $post_id, '_thumbnail_id', $new_id );
			}

		}

		exit('update_attachment');
	}

	/**
	 * Update attachments
	 *
	 * Import almost last step
	 *
	 * @since 1.0.0
	 */
	function update_attachment() {

		$this->verify_nonce();

		global $wpdb;

		$url_remap = isset( $_SESSION['url_remap'] ) ? $_SESSION['url_remap'] : array();

		// make sure we do the longest urls first, in case one is a substring of another
		uksort( $url_remap, array( $this, 'sort_url' ) );

		foreach ( $url_remap as $from_url => $to_url ) {
			// remap urls in post_content
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_content = REPLACE( post_content, %s, %s )", $from_url, $to_url) );
			// remap enclosure urls
			$result = $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE( meta_value, %s, %s ) WHERE meta_key='enclosure'", $from_url, $to_url) );
		}

		/**
		 * Hook fires after attachment updating
		 */
		do_action( 'cherry_data_manager_update_attachment' );

		$this->import_end();
	}

	function test_cb( $matches ) {
		var_dump( $matches );
		return $matches[0];
	}

	/**
	 * Import widgets and set necessary settings
	 *
	 * Import last step
	 *
	 * @since 1.0.0
	 */
	function import_json() {

		do_action( 'cherry_plugin_import_json' );

		$widgets_file = 'widgets.json';
		$upload_dir   = cherry_dm_get_upload_path();
		$json         = $this->tools->get_contents( $upload_dir . $widgets_file );

		if ( $this->tools->is_package_install() ) {
			$this->install_complete();
		}

		$this->remap_option_ids();
		$this->remap_slider_ids();

		if ( isset($_POST['file']) && $_POST['file'] ) {
			$widgets_file = $_POST['file'];
		}

		if ( class_exists( 'Cherry_Options_Framework' ) ) {
			$options_framework = Cherry_Options_Framework::get_instance();
			$options_framework->restore_default_settings_array();
		}

		if ( false == $json ) {
			$this->install_complete();
		}

		$upload_dir = wp_upload_dir();
		$upload_url = $upload_dir['url'] . '/';

		$json = preg_replace(
			array(
				'/http:.[^\'\"]*wp-content.[^\'\"]*\/(.[^\/\'\"]*\.(?:jp[e]?g|png|mp4|ogg|webm))/',
				'/\[template_url\].{2}wp-content.[^\'\"]*\/(.[^\/\'\"]*\.(?:jp[e]?g|png|mp4|ogg|webm))/'
			),
			array(
				trim( json_encode( $upload_url . '$1' ), "\"" ),
				trim( json_encode( str_replace( get_bloginfo( 'url' ), '[template_url]', $upload_url ) . '$1' ), "\"" )
			),
			$json
		);

		if( is_wp_error( $json ) ) {
			exit('error');
		};

		$json_data    = json_decode( $json, true );
		$sidebar_data = $json_data[0];
		$widget_data  = $json_data[1];

		if ( ! is_array( $widget_data ) ) {
			$this->install_complete();
		}

		foreach ( $widget_data as $widget_title => $widget_value ) {

			if ( !is_array($widget_value) ) {
				continue;
			}

			foreach ( $widget_value as $widget_key => $widget_value ) {
				// fix for nav_menu widget
				if ( 'nav_menu' == $widget_title ) {
					if (is_array($widget_data[$widget_title][$widget_key])) {
						if ( array_key_exists('nav_menu_slug', $widget_data[$widget_title][$widget_key]) ) {
							$nav_menu_slug = $widget_data[$widget_title][$widget_key]['nav_menu_slug'];

							$term_id = term_exists( $nav_menu_slug, 'nav_menu' );
							if ( $term_id ) {
								if ( is_array($term_id) ) $term_id = $term_id['term_id'];
								$widget_data['nav_menu'][$widget_key]['nav_menu'] = $term_id;
							}
						}
					}
				}
			}
		}

		$sidebar_data = array( array_filter( $sidebar_data ), $widget_data );

		if( $this->parse_widgets_data( $sidebar_data ) ) {
			$this->install_complete();
		} else {
			exit( 'error' );
		}
	}

	/**
	 * Fires install compleete hook and required functions
	 *
	 * @since  1.0.9
	 * @return void
	 */
	public function install_complete() {

		/**
		 * Hook fires after successfull demo content installation
		 */
		do_action( 'cherry_data_manager_install_complete' );

		$this->tools->clean_files();

		if ( isset( $_SESSION['monstroid_install_type'] ) ) {
			unset( $_SESSION['monstroid_install_type'] );
		}

		exit( 'import_end' );
	}

	/**
	 * Remap slider images id's
	 *
	 * @since  1.0.8
	 * @return void|bool false
	 */
	public function remap_slider_ids() {

		global $wpdb;

		$table = $wpdb->prefix . 'mpsl_slides';

		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" );

		if ( $table !== $table_exists ) {
			return false;
		}

		$slides = $wpdb->get_results(
			"
			SELECT *
			FROM $table
			"
		);

		if ( ! is_array( $slides ) ) {
			return false;
		}

		foreach ( $slides as $slide ) {

			if ( ! isset( $slide->options ) ) {
				continue;
			}

			$slide_opt = json_decode( $slide->options, true );

			if ( ! isset( $slide_opt['bg_image_id'] ) ) {
				continue;
			}

			$new_id = $this->remap_img_ids( array( $slide_opt['bg_image_id'] ) );

			if ( empty( $new_id ) ) {
				continue;
			}

			$slide_opt['bg_image_id'] = $new_id[0];

			$slide_opt = json_encode( $slide_opt );

			$wpdb->update(
				$table,
				array( 'options' => $slide_opt ),
				array( 'id' => $slide->id ),
				array( '%s' ),
				array( '%d' )
			);
		}

	}

	/**
	 * parse widgets data
	 *
	 * @since 1.0.0
	 * @param array  $import_array  imported widgets array
	 */
	public function parse_widgets_data( $import_array ) {

		if ( ! is_array( $import_array ) ) {
			return false;
		}

		$sidebars_data    = $import_array[0];
		$widget_data      = $import_array[1];
		$new_widgets      = array();
		$inactive_widgets = array();

		$sidebars_data['wp_inactive_widgets'] = array();
		update_option( 'sidebars_widgets', $sidebars_data );

		foreach ( $widget_data as $title => $content ) {
			array_walk_recursive( $content, array( $this, 'upd_mega_parents' ) );
			update_option( 'widget_' . $title, $content );
		}

		return true;
	}

	/**
	 * Update mega menu parent item IDs in widget settings
	 *
	 * @since  1.0.0
	 *
	 * @param  mixed  $item  array value
	 * @param  string $key   array key
	 * @return [type] [description]
	 */
	function upd_mega_parents( &$item, $key ) {
		if( 'mega_menu_parent_menu_id' == $key ) {
			$old  = $item;
			$item = isset( $_SESSION['processed_menu_items'][$old] ) ? $_SESSION['processed_menu_items'][$old] : false;
		}
	}

	/**
	 * get new widget name to save into DB by widget index
	 *
	 * @since  1.0.0
	 * @param  string  $widget_name   imported widgets name
	 * @param  int     $widget_index  imported widgets index
	 */
	function get_new_widget_name( $widget_name, $widget_index ) {

		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array();

		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( ! empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		/*while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}*/
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}

	/**
	 * Update options which conteins posts or page ID's (imported on 'importing_options' step) - replace old id's to new
	 *
	 * @since  1.0.0
	 * @param  int   $old_id   old page ID provided in JSON file
	 * @param  int   $new_id   new page ID recieved after import
	 * @param  array $ids_data array with all id's data
	 */
	public function new_id_to_option( $old_id, $new_id, $ids_data ) {

		if ( empty( $ids_data ) ) {
			return;
		}

		if ( ! isset( $ids_data[ $old_id ] ) ) {
			return;
		}

		foreach ( $ids_data[ $old_id ] as $option_name ) {
			update_option( $option_name, $new_id );
		}

	}

	/**
	 * Replace old image ID's in meta with new
	 *
	 * @since  1.0.0
	 *
	 * @param  array $old_ids old ID's array
	 * @return array $old_ids new ID's array
	 */
	function remap_img_ids( $old_ids ) {

		$new_ids = array();

		if ( ! is_array( $old_ids ) ) {
			return $new_ids;
		}

		foreach ( $old_ids as $id ) {
			$new_ids[] = isset( $_SESSION['processed_posts'][ $id ] ) ? $_SESSION['processed_posts'][ $id ] : false;
		}

		return $new_ids;
	}

	/**
	 * Remap image ID's in theme options to new
	 *
	 * @return void|null
	 */
	function remap_option_ids() {

		if ( ! function_exists( 'cherry_get_option' ) ) {
			return;
		}

		$remap_options = apply_filters(
			'cherry_data_manager_rewrite_options',
			array(
				'logo-image-path'         => 'logo-subsection',
				'footer-logo-image-path'  => 'footer-logo-subsection',
				'general-favicon'         => 'general-section',
				'header-background:image' => 'header-section',
				'footer-background:image' => 'footer-section',
			)
		);

		if ( ! is_array($remap_options) ) {
			return;
		}

		$opt_id = get_option( 'cherry-options' );
		$opt_id = isset( $opt_id['id'] ) ? $opt_id['id'] : false;

		if ( !$opt_id ) {
			return;
		}

		$options         = get_option( $opt_id );
		$default_options = get_option( $opt_id . '_defaults' );

		if ( ! $default_options ) {
			$default_options = $options;
		}

		foreach ( $remap_options as $name => $section ) {

			$name        = explode( ':', $name );
			$opt_name    = $name[0];
			$opt_subname = false;

			if ( isset( $name[1] ) ) {
				$opt_subname = $name[1];
			}

			$values = cherry_get_option( $opt_name );

			if ( is_array( $values ) ) {
				$values = $values[ $opt_subname ];
			}

			$values     = explode( ',', $values );
			$new_values = $this->remap_img_ids( $values );

			if ( empty( $new_values ) ) {
				continue;
			}

			$new_values = implode( ',', $new_values );

			if ( false !== $opt_subname ) {
				$options[ $section ]['options-list'][ $opt_name ][ $opt_subname ]         = $new_values;
				$default_options[ $section ]['options-list'][ $opt_name ][ $opt_subname ] = $new_values;
			} else {
				$options[ $section ]['options-list'][ $opt_name ]         = $new_values;
				$default_options[ $section ]['options-list'][ $opt_name ] = $new_values;
			}
		}

		update_option( $opt_id, $options );
		update_option( $opt_id . '_defaults', $default_options );
	}

	/**
	 * Sort url by length
	 *
	 * @since 1.0.0
	 */
	function sort_url( $a, $b ) {
		return strlen($b) - strlen($a);
	}

	/**
	 * Insert single attachment
	 *
	 * @since 1.0.0
	 * @param array   $post       array with imported post data
	 * @param string  $url        attachment URL
	 * @param string  $upload_dir uploar dir URL
	 */
	function add_attachment( $post, $url, $upload_dir ) {

		global $cherry_data_manager;
		$file_name = basename( $url );

		$upload['url'] = $url;
		$upload['file'] = $upload_dir . $file_name;
		if ( is_wp_error( $upload ) )
			return $upload;

		if ( $info = wp_check_filetype( $upload['file'] ) ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			return new WP_Error( 'attachment_processing_error', __( 'Invalid file type', $cherry_data_manager->slug ) );
		}

		$post['guid']                         = $upload['url'];
		$_SESSION['url_remap'][$url]          = $upload['url'];
		$_SESSION['url_remap'][$post['guid']] = $upload['url'];


		// as per wp-admin/includes/upload.php
		ini_set('max_execution_time', -1);
		set_time_limit(0);
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		array_push( $_SESSION['attachment_metapost'], array('post_id' => $post_id, 'file' => $upload['file']) );

		$_SESSION['processed_posts'][intval($post['import_id'])] = intval($post_id);

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {

			$parts = pathinfo( $url );
			$name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

			$parts_new = pathinfo( $upload['url'] );
			$name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$_SESSION['url_remap'][$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;

		}

		return $post_id;
	}

	/**
	 * Insert single menu item
	 *
	 * @since 1.0.0
	 * @param $item       - array with item data
	 * @param $extra_meta - array with additional meta fields to save it on import
	 */
	function add_menu_item( $item, $extra_meta = array() ) {
		// skip draft, orphaned menu items
		if ( 'draft' == $item['status'] )
			return;

		$menu_slug = false;
		if ( isset($item['terms']) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					//$menu_name = $term['slug'];
					break;
				}
			}
		}

		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			// echo theme_locals('menu_item');
			return;
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );

		if ( ! $menu_id ) {
			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}

		foreach ( $item['postmeta'] as $meta ) {
			$$meta['key'] = $meta['value'];
		}

		if ( 'taxonomy' == $_menu_item_type && isset( $_SESSION['processed_terms'][intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $_SESSION['processed_terms'][intval($_menu_item_object_id)];
		} else if ( 'post_type' == $_menu_item_type && isset( $_SESSION['processed_posts'][intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $_SESSION['processed_posts'][intval($_menu_item_object_id)];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$_SESSION['missing_menu_items'][] = $item;
			return;
		}

		if ( isset( $_SESSION['processed_menu_items'][intval($_menu_item_menu_item_parent)] ) ) {
			$_menu_item_menu_item_parent = $_SESSION['processed_menu_items'][intval($_menu_item_menu_item_parent)];
		} else if ( $_menu_item_menu_item_parent ) {
			$_SESSION['menu_item_orphans'][intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) )
			$_menu_item_classes = implode( ' ', $_menu_item_classes );

		$args = array(
			'menu-item-object-id'   => $_menu_item_object_id,
			'menu-item-object'      => $_menu_item_object,
			'menu-item-parent-id'   => $_menu_item_menu_item_parent,
			'menu-item-position'    => intval( $item['menu_order'] ),
			'menu-item-type'        => $_menu_item_type,
			'menu-item-title'       => $item['post_title'],
			'menu-item-url'         => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title'  => $item['post_excerpt'],
			'menu-item-target'      => $_menu_item_target,
			'menu-item-classes'     => $_menu_item_classes,
			'menu-item-xfn'         => $_menu_item_xfn,
			'menu-item-status'      => $item['status']
		);

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );

		if ( !$id || is_wp_error( $id ) ) {
			return;
		}

		$_SESSION['processed_menu_items'][intval($item['post_id'])] = (int) $id;

		// save additional menu meta
		if ( !is_array( $extra_meta ) || empty( $extra_meta ) ) {
			return;
		}

		foreach ( $extra_meta as $meta_key ) {

			if ( !isset( $$meta_key ) ) {
				continue;
			}

			update_post_meta( $id, $meta_key, maybe_unserialize( $$meta_key ) );

		}

		unset( $meta_key );

	}

	/**
	 * nonce verification for ajax handlers
	 *
	 * @since 1.0.0
	 */
	public function verify_nonce() {

		$verify = check_ajax_referer( 'cherry_data_manager_ajax', 'nonce', false );

		if ( ! $verify ) {
			exit ( 'instal_error' );
		}
	}

	/**
	 * Do some actions on import start
	 *
	 * @since 1.0.0
	 */
	public function import_start(){
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
		wp_suspend_cache_invalidation( true );
	}

	/**
	 * Do some actions on import end
	 *
	 * @since 1.0.0
	 */
	public function import_end(){

		wp_cache_flush();

		if ( $this->tools->is_package_install() ) {
			do_action( 'cherry_data_manager_import_end' );
			exit('import_json');
		}

		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false);
		wp_defer_comment_counting( false );

		update_option('cherry_data_manager_sample_data', 1);

		$this->set_to_draft('hello-world');
		$this->set_to_draft('sample-page');

		$this->set_settings();

		do_action( 'cherry_data_manager_import_end' );

		exit('import_json');
	}


	/**
	 * Set post_status for default WP posts (post_status = draft)
	 *
	 * @since 1.0.0
	 */
	function set_to_draft( $title ) {

		global $wpdb;

		$id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$title'");

		if ($id) {
			$default_post = array(
				'ID'           => $id,
				'post_status' => 'draft'
			);
			// Update the post into the database
			wp_update_post( $default_post );
		}

		$comment_id = $wpdb->get_var("SELECT comment_ID FROM $wpdb->comments WHERE comment_author = 'Mr WordPress'");

		if ($comment_id) wp_delete_comment($comment_id, false);

	}


	/**
	 * Set neccessary site settings
	 *
	 * @since 1.0.0
	 */
	function set_settings() {

		global $wp_rewrite;

		if ( !empty( $_SESSION['processed_menus'] ) ) {

			$locations = array();
			$ids_data  = get_transient( $this->transients_prefix . 'ids_data' );
			$ids_data  = isset($ids_data['menus']) ? $ids_data['menus'] : array();

			foreach ( $_SESSION['processed_menus'] as $new_menu_id => $old_menu_id ) {

				$location_name = isset( $ids_data[$old_menu_id] ) ? $ids_data[$old_menu_id] : '';
				if ( !$location_name ) {
					continue;
				}
				$locations[$location_name] = $new_menu_id;

			}

			set_theme_mod( 'nav_menu_locations', array_map( 'absint', $locations ) );
		}

		// Set permalink custom structure
		$permalink_structure = '/%category%/%postname%/';
		update_option( 'permalink_structure', $permalink_structure );
		$wp_rewrite->set_permalink_structure( $permalink_structure );
		$wp_rewrite->flush_rules();

		// write to .htaccess MIME Type
		$htaccess = ABSPATH . '/.htaccess';
		if ( ! file_exists($htaccess)) {
			return;
		}

		ini_set( 'track_errors', 1 );
		$fp = @fopen( $htaccess, 'a+' );

		if ( ! empty( $php_errormsg ) ) {
			return;
		}

		if (!$fp) {
			return;
		}

		$contents = fread($fp, filesize($htaccess));
		$pos = strpos('# AddType TYPE/SUBTYPE EXTENSION', $contents);
		if ( $pos !== false ) {
			fwrite($fp, "\r\n# AddType TYPE/SUBTYPE EXTENSION\r\n");
			fwrite($fp, "AddType audio/mpeg mp3\r\n");
			fwrite($fp, "AddType audio/mp4 m4a\r\n");
			fwrite($fp, "AddType audio/ogg ogg\r\n");
			fwrite($fp, "AddType audio/ogg oga\r\n");
			fwrite($fp, "AddType audio/webm webma\r\n");
			fwrite($fp, "AddType audio/wav wav\r\n");
			fwrite($fp, "AddType video/mp4 mp4\r\n");
			fwrite($fp, "AddType video/mp4 m4v\r\n");
			fwrite($fp, "AddType video/ogg ogv\r\n");
			fwrite($fp, "AddType video/webm webm\r\n");
			fwrite($fp, "AddType video/webm webmv\r\n");
			fclose($fp);
		}

		/**
		 * Hook fires after succesfull settings update
		 */
		do_action( 'cherry_data_manager_set_settings' );

	}

}

new cherry_data_manager_content_installer();