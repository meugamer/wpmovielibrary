
					<# var settings = data.settings; #>
					<div class="wpmoly-meta-search-settings">
						<span class="setting-icon"><span class="wpmolicon icon-heart"></span></span>
						<span class="setting-text"><a id="wpmoly-search-adult" href="#"><span class="wpmolicon icon-<# if ( ! settings.adult ) { #>no-alt<# } else { #>yes<# } #>"></span>&nbsp; <?php _e( 'Include adult movies', 'wpmovielibrary' ); ?></a></span>
					</div>
					<div class="wpmoly-meta-search-settings">
						<span class="setting-icon"><span class="wpmolicon icon-ellipsis-h"></span></span>
						<span class="setting-text"><a id="wpmoly-search-paginate" href="#"><span class="wpmolicon icon-<# if ( ! settings.paginate ) { #>no-alt<# } else { #>yes<# } #>"></span>&nbsp; <?php _e( 'Enable paginated results', 'wpmovielibrary' ); ?></a></span>
					</div>
					<div class="wpmoly-meta-search-settings">
						<span class="setting-icon"><span class="wpmolicon icon-date"></span></span>
						<span class="setting-text"><label><?php _e( 'Search a specific year:', 'wpmovielibrary' ); ?>&nbsp; <input id="wpmoly-search-year" type="text" size="4" maxlength="4" value="<# if ( settings.year ) { #>{{ settings.year }}<# } #>" /></label></span>
					</div>
					<div class="wpmoly-meta-search-settings">
						<span class="setting-icon"><span class="wpmolicon icon-date"></span></span>
						<span class="setting-text"><label><?php _e( 'Search a specific primary year:', 'wpmovielibrary' ); ?>&nbsp; <input id="wpmoly-search-pyear" type="text" size="4" maxlength="4" value="<# if ( settings.pyear ) { #>{{ settings.pyear }}<# } #>" /></label></span>
					</div>
