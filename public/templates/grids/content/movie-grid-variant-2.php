<?php
/**
 * Variant-2 Movie Grid template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $content
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>
			<div class="grid-content-inner grid <?php echo $grid->get_columns(); ?>-columns theme-variant-2">
<?php
	if ( $items->has_items() ) :
		while ( $items->has_items() ) :
			$movie = $items->the_item();
?>
				<div class="node post-node movie">
					<div class="node-thumbnail node-poster post-poster movie-poster" style="background-image:url(<?php $movie->get_poster()->render( 'medium' ); ?>)">
						<a href="<?php $movie->the_link(); ?>" title="<?php echo $movie->get_title(); ?>"></a>
					</div>
					<div class="node-content clearfix">
						<div class="movie-title"><a href="<?php $movie->the_link(); ?>"><?php $movie->the_title(); ?></a></div>
						<div class="movie-year"><?php echo get_formatted_movie_year( $movie->get_year(), array( 'is_link' => false ) ); ?></div>
						<div class="movie-rating"><?php $movie->the_rating(); ?></div>
					</div>
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
					<a href="{{ data.node.get( 'link' ) }}" title="{{ data.node.get( 'title' ).raw }}"></a>
				</div>
				<div class="node-content clearfix">
					<div class="movie-title"><a href="{{ data.node.get( 'link' ) }}">{{{ data.node.get( 'title' ).rendered }}}</a></div>
					<div class="movie-year">{{{ data.node.get( 'meta' ).year.rendered }}}</div>
					<div class="movie-rating">{{{ data.node.get( 'meta' ).rating.raw }}}</div>
				</div>
<?php endif; ?>