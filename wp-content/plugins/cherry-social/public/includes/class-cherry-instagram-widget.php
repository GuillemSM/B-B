<?php
/**
 * Cherry Instagram Widget.
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

if ( ! class_exists( 'Cherry_Instagram' ) ) {

	/**
	 * Class for Instagram Widget.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Removed `get_user_id` method.
	 */
	class Cherry_Instagram extends WP_Widget {

		/**
		 * Unique identifier for widget.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		protected $widget_slug = 'cherry-instagram';

		/**
		 * Instagram API server.
		 *
		 * @since 1.0.4
		 * @var string
		 */
		private $service_url = 'https://www.instagram.com/';

		/**
		 * Data URL.
		 *
		 * @since 1.0.4
		 * @var string
		 */
		private $data_url = '';

		/**
		 * Specifies the classname and description.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct(
				$this->get_widget_slug(),
				__( 'Cherry Instagram', 'cherry-social' ),
				array(
					'classname'   => $this->get_widget_slug() . '_widget',
					'description' => __( 'A widget for Instagram.', 'cherry-social' ),
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

			$user_name     = ! empty( $instance['user_name'] )     ? strtolower( trim( $instance['user_name'] ) ) : '';
			$tag           = ! empty( $instance['tag'] )           ? esc_attr( $instance['tag'] ) : '';
			$image_counter = ! empty( $instance['image_counter'] ) ? absint( $instance['image_counter'] ) : '';
			$button_text   = ! empty( $instance['button_text'] )   ? esc_attr( $instance['button_text'] ) : '';

			$endpoints  = ( ! empty( $instance['endpoints'] ) && in_array( $instance['endpoints'], array_keys( $this->get_endpoints_options() ) ) ) ? $instance['endpoints'] : '';
			$image_size = ( ! empty( $instance['image_size'] ) && in_array( $instance['image_size'], array_keys( $this->get_image_size_options() ) ) ) ? $instance['image_size'] : '';

			if ( 'hashtag' == $endpoints ) {
				if ( empty( $tag ) ) {
					return print $before_widget . __( 'Please, enter #hashtag.', 'cherry-social' ) . $after_widget;
				}
			}

			if ( 'self' == $endpoints ) {
				if ( empty( $user_name ) ) {
					return print $before_widget . __( 'Please, enter your username.', 'cherry-social' ) . $after_widget;
				}
			}

			if ( $image_counter <= 0 ) {
				return '';
			}

			$config = array();

			if ( ! empty( $instance['display_time'] ) ) {
				$config[] = 'date';
			}

			if ( ! empty( $instance['display_description'] ) ) {
				$config[] = 'caption';
			}

			if ( ! empty( $image_size ) ) {
				$config['thumb'] = $image_size;
			}

			$data = ( 'self' == $endpoints ) ? $user_name : $tag;

			$config['endpoints'] = $endpoints;
			$photos = $this->get_photos( $data, $image_counter, $config );

			if ( ! $photos ) {
				return print $before_widget . __( 'No photos. Maybe you entered a invalid username or hashtag.', 'cherry-social' ) . $after_widget;
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

			$date_format = get_option( 'date_format' );
			$template    = $this->view_template();

			$output .= '<div class="cherry-instagram_items">';

			foreach ( (array) $photos as $key => $photo ) {

				$image   = $this->get_image( $photo, $image_size );
				$caption = $this->get_caption( $photo );
				$date    = $this->get_date( $photo, $date_format );

				ob_start();
				include $template;
				$output .= ob_get_clean();
			}

			$output .= '</div>';

			$output .= $button_text ? '<a href="' . esc_url( $this->data_url ) . '" class="btn btn-primary" role="button" target="_blank">' . $button_text . '</a>' : '';

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

			$instance['title']         = strip_tags( $new_instance['title'] );
			$instance['tag']           = trim( $new_instance['tag'], '#' );
			$instance['user_name']     = esc_attr( $new_instance['user_name'] );
			$instance['button_text']   = esc_attr( $new_instance['button_text'] );
			$instance['endpoints']     = esc_attr( $new_instance['endpoints'] );
			$instance['image_size']    = esc_attr( $new_instance['image_size'] );
			$instance['image_counter'] = absint( $new_instance['image_counter'] );

			$instance['display_description'] = ! empty( $new_instance['display_description'] ) ? 1 : 0;
			$instance['display_time']        = ! empty( $new_instance['display_time'] ) ? 1 : 0;

			// Delete a cache.
			delete_transient( 'cherry_instagram_photos' );

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
			$defaults = array(
				'title'               => '',
				'endpoints'           => 'hashtag', // hashtag or self
				'user_name'           => '',
				'tag'                 => '',
				'image_counter'       => 4,
				'image_size'          => 'thumbnail',
				'display_description' => 0,
				'display_time'        => 0,
				'button_text'         => '',
			);

			// Input (string)
			$instance    = wp_parse_args( (array) $instance, $defaults );
			$title       = esc_attr( $instance['title'] );
			$user_name   = esc_attr( $instance['user_name'] );
			$tag         = esc_attr( $instance['tag'] );
			$button_text = esc_attr( $instance['button_text'] );

			// Input (number)
			$image_counter = ! empty( $instance['image_counter'] ) ? intval( $instance['image_counter'] ) : esc_attr( $defaults['image_counter'] );

			// Select
			$endpoints  = $this->get_endpoints_options();
			$image_size = $this->get_image_size_options();

			// Checkbox
			$display_description = (bool) $instance['display_description'];
			$display_time        = (bool) $instance['display_time'];

			// Display the admin form.
			include( trailingslashit( CHERRY_SOCIAL_ADMIN ) . 'views/instagram-admin.php' );
		}

		/**
		 * Retrieve a photos.
		 *
		 * @since  1.0.0
		 * @since  1.0.4  Removed `$client_id` parametr.
		 * @param  string $data        User name or hashtag.
		 * @param  int    $img_counter Number of images.
		 * @param  array  $config      Set of configuration.
		 * @return array
		 */
		public function get_photos( $data, $img_counter, $config ) {
			$cached = get_transient( 'cherry_instagram_photos' );

			if ( false !== $cached ) {
				return $cached;
			}

			if ( 'self' == $config['endpoints'] ) {
				$this->data_url = $this->service_url . $data;
			} else {
				$this->data_url = $this->service_url . 'explore/tags/' . $data;
			}

			$url = add_query_arg(
				array( '__a' => 1 ),
				$this->data_url
			);

			$response = wp_remote_get( $url );

			if ( is_wp_error( $response ) || empty( $response ) || '200' != $response ['response']['code'] ) {
				return false;
			}

			$result  = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( ! is_array( $result ) ) {
				return false;
			}

			$key = ( 'self' == $config['endpoints'] ) ? 'user' : 'tag';

			if ( empty( $result[ $key ] ) ) {
				return false;
			}

			if ( empty( $result[ $key ]['media']['nodes'] ) ) {
				return false;
			}

			$nodes   = $result[ $key ]['media']['nodes'];
			$photos  = array();
			$counter = 1;

			foreach ( $nodes as $photo ) {

				if ( $counter > $img_counter ) {
					break;
				}

				$_photo          = array();
				$_photo['image'] = $photo['thumbnail_src'];
				$_photo['link']  = $photo['code'];

				if ( in_array( 'date', $config ) ) {
					$_photo['date'] = sanitize_text_field( $photo['date'] );
				}

				if ( in_array( 'caption', $config ) && ! empty( $photo['caption'] ) ) {
					$_photo['caption'] = wp_html_excerpt(
						$photo['caption'],
						apply_filters( 'cherry_instagram_caption_length', 10 ),
						apply_filters( 'cherry_instagram_caption_more', '&hellip;' )
					);
				}

				array_push( $photos, $_photo );
				$counter++;
			}

			set_transient( 'cherry_instagram_photos', $photos, HOUR_IN_SECONDS );

			return $photos;
		}

		/**
		 * Get an array of the available endpoints options.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_endpoints_options() {
			return apply_filters( 'cherry_instagram_get_endpoints_options', array(
				'self'    => __( 'My Photos', 'cherry-social' ),
				'hashtag' => __( 'Tagged photos', 'cherry-social' ),
			) );
		}

		/**
		 * Get an array of the available endpoints options.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_image_size_options() {
			return apply_filters( 'cherry_instagram_get_image_size_options', array(
				'large'     => __( 'Large', 'cherry-social' ),
				'thumbnail' => __( 'Thumbnail', 'cherry-social' ),
			) );
		}

		/**
		 * Retrieve a HTML link with image.
		 *
		 * @since  1.0.4  Changed link `href` attribute.
		 * @param  array  $photo      Item photo data.
		 * @param  string $image_size Photo size.
		 * @return string
		 */
		public function get_image( $photo, $image_size ) {
			$link = sprintf( $this->get_post_url(), $photo['link'] );
			$size = $this->_get_relation_image_size( $image_size );

			if ( ! is_array( $size ) || empty( $size ) ) {
				$size = array( 150, 150 );
			}

			// Replace auto-generated photo size.
			$width  = $size[0];
			$height = $size[1];
			$image  = str_replace( '640x640', "{$width}x{$height}", $photo['image'] );

			return sprintf(
				'<div class="cherry-instagram_thumbnail %s"><a class="cherry-instagram_link" href="%s" target="_blank" rel="nofollow"><img src="%s" alt="" width="%s" height="%s"></a></div>',
				sanitize_html_class( $image_size ),
				esc_url( $link ),
				esc_url( $image ),
				$width,
				$height
			);
		}

		/**
		 * Retrieve a photo sizes (in px) by option name.
		 *
		 * @since  1.0.4
		 * @param  string $image_size Photo size.
		 * @return array
		 */
		public function _get_relation_image_size( $image_size ) {

			switch ( $image_size ) {
				case 'large':
					$size = array( 320, 320 );
					break;

				default:
					$size = array( 150, 150 );
					break;
			}

			return apply_filters( 'cherry_instagram_get_relation_image_size', $size, $image_size );
		}

		/**
		 * Retrieve a URL for post.
		 *
		 * @since  1.0.4
		 * @return string
		 */
		public function get_post_url() {
			return apply_filters( 'cherry_instagram_get_post_url', $this->service_url . 'p/%s/' );
		}

		/**
		 * Retrieve a caption.
		 *
		 * @since  1.0.4
		 * @param  array $photo Item photo data.
		 * @return string
		 */
		public function get_caption( $photo ) {

			if ( empty( $photo['caption'] ) ) {
				return;
			}

			return sprintf( '<div class="cherry-instagram_desc">%s</div>', $photo['caption'] );
		}

		/**
		 * Retrieve a HTML tag with date.
		 *
		 * @since  1.0.4
		 * @param  array  $photo  Item photo data.
		 * @param  string $format Date format.
		 * @return string
		 */
		public function get_date( $photo, $format ) {

			if ( empty( $photo['date'] ) ) {
				return;
			}

			return sprintf( '<time class="cherry-instagram_date" datetime="%s">%s</time>', date( 'Y-m-d\TH:i:sP', $photo['date'] ), date( $format, $photo['date'] ) );
		}

		/**
		 * Retrieve a view template for widget.
		 *
		 * @since  1.0.4
		 * @return string
		 */
		public function view_template() {
			$template = locate_template( 'templates/instagram/instagram-widget.php', false, false );

			if ( '' === $template ) {
				$template = trailingslashit( CHERRY_SOCIAL_PUBLIC ) . 'views/instagram-widget.php';
			}

			return apply_filters( 'cherry_instagram_get_view_template', $template );
		}
	}
}

/**
 * Registers a widget.
 *
 * @since 1.0.0
 */
function cherry_instagram_register_widget() {
	register_widget( 'Cherry_Instagram' );
}

add_action( 'widgets_init', 'cherry_instagram_register_widget' );
