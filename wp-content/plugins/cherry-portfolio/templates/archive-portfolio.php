<?php
/**
 * Template Name: Portfolio
 *
 * The template for displaying CPT Portfolio.
 *
 * @package Cherry_Portfolio
 * @since   1.0.0
 */
?>
<article <?php if ( function_exists( 'cherry_attr' ) ) cherry_attr( 'post' ); ?>>

	<?php
		global $wp_query;

		$filter_visible = ( is_tax() ) ? 'false' : 'true';

		$attr = array(
			'filter_visible' => $filter_visible,
			'single_term'    => ! empty( $wp_query->query_vars['term'] ) ? $wp_query->query_vars['term'] : '',
		);

		$data = new Cherry_Portfolio_Data;
		$data->the_portfolio( $attr );
	?>

</article>
