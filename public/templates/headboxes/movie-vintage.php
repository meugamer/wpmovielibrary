<?php
/**
 * Movie Headbox view Template
 * 
 * Showing a movie's vintage headbox.
 * 
 * @since    3.0
 * 
 * @uses    $movie
 * @uses    $headbox
 */

?>
	<div id="movie-headbox-<?php echo $headbox->id; ?>" class="wpmoly headbox post-headbox movie-headbox theme-vintage" style="background-image:url(<?php $movie->get_poster()->render( 'large', 'raw' ); ?>)">
		<div class="headbox-header">
			<div class="headbox-rating"><span class="wpmolicon icon-star-filled"></span><?php $movie->the_rating(); ?></div>
			<div class="headbox-titles">
				<div class="movie-title"><a href="<?php the_permalink( $movie->id ); ?>"><?php $movie->the_title(); ?></a></div>
				<div class="movie-tagline"><?php $movie->the_tagline(); ?></div>
			</div>
			<div class="headbox-release-info">
<?php if ( $movie->get( 'year' ) ) { ?>
				<span class="movie-year"><?php $movie->the_year(); ?></span>
<?php } if ( $movie->get( 'runtime' ) ) { ?>
				<span class="movie-runtime"><?php $movie->the_runtime(); ?></span>
<?php } if ( $movie->get( 'certification' ) ) { ?>
				<span class="movie-certification"><?php $movie->the_certification(); ?></span>
<?php } ?>
			</div>
			<div class="headbox-menu">
				<ul>
					<li class="active"><a href="#"><span class="wpmolicon icon-overview"></span></a></li>
					<li><a href="#"><span class="wpmolicon icon-meta"></span></a></li>
					<li><a href="#"><span class="wpmolicon icon-details"></span></a></li>
					<li><a href="#"><span class="wpmolicon icon-images"></span></a></li>
					<li><a href="#"><span class="wpmolicon icon-actor"></span></a></li>
				</ul>
			</div>
		</div>
		<div class="headbox-content">
			<div class="headbox-tab overview active">
				<span class="wpmolicon icon-overview"></span><?php $movie->the_overview(); ?>
			</div>
			<div class="headbox-tab meta">
				<div class="meta field director">
					<span class="meta field title"><?php _e( 'Director', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_director(); ?></span>
				</div>
				<div class="meta field producer">
					<span class="meta field title"><?php _e( 'Producer', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_producer(); ?></span>
				</div>
				<div class="meta field photography">
					<span class="meta field title"><?php _e( 'Director of photography', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_photography(); ?></span>
				</div>
				<div class="meta field composer">
					<span class="meta field title"><?php _e( 'Original music composer', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_composer(); ?></span>
				</div>
				<div class="meta field writer">
					<span class="meta field title"><?php _e( 'Writer', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_writer(); ?></span>
				</div>
				<div class="meta field author">
					<span class="meta field title"><?php _e( 'Author', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_author(); ?></span>
				</div>
				<div class="meta field companies">
					<span class="meta field title"><?php _e( 'Production companies', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_production_companies(); ?></span>
				</div>
				<div class="meta field countries">
					<span class="meta field title"><?php _e( 'Production countries', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_production_countries(); ?></span>
				</div>
				<div class="meta field languages">
					<span class="meta field title"><?php _e( 'Spoken languages', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_spoken_languages(); ?></span>
				</div>
				<div class="meta field adult">
					<span class="meta field title"><?php _e( 'Adult movie', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_adult(); ?></span>
				</div>
			</div>
			<div class="headbox-tab details">
				<div class="meta detail field format">
					<span class="meta field title"><?php _e( 'Format', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_format(); ?></span>
				</div>
				<div class="meta field media">
					<span class="meta field title"><?php _e( 'Media', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_media(); ?></span>
				</div>
				<div class="meta field status">
					<span class="meta field title"><?php _e( 'Status', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_status(); ?></span>
				</div>
				<div class="meta field rating">
					<span class="meta field title"><?php _e( 'Rating', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_rating(); ?></span>
				</div>
				<div class="meta field language">
					<span class="meta field title"><?php _e( 'Language', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_language(); ?></span>
				</div>
				<div class="meta field subtitles">
					<span class="meta field title"><?php _e( 'Subtitles', 'wpmovielibrary' ); ?></span>
					<span class="meta field value"><?php $movie->the_subtitles(); ?></span>
				</div>
			</div>
			<div class="headbox-tab images">
				<div class="movie-backdrops clearfix">
				<?php
					$backdrops = $movie->get_backdrops();
					while ( $backdrops->has_items() ) :
						$backdrop = $backdrops->the_item();
					?>
					<div class="movie-backdrop"><a href="<?php $backdrop->render( 'original', 'raw' ); ?>"><?php $backdrop->render( 'thumbnail', 'html' ); ?></a></div>
				<?php
					endwhile;
				?>
				</div>
				<div class="movie-posters clearfix">
				<?php
					$posters = $movie->get_posters();
					while ( $posters->has_items() ) :
						$poster = $posters->the_item();
				?>
					<div class="movie-poster"><a href="<?php $poster->render( 'original', 'raw' ); ?>"><?php $poster->render( 'thumbnail', 'html' ); ?></a></div>
				<?php
					endwhile;
				?>
				</div>
			</div>
			<div class="headbox-tab actors"><?php $movie->the_actors(); ?></div>
		</div>
	</div>
