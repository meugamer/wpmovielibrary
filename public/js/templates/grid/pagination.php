
<# if ( 1 < data.current ) { #>
			<button type="button" data-action="grid-navigate" data-value="prev" class="button left" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></button>
<# } else { #>
			<button type="button" disabled="disabled" class="button left"><span class="wpmolicon icon-arrow-left"></span></button>
<# } #>
			<div class="pagination-menu"><span class="page-label"><?php _e( 'Page', 'wpmovielibrary' ); ?></span><span class="current-page"><# if ( 1 < data.total ) { #><input type="text" size="1" data-action="grid-paginate" value="{{ data.current }}" <# if ( data.preview ) { #>readonly="true" <# } #>/><# } else { #>{{ data.current }}<# } #></span><span class="of-label"><?php _e( 'of', 'wpmovielibrary' ); ?></span><span class="total-pages">{{ data.total }}</span></div>
<# if ( data.current < data.total ) { #>
			<button type="button" data-action="grid-navigate" data-value="next" class="button right" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></button>
<# } else { #>
			<button type="button" disabled="disabled" class="button right"><span class="wpmolicon icon-arrow-right"></span></button>
<# } #>
