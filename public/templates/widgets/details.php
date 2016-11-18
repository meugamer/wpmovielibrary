<?php
/**
 * Statistics Widget default template.
 * 
 * @since    3.0
 * 
 * @uses    $widget WP_Widget instance.
 * @uses    $type Detail type.
 * @uses    $details Detail array.
 * @uses    $data Widget data.
 */

?>

		<section id="<?php echo esc_attr( $widget->id ); ?>" class="<?php echo esc_attr( $widget->classname ); ?>">
<?php if ( ! empty( $data['title'] ) ) : ?>
			<?php echo $data['title']; ?>
<?php endif; ?>
<?php if ( ! empty( $data['description'] ) ) : ?>
			<p><?php echo $data['description']; ?></p>
<?php endif; ?>

<?php if ( $data['is_list'] ) : ?>
			<select>
				<option value=""><?php echo esc_html__( 'Select detail…', 'wpmovielibrary' ); ?></option>
<?php foreach ( $detail['options'] as $slug => $title ) : ?>
				<option value="<?php echo esc_html( $slug ); ?>"><?php echo esc_html__( $title, 'wpmovielibrary' ); ?></option>
<?php endforeach; ?>
			</select>
<?php else : ?>
			<ul class="">
<?php foreach ( $data['details'] as $slug => $detail ) : ?>
				<li><?php echo $detail; ?></li>
<?php endforeach; ?>
			</ul>
<?php endif; ?>
		</section>
