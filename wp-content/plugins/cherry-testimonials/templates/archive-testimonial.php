<?php
/**
 * The archive index page for CPT Tesimonial.
 *
 * @package Cherry_Testimonials
 * @since   1.0.2
 */

global $wp_query;

$args = array(
	'limit'        => Cherry_Testimonials_Page_Template::$posts_per_archive_page,
	'size'         => 100,
	'pager'        => 'true',
	'template'     => 'page.tmpl',
	'category'     => ! empty( $wp_query->query_vars['term'] ) ? $wp_query->query_vars['term'] : '',
	'custom_class' => 'testimonials-page testimonials-page_archive',
);
$data = new Cherry_Testimonials_Data;
$data->the_testimonials( $args );
