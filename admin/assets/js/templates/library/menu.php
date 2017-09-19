
			<ul>
				<li><a class="<# if ( 'latest' == data.mode ) { #>current<# } #>" data-action="library-mode" data-value="latest" href="#"><?php _e( 'Latest', 'wpmovielibrary' ); ?></a></li>
				<li><a class="<# if ( 'favorites' == data.mode ) { #>current<# } #>" data-action="library-mode" data-value="favorites" href="#"><?php _e( 'Favorites', 'wpmovielibrary' ); ?></a></li>
				<li><a class="<# if ( 'import' == data.mode ) { #>current<# } #>" data-action="library-mode" data-value="import" href="#"><?php _e( 'Import', 'wpmovielibrary' ); ?></a></li>
			</ul>
