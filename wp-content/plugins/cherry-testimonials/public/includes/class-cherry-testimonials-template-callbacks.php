<?php
/**
 * Define callback functions for templater.
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2012 - 2015, Cherry Team
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Callbacks for testimonial shortcode templater.
 *
 * @since 1.0.2
 */
class Cherry_Testimonials_Template_Callbacks {

	/**
	 * Shortcode attributes array.
	 * @var array
	 */
	public $atts = array();

	/**
	 * Current post meta.
	 *
	 * @since 1.1.0
	 * @var   array
	 */
	public $post_meta = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.1.0
	 * @param array $atts Set of attributes.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
	}

	/**
	 * Get post meta.
	 *
	 * @since 1.1.0
	 */
	public function get_meta() {
		if ( null === $this->post_meta ) {
			global $post;

			$this->post_meta = get_post_meta( $post->ID, CHERRY_TESTI_POSTMETA, true );
		}

		return $this->post_meta;
	}

	/**
	 * Clear post data after loop iteration.
	 *
	 * @since  1.1.0
	 *
	 * @return void
	 */
	public function clear_data() {
		$this->post_meta = null;
	}

	/**
	 * Get post thumbnail.
	 *
	 * @since 1.0.2
	 */
	public function get_avatar() {
		global $post;

		if ( isset( $this->atts['display_avatar'] ) && false === $this->atts['display_avatar'] ) {
			return;
		}

		$size = 50;
		if ( ! empty( $this->atts['size'] ) ) {
			$size = $this->atts['size'];
		}

		$avatar = Cherry_Testimonials_Data::get_image( $post->ID, $size );

		return apply_filters( 'cherry_testimonials_avatar_template_callbacks', $avatar, $post->ID, $this->atts );
	}

	/**
	 * Get post content.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		global $post;

		$_content = apply_filters( 'cherry_testimonials_content', get_the_content( '' ), $post );

		if ( ! $_content ) {
			return;
		}

		$content_type   = sanitize_key( $this->atts['content_type'] );
		$content_length = absint( $this->atts['content_length'] );

		if ( 'full' == $content_type || post_password_required() ) {
			$content = apply_filters( 'the_content', $_content );
		} else {
			/* wp_trim_excerpt analog */
			$content = strip_shortcodes( $_content );
			$content = apply_filters( 'the_content', $content );
			$content = str_replace( ']]>', ']]&gt;', $content );
			$content = wp_trim_words( $content, $content_length, apply_filters( 'cherry_testimonials_content_more', '', $this->atts, Cherry_Testimonials_Shortcode::$name ) );
		}

		return apply_filters( 'cherry_testimonials_content_template_callbacks', $content, $post->ID, $this->atts );
	}

	/**
	 * Get testimonial's author.
	 *
	 * @since 1.0.2
	 */
	public function get_author() {
		global $post;

		if ( isset( $this->atts['display_author'] ) && false === $this->atts['display_author'] ) {
			return;
		}

		$post_meta = $this->get_meta();
		$name      = ( $post_meta && ! empty( $post_meta['name'] ) ) ? $post_meta['name'] : get_the_title( $post->ID );
		$url       = ( $post_meta && ! empty( $post_meta['url'] ) ) ? $post_meta['url'] : '';
		$author    = '<footer><cite class="author" title="' . esc_attr( $name ) . '">';

		if ( ! empty( $url ) ) {
			$author .= '<a href="' . esc_url( $url ) . '">' . $name . '</a>';
		} else {
			$author .= esc_html( $name );
		}
		$author .= '</cite></footer>';

		return apply_filters( 'cherry_testimonials_author_template_callbacks', $author, $post->ID, $this->atts );
	}

	/**
	 * Get testimonial's email.
	 *
	 * @since 1.0.2
	 */
	public function get_email() {
		global $post;

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['email'] ) ) {
			return;
		}

		$email = '<a href="mailto:' . antispambot( $post_meta['email'], 1 ) .'" class="testimonials-item_email">' . antispambot( $post_meta['email'] ) .'</a>';

		return apply_filters( 'cherry_testimonials_email_template_callbacks', $email, $post->ID, $this->atts );
	}

	/**
	 * Get testimonial's name.
	 *
	 * @since 1.0.2
	 */
	public function get_name() {
		global $post;

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['name'] ) ) {
			return;
		}

		return apply_filters( 'cherry_testimonials_author_name_template_callbacks',
			esc_html( $post_meta['name'] ),
			$post->ID,
			$this->atts
		);
	}

	/**
	 * Get testimonial's url.
	 *
	 * @since 1.0.2
	 */
	public function get_url() {
		global $post;

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['url'] ) ) {
			return;
		}

		if ( isset( $this->atts['clickable_url'] ) && true === $this->atts['clickable_url'] ) {
			$format = '<a href="%1$s" target="_blank" rel="external">%1$s</a>';
		} else {
			$format = '%s';
		}

		$link = sprintf( $format, esc_url( $post_meta['url'] ) );

		return apply_filters( 'cherry_testimonials_url_template_callbacks',
			$link,
			$post->ID,
			$this->atts
		);
	}

	/**
	 * Get author's position.
	 *
	 * @since 1.0.3
	 */
	public function get_position() {
		global $post;

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['position'] ) ) {
			return;
		}

		return apply_filters( 'cherry_testimonials_position_template_callbacks',
			esc_html( $post_meta['position'] ),
			$post->ID,
			$this->atts
		);
	}

	/**
	 * Get company name.
	 *
	 * @since 1.0.3
	 */
	public function get_company() {
		global $post;

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['company'] ) ) {
			return;
		}

		return apply_filters( 'cherry_testimonials_company_template_callbacks',
			esc_html( $post_meta['company'] ),
			$post->ID,
			$this->atts
		);
	}
}
