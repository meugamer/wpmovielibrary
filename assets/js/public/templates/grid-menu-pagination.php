<# if ( data.total ) {
	if ( data.prev ) { #>
					<a class="grid-menu-action" data-action="browse" data-value="prev" href="#" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></a>
<# 	} else { #>
					<a class="grid-menu-action disabled" disabled="disabled" href="#" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></a>
<# 	} #>
					<span>Page <input data-action="browse" type="text" size="3" value="{{ data.paged }}"/> of {{ data.total }}</span>
<#	if ( data.next ) { #>
					<a class="grid-menu-action" data-action="browse" data-value="next" href="#" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></a>
<# 	} else { #>
					<a class="grid-menu-action disabled" disabled="disabled" href="#" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></a>
<# 	} #>
				
<# } #>