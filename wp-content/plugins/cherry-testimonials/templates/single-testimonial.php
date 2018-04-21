<?php
/**
 * The Template for displaying single CPT Testimonial.
 *
 * @package Cherry_Testimonials
 * @since   1.0.0
 */

while ( have_posts() ) : the_post();

	do_action( 'cherry_entry_before' );

	$args = array(
		'id'           => get_the_ID(),
		'size'         => 100,
		'template'     => 'single.tmpl',
		'custom_class' => 'testimonials-page-single',
	);
	$data = new Cherry_Testimonials_Data;
	$data->the_testimonials( $args );

	do_action( 'cherry_entry_after' );

endwhile;
