<?php
/**
 * Fires before a widget form.
 *
 * @package Cherry_Testimonials_Admin
 * @since   1.0.0
 */
do_action( 'cherry_instagram_widget_form_before' );
?>

<!-- Widget Title: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
</p>
<!-- Widget Endpoints: Select Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'endpoints' ); ?>"><?php _e( 'Content type:', 'cherry-social' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'endpoints' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'endpoints' ); ?>">
	<?php foreach ( $endpoints as $k => $v ) { ?>
		<option value="<?php echo $k; ?>"<?php selected( $instance['endpoints'], $k ); ?>><?php echo $v; ?></option>
	<?php } ?>
	</select>
</p>
<!-- Widget Username: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'user_name' ); ?>"><?php _e( 'User Name:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'user_name' ); ?>" value="<?php echo $user_name; ?>" class="widefat" id="<?php echo $this->get_field_id( 'user_name' ); ?>" /><br>
	<small><?php _e( 'Widget will work only for users who have full rights opened in Instagram account.', 'cherry-social' ); ?></small>
</p>
<!-- Widget Hashtag: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'tag' ); ?>"><?php _e( 'Hashtag:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'tag' ); ?>" value="<?php echo $tag; ?>" class="widefat" id="<?php echo $this->get_field_id( 'tag' ); ?>" /><br>
	<small><?php _e( 'Enter without `#` symbol.', 'cherry-social' ); ?></small>
</p>
<!-- Widget Images count: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'image_counter' ); ?>"><?php _e( 'Number of photos:', 'cherry-social' ); ?></label>
	<input type="number" min="1" max="12" step="1" name="<?php echo $this->get_field_name( 'image_counter' ); ?>" value="<?php echo $image_counter; ?>" class="widefat" id="<?php echo $this->get_field_id( 'image_counter' ); ?>" />
</p>
<!-- Widget Images size: Select Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Size of photos', 'cherry-social' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'image_size' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'image_size' ); ?>">
	<?php foreach ( $image_size as $k => $v ) { ?>
		<option value="<?php echo $k; ?>"<?php selected( $instance['image_size'], $k ); ?>><?php echo $v; ?></option>
	<?php } ?>
	</select>
</p>
<!-- Widget Description: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'display_description' ); ?>" name="<?php echo $this->get_field_name( 'display_description' ); ?>" type="checkbox" <?php checked( $display_description ); ?>>
	<label for="<?php echo $this->get_field_id( 'display_description' ); ?>"><?php _e( 'Description', 'cherry-social' ); ?></label>
</p>
<!-- Widget Time: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'display_time' ); ?>" name="<?php echo $this->get_field_name( 'display_time' ); ?>" type="checkbox" <?php checked( $display_time ); ?>>
	<label for="<?php echo $this->get_field_id( 'display_time' ); ?>"><?php _e( 'Date', 'cherry-social' ); ?></label>
</p>
<!-- Widget Button text: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'User account button text:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php echo $button_text; ?>" class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>" />
</p>
<?php
/**
 * Fires after a widget form.
 *
 * @since 1.0.0
 */
do_action( 'cherry_instagram_widget_form_after' );
