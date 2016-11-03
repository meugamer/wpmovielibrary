<?php
/**
 * Statistics Widget default template.
 * 
 * @since    3.0
 * 
 * @uses    $widget
 */

?>
	<div id="<?php echo esc_attr( $widget->id ); ?>" class="<?php echo esc_attr( $widget->classname ); ?>">
<?php if ( ! empty( $description ) ) : ?>
		<p><?php echo $description; ?></p>
<?php endif; ?>

		<?php echo $content; ?>
	</div>
