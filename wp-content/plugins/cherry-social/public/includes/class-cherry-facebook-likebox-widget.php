<?php
/**
 * Cherry Facebook Like Box Widget.
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

if ( ! class_exists( 'Cherry_Facebook_Like_Box' ) ) {

	/**
	 * Class for Facebook Like Box Widget.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Facebook_Like_Box extends WP_Widget {

		/**
		 * Unique identifier for widget.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		protected $widget_slug = 'cherry-facebook-like-box';

		/**
		 * Specifies the classname and description.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct(
				$this->get_widget_slug(),
				__( 'Cherry Facebook Like Box', 'cherry-social' ),
				array(
					'classname'   => $this->get_widget_slug() . '-class',
					'description' => __( 'A widget for Facebook Like Box.', 'cherry-social' ),
				)
			);

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

			extract( $args, EXTR_SKIP );

			if ( empty( $instance['page_url'] ) ) {
				return print $before_widget . __( 'Please, enter the URL of your facebook page.' ) . $after_widget;
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

				// Display the widget title if one was input.
				if ( $title ) {
					$output .= $before_title;
					$output .= $title;
					$output .= $after_title;
				}
			}

			$page_url = esc_url( $instance['page_url'] );
			$height   = ! empty( $instance['height'] ) ? absint( $instance['height'] ) : '';
			$cover    = ! empty( $instance['cover'] ) ? 1 : 0;
			$header   = ! empty( $instance['header'] ) ? 1 : 0;
			$faces    = ! empty( $instance['faces'] )  ? 1 : 0;
			$posts    = ! empty( $instance['posts'] ) ? 1 : 0;

			ob_start();
			include( trailingslashit( CHERRY_SOCIAL_PUBLIC ) . 'views/facebook-like-box.php' );
			$output .= ob_get_clean();

			/**
			 * Fires after a content widget.
			 *
			 * @since 1.0.0
			 */
			do_action( $this->widget_slug . '_after', $args, $instance );

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

			$instance['title']    = strip_tags( $new_instance['title'] );
			$instance['page_url'] = esc_url( $new_instance['page_url'] );
			$instance['height']   = absint( $new_instance['height'] );
			$instance['cover']    = ! empty( $new_instance['cover'] ) ? 1 : 0;
			$instance['header']   = ! empty( $new_instance['header'] ) ? 1 : 0;
			$instance['faces']    = ! empty( $new_instance['faces'] )  ? 1 : 0;
			$instance['posts']    = ! empty( $new_instance['posts'] ) ? 1 : 0;

			return $instance;
		}

		/**
		 * Generates the administration form for the widget.
		 *
		 * @since 1.0.0
		 * @param array $instance The array of keys and values for the widget.
		 */
		public function form( $instance ) {
			/**
			 * Filters default widget settings.
			 *
			 * @since 1.0.0
			 * @param array
			 */
			$defaults = apply_filters( 'cherry_facebook_like_box_widget_form_defaults_args', array(
				'title'    => '',
				'page_url' => '',
				'height'   => 250,
				'cover'    => 0,
				'header'   => 1,
				'faces'    => 0,
				'posts'    => 1,
			) );

			$instance = wp_parse_args( (array) $instance, $defaults );
			$title    = esc_attr( $instance['title'] );
			$page_url = esc_url( $instance['page_url'] );
			$height   = intval( $instance['height'] );
			$cover    = (bool) $instance['cover'];
			$faces    = (bool) $instance['faces'];
			$header   = (bool) $instance['header'];
			$posts    = (bool) $instance['posts'];

			// Display the admin form.
			include( trailingslashit( CHERRY_SOCIAL_ADMIN ) . 'views/facebook-like-box-admin.php' );
		}
	}
}

/**
 * Registers a widget.
 *
 * @since 1.0.0
 */
function cherry_facebok_like_box_register_widget() {
	register_widget( 'Cherry_Facebook_Like_Box' );
}

add_action( 'widgets_init', 'cherry_facebok_like_box_register_widget' );
