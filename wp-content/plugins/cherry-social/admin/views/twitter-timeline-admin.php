<?php
/**
 * Fires before a widget form.
 *
 * @package Cherry_Testimonials_Admin
 * @since   1.0.0
 */
do_action( 'cherry_twitter_timeline_widget_form_before' );
?>

<!-- Widget Title: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
</p>
<!-- Widget ID: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'widget_ID' ); ?>"><?php _e( 'Widget ID:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'widget_ID' ); ?>" value="<?php echo $tw_widget_id; ?>" class="widefat" id="<?php echo $this->get_field_id( 'widget_ID' ); ?>" /><br>
	<?php $link = sprintf( __( "<a href='%s' target='_blank'>page</a>", 'cherry-social' ), esc_url( 'https://twitter.com/settings/widgets/new' ) ); ?>
	<small><?php printf( __( "Your widget ID. First of all you need  to make a new widget on %s. After that copy and paste widget ID here. You can find it after creating the widget in browser's URL field. It contains only numbers.", 'cherry-social' ), $link ); ?></small>
</p>
<!-- Widget Height: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height (px):', 'cherry-social' ); ?></label>
	<input type="number" min="250" max="2000" step="10" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $height; ?>" class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" placeholder="<?php sprintf( _e( 'Default (%spx)', 'cherry-social' ), $height ); ?>" /><br>
</p>
<!-- Widget Limit: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Tweet limit:', 'cherry-social' ); ?></label>
	<input type="number" min="1" max="20" step="1" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $limit; ?>" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" /><br>
	<small><?php _e( 'Value between 1 and 20 Tweets', 'cherry-social' ); ?></small>
</p>
<!-- Widget Skin: Select Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'skin' ); ?>"><?php _e( 'Skin:', 'cherry-social' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'skin' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'skin' ); ?>">
	<?php foreach ( $skin as $k => $v ) { ?>
		<option value="<?php echo $k; ?>"<?php selected( $instance['skin'], $k ); ?>><?php echo $v; ?></option>
	<?php } ?>
	</select>
</p>
<!-- Widget Link Color: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'link_color' ); ?>"><?php _e( 'Link Color (#):', 'cherry-social' ); ?></label>
	<input type="text" maxlength="6" name="<?php echo $this->get_field_name( 'link_color' ); ?>" value="<?php echo $link_color; ?>" class="widefat" id="<?php echo $this->get_field_id( 'link_color' ); ?>" /><br>
	<small><?php _e( 'e.g. 89c9fa', 'cherry-social' ); ?></small>
</p>
<!-- Widget Border Color: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'border_color' ); ?>"><?php _e( 'Border Color (#):', 'cherry-social' ); ?></label>
	<input type="text" maxlength="6" name="<?php echo $this->get_field_name( 'border_color' ); ?>" value="<?php echo $border_color; ?>" class="widefat" id="<?php echo $this->get_field_id( 'border_color' ); ?>" /><br>
	<small><?php _e( 'e.g. 89c9fa', 'cherry-social' ); ?></small>
</p>
<!-- Widget Header: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'noheader' ); ?>" name="<?php echo $this->get_field_name( 'noheader' ); ?>" type="checkbox" <?php checked( $noheader ); ?>>
	<label for="<?php echo $this->get_field_id( 'noheader' ); ?>"><?php _e( 'Hide header?', 'cherry-social' ); ?></label>
</p>
<!-- Widget Footer: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'nofooter' ); ?>" name="<?php echo $this->get_field_name( 'nofooter' ); ?>" type="checkbox" <?php checked( $nofooter ); ?>>
	<label for="<?php echo $this->get_field_id( 'nofooter' ); ?>"><?php _e( 'Hide footer?', 'cherry-social' ); ?></label>
</p>
<!-- Widget Borders: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'noborders' ); ?>" name="<?php echo $this->get_field_name( 'noborders' ); ?>" type="checkbox" <?php checked( $noborders ); ?>>
	<label for="<?php echo $this->get_field_id( 'noborders' ); ?>"><?php _e( 'Hide borders?', 'cherry-social' ); ?></label>
</p>
<!-- Widget Scrollbar: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'noscrollbar' ); ?>" name="<?php echo $this->get_field_name( 'noscrollbar' ); ?>" type="checkbox" <?php checked( $noscrollbar ); ?>>
	<label for="<?php echo $this->get_field_id( 'noscrollbar' ); ?>"><?php _e( 'Hide scrollbar?', 'cherry-social' ); ?></label>
</p>
<!-- Widget Background: Checkbox -->
<p>
	<input id="<?php echo $this->get_field_id( 'transparent' ); ?>" name="<?php echo $this->get_field_name( 'transparent' ); ?>" type="checkbox" <?php checked( $transparent ); ?>>
	<label for="<?php echo $this->get_field_id( 'transparent' ); ?>"><?php _e( 'Hide background?', 'cherry-social' ); ?></label>
</p>
<?php
/**
 * Fires after a widget form.
 *
 * @since 1.0.0
 */
do_action( 'cherry_twitter_timeline_widget_form_after' );
