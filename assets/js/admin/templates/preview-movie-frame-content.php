
			<# console.log( data ); #>
			<div class="movie-preview-poster">
				<img src="{{ data.post.post_thumbnail.large }}" alt="" />
			</div>
			<div class="movie-preview-content">
				<# if ( data.post.images.length && '' != data.post.images[0].sizes.large ) { #>
				<div class="movie-preview-header with-background" style="background-image:url({{ data.post.images[0].sizes.large }})">
				<# } else { #>
				<div class="movie-preview-header with-background default-background" style="background-image:url({{ data.post.post_thumbnail.large }})">
				<# } #>
					<h2 class="movie-title">{{ data.meta.title }} <span class="year">{{ data.meta.year }}</span></h2>
					<p class="movie-genres">{{ data.meta.genres }}</p>
				</div>
				<div class="movie-preview-details">
				</div>
				<div class="movie-preview-meta">
				</div>
			</div>
