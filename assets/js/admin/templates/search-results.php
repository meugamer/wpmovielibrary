
			<# if ( true == data.paginated ) { #>
			<div id="wpmoly-meta-search-results-pagination">
				<# if ( data.page > 1 ) { #>
				<div class="wpmoly-meta-search-results-nav"><a id="wpmoly-meta-search-results-prev" href="#"><span class="wpmolicon icon-arrow-left"></span></a></div>
				<# } #>
				<div class="wpmoly-meta-search-results-text"><span>Page {{ data.page }} of {{ data.total }}</span></div>
				<# if ( data.page < data.total ) { #>
				<div class="wpmoly-meta-search-results-nav"><a id="wpmoly-meta-search-results-next" href="#"><span class="wpmolicon icon-arrow-right"></span></a></div>
				<# } #>
			</div>
			<# } #>
			<div id="wpmoly-meta-search-results-container">
			<# _.each( data.results, function( result ) { #>
				<div class="wpmoly-select-movie">
					<a id="wpmoly-select-movie-{{ result.id }}" href="#{{ result.id }}">
						<img src="{{ result.poster }}" alt="{{ result.title }}" />
						<em>{{ result.title }}</em> ({{ result.year }})
					</a>
				</div>
			<# } ); #>

				<a id="wpmoly-empty-select-results" href="#"><span class="wpmolicon icon-no-alt"></span></a>
			</div>
			<div id="wpmoly-meta-search-results-loading"><img src="<?php echo WPMOLY_URL . '/assets/img/grid.svg'; ?>" width="24" height="24" alt="" /></div>
