<?php
/**
 * Handles custom post meta boxes for the 'services' post type.
 *
 * @package   Cherry_Services_Admin
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

/**
 * Metabox controller for services pot type.
 *
 * @since 1.0.0
 */
class Cherry_Services_Meta_Boxes {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'add_meta_boxes_' . CHERRY_SERVICES_NAME, array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

	}

	/**
	 * Adds the meta box container.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		/**
		 * Filter the array of 'add_meta_box' parametrs.
		 *
		 * @since 1.0.0
		 */
		$metabox = apply_filters(
			'cherry_services_metabox_params',
			array(
				'id'            => 'cherry-services-options',
				'title'         => __( 'Services Options', 'cherry-services' ),
				'page'          => CHERRY_SERVICES_NAME,
				'context'       => 'normal',
				'priority'      => 'core',
				'callback_args' => array(
					'font-icon' => array(
						'id'			=> 'font-icon',
						'type'			=> 'text',
						'title'			=> __( 'Font icon CSS class:', 'cherry-services' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'fetures-text' => array(
						'id'			=> 'fetures-text',
						'type'			=> 'textarea',
						'title'			=> __( 'Service features description:', 'cherry-services' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'price' => array(
						'id'			=> 'price',
						'type'			=> 'text',
						'title'			=> __( 'Price:', 'cherry-services' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'order-url' => array(
						'id'			=> 'order-url',
						'type'			=> 'text',
						'title'			=> __( 'URL to order this service:', 'cherry-services' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'is-featured' => array(
						'id'			=> 'is-featured',
						'type'			=> 'switcher',
						'title'			=> __( 'Is service featured:', 'cherry-services' ),
						'value'			=> 'false',
					),
				),
			)
		);

		/**
		 * Add meta box to the administrative interface.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
		 */
		add_meta_box(
			$metabox['id'],
			$metabox['title'],
			array( $this, 'callback_metabox' ),
			$metabox['page'],
			$metabox['context'],
			$metabox['priority'],
			$metabox['callback_args']
		);
	}

	/**
	 * Prints the box content.
	 *
	 * @since 1.0.0
	 * @param object $post    Current post object.
	 * @param array  $metabox metabox attributes.
	 */
	public function callback_metabox( $post, $metabox ) {

		if ( ! class_exists( 'Cherry_Interface_Builder' ) ) {
			return;
		}

		// open core UI wrappers
		echo '<div class="cherry-ui-core">';

		// Add an nonce field so we can check for it later.
		wp_nonce_field( plugin_basename( __FILE__ ), 'cherry_services_meta_nonce' );

		$builder = new Cherry_Interface_Builder(
			array(
				'name_prefix' => CHERRY_SERVICES_POSTMETA,
				'pattern'     => 'inline',
				'class'       => array( 'section' => 'single-section' ),
			)
		);

		$meta = get_post_meta( $post->ID, CHERRY_SERVICES_POSTMETA, true );

		foreach ( $metabox['args'] as $field ) {

			// Check if set the 'id' value for custom field. If not - don't add field.
			if ( ! isset( $field['id'] ) ) {
				continue;
			}

			if ( ! empty( $meta[ $field['id'] ] ) ) {
				if ( is_array( $meta[ $field['id'] ] ) ) {
					$field['value'] = 'false';
				} else {
					$field['value'] = esc_attr( $meta[ $field['id'] ] );
				}
			}

			echo $builder->add_form_item( $field );

		}

		/**
		 * Fires after testimonial fields of metabox.
		 *
		 * @since 1.0.0
		 * @param object $post                    Current post object.
		 * @param array  $metabox
		 * @param string CHERRY_SERVICES_POSTMETA Name for 'meta_key' value in the 'wp_postmeta' table.
		 */
		do_action( 'cherry_services_metabox_after', $post, $metabox, CHERRY_SERVICES_POSTMETA );

		// close core UI wrappers
		echo '</div>';
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @since 1.0.0
	 * @param int    $post_id current post ID.
	 * @param object $post    current post object.
	 */
	public function save_post( $post_id, $post ) {

		// Verify the nonce.
		if ( ! isset( $_POST['cherry_services_meta_nonce'] ) || ! wp_verify_nonce( $_POST['cherry_services_meta_nonce'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post.
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		// Don't save if the post is only a revision.
		if ( 'revision' == $post->post_type ) {
			return;
		}

		// Check if $_POST have a needed key.
		if ( ! isset( $_POST[ CHERRY_SERVICES_POSTMETA ] ) || empty( $_POST[ CHERRY_SERVICES_POSTMETA ] ) ) {
			return;
		}

		$meta_data = $_POST[ CHERRY_SERVICES_POSTMETA ];

		array_walk_recursive( $meta_data, array( $this, 'sanitize_meta' ) );

		// Check if nothing found in $_POST array.
		if ( empty( $meta_data ) ) {
			return;
		}

		update_post_meta( $post_id, CHERRY_SERVICES_POSTMETA, $meta_data );
	}

	/**
	 * Sanitize meta item value
	 *
	 * @todo  personally sanitize item values by their keys
	 *
	 * @since 4.0.0
	 * @param mixed  $item item value to sanitize.
	 * @param string $key  sanitized item key.
	 */
	public function sanitize_meta( &$item, $key ) {

		$item = esc_attr( $item );

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

Cherry_Services_Meta_Boxes::get_instance();
