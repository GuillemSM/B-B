<?php
/**
 * The Template for displaying single CPT Testimonial.
 *
 * @package   Cherry_Services
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

while ( have_posts() ) :

		the_post(); ?>

		<article <?php
		if ( function_exists( 'cherry_attr' ) ) {
			cherry_attr( 'post' );
		}
		?>>
		<?php

			do_action( 'cherry_post_before' );

			$args = array(
				'id'           => get_the_id(),
				'template'     => 'single-service.tmpl',
				'linked_title' => 'no',
				'before_title' => '<h3 class="cherry-services_title">',
				'after_title'  => '</h3>',
				'container'    => false,
				'size'         => 'cherry-thumb-s',
				'pager'        => true,
			);
			$data = new Cherry_Services_Data;
			$data->the_services( $args );

		?>
		</article>

		<?php do_action( 'cherry_post_after' );

endwhile;
