<?php
/**
 * Optinization and improvements for a third-party plugins.
 *
 * @package themeXXXXX
 * @since   1.0.0
 */


/* MotoPress Contant Editor */
add_action( 'wp_head', 'cherry_child_dequeue_motopress_assets', 7 );

/**
 * Removed a Font Awesome file that was enqueued in `MotoPress Content Editor` plugin.
 *
 * @ignore
 */
function cherry_child_dequeue_motopress_assets() {
	wp_dequeue_style( 'mpce-font-awesome' );
}



/* Contact Form 7 */
add_filter( 'cherry_compiler_static_css', 'cherry_child_add_wpcf7_css_to_compiler' );
add_action( 'wpcf7_contact_form',         'cherry_child_wpcf7_enqueue_scripts' );

// Removed standard loader.
add_filter( 'wpcf7_ajax_loader',          '__return_empty_string' );

// Stop loading javascript on all pages.
add_filter( 'wpcf7_load_js',              '__return_false' );

/**
 * Pass stylesheet handle to CSS compiler.
 *
 * @ignore
 * @param  array $handles Stylesheet handles to optimize.
 * @return array
 */
function cherry_child_add_wpcf7_css_to_compiler( $handles ) {

	if ( ! defined( 'WPCF7_PLUGIN' ) ) {
		return $handles;
	}

	$handles = array_merge(
		array( 'contact-form-7' => plugins_url( 'includes/css/styles.css', WPCF7_PLUGIN ) ),
		$handles
	);

	return $handles;
}

/**
 * Enqueue javascript.
 *
 * @ignore
 */
function cherry_child_wpcf7_enqueue_scripts() {

	if ( is_admin() ) {
		return;
	}

	if ( ! function_exists( 'wpcf7_enqueue_scripts' ) ) {
		return;
	}

	wpcf7_enqueue_scripts();
}



/* MailChimp for WordPress */
add_filter( 'cherry_compiler_static_css', 'cherry_child_add_mc4wp_css_to_compiler' );

/**
 * Pass stylesheet handle to CSS compiler.
 *
 * @ignore
 * @param  array $handles Stylesheet handles to optimize.
 * @return array
 */
function cherry_child_add_mc4wp_css_to_compiler( $handles ) {

	if ( ! defined( 'MC4WP_PLUGIN_URL' ) ) {
		return $handles;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	$handles = array_merge(
		array(
			'mc4wp-form-basic'  => MC4WP_PLUGIN_URL . 'assets/css/form-basic' . $suffix . '.css',
			'mc4wp-form-themes' => MC4WP_PLUGIN_URL . 'assets/css/form-themes' . $suffix . '.css',
		),
		$handles
	);

	return $handles;
}
