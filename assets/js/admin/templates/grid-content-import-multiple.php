
			<div id="importer-search-form">
				<div id="importer-search-list-form">
					<p><?php _e( 'Type in a list of movies to search and import, separated by comma. Titles do not need to be exact, but try to be specific to get better results. Ex: interview with the vampire, Se7en, Twelve Monkeys, joe black, fight club, snatch, babel, inglourious basterds…', 'wpmovielibrary' ); ?></p>
					<p><?php printf( __( 'This list is automatically saved so you can edit it frequently without losing any title. You can %s the list or %s right now if you like.', 'wpmovielibrary' ), sprintf( '<a id="importer-search-list-reload" href="#">%s</a>', __( 'reload', 'wpmovielibrary' ) ), sprintf( '<a id="importer-search-list-save" href="#">%s</a>', __( 'save it', 'wpmovielibrary' ) ) ); ?></p>
					<div id="importer-search-list-container">
						<textarea id="importer-search-list"></textarea>
						<div id="importer-search-list-menu" class="menu">
							<a id="importer-search-list-add" href="#"><span class="wpmolicon icon-yes"></span></a><a id="importer-search-list-quit" href="#"><span class="wpmolicon icon-no-alt"></span></a>
							<div id="confirm-quit-container"><?php _e( 'Are you sure you want to do this?', 'wpmovielibrary' ); ?> <a href="#"><?php _e( 'Yes', 'wpmovielibrary' ); ?></a> &bull; <a href="#"><?php _e( 'No', 'wpmovielibrary' ); ?></a></div>
							<div id="importer-loading-bar"><div id="importer-loading"></div><div id="importer-loading-label">Creating movie 42 on 100…</div></div>
						</div>
					</div>
					<div id="importer-search-list-draftees-container">
						<ul id="importer-search-list-draftees"></ul>
					</div>
				</div>
			</div>

			<div id="importer-search-container">
				<div id="importer-search-settings"></div>
			</div>
