
			<# if ( true == data.paginated ) { #>
			<div id="wpmoly-meta-search-results-pagination" class="pagination">
				<# if ( data.page > 1 ) { #>
				<div class="wpmoly-meta-search-results-nav"><a id="wpmoly-meta-search-results-prev" href="#"><span class="wpmolicon icon-arrow-left"></span></a></div>
				<# } #>
				<div class="wpmoly-meta-search-results-text"><span>Page {{ data.page }} of {{ data.total }}</span></div>
				<# if ( data.page < data.total ) { #>
				<div class="wpmoly-meta-search-results-nav"><a id="wpmoly-meta-search-results-next" href="#"><span class="wpmolicon icon-arrow-right"></span></a></div>
				<# } #>
			</div>
			<# } #>
			<div id="wpmoly-meta-search-results-container" class="container">
			<# _.each( data.results, function( result ) { #>
				<div class="wpmoly-select-movie" data-id="{{ result.id }}">
					<div class="poster">
						<img src="{{ result.poster }}" alt="{{ result.title }}" />
					</div>
					<span class="movie-title">{{ result.title }}</span> ({{ result.year }})
				</div>
			<# } ); #>

				<a id="wpmoly-empty-select-results" href="#"><span class="wpmolicon icon-no-alt"></span></a>
			</div>
			<div id="wpmoly-meta-search-results-loading"><img src="<?php echo WPMOLY_URL . '/assets/img/grid.svg'; ?>" width="24" height="24" alt="" /></div>
