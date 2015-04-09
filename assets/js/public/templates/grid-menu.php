
			<ul class="wpmoly-grid-menu-container">
				<li class="wpmoly-grid-menu-item">
					<ul class="wpmoly-grid-submenu">
						<li>
							<a class="grid-menu-action" data-action="openmenu" href="#" title="<?php _e( 'Change the grid ordering', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-order"></span> <span class="grid-menu-label">Sort by…</span></a>
							<a href="#" data-action="order" data-value="asc" title="<?php _e( 'Sort ascendingly', 'wpmovielibrary' ); ?>" class="<# if ( 'ASC' == data.order ) { #>active<# } #>"><span class="wpmolicon icon-arrow-up"></span></a>
							<a href="#" data-action="order" data-value="desc" title="<?php _e( 'Sort descendingly', 'wpmovielibrary' ); ?>" class="<# if ( 'DESC' == data.order ) { #>active<# } #>"><span class="wpmolicon icon-arrow-down"></span></a>
						</li>
						<li><a class="grid-menu-action<# if ( 'date' == data.orderby ) { #> active<# } #>" data-action="orderby" data-value="date" href="#" title="<?php _e( 'Classer par date de publication', 'wpmovielibrary' ); ?>"><span class="dashicons dashicons-calendar"></span> <span class="grid-menu-label"><?php _e( 'Sort by post date', 'wpmovielibrary' ); ?></span></a></li>
						<li><a class="grid-menu-action<# if ( 'title' == data.orderby ) { #> active<# } #>" data-action="orderby" data-value="title" href="#" title="<?php _e( 'Classer par titre', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-movie"></span> <span class="grid-menu-label"><?php _e( 'Sort by title', 'wpmovielibrary' ); ?></span></a></li>
						<li><a class="grid-menu-action<# if ( 'release_date' == data.orderby ) { #> active<# } #>" data-action="orderby" data-value="release_date" href="#" title="<?php _e( 'Classer par date de sortie', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-date"></span> <span class="grid-menu-label"><?php _e( 'Sort by release date', 'wpmovielibrary' ); ?></span></a></li>
						<li><a class="grid-menu-action<# if ( 'rating' == data.orderby ) { #> active<# } #>" data-action="orderby" data-value="rating" href="#" title="<?php _e( 'Classer par note', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-star-half"></span> <span class="grid-menu-label"><?php _e( 'Sort by rating', 'wpmovielibrary' ); ?></span></a></li>
					</ul>
				</li>
				<li class="wpmoly-grid-menu-item">
					<ul class="wpmoly-grid-submenu">
						<li><a class="grid-menu-action" data-action="openmenu" href="#" title="<?php _e( 'Select a grid view', 'wpmovielibrary' ); ?>"><span class="dashicons dashicons-schedule"></span> <span class="grid-menu-label"><?php _e( 'View as…', 'wpmovielibrary' ); ?></span></a></li>
						<li><a class="grid-menu-action<# if ( 'grid' == data.view ) { #>active<# } #>" data-action="view" data-value="grid" href="#" title="<?php _e( 'View movies in grid', 'wpmovielibrary' ); ?>"><span class="dashicons dashicons-grid-view"></span> <span class="grid-menu-label"><?php _e( 'Grid View', 'wpmovielibrary' ); ?></span></a></li>
						<li><a class="grid-menu-action<# if ( 'exerpt' == data.view ) { #>active<# } #>" data-action="view" data-value="exerpt" href="#" title="<?php _e( 'View movies in extended grid with additional meta', 'wpmovielibrary' ); ?>"><span class="dashicons dashicons-exerpt-view"></span> <span class="grid-menu-label"><?php _e( 'Extended Grid View', 'wpmovielibrary' ); ?></span></a></li>
						<li><a class="grid-menu-action<# if ( 'list' == data.view ) { #>active<# } #>" data-action="view" data-value="list" href="#" title="<?php _e( 'View movies in list view', 'wpmovielibrary' ); ?>"><span class="dashicons dashicons-list-view"></span> <span class="grid-menu-label"><?php _e( 'List View', 'wpmovielibrary' ); ?></span></a></li>
					</ul>
				</li>
				<li class="wpmoly-grid-menu-item wpmoly-grid-menu-item-settings">
					<a class="grid-menu-action" data-action="opensettings" href="#" title="<?php _e( 'Change the grid settings', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-ellipsis-h"></span></a>
					<div class="grid-menu-settings">
						<div class="grid-menu-settings-section">
							<input type="hidden" data-value="scroll" value="<# if ( data.scroll ) { #>1<# } else { #>0<# } #>" />
							<span class="grid-menu-label"><span class="wpmolicon icon-infinite"></span>&nbsp; <?php _e( 'Infinite scrolling:', 'wpmovielibrary' ); ?></span> <a href="#" data-action="scroll" data-value="1" title="" <# if ( data.scroll ) { #>class="selected"<# } #>>Yes</a>&nbsp;&nbsp;&bull;&nbsp;&nbsp;<a href="#" data-action="scroll" data-value="0" title=""<# if ( ! data.scroll ) { #>class="selected"<# } #>>No</a>
						</div>
						<div class="grid-menu-settings-section">
							<span class="grid-menu-label"><span class="wpmolicon icon-grid"></span>&nbsp; <?php _e( 'Movies per page:', 'wpmovielibrary' ); ?></span> <input data-action="perpage" type="text" size="3" value="{{ data.perpage }}"/>
						</div>
						<div class="grid-menu-settings-toolbar">
							<a data-action="applysettings" href="#" title=""><span class="wpmolicon icon-yes"></span></a>
						</div>
					</div>
				</li>
				<li class="wpmoly-grid-menu-item wpmoly-grid-menu-item-expand">
					<a class="grid-menu-action grid-menu-enlarge" data-action="expand" data-value="enlarge" href="#" title="<?php _e( 'Set the grid to full view', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-resize-enlarge"></span> <span class="grid-menu-label"><?php _e( 'Expand', 'wpmovielibrary' ); ?></span></a>
					<a class="grid-menu-action grid-menu-shrink" data-action="expand" data-value="shrink" href="#" title="<?php _e( 'Set the grid back to normal view', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-resize-shrink"></span> <span class="grid-menu-label"><?php _e( 'Shrink', 'wpmovielibrary' ); ?></span></a>
				</li>
				<li class="wpmoly-grid-menu-item wpmoly-grid-menu-item-search">
					<a class="grid-menu-action" data-action="opensearch" href="#" title="<?php _e( 'Search the grid', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-search"></span> <span class="grid-menu-label"><?php _e( 'Search', 'wpmovielibrary' ); ?></span></a>
					<div class="grid-menu-search-container">
						<input type="text" placeholder="<?php _e( 'Search...', 'wpmovielibrary' ); ?>" />
						<a href="#" title="<?php _e( 'Launch search!', 'wpmovielibrary' ); ?>" data-action="search"><span class="wpmolicon icon-arrow-right2"></span></a>
					</div>
				</li>
			</ul>
