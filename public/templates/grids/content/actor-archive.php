<?php
/**
 * Default Actor Archive template.
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
			$actor = $items->the_item();
?>
				<div class="node term-node actor">
<?php
					$headbox = get_genre_headbox_template( $actor );
					echo $headbox->render();
?>
				</div>

<?php
		endwhile;
	else :
?>
				<div class="empty-nodes">
					<p><em><?php _e( 'No actors found matching you request.', 'wpmovielibrary' ); ?></em></p>
				</div>
<?php
	endif;
?>
			</div>
<?php else : ?>

<?php
					$headbox = wpmoly_get_template( 'headboxes/actor-default.php' );
					echo $headbox->render();
?>

<?php endif; ?>