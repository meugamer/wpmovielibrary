
				<div class="wpmoly-grid-pagination-container" style="display:none">
<# if ( data.total ) {
	if ( data.prev ) { #>
					<a class="grid-menu-action" data-action="prev" href="#" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></a>
<# 	} else { #>
					<a class="grid-menu-action disabled" disabled="disabled" href="#" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></a>
<# 	} #>
					<span>Page <input data-action="browse" type="text" size="3" value="{{ data.current }}"/> of {{ data.total }}</span>
<# 	if ( data.next && data.next <= data.total ) { #>
					<a class="grid-menu-action" data-action="next" href="#" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></a>
<# 	} else { #>
					<a class="grid-menu-action disabled" disabled="disabled" href="#" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></a>
<# 	}
 } else { #>
					
<# } #>
				</div>
