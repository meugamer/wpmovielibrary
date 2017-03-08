<?php
/**
 * Default Movie List template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $content
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>
			<ul class="grid-content-inner list nodes-list nodes-<?php echo $grid->get_columns(); ?>-columns">
<?php
	if ( $items->has_items() ) :
		while ( $items->has_items() ) :
			$movie = $items->the_item();
?>

				<li class="node post-node movie">
					<div class="node-title movie-title"><a href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->the( 'title' ); ?></a></div>
					<div class="node-count movie-count"><span class="movie-runtime"><?php echo get_formatted_movie_runtime( $movie->get( 'runtime' ) ); ?></span>&nbsp;−&nbsp;<span class="movie-genres"><?php echo get_formatted_movie_genres( $movie->get( 'genres' ) ); ?></span></div>
				</li>
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
			</ul>
<?php else : ?>

					<div class="node-title movie-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'meta' ).get( 'title' ).rendered }}</a></div>
					<div class="node-count movie-count"><span class="movie-runtime">{{ data.node.get( 'meta' ).get( 'runtime' ).rendered }}</span>&nbsp;−&nbsp;<span class="movie-genres">{{ data.node.get( 'meta' ).get( 'genres' ).raw }}</span></div>
<?php endif; ?>