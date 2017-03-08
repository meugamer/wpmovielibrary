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
	<div id="movie-headbox-<?php echo $headbox->id; ?>" class="wpmoly headbox post-headbox movie-headbox theme-default">
		<div class="headbox-header">
			<div class="headbox-backdrop-container">
				<div class="headbox-backdrop" style="background-image:url(<?php echo $movie->get_backdrop( 'random' )->render( 'medium', 'raw' ); ?>);"></div>
				<div class="headbox-poster-shadow"></div>
				<div class="headbox-angle"></div>
			</div>
			<div class="headbox-poster" style="background-image:url(<?php echo $movie->get_poster()->render( 'medium', 'raw' ); ?>);"></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="<?php the_permalink( $movie->id ); ?>"><?php $movie->the_title(); ?></a></div>
				<div class="movie-original-title"><?php $movie->the_original_title(); ?></div>
				<div class="movie-tagline"><?php $movie->the_tagline(); ?></div>
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-cast">
				<div class="movie-director"><?php _e( 'Directed by', 'wpmovielibrary' ); ?> <?php $movie->the_director(); ?></div>
				<div class="movie-actors"><?php _e( 'Staring', 'wpmovielibrary' ); ?> <?php $movie->the_actors(); ?></div>
			</div>
			<div class="headbox-metadata">
				<div class="movie headbox-release-info">
<?php if ( $movie->get( 'year' ) ) { ?>
					<span class="movie-year"><?php $movie->the_year(); ?></span>
<?php } if ( $movie->get( 'runtime' ) ) { ?>
					<span class="movie-runtime"><?php $movie->the_runtime(); ?></span>
<?php } if ( $movie->get( 'genres' ) ) { ?>
					<span class="movie-genres"><?php $movie->the_genres(); ?></span>
<?php } if ( $movie->get( 'certification' ) ) { ?>
					<span class="movie-certification"><?php $movie->the_certification(); ?></span>
<?php } ?>
				</div>
				<div class="movie-overview"><?php $movie->the_overview(); ?></div>
			</div>
		</div>
		<div class="headbox-more"><button data-action="expand"><span class="wpmolicon icon-arrow-down"></span></button></div>
		<div class="headbox-less"><button data-action="collapse"><span class="wpmolicon icon-arrow-up"></span></button></div>
	</div>

<?php else : ?>

	<div id="movie-headbox-{{ data.node.get( 'id' ) }}" class="wpmoly headbox post-headbox movie-headbox theme-default">
		<div class="headbox-header">
			<div class="headbox-backdrop-container">
				<div class="headbox-backdrop" style="background-image:url({{ data.node.get( 'backdrop' ).sizes.medium.url }});"></div>
				<div class="headbox-poster-shadow"></div>
				<div class="headbox-angle"></div>
			</div>
			<div class="headbox-poster" style="background-image:url({{ data.node.get( 'poster' ).sizes.medium.url }});"></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'meta' ).get( 'title' ).rendered }}</a></div>
				<div class="movie-original-title">{{ data.node.get( 'meta' ).get( 'original_title' ).rendered }}</div>
				<div class="movie-tagline">{{ data.node.get( 'meta' ).get( 'tagline' ).rendered }}</div>
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-cast">
				<div class="movie-director"><?php _e( 'Directed by', 'wpmovielibrary' ); ?> {{{ data.node.get( 'meta' ).get( 'director' ).rendered }}}</div>
				<div class="movie-actors"><?php _e( 'Staring', 'wpmovielibrary' ); ?> {{{ data.node.get( 'meta' ).get( 'cast' ).rendered }}}</div>
			</div>
			<div class="headbox-metadata">
				<div class="movie headbox-release-info">
<# /*if ( ! _.isEmpty(  ) ) { #>
					<span class="movie-year"><?php //$movie->the_year(); ?></span>
<# }*/ if ( ! _.isEmpty( data.node.get( 'meta' ).get( 'runtime' ).rendered ) ) { #>
					<span class="movie-runtime">{{ data.node.get( 'meta' ).get( 'runtime' ).rendered }}</span>
<# } if ( ! _.isEmpty( data.node.get( 'meta' ).get( 'genres' ).rendered ) ) { #>
					<span class="movie-genres">{{{ data.node.get( 'meta' ).get( 'genres' ).rendered }}}</span>
<# } if ( ! _.isEmpty( data.node.get( 'meta' ).get( 'certification' ).rendered ) ) { #>
					<span class="movie-certification">{{{ data.node.get( 'meta' ).get( 'certification' ).rendered }}}</span>
<# } #>
				</div>
				<div class="movie-overview">{{ data.node.get( 'meta' ).get( 'overview' ).rendered }}</div>
			</div>
		</div>
		<div class="headbox-more"><button data-action="expand"><span class="wpmolicon icon-arrow-down"></span></button></div>
		<div class="headbox-less"><button data-action="collapse"><span class="wpmolicon icon-arrow-up"></span></button></div>
	</div>
<?php endif; ?>