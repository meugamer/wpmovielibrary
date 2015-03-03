
			<# _.each( data.attachments, function( attachment ) { #>
			<li class="wpmoly-backdrop wpmoly-imported-backdrop">
				<img width="{{ attachment.sizes.medium.width }}" height="{{ attachment.sizes.medium.height }}" src="{{ attachment.sizes.medium.url }}" class="attachment-medium" alt="{{ attachment.title }}" />
			</li>

			<# } ); #>

			<li class="wpmoly-backdrop"><a href="#" id="wpmoly-load-backdrops" title="<?php _e( 'Load Images', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-plus"></span></a></li>
