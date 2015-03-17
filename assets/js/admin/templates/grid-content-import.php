
			<div id="importer-search-form">
				<div id="importer-search-container">
					<input id="importer-search-query" type="text" value="Pirates of the Caribbean" placeholder="<?php _e( 'Search a movie', 'wpmovielibrary' ); ?>" />
					<a id="importer-search" href="#"><span class="wpmolicon icon-search"></span></a>
					<a id="importer-settings" href="#"><span class="wpmolicon icon-settings"></span></a>
					<p id="importer-add" href="#"><?php _e( 'Or <a href="#">create a list</a> of movies to import', 'wpmovielibrary' ); ?></p>
				</div>
				<div id="importer-search-list-form">
					<div id="importer-search-list-container">
						<textarea id="importer-search-list"></textarea>
						<div id="importer-search-list-menu" class="importing">
							<a id="importer-search-list-add" href="#"><span class="wpmolicon icon-yes"></span></a><a id="importer-search-list-quit" href="#"><span class="wpmolicon icon-no-alt"></span></a>
							<div id="confirm-quit-container"><?php _e( 'Are you sure you want to do this?', 'wpmovielibrary' ); ?> <a href="#"><?php _e( 'Yes', 'wpmovielibrary' ); ?></a> &bull; <a href="#"><?php _e( 'No', 'wpmovielibrary' ); ?></a></div>
							<div id="importer-loading-bar"><div id="importer-loading"></div><div id="importer-loading-label">Creating movie 42 on 100…</div></div>
						</div>
					</div>
					<ul id="importer-search-list-draftees"></ul>
				</div>
			</div>

			<div id="importer-search-container">
				<div id="importer-search-settings">
				</div>

				<div id="importer-search-results">
					<ul class="attachments movies">
						<li class="attachment movie search-result">
							<div class="attachment-preview movie-preview js--select-attachment">
								<a class="movie-action center import-movie" href="#" title="<?php _e( 'Import Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-import"></span></a>
								<a class="movie-action top-right enqueue-movie" href="#" title="<?php _e( 'Enqueue Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-plus"></span></a>
								<div class="poster">
									<img src="https://image.tmdb.org/t/p/w185/jUkGuSC9Kt29rW3x6UiB9zyZr1M.jpg" alt="" />
								</div>
							</div>
							<div class="attachment-meta movie-meta">
								<div class="movie-year">2011</div>
								<div class="movie-title">Pirates of the Caribbean: On Stranger Tides</div>
							</div>
						</li>
						<li class="attachment movie search-result">
							<div class="attachment-preview movie-preview js--select-attachment">
								<a class="movie-action center import-movie" href="#" title="<?php _e( 'Import Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-import"></span></a>
								<a class="movie-action top-right enqueue-movie" href="#" title="<?php _e( 'Enqueue Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-plus"></span></a>
								<div class="poster">
									<img src="https://image.tmdb.org/t/p/w185/tkt9xR1kNX5R9rCebASKck44si2.jpg" alt="" />
								</div>
							</div>
							<div class="attachment-meta movie-meta">
								<div class="movie-year">2003</div>
								<div class="movie-title">Pirates of the Caribbean: The Curse of the Black Pearl</div>
							</div>
						</li>
						<li class="attachment movie search-result">
							<div class="attachment-preview movie-preview js--select-attachment">
								<a class="movie-action center import-movie" href="#" title="<?php _e( 'Import Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-import"></span></a>
								<a class="movie-action top-right enqueue-movie" href="#" title="<?php _e( 'Enqueue Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-plus"></span></a>
								<div class="poster">
									<img src="https://image.tmdb.org/t/p/w185/iwvyZBRD7qfDQ8ylRmf5NbLC5Oi.jpg" alt="" />
								</div>
							</div>
							<div class="attachment-meta movie-meta">
								<div class="movie-year">2006</div>
								<div class="movie-title">Pirates of the Caribbean: Dead Man's Chest</div>
							</div>
						</li>
						<li class="attachment movie search-result">
							<div class="attachment-preview movie-preview js--select-attachment">
								<a class="movie-action center import-movie" href="#" title="<?php _e( 'Import Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-import"></span></a>
								<a class="movie-action top-right enqueue-movie" href="#" title="<?php _e( 'Enqueue Movie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-plus"></span></a>
								<div class="poster">
									<img src="https://image.tmdb.org/t/p/w185/llx7OTBy7BvflJzoFSStmBcdegy.jpg" alt="" />
								</div>
							</div>
							<div class="attachment-meta movie-meta">
								<div class="movie-year">2007</div>
								<div class="movie-title">Pirates of the Caribbean: At World's End</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
