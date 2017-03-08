<?php
/**
 * Movie Headbox view Template
 * 
 * Showing a movie's default headbox.
 * 
 * @since    3.0
 * 
 * @uses    $movie
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>
	<div id="movie-headbox-<?php echo $movie->id; ?>" class="wpmoly headbox movie-headbox theme-extended">
		<div class="headbox-header">
			<div class="headbox-backdrop-container">
				<div class="headbox-backdrop" style="background-image:url(<?php echo $movie->get_backdrop( 'random' )->render( 'medium', 'raw' ); ?>);"></div>
				<div class="headbox-poster-shadow"></div>
				<div class="headbox-angle"></div>
			</div>
			<div class="headbox-poster" style="background-image:url(<?php echo $movie->get_poster()->render( 'medium', 'raw' ); ?>);"></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="<?php $movie->the_url(); ?>"><?php $movie->the_title(); ?></a></div>
				<div class="movie-original-title"><?php $movie->the_original_title(); ?></div>
				<div class="movie-tagline"><?php $movie->the_tagline(); ?></div>
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-menu">
				
			</div>
			<div class="headbox-metadata">
				<div class="movie-overview"><?php $movie->the( 'overview' ); ?></div>
			</div>
		</div>
	</div>

<?php else : ?>
	<div id="movie-headbox-{{ data.node.get( 'id' ) }}" class="wpmoly movie-headbox theme-extended">
		<div class="headbox-header">
			<div class="headbox-backdrop-container">
				<div class="headbox-backdrop" style="background-image:url({{ data.node.get( 'backdrop' ).sizes.medium.url }});"></div>
				<div class="headbox-angle"></div>
			</div>
			<div class="headbox-poster" style="background-image:url({{ data.node.get( 'poster' ).sizes.medium.url }});"></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'title' ).rendered }}</a></div>
				<div class="movie-original-title">{{ data.node.get( 'meta' ).get( 'original_title' ).rendered }}</div>
				<div class="movie-tagline">{{ data.node.get( 'meta' ).get( 'tagline' ).rendered }}</div>
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-menu">
				
			</div>
			<div class="headbox-metadata">
				<div class="movie-overview">{{ data.node.get( 'meta' ).get( 'overview' ).rendered }}</div>
			</div>
		</div>
	</div>
<?php endif; ?>