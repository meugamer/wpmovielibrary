<# if ( ! data.scroll ) { #>
				<div class="wpmoly-grid-pagination-container">
<# 	if ( data.total ) {
		if ( data.prev ) { #>
					<a class="grid-menu-action" data-action="prev" href="#" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></a>
<# 		} else { #>
					<a class="grid-menu-action disabled" disabled="disabled" href="#" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></a>
<# 		} #>
					<span>Page <input data-action="browse" type="text" size="3" value="{{ data.current }}"/> of {{ data.total }}</span>
<#		if ( data.next && data.next <= data.total ) { #>
					<a class="grid-menu-action" data-action="next" href="#" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></a>
<# 		} else { #>
					<a class="grid-menu-action disabled" disabled="disabled" href="#" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></a>
<# 		} #>
				
<#	} #>
				</div>
<# } else if ( 1 != 1 ) { #>
					<a class="grid-menu-action" href="#" title=""><span class="wpmolicon icon-infinite"></span></a>

					<a class="grid-menu-action grid-pagination-settings-toggle" data-action="openmenu" href="#" title=""><span class="wpmolicon icon-settings"></span></a>
					<div class="grid-pagination-settings">
						<div class="grid-pagination-settings-section">
							<input type="hidden" data-value="scroll" value="<# if ( data.scroll ) { #>1<# } else { #>0<# } #>" />
							<span class="grid-menu-label"><?php _e( 'Infinite scrolling:', 'wpmovielibrary' ); ?></span> <a href="#" data-action="scroll" data-value="1" title="" <# if ( data.scroll ) { #>class="selected"<# } #>>Yes</a>&nbsp;&nbsp;&bull;&nbsp;&nbsp;<a href="#" data-action="scroll" data-value="0" title=""<# if ( ! data.scroll ) { #>class="selected"<# } #>>No</a>
						</div>
						<div class="grid-pagination-settings-section">
							<span class="grid-menu-label"><?php _e( 'Movies per page:', 'wpmovielibrary' ); ?></span> <input data-action="perpage" type="text" size="3" value="{{ data.perpage }}"/>
						</div>
						<div class="grid-pagination-settings-toolbar">
							<a data-action="applysettings" href="#" title=""><span class="wpmolicon icon-yes"></span></a>
						</div>
					</div>
<# } #>
