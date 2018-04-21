<?php
/**
 * Plugin Name: Cherry Social
 * Plugin URI:  http://www.cherryframework.com/
 * Description: A social plugin for WordPress.
 * Version:     1.0.4
 * Author:      Cherry Team
 * Author URI:  http://www.cherryframework.com/
 * Text Domain: cherry-social
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 *
 * @package  Cherry Social
 * @category Core
 * @author   Cherry Team
 * @license  GPL-3.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class `Cherry_Social` not exists.
if ( ! class_exists( 'Cherry_Social' ) ) {

	/**
	 * Sets up and initializes the Cherry Social plugin.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Social {

		/**
		 * Unique identifier for a plugin.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		protected $plugin_slug = 'cherry-social';

		/**
		 * Instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		protected static $instance = null;

		/**
		 * Counter for a share buttons group.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		protected static $share_group_counter = 0;

		/**
		 * Counter for a follow buttons group.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		protected static $follow_group_counter = 0;

		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );
			add_action( 'plugins_loaded', array( $this, 'lang' ),      2 );
			add_action( 'plugins_loaded', array( $this, 'includes' ),  3 );
			add_action( 'plugins_loaded', array( $this, 'admin' ),     4 );

			add_action( 'init', array( $this, 'register_static' ), 11 );

			// Callback for `%%SHARE%%` macros (for Blog and Single Post pages in themes based on cherryframework).
			add_filter( 'cherry_pre_get_the_post_share', array( $this, 'share' ), 9, 2 );

			// Adds a section to `Cherry Options`.
			add_filter( 'cherry_defaults_settings', array( $this, 'add_cherry_options' ), 11 );

			// Load public-facing stylesheet.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			// Pass style handle to CSS compiler.
			add_filter( 'cherry_compiler_static_css', array( $this, 'add_style_to_compiler' ) );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Defines constants for the plugin.
		 *
		 * @since 1.0.0
		 */
		public function constants() {
			define( 'CHERRY_SOCIAL_VERSION', '1.0.4' );
			define( 'CHERRY_SOCIAL_SLUG',    basename( dirname( __FILE__ ) ) );
			define( 'CHERRY_SOCIAL_DIR',     trailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'CHERRY_SOCIAL_URI',     trailingslashit( plugin_dir_url( __FILE__ ) ) );
			define( 'CHERRY_SOCIAL_ADMIN',   CHERRY_SOCIAL_DIR . 'admin' );
			define( 'CHERRY_SOCIAL_PUBLIC',  CHERRY_SOCIAL_DIR . 'public' );
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since 1.0.0
		 */
		public function lang() {
			$domain = $this->plugin_slug;
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
		}

		/**
		 * Loads files from the 'public/includes' folder.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			// Shortcodes.
			require_once( trailingslashit( CHERRY_SOCIAL_PUBLIC ) . 'includes/class-cherry-social-shortcodes.php' );

			// Widgets.
			require_once( trailingslashit( CHERRY_SOCIAL_PUBLIC ) . 'includes/class-cherry-twitter-timeline-widget.php' );
			require_once( trailingslashit( CHERRY_SOCIAL_PUBLIC ) . 'includes/class-cherry-facebook-likebox-widget.php' );
			require_once( trailingslashit( CHERRY_SOCIAL_PUBLIC ) . 'includes/class-cherry-social-follow-widget.php' );
			require_once( trailingslashit( CHERRY_SOCIAL_PUBLIC ) . 'includes/class-cherry-instagram-widget.php' );
		}

		/**
		 * Loads admin files.
		 *
		 * @since 1.0.0
		 */
		public function admin() {

			if ( is_admin() ) {
				require_once( CHERRY_SOCIAL_ADMIN . '/includes/class-cherry-update/class-cherry-plugin-update.php' );

				$updater = new Cherry_Plugin_Update();
				$updater->init( array(
					'version'         => CHERRY_SOCIAL_VERSION,
					'slug'            => CHERRY_SOCIAL_SLUG,
					'repository_name' => CHERRY_SOCIAL_SLUG,
				) );
			}
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_styles() {
			wp_register_style(
				$this->plugin_slug . '-flaticon',
				plugins_url( 'public/assets/fonts/flaticon.min.css', __FILE__ ),
				array(),
				CHERRY_SOCIAL_VERSION
			);
			wp_register_style(
				$this->plugin_slug,
				plugins_url( 'public/assets/css/public.css', __FILE__ ),
				array( $this->plugin_slug . '-flaticon' ),
				CHERRY_SOCIAL_VERSION
			);

			wp_enqueue_style( $this->plugin_slug );
		}

		/**
		 * Pass style handle to CSS compiler.
		 *
		 * @since 1.0.0
		 * @param array $handles CSS handles to compile.
		 * @return array
		 */
		function add_style_to_compiler( $handles ) {
			$handles = array_merge(
				array( $this->plugin_slug => plugins_url( 'public/assets/css/public.css', __FILE__ ) ),
				$handles
			);

			return $handles;
		}

		/**
		 * Register and enqueues public-facing JavaScript files.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			wp_register_script(
				$this->plugin_slug . '-plugin-script',
				plugins_url( 'public/assets/js/public.js', __FILE__ ),
				array( 'jquery' ),
				CHERRY_SOCIAL_VERSION,
				true
			);

			wp_enqueue_script( $this->plugin_slug . '-plugin-script' );
		}

		/**
		 * Checks if SSL is being used.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function http() {
			return is_ssl() ? 'https' : 'http';
		}

		/**
		 * Retrieve a title for sharing.
		 *
		 * @since  1.0.0
		 * @param  int $post_id Post ID.
		 * @return string
		 */
		public function get_share_title( $post_id ) {
			$title = apply_filters( 'cherry_social_get_share_title', get_the_title( $post_id ), $post_id );

			return html_entity_decode( wp_kses( $title, null ) );
		}

		/**
		 * Retrieve a url for sharing.
		 *
		 * @since  1.0.0
		 * @param  int $post_id Post ID.
		 * @return string
		 */
		public function get_share_url( $post_id ) {
			return apply_filters( 'cherry_social_get_share_url', get_permalink( $post_id ), $post_id );
		}

		/**
		 * Retrieve a prefix for icon css-class.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_icon_class_prefix() {
			return apply_filters( 'cherry_social_share_get_icon_class_prefix', 'flaticon-' );
		}

		/**
		 * Replace a macros on the real data.
		 *
		 * @since  1.0.0
		 * @param  array $query     Set of query.
		 * @param  array $post_data Set of post data.
		 * @return array
		 */
		public function prepare_query( $query, $post_data ) {

			foreach ( $query as $k => $v ) {

				if ( false === strpos( $v, '@@' ) ) {
					continue;
				} else {
					$_v = strtolower( trim( $v, '@@' ) );
				}

				$query[ $k ] = ( isset( $post_data[ $_v ] ) ) ? urlencode( $post_data[ $_v ] ) : '';
			}

			return $query;
		}

		/**
		 * Retrieve a html-formatted share item.
		 *
		 * @since  1.0.0
		 * @param  string $url  Social URL.
		 * @param  array  $data Share data.
		 * @return string
		 */
		public function build_html_item( $url, $data ) {

			$output = sprintf( '<li class="cherry-share_item %2$s-item"><a class="cherry-share_link" href="%1$s" rel="nofollow" target="_blank" title="%3$s"><i class="%4$s"></i><span class="cherry-share_label">%5$s</span></a></li>',
				htmlspecialchars( $url ),
				sanitize_html_class( $data['id'] ),
				esc_html__( 'Share on ' . $data['name'], 'cherry-social' ),
				esc_attr( $this->get_icon_class_prefix() . $data['id'] ),
				esc_attr( $data['name'] )
			);

			return apply_filters( 'cherry_social_share_build_html_item', $output, $url, $data );
		}

		/**
		 * Before outputing a share buttons check options.
		 *
		 * @since  1.0.0
		 * @param  bool  $pre  Value to return instead of the callback-function.
		 * @param  array $attr Set of attributes.
		 * @return string|bool
		 */
		public function share( $pre = false, $attr = array() ) {
			$share_options = ( false === $pre ) ? $this->get_option( 'share-items' ) : explode( ',', $pre );

			if ( empty( $attr ) ) {
				return $this->share_buttons( $share_options, false );
			}

			if ( ! empty( $attr['where'] ) ) {
				if ( ( ( 'loop' === $attr['where'] ) && is_singular() )
					|| ( ( 'single' === $attr['where'] ) && ! is_singular() )
					) {
					return '';
				}
			}

			$_attr = $attr;

			if ( is_array( $attr ) && ! empty( $attr['networks'] ) ) {
				$_attr = $attr['networks'];
			}

			if ( is_string( $_attr ) ) {
				$share_options = explode( ',', $_attr );
			}

			if ( empty( $share_options ) ) {
				return $pre;
			}

			return $this->share_buttons( $share_options, false );
		}

		/**
		 * Output or retrieve a share buttons in the list-style.
		 *
		 * @since  1.0.0
		 * @param  array  $networks     Set of social networks.
		 * @param  bool   $echo         Echo or return.
		 * @param  string $custom_class Extra CSS-class.
		 * @return string
		 */
		public function share_buttons( $networks, $echo = true, $custom_class = '' ) {
			$share_btns = $this->get_the_share_btns();

			if ( empty( $share_btns ) ) {
				return;
			}

			// Prepare a data for sharing.
			$id           = get_the_ID();
			$type         = get_post_type( $id );
			$url          = $this->get_share_url( $id );
			$title        = $this->get_share_title( $id );
			$summary      = get_the_excerpt();
			$thumbnail_id = get_post_thumbnail_id( $id );
			$thumbnail    = '';

			if ( ! empty( $thumbnail_id ) ) {
				$thumbnail = wp_get_attachment_image_src( $thumbnail_id );
				$thumbnail = $thumbnail[0];
			}

			$post_data     = compact( 'id', 'type', 'url', 'title', 'summary', 'thumbnail' );
			$share_buttons = '';

			foreach ( (array) $networks as $network ) :

				if ( ! empty( $share_btns[ $network ]['callback'] )
					&& is_callable( $share_btns[ $network ]['callback'] )
					) {
					$_url = call_user_func( $share_btns[ $network ]['callback'], $post_data );
					$share_buttons .= $this->build_html_item( $_url, $share_btns[ $network ] );
					continue;
				}

				if ( empty( $share_btns[ $network ]['share_url'] ) ) {
					continue;
				}

				if ( 'pinterest' === $network && empty( $post_data['thumbnail'] ) ) {
					continue;
				}

				// Parse a URL and return its components (array).
				$parse_url = parse_url( $share_btns[ $network ]['share_url'] );
				$new_url   = $this->http() . '://' . $parse_url['host'] . $parse_url['path'];

				if ( empty( $parse_url['query'] ) ) {
					$share_buttons .= $this->build_html_item( $new_url, $share_btns[ $network ] );
					continue;
				}

				// Parse a query-string (after the question mark `?`) into variables to be stored in an array.
				wp_parse_str( $parse_url['query'], $query );

				$_query = $this->prepare_query( $query, $post_data );
				$_url   = add_query_arg( $_query, $new_url );

				$share_buttons .= $this->build_html_item( $_url, $share_btns[ $network ] );

			endforeach;

			if ( empty( $share_buttons ) ) {
				return;
			}

			$custom_class = ! empty( $custom_class ) ? ' ' . $custom_class : '';

			$output = sprintf(
				'<div id="cherry-share-btns-%1$d" class="cherry-share-btns_wrap%2$s"><ul class="cherry-share_list clearfix">%3$s</ul></div>',
				++self::$share_group_counter,
				esc_attr( $custom_class ),
				$share_buttons
			);

			$output = apply_filters( 'cherry_social_share_btns_html', $output, $networks, $share_btns );

			if ( true === $echo ) {
				echo $output;
			} else {
				return $output;
			}
		}

		/**
		 * Static registration.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function register_static() {
			$static_file = apply_filters( 'cherry_social_static_file', 'social-follow.php' );

			$abspath = preg_replace( '#/+#', '/', trailingslashit( get_stylesheet_directory() ) . $static_file );

			// If file found in child theme - include it and break function.
			if ( file_exists( $abspath ) ) {
				require_once $abspath;
				return;
			}

			$abspath = preg_replace( '#/+#', '/', trailingslashit( get_template_directory() ) . $static_file );

			if ( file_exists( $abspath ) ) {
				require_once $abspath;
				return;
			}

			require_once CHERRY_SOCIAL_DIR . 'init/statics/' . $static_file;
		}

		/**
		 * Output or retrieve HTML-formatted list with `Follow Us` networks.
		 *
		 * @since  1.0.0
		 * @param  array|init $networks     Array with network names or `-1` - if you want get all networks.
		 * @param  bool       $echo         Output or retrieve result.
		 * @param  string     $custom_class Extra CSS-class.
		 * @return string
		 */
		public function get_follows( $networks, $echo = true, $custom_class = '' ) {

			if ( empty( $networks ) ) {
				return;
			}

			$follows = $this->get_option( 'follow-items', false );

			if ( false === $follows ) {
				return;
			}

			if ( -1 != $networks ) {
				foreach ( $follows as $id => $follow ) {

					if ( ( ! empty( $follow['link-label'] ) && ( in_array( sanitize_key( $follow['link-label'] ), $networks ) ) )
						||
						( ! empty( $follow['network-id'] ) && ( in_array( sanitize_key( $follow['network-id'] ), $networks ) ) ) ) {
						continue;
					}

					unset( $follows[ $id ] );
				}
			}

			if ( empty( $follows ) ) {
				return;
			}

			$count = ++self::$follow_group_counter;
			$custom_class = esc_attr( $custom_class );
			$custom_class = ! empty( $custom_class ) ? ' ' . $custom_class : '';

			$output = "<div id='cherry-follow-items-{$count}' class='cherry-follow_wrap{$custom_class}'>";
				$output .= "<ul class='cherry-follow_list clearfix'>";

				foreach ( $follows as $i => $follow ) {
					$url        = esc_url( $follow['external-link'] );
					$label      = sanitize_text_field( $follow['link-label'] );
					$item_class = strtolower( sanitize_html_class( $follow['link-label'], 'cherry-follow-' . $i ) . '-item' );
					$icon       = '';
					$icon_class = $follow['font-class'];

					if ( ! empty( $icon_class ) ) {
						$icon_classes = explode( ' ', $icon_class );
						$icon_classes = array_map( 'sanitize_html_class', $icon_classes );
						$icon_class   = join( ' ', $icon_classes );
						$icon         = "<i class='{$icon_class}'></i>";
					}

					$format = '<li class="cherry-follow_item %1$s"><a class="cherry-follow_link" href="%2$s" target="_blank" rel="nofollow" title="%3$s">%4$s<span class="cherry-follow_label">%3$s</span></a></li>';

					/**
					 * Filters a html-formatted string for outputing a `follow` item.
					 *
					 * @since 1.0.3
					 */
					$format = apply_filters( 'cherry_social_get_follows_item_format', $format, $item_class, $url, $label, $icon_class );

					$item = sprintf( $format, $item_class, $url, $label, $icon );

					$output .= $item;
				}

				$output .= '</ul>';
			$output .= '</div>';
			$output = apply_filters( 'cherry_social_get_follows_html', $output );

			if ( $echo ) {
				echo $output;
			} else {
				return $output;
			}
		}

		/**
		 * Return a values for a named option from the options database table.
		 *
		 * @since  1.0.0
		 * @param  string $option  Name of the option to retrieve.
		 * @param  mixed  $default The default value to return if no value is returned.
		 * @return mixed           Current value for the specified option. If the option does not exist, returns
		 *                         parameter $default if specified or boolean FALSE by default.
		 */
		public function get_option( $option, $default = false ) {

			if ( ! function_exists( 'cherry_get_option' ) ) {
				return $default;
			}

			return cherry_get_option( $option, $default );
		}

		/**
		 * Return the plugin slug.
		 *
		 * @since  1.0.0
		 * @return Plugin slug variable.
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug;
		}

		/**
		 * Return an instance of this class.
		 *
		 * @since  1.0.0
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * On plugin activation.
		 *
		 * @since 1.0.0
		 */
		public static function activation() {
			do_action( 'cherry_social_activate' );
		}

		/**
		 * On plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		public static function deactivation() {
			do_action( 'cherry_social_deactivate' );
		}

		/**
		 * Retrieve a share buttons with settings.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_the_share_btns() {

			$share_btns = array(
				'facebook' => array(
					'id'        => 'facebook',
					'name'      => 'Facebook',
					'share_url' => 'https://www.facebook.com/sharer/sharer.php?u=@@URL@@&t=@@TITLE@@',
				),
				'twitter' => array(
					'id'        => 'twitter',
					'name'      => 'Twitter',
					'share_url' => 'https://twitter.com/intent/tweet?url=@@URL@@&text=@@TITLE@@',
				),
				'googleplus' => array(
					'id'        => 'googleplus',
					'name'      => 'Google+',
					'share_url' => 'https://plus.google.com/share?url=@@URL@@',
				),
				'pinterest' => array(
					'id'        => 'pinterest',
					'name'      => 'Pinterest',
					'share_url' => 'https://www.pinterest.com/pin/create/button/?url=@@URL@@&description=@@TITLE@@&media=@@THUMBNAIL@@',
				),
				'linkedin' => array(
					'id'        => 'linkedin',
					'name'      => 'LinkedIn',
					'share_url' => 'https://www.linkedin.com/cws/share?token=&isFramed=false&title=@@TITLE@@&url=@@URL@@',
				),
				'tumblr' => array(
					'id'        => 'tumblr',
					'name'      => 'Tumblr',
					'share_url' => 'http://www.tumblr.com/share?v=3&u=@@URL@@&t=@@TITLE@@&s=',
				),
				'stumbleupon' => array(
					'id'        => 'stumbleupon',
					'name'      => 'StumbleUpon',
					'share_url' => 'https://www.stumbleupon.com/submit?url=@@URL@@&title=@@TITLE@@',
				),
				'reddit' => array(
					'id'        => 'reddit',
					'name'      => 'Reddit',
					'share_url' => 'http://www.reddit.com/submit?url=@@URL&title=@@TITLE@@',
				),
			);

			return apply_filters( 'cherry_social_share_btns', $share_btns );
		}

		/**
		 * Adds `Social settings` tab with options.
		 *
		 * @since 1.0.0
		 * @param array $sections Set of layout options.
		 */
		public function add_cherry_options( $sections ) {
			$social_options = array();

			// Sharing.
			$share_btns    = $this->get_the_share_btns();
			$items_value   = array_keys( $share_btns );
			$items_options = wp_list_pluck( $share_btns, 'name' );

			$social_options['share-items'] = array(
				'type'        => 'checkbox',
				'title'       => __( 'Sharing networks', 'cherry-social' ),
				'decsription' => __( 'Select the social networks to display for sharing', 'cherry-social' ),
				'hint'        => array(
					'type'    => 'text',
					'content' => __( 'Type a macros', 'cherry-social' ) . '<code>%%SHARE%%</code> in *.tmpl file. <br><small>e.g. /my-theme/content/standard.tmpl</small>.',
				),
				'value'   => $items_value,
				'options' => $items_options,
			);

			// Follow Us.
			$social_options['follow-title'] = array(
				'type'        => 'text',
				'title'       => __( 'Follow Us title', 'cherry-social' ),
				'decsription' => __( 'This title is used in `Follow Us` static.', 'cherry-social' ),
				'value'       => __( 'Follow Us', 'cherry-social' ),
			);
			$social_options['follow-items'] = array(
				'type'        => 'repeater',
				'title'       => __( 'Follow Us networks', 'cherry-social' ),
				'decsription' => __( 'Set the social networks to display for following', 'cherry-social' ),
				'value'       => array(
					array(
						'external-link' => 'https://www.facebook.com/cherry.framework',
						'font-class'    => 'flaticon-facebook',
						'link-label'    => __( 'Facebook', 'cherry-social' ),
						'network-id'    => 'network-0',
					),
					array(
						'external-link' => 'https://twitter.com/CherryFramework',
						'font-class'    => 'flaticon-twitter',
						'link-label'    => __( 'Twitter', 'cherry-social' ),
						'network-id'    => 'network-1',
					),
					array(
						'external-link' => 'https://plus.google.com/u/0/110473764189007055556/posts',
						'font-class'    => 'flaticon-googleplus',
						'link-label'    => __( 'Google+', 'cherry-social' ),
						'network-id'    => 'network-2',
					),
				),
			);

			$sections['social-section'] = array(
				'name'         => __( 'Social', 'cherry-social' ),
				'icon'         => 'dashicons dashicons-share',
				'priority'     => 120,
				'options-list' => $social_options,
			);

			return $sections;
		}
	}

	Cherry_Social::get_instance();
}
