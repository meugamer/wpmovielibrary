
			<# console.log( data ); #>
			<div class="movie-preview-poster">
				<img src="{{ data.poster }}" alt="" />
			</div>
			<div class="movie-preview-content">
				<div class="movie-preview-header with-background" style="background-image:url({{ data.backdrop }})">
					<h2 class="movie-title">{{ data.title }} <span class="year">{{ data.year }}</span></h2>
					<p class="movie-genres">{{ data.genres }}</p>
				</div>
				<div class="movie-preview-details">
					<div class="movie-detail movie-status">
						<span class="movie-detail-label"><?php _e( 'Status', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{ data.status }}</span>
					</div>
					<div class="movie-detail movie-media">
						<span class="movie-detail-label"><?php _e( 'Media', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{ data.media }}</span>
					</div>
					<div class="movie-detail movie-rating">
						<span class="movie-detail-label"><?php _e( 'Rating', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{ data.rating }}</span>
					</div>
					<div class="movie-detail movie-format">
						<span class="movie-detail-label"><?php _e( 'Format', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{ data.format }}</span>
					</div>
					<div class="movie-detail movie-language">
						<span class="movie-detail-label"><?php _e( 'Language', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{ data.language }}</span>
					</div>
					<div class="movie-detail movie-subtitles">
						<span class="movie-detail-label"><?php _e( 'Subtitles', 'wpmovielibrary' ); ?></span>
						<span class="movie-detail-value">{{ data.subtitles }}</span>
					</div>
				</div>
				<div class="movie-preview-meta">
				</div>
			</div>
