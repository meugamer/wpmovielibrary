
		<div class="attachment-preview movie-preview js--select-attachment" style="<# if ( '' != data.size.height ) { #>height:{{ data.size.height }}px;<# } if ( '' != data.size.height ) { #>width:{{ data.size.width }}px<# } #>">
			<a class="movie-action center preview-movie" data-id="{{ data.post.post_id }}" href="#" title="<?php _e( 'View Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-movie"></span></a>
			<a class="movie-action top-right edit-movie" data-id="{{ data.post.post_id }}" href="<?php echo admin_url( 'post.php?post={{ data.post.post_id }}&action=edit' ); ?>" title="<?php _e( 'Edit Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-edit"></span></a>
			<div class="movie-action bottom-left movie-status"><# if ( '' != data.details.status ) { #><span title="{{ wpmoly.l10n.misc[ data.details.status ] }}" class="wpmolicon icon-{{ data.details.status }}"></span><# } #></div>
			<div class="movie-action bottom-right movie-rating"><span title="{{ data.details.rating }}" class="wpmolicon icon-star-{{ data.details.star }}"></span></div>
			<div class="poster">
				<img src="{{ data.post.post_thumbnail.medium }}" alt="{{ data.post.post_title }}" />
			</div>
		</div>
		<div class="attachment-meta movie-meta">
			<div class="movie-year">{{ data.meta.year }}</div>
			<div class="movie-title">{{ data.meta.title }}</div>
			<div class="movie-genres">{{ data.meta.genres }}</div>
			<div class="movie-runtime"><# if ( '' != data.meta.runtime ) { #>{{ data.meta.runtime }}min<# } #></div>
		</div>
