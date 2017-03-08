<?php
/**
 * Default Movie Grid template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $content
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>
			<div class="grid-content-inner grid <?php echo $grid->get_columns(); ?>-columns">
<?php
	if ( $items->has_items() ) :
		while ( $items->has_items() ) :
			$movie = $items->the_item();
?>
				<div class="node post-node movie">
					<div class="node-thumbnail node-poster post-poster movie-poster" style="background-image:url(<?php $movie->get_poster()->render( 'medium' ); ?>)">
						<a href="<?php echo get_the_permalink( $movie->id ); ?>"></a>
					</div>
					<div class="node-title post-title movie-title"><a href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->the( 'title' ); ?></a></div>
					<div class="node-genres post-genres movie-genres"><?php echo get_formatted_movie_genres( $movie->get( 'genres' ) ); ?></div>
					<div class="node-runtime post-runtime movie-runtime"><?php echo get_formatted_movie_runtime( $movie->get( 'runtime' ) ); ?></div>
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

				<div class="node-thumbnail node-poster post-poster movie-poster" style="background-image:url({{ data.node.get( 'poster' ).sizes.medium.url }})">
					<a href="{{ data.node.get( 'link' ) }}"></a>
				</div>
				<div class="node-title post-title movie-title"><a href="{{ data.node.get( 'link' ) }}">{{{ data.node.get( 'title' ).rendered }}}</a></div>
				<div class="node-genres post-genres movie-genres">{{{ data.node.get( 'meta' ).get( 'genres' ).rendered }}}</div>
				<div class="node-runtime post-runtime movie-runtime">{{ data.node.get( 'meta' ).get( 'runtime' ).rendered }}</div>
<?php endif; ?>