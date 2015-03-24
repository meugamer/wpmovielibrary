
			<div class="movie-menu wp-filter">
				<div class="media-toolbar-secondary">
					<div class="view-switch movie-grid-view-switch">
						<a data-mode="grid" title="<?php _e( 'Grid View' ); ?>" href="<?php echo admin_url( 'edit.php?post_type=movie' ); ?>" class="movie-grid-grid <# if ( 'grid' == data ) { #>current<# } #>">
							<span class="dashicons dashicons-grid-view"></span><span class="screen-reader-text"><?php _e( 'Grid View' ); ?></span>
						</a><a data-mode="exerpt" title="<?php _e( 'Extended Grid View', 'wpmovielibrary' ); ?>" href="<?php echo admin_url( 'edit.php?post_type=movie&mode=exerpt' ); ?>" class="movie-grid-exerpt <# if ( 'exerpt' == data ) { #>current<# } #>">
							<span class="dashicons dashicons-exerpt-view"></span><span class="screen-reader-text"><?php _e( 'Extended Grid View', 'wpmovielibrary' ); ?></span>
						</a><a data-mode="list" title="<?php _e( 'List View' ); ?>" href="<?php echo admin_url( 'edit.php?post_type=movie&mode=list' ); ?>" class="movie-grid-list <# if ( 'list' == data ) { #>current<# } #>">
							<span class="dashicons dashicons-list-view"></span><span class="screen-reader-text"><?php _e( 'List View' ); ?></span>
						</a><a data-mode="import" title="<?php _e( 'Importer', 'wpmovielibrary' ); ?>" href="<?php echo admin_url( 'edit.php?post_type=movie&mode=import' ); ?>" class="movie-grid-import <# if ( 'import' == data ) { #>current<# } #>">
							<span class="wpmolicon icon-import"></span><span class="screen-reader-text"><?php _e( 'Importer', 'wpmovielibrary' ); ?></span>
						</a>
					</div>

					<# if ( 'grid' == data || 'exerpt' == data ) { #>
					<label class="screen-reader-text" for="media-attachment-filters">Filtrer par type</label>
					<select class="attachment-filters" id="media-attachment-filters">
						<option value="all">Tous les éléments média</option>
						<option value="image">Images</option>
						<option value="audio">Sons</option>
						<option value="video">Vidéos</option>
						<option value="unattached">Non-attaché</option>
					</select>

					<label class="screen-reader-text" for="media-attachment-date-filters">Filtrer par date</label>
					<select class="attachment-filters" id="media-attachment-date-filters">
						<option value="all">Toutes les dates</option>
						<option value="0">mars 2015</option>
						<option value="1">février 2015</option>
						<option value="2">janvier 2015</option>
						<option value="3">décembre 2014</option>
					</select>

					<!--<a class="button media-button button-large  select-mode-toggle-button" href="#">Sélection en masse</a>-->
					<span style="display: none;" class="spinner"></span>

					<a disabled="disabled" class="button media-button button-primary button-large  delete-selected-button hidden" href="#">Supprimer la sélection</a>
					<# } #>
				</div>
				<# if ( 'grid' == data || 'exerpt' == data ) { #>
				<div class="media-toolbar-primary search-form">
					<label class="screen-reader-text" for="media-search-input">Rechercher un média</label>
					<input class="search" id="media-search-input" placeholder="Recherche" type="search" />
				</div>
				<# } #>
			</div>
