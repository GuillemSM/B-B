<?php
/**
 * The Template for displaying archive CPT Services.
 *
 * @package   Cherry_Services
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

global $wp_query;

$args = array(
	'template'     => 'image-box.tmpl',
	'before_title' => '<h3 class="cherry-services_title">',
	'after_title'  => '</h3>',
	'container'    => false,
	'linked_title' => 'yes',
	'size'         => 'cherry-thumb-s',
	'pager'        => true,
	'col_xs'       => '12',
	'col_sm'       => '6',
	'col_md'       => '4',
	'col_lg'       => 'none',
	'limit'        => Cherry_Services_Templater::get_posts_per_archive_page(),
	'categories'   => ! empty( $wp_query->query_vars['term'] ) ? $wp_query->query_vars['term'] : '',
);
$data = new Cherry_Services_Data;
$data->the_services( $args );
