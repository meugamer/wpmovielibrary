
			<# _.each( data.attachments, function( attachment ) { #>
			<li class="wpmoly-poster wpmoly-imported-poster">
				<img width="{{ attachment.sizes.medium.width }}" height="{{ attachment.sizes.medium.height }}" src="{{ attachment.sizes.medium.url }}" class="attachment-medium" alt="{{ attachment.title }}" />
			</li>

			<# } ); #>

			<li class="wpmoly-poster"><a href="#" id="wpmoly-load-posters" title="<?php _e( 'Load Posters', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-plus"></span></a></li>
