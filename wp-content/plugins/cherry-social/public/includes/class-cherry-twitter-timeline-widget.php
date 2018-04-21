<?php
/**
 * Cherry Twitter Timeline Widget.
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

if ( ! class_exists( 'Cherry_Twitter_Timeline' ) ) {

	/**
	 * Class for Twitter Timeline Widget.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Twitter_Timeline extends WP_Widget {

		/**
		 * Unique identifier for widget.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		protected $widget_slug = 'cherry-twitter-timeline';

		/**
		 * Specifies the classname and description.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct(
				$this->get_widget_slug(),
				__( 'Cherry Twitter Timeline', 'cherry-social' ),
				array(
					'classname'   => $this->get_widget_slug() . '-class',
					'description' => __( 'A widget for Twitter timeline.', 'cherry-social' ),
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

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				return print $cache[ $args['widget_id'] ];
			}

			extract( $args, EXTR_SKIP );

			if ( empty( $instance['widget_ID'] ) ) {
				return print $before_widget . __( 'Please, enter your Twitter widget ID.' ) . $after_widget;
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

			$tw_widget_id = esc_attr( $instance['widget_ID'] );
			$height       = ! empty( $instance['height'] )       ? absint( $instance['height'] ) : '';
			$limit        = ! empty( $instance['limit'] )        ? absint( $instance['limit'] ) : '';
			$link_color   = ! empty( $instance['link_color'] )   ? esc_attr( $instance['link_color'] ) : '';
			$border_color = ! empty( $instance['border_color'] ) ? esc_attr( $instance['border_color'] ) : '';
			$skin         = ( ! empty( $instance['skin'] ) && in_array( $instance['skin'], array_keys( $this->get_skin_options() ) ) ) ? $instance['skin'] : '';

			$chrome = array();
			if ( ! empty( $instance['noheader'] ) ) {
				$chrome[] = 'noheader';
			}
			if ( ! empty( $instance['nofooter'] ) ) {
				$chrome[] = 'nofooter';
			}
			if ( ! empty( $instance['noborders'] ) ) {
				$chrome[] = 'noborders';
			}
			if ( ! empty( $instance['noscrollbar'] ) ) {
				$chrome[] = 'noscrollbar';
			}
			if ( ! empty( $instance['transparent'] ) ) {
				$chrome[] = 'transparent';
			}

			ob_start();
			include( trailingslashit( CHERRY_SOCIAL_PUBLIC ) . 'views/twitter-timeline-widget.php' );
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

			$instance['title']        = strip_tags( $new_instance['title'] );
			$instance['widget_ID']    = strip_tags( $new_instance['widget_ID'] );
			$instance['height']       = absint( $new_instance['height'] );
			$instance['limit']        = absint( $new_instance['limit'] );
			$instance['link_color']   = trim( $new_instance['link_color'], '#' );
			$instance['border_color'] = trim( $new_instance['border_color'], '#' );
			$instance['noheader']     = ! empty( $new_instance['noheader'] ) ? 1 : 0;
			$instance['nofooter']     = ! empty( $new_instance['nofooter'] ) ? 1 : 0;
			$instance['noborders']    = ! empty( $new_instance['noborders'] ) ? 1 : 0;
			$instance['noscrollbar']  = ! empty( $new_instance['noscrollbar'] ) ? 1 : 0;
			$instance['transparent']  = ! empty( $new_instance['transparent'] ) ? 1 : 0;
			$instance['skin']         = esc_attr( $new_instance['skin'] );

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
			$defaults = apply_filters( 'cherry_twitter_timeline_widget_form_defaults_args', array(
				'title'        => '',
				'widget_ID'    => '',
				'height'       => 400,
				'limit'        => '',
				'skin'         => 'light',
				'link_color'   => '',
				'border_color' => '',
				'noheader'     => 0,
				'nofooter'     => 0,
				'noborders'    => 0,
				'noscrollbar'  => 0,
				'transparent'  => 0,
			) );

			$instance     = wp_parse_args( (array) $instance, $defaults );
			$title        = esc_attr( $instance['title'] );
			$tw_widget_id = esc_attr( $instance['widget_ID'] );
			$height       = intval( $instance['height'] );
			$limit        = ! empty( $instance['limit'] ) ? intval( $instance['limit'] ) : esc_attr( $defaults['limit'] );
			$skin         = $this->get_skin_options();
			$link_color   = esc_attr( $instance['link_color'] );
			$border_color = esc_attr( $instance['border_color'] );
			$noheader     = (bool) $instance['noheader'];
			$nofooter     = (bool) $instance['nofooter'];
			$noborders    = (bool) $instance['noborders'];
			$noscrollbar  = (bool) $instance['noscrollbar'];
			$transparent  = (bool) $instance['transparent'];

			// Display the admin form.
			include( trailingslashit( CHERRY_SOCIAL_ADMIN ) . 'views/twitter-timeline-admin.php' );
		}

		/**
		 * Get an array of the available orderby options.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_skin_options() {
			return apply_filters( 'cherry_twitter_timeline_get_skin_options', array(
				'light' => __( 'Light', 'cherry-social' ),
				'dark'  => __( 'Dark', 'cherry-social' ),
			) );
		}
	}
}

/**
 * Registers a widget.
 *
 * @since 1.0.0
 */
function cherry_twitter_timeline_register_widget() {
	register_widget( 'Cherry_Twitter_Timeline' );
}

add_action( 'widgets_init', 'cherry_twitter_timeline_register_widget' );
