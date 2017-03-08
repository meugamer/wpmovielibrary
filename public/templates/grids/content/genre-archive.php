<?php
/**
 * Default Genre Archive template.
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
			$genre = $items->the_item();
?>
				<div class="node term-node genre">
<?php
					$headbox = get_genre_headbox_template( $genre );
					echo $headbox->render();
?>
				</div>

<?php
		endwhile;
	else :
?>
				<div class="empty-nodes">
					<p><em><?php _e( 'No genres found matching you request.', 'wpmovielibrary' ); ?></em></p>
				</div>
<?php
	endif;
?>
			</div>
<?php else : ?>

<?php
					$headbox = wpmoly_get_template( 'headboxes/genre-default.php' );
					echo $headbox->render();
?>

<?php endif; ?>