<?php
/**
 * Cherry Social Follow Widget.
 *
 * @package   Cherry_Social
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @copyright 2012 - 2015, Cherry Team
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * PHP-class for `Social Follow` widget.
 *
 * @since 1.0.0
 */
class Cherry_Social_Follow extends WP_Widget {

	/**
	 * Unique identifier for widget.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $widget_slug = 'cherry-social-follow';

	/**
	 * Instance of this plugin.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	protected $plugin = null;

	/**
	 * Specifies the classname and description.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			$this->get_widget_slug(),
			__( 'Cherry Follow Us', 'cherry-social' ),
			array(
				'classname'   => $this->get_widget_slug() . '-class',
				'description' => __( 'A social follow widget.', 'cherry-social' ),
			)
		);

		$this->plugin = Cherry_Social::get_instance();
		$this->follow_items = $this->plugin->get_option( 'follow-items', array() );

		if ( ! empty( $this->follow_items ) ) {
			$values = array_filter( $this->follow_items );
			$values = wp_list_pluck( $values, 'link-label' );
			$keys   = array_map( 'sanitize_key', $values );
			$this->follow_items = array_combine( $keys, $values );
		}

		// Refreshing the widget's cached output with each new post.
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Return the widget slug.
	 *
	 * @since  1.0.0
	 * @return Plugin slug variable.
	 */
	public function get_widget_slug() {
		return $this->widget_slug;
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.0.0
	 * @param array $args     The array of form elements.
	 * @param array $instance The current instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// Check if there is a cached output.
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			return print $cache[ $args['widget_id'] ];
		}

		extract( $args, EXTR_SKIP );

		if ( ! is_array( $this->follow_items ) || empty( $this->follow_items ) ) {
			return print $before_widget . __( 'Sorry, but networks are not found.', 'cherry-social' ) . $after_widget;
		}

		$output = $before_widget;

		/**
		 * Fires before a content widget.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->widget_slug . '_before', $args, $instance );

		if ( ! empty( $instance['title'] ) ) {
			/**
			 * Filter the widget title.
			 *
			 * @since 1.0.0
			 * @param string $title       The widget title.
			 * @param array  $instance    An array of the widget's settings.
			 * @param mixed  $widget_slug The widget ID.
			 */
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->widget_slug );

			/**
			 * Fires before a content widget.
			 *
			 * @since 1.0.0
			 */

			// Display the widget title if one was input.
			if ( $title ) {
				$output .= $before_title;
				$output .= $title;
				$output .= $after_title;
			}
		}

		$items = array();
		foreach ( $this->follow_items as $id => $value ) {
			if ( empty( $instance[ $id ] ) ) {
				continue;
			}

			if ( ! $instance[ $id ] ) {
				continue;
			}

			$items[] = $id;
		}

		$output .= $this->plugin->get_follows( $items, false, $instance['custom_class'] );

		/**
		 * Fires after a content widget.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->widget_slug . '_after' );

		$output .= $after_widget;

		$cache[ $args['widget_id'] ] = $output;
		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

		print $output;
	}

	/**
	 * Removes the cache contents matching key and group.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since 1.0.0
	 * @param array $new_instance The new instance of values to be generated via the update.
	 * @param array $old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['custom_class'] = sanitize_html_class( $new_instance['custom_class'] );

		foreach ( $this->follow_items as $id => $value ) {
			$instance[ $id ] = ! empty( $new_instance[ $id ] ) ? 1 : 0;
		}

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @since 1.0.0
	 * @param array $instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {
		$_defaults = array(
			'title'        => '',
			'custom_class' => '',
		);

		$defaults = wp_parse_args( (array) $this->follow_items, $_defaults );

		/**
		 * Filters default widget settings.
		 *
		 * @since 1.0.0
		 * @param array
		 */
		$defaults = apply_filters( 'cherry_social_follow_widget_form_defaults_args', $defaults );

		$instance     = wp_parse_args( (array) $instance, $defaults );
		$title        = esc_attr( $instance['title'] );
		$custom_class = esc_attr( $instance['custom_class'] );

		// Display the admin form.
		include( trailingslashit( CHERRY_SOCIAL_ADMIN ) . 'views/social-follow.php' );
	}
}

/**
 * Registers a widget.
 *
 * @since 1.0.0
 */
function cherry_social_register_widget() {
	if ( class_exists( 'Cherry_Framework' ) ) {
		register_widget( 'Cherry_Social_Follow' );
	}
}

add_action( 'widgets_init', 'cherry_social_register_widget' );
