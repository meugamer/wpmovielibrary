

					<div id="wpmoly-movie-meta" class="wpmoly-meta">
<?php 
foreach ( $metas as $slug => $meta ) :
	$value = '';
	if ( ! $empty && isset( $metadata[ $slug ] ) )
		$value = apply_filters( 'wpmoly_stringify_array', $metadata[ $slug ] );
?>
						<div class="wpmoly-meta-edit wpmoly-meta-edit-<?php echo $slug; ?> <?php echo $meta['size'] ?>">
							<div class="wpmoly-meta-label">
								<label for="meta_data_<?php echo $slug; ?>"><?php _e( $meta['title'], 'wpmovielibrary' ) ?></label>
							</div>
							<div class="wpmoly-meta-value">
<?php if ( 'textarea' == $meta['type'] ) : ?>
								<textarea id="meta_data_<?php echo $slug; ?>" name="meta[<?php echo $slug; ?>]" class="meta-data-field" rows="6"><?php echo $value ?></textarea>
<?php elseif ( in_array( $meta['type'], array( 'text', 'hidden' ) ) ) : ?>
								<input type="<?php echo $meta['type']; ?>" id="meta_data_<?php echo $slug; ?>" name="meta[<?php echo $slug; ?>]" class="meta-data-field" value="<?php echo $value ?>" />
<?php endif; ?>
							</div>
						</div>
<?php endforeach; ?>

					</div>
