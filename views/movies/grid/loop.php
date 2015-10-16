
				<div id="wpmoly-movie-grid" class="wpmoly movies grid <?php echo $theme; ?><?php if ( $title || $year || $rating ) echo ' spaced'; ?>">
					<div class="grid-frame-menu"></div>
					<div class="grid-frame-content" data-columns="<?php echo $columns; ?>">

<?php
global $post;
if ( ! empty( $movies ) ) :
?>
						<ul class="attachments movies" id="grid-content-grid">

<?php
	foreach ( $movies as $post ) :
		setup_postdata( $post );

		$size = 'medium';
		if ( 1 == $columns )
			$size = 'large';

		$poster = '';
		if ( has_post_thumbnail() ) {
			$poster = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $size );
			if ( isset( $poster[0] ) ) {
				$poster = $poster[0];
			} else {
				$poster = '';
			}
		}

		$class = 'wpmoly movie';
		if ( $title )
			$class .= ' with-title';
		if ( $year )
			$class .= ' with-year';
		if ( $rating )
			$class .= ' with-rating';
?>

							<li class="attachment movie">
								<div id="wpmoly-movie-<?php the_ID(); ?>" <?php post_class( $class ) ?>>
									<div class="movie-preview">
										<a href="<?php the_permalink(); ?>" class="wpmoly grid movie link" title="<?php echo $title; ?>" href="#">
<?php if ( ! empty( $poster ) ) { ?>
											<img src="<?php echo $poster ?>" alt="" />
<?php } ?>
										</a>
									</div>
<?php if ( $title ) { ?>
									<a href="<?php the_permalink(); ?>" class="wpmoly grid movie link" title="<?php echo $title; ?>">
										<h4 class="wpmoly grid movie title"><?php echo apply_filters( 'wpmoly_format_movie_title', wpmoly_get_movie_meta( get_the_ID(), 'title' ) ); ?></h4>
									</a>
<?php } if ( $genre ) { ?>
									<span class="wpmoly grid movie genres"><?php echo apply_filters( 'wpmoly_format_movie_genres', wpmoly_get_movie_meta( get_the_ID(), 'genres' ) ) ?></span>
<?php } if ( $year ) { ?>
									<span class="wpmoly grid movie year"><?php echo apply_filters( 'wpmoly_format_movie_release_date', wpmoly_get_movie_meta( get_the_ID(), 'release_date' ), 'Y' ) ?></span>
<?php } if ( $runtime ) { ?>
									<span class="wpmoly grid movie runtime"><?php echo apply_filters( 'wpmoly_format_movie_runtime', wpmoly_get_movie_meta( get_the_ID(), 'runtime' ) ) ?></span>
<?php } if ( $rating ) { ?>
									<span class="wpmoly grid movie rating"><?php echo apply_filters( 'wpmoly_movie_rating_stars', wpmoly_get_movie_rating( get_the_ID() ) ); ?></span>
<?php } ?>
								</div>
							</li>

<?php
	endforeach;
	wp_reset_postdata();
?>
						</ul>
					</div>
					<div class="grid-frame-pagination"></div>
<?php
else :
?>
					<h5><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'wpmovielibrary' ); ?></h5>
					<p><?php _e( 'We could&rsquo;t find any movie matching your criteria.', 'wpmovielibrary' ); ?></p>
<?php endif; ?>

				</div>
