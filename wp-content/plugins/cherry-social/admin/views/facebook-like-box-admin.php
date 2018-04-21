<?php
/**
 * Fires before a widget form.
 *
 * @package Cherry_Testimonials_Admin
 * @since   1.0.0
 */
do_action( 'cherry_facebook_like_box_widget_form_before' );
?>

<!-- Widget Title: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
</p>
<!-- Widget Page URL: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'page_url' ); ?>"><?php _e( 'Page URL:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'page_url' ); ?>" value="<?php echo $page_url; ?>" class="widefat" id="<?php echo $this->get_field_id( 'page_url' ); ?>" />
	<?php $link = sprintf( "<a href='%s' target='_blank'>Facebook Pages</a>", esc_url( 'https://www.facebook.com/help/174987089221178/' ) ); ?>
	<small><?php printf( __( 'The Like Box only works with %s.', 'cherry-social' ), $link ); ?></small>
</p>
<!-- Widget Height: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height (px):', 'cherry-social' ); ?></label>
	<input type="number" min="70" max="2000" step="10" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $height; ?>" class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" placeholder="<?php sprintf( _e( 'Default (%spx)', 'cherry-social' ), $height ); ?>" /><br>
</p>
<!-- Widget Cover: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'cover' ); ?>" name="<?php echo $this->get_field_name( 'cover' ); ?>" type="checkbox" <?php checked( $cover ); ?>>
	<label for="<?php echo $this->get_field_id( 'cover' ); ?>"><?php _e( 'Hide cover photo in header', 'cherry-social' ); ?></label>
</p>
<!-- Widget Header: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'header' ); ?>" name="<?php echo $this->get_field_name( 'header' ); ?>" type="checkbox" <?php checked( $header ); ?>>
	<label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e( 'Use small header instead', 'cherry-social' ); ?></label>
</p>
<!-- Widget Faces: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'faces' ); ?>" name="<?php echo $this->get_field_name( 'faces' ); ?>" type="checkbox" <?php checked( $faces ); ?>>
	<label for="<?php echo $this->get_field_id( 'faces' ); ?>"><?php _e( 'Show profile photos when friends like this', 'cherry-social' ); ?></label>
</p>
<!-- Widget Posts: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" type="checkbox" <?php checked( $posts ); ?>>
	<label for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e( "Show posts from the Page's timeline", 'cherry-social' ); ?></label>
</p>
<?php
/**
 * Fires after a widget form.
 *
 * @since 1.0.0
 */
do_action( 'cherry_facebook_like_box_widget_form_after' );
