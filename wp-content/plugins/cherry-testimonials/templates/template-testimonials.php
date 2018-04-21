<?php
/**
 * Template Name: Testimonials
 *
 * The template for displaying CPT Testimonials.
 *
 * @package Cherry_Testimonials
 * @since   1.0.0
 */

if ( have_posts() ) :

	while ( have_posts() ) : the_post();

		do_action( 'cherry_entry_before' );

		$args = array(
			'limit'        => 4,
			'size'         => 100,
			'pager'        => 'true',
			'template'     => 'page.tmpl',
			'custom_class' => 'testimonials-page',
		);
		$data = new Cherry_Testimonials_Data;
		$data->the_testimonials( $args );

		do_action( 'cherry_entry_after' );

	endwhile;

endif;
