<?php
/**
 * Default Movie Archive template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $content
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>
			<div class="grid-content-inner archive">
<?php
	if ( $items->has_items() ) :
		while ( $items->has_items() ) :
			$movie = $items->the_item();
?>
				<div class="node post-node movie">
<?php
					$headbox = get_genre_headbox_template( $movie );
					echo $headbox->render();
?>
				</div>

<?php
		endwhile;
	else :
?>
				<div class="empty-nodes">
					<p><em><?php _e( 'No movies found matching you request.', 'wpmovielibrary' ); ?></em></p>
				</div>
<?php
	endif;
?>
			</div>
<?php else : ?>

<?php
					$headbox = wpmoly_get_template( 'headboxes/movie-default.php' );
					echo $headbox->render();
?>

<?php endif; ?>