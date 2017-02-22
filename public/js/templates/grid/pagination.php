
<# if ( 1 < data.state.get( 'currentPage' ) ) { #>
			<button data-action="grid-navigate" data-value="prev" class="button left" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></button>
<# } else { #>
			<button class="button left disabled"><span class="wpmolicon icon-arrow-left"></span></button>
<# } #>
			<div class="pagination-menu"><?php _e( 'Page', 'wpmovielibrary' ); ?> <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="{{ data.state.get( 'currentPage' ) }}" /></span> <?php _e( 'of', 'wpmovielibrary' ); ?> <span class="total-pages">{{ data.state.get( 'totalPages' ) }}</span></div>
<# if ( data.state.get( 'currentPage' ) < data.state.get( 'totalPages' ) ) { #>
			<button data-action="grid-navigate" data-value="next" class="button right" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></button>
<# } else { #>
			<button class="button right disabled"><span class="wpmolicon icon-arrow-right"></span></button>
<# } #>
