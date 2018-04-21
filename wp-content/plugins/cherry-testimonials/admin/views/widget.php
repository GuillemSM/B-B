<?php
	/**
	 * Fires before a widget form.
	 *
	 * @package Cherry_Testimonials_Admin
	 * @since   1.0.0
	 */
	do_action( 'cherry_testimonials_widget_form_before' );
?>
<!-- Widget Title: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cherry-testimonials' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
</p>
<!-- Widget Limit: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'cherry-testimonials' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $limit; ?>" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
</p>
<!-- Widget Image Size: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Image Size (in pixels):', 'cherry-testimonials' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'size' ); ?>" value="<?php echo $size; ?>" class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>" />
</p>
<!-- Widget Display Author: Checkbox Input -->
<p>
	<input id="<?php echo $this->get_field_id( 'display_author' ); ?>" name="<?php echo $this->get_field_name( 'display_author' ); ?>" type="checkbox"<?php checked( $display_author, 1 ); ?> />
	<label for="<?php echo $this->get_field_id( 'display_author' ); ?>"><?php _e( 'Display Author', 'cherry-testimonials' ); ?></label>
</p>
<!-- Widget Display Avatar: Checkbox Input -->
<p>
	<input id="<?php echo $this->get_field_id( 'display_avatar' ); ?>" name="<?php echo $this->get_field_name( 'display_avatar' ); ?>" type="checkbox"<?php checked( $display_avatar, 1 ); ?> />
	<label for="<?php echo $this->get_field_id( 'display_avatar' ); ?>"><?php _e( 'Display Avatar', 'cherry-testimonials' ); ?></label>
</p>
<!-- Widget ID: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'specific_id' ); ?>"><?php _e( 'Specific ID:', 'cherry-testimonials' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'specific_id' ); ?>" value="<?php echo $specific_id; ?>" class="widefat" id="<?php echo $this->get_field_id( 'specific_id' ); ?>" />
</p>
<p><small><?php _e( 'Testimonials Post IDs, separated by commas.', 'cherry-testimonials' ); ?></small></p>
<!-- Widget Order By: Select Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order By:', 'cherry-testimonials' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>">
	<?php foreach ( $orderby as $k => $v ) { ?>
		<option value="<?php echo $k; ?>"<?php selected( $instance['orderby'], $k ); ?>><?php echo $v; ?></option>
	<?php } ?>
	</select>
</p>
<!-- Widget Order: Select Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order Direction:', 'cherry-testimonials' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>">
	<?php foreach ( $order as $k => $v ) { ?>
		<option value="<?php echo $k; ?>"<?php selected( $instance['order'], $k ); ?>><?php echo $v; ?></option>
	<?php } ?>
	</select>
</p>
<!-- Widget Post content: Select Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'content_type' ); ?>"><?php _e( 'Post content:', 'cherry-testimonials' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'content_type' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'content_type' ); ?>">
	<?php foreach ( $content_type as $k => $v ) { ?>
		<option value="<?php echo $k; ?>"<?php selected( $instance['content_type'], $k ); ?>><?php echo $v; ?></option>
	<?php } ?>
	</select>
</p>
<!-- Widget Content Length: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'content_length' ); ?>"><?php _e( 'Content Length:', 'cherry-testimonials' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'content_length' ); ?>" value="<?php echo $content_length; ?>" class="widefat" id="<?php echo $this->get_field_id( 'content_length' ); ?>" />
</p>
<!-- Widget Template: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template:', 'cherry-testimonials' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'template' ); ?>" value="<?php echo $template; ?>" class="widefat" id="<?php echo $this->get_field_id( 'template' ); ?>" />
</p>
<!-- Widget Custom CSS Class: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'custom_class' ); ?>"><?php _e( 'Custom CSS Class:', 'cherry-testimonials' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'custom_class' ); ?>" value="<?php echo $custom_class; ?>" class="widefat" id="<?php echo $this->get_field_id( 'custom_class' ); ?>" />
</p>
<?php
	/**
	 * Fires after a widget form.
	 *
	 * @since 1.0.0
	 */
	do_action( 'cherry_testimonials_widget_form_after' );
