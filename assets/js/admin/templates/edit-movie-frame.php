
		<div class="edit-media-header edit-movie-header">
			<button class="switch-mode preview dashicons" data-state="preview-movie"><span class="screen-reader-text"><?php _e( 'Preview movie' ); ?></span></button>
			<button class="switch-mode edit dashicons" data-state="edit-movie"><span class="screen-reader-text"><?php _e( 'Edit movie' ); ?></span></button>
			<button class="left dashicons <# if ( ! data.hasPrevious ) { #> disabled <# } #>"><span class="screen-reader-text"><?php _e( 'Edit previous movie' ); ?></span></button>
			<button class="right dashicons <# if ( ! data.hasNext ) { #> disabled <# } #>"><span class="screen-reader-text"><?php _e( 'Edit next movie' ); ?></span></button>
		</div>
		<div class="media-frame-title movie-frame-title"></div>
		<div class="media-frame-content movie-frame-content"></div>
