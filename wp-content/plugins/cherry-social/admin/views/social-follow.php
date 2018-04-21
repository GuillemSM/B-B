<?php
/**
 * Fires before a widget form.
 *
 * @package Cherry_Testimonials_Admin
 * @since   1.0.0
 */
do_action( 'cherry_social_follow_widget_form_before' );
?>

<!-- Widget Title: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
</p>

<fieldset>
	<legend><?php _e( 'Networks:', 'cherry-social' ); ?></legend>

	<?php foreach ( $this->follow_items as $id => $name ) { ?>
	<p>
		<input id="<?php echo $this->get_field_id( $id ); ?>" name="<?php echo $this->get_field_name( $id ); ?>" type="checkbox" <?php checked( isset( $instance[ $id ] ) ? (bool) $instance[ $id ] : 0 ); ?>>
		<label for="<?php echo $this->get_field_id( $id ); ?>"><?php echo esc_html( $name ); ?></label>
	</p>
<?php } ?>

</fieldset>

<!-- Widget Custom CSS Class: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'custom_class' ); ?>"><?php _e( 'Custom CSS Class:', 'cherry-social' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'custom_class' ); ?>" value="<?php echo $custom_class; ?>" class="widefat" id="<?php echo $this->get_field_id( 'custom_class' ); ?>" />
</p>
<?php
/**
 * Fires after a widget form.
 *
 * @since 1.0.0
 */
do_action( 'cherry_social_follow_widget_form_after' );
