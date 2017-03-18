
<# if ( ! data.preview && 1 < data.state.get( 'currentPage' ) ) { #>
			<button type="button" data-action="grid-navigate" data-value="prev" class="button left" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></button>
<# } else { #>
			<button type="button" disabled="disabled" class="button left"><span class="wpmolicon icon-arrow-left"></span></button>
<# } #>
			<div class="pagination-menu"><?php _e( 'Page', 'wpmovielibrary' ); ?> <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="{{ data.state.get( 'currentPage' ) }}" <# if ( data.preview ) { #>readonly="true" <# } #>/></span> <?php _e( 'of', 'wpmovielibrary' ); ?> <span class="total-pages">{{ data.state.get( 'totalPages' ) }}</span></div>
<# if ( ! data.preview && data.state.get( 'currentPage' ) < data.state.get( 'totalPages' ) ) { #>
			<button type="button" data-action="grid-navigate" data-value="next" class="button right" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></button>
<# } else { #>
			<button type="button" disabled="disabled" class="button right"><span class="wpmolicon icon-arrow-right"></span></button>
<# } #>
