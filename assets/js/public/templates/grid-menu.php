
			<# console.log( data ); #>
			<ul class="wpmoly-grid-menu-container">
				<li class="wpmoly-grid-menu-item">
					<a class="grid-menu-action" data-action="openmenu" data-value="content" href="#" title="<?php _e( 'Edit the grid content', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-order"></span></a>
				</li>
				<li class="wpmoly-grid-menu-item item-right">
					<a class="grid-menu-action" data-action="openmenu" data-value="settings" href="#" title="<?php _e( 'Change the grid settings', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-settings"></span></a>
				</li>
			</ul>

			<div class="wpmoly-grid-settings-container">
				<div class="wpmoly-grid-settings">
					<div class="wpmoly-grid-settings-content">
						<div class="wpmoly-grid-settings-block">
							<div class="wpmoly-grid-settings-header"><?php _e( 'Sorting', 'wpmovielibrary' ); ?></div>
							<div class="wpmoly-grid-settings-section">
								<label>
									<a href="#" data-action="orderby" data-value="title" title="<?php _e( 'Sort movies by title', 'wpmovielibrary' ); ?>"><?php _e( 'Title', 'wpmovielibrary' ); ?> <span class="wpmolicon <# if ( 'title' == data.orderby ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="orderby" data-value="release_date" title="<?php _e( 'Sort movies by release date', 'wpmovielibrary' ); ?>"><?php _e( 'Release Date', 'wpmovielibrary' ); ?> <span class="wpmolicon <# if ( 'release_date' == data.orderby ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="orderby" data-value="date" title="<?php _e( 'Sort movies by post date', 'wpmovielibrary' ); ?>"><?php _e( 'Post Date', 'wpmovielibrary' ); ?> <span class="wpmolicon <# if ( 'date' == data.orderby ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="orderby" data-value="rating" title="<?php _e( 'Sort movies by rating', 'wpmovielibrary' ); ?>"><?php _e( 'Rating', 'wpmovielibrary' ); ?> <span class="wpmolicon <# if ( 'rating' == data.orderby ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
							</div>
						</div>
						<div class="wpmoly-grid-settings-block">
							<div class="wpmoly-grid-settings-header"><?php _e( 'Ordering', 'wpmovielibrary' ); ?></div>
							<div class="wpmoly-grid-settings-section">
								<label>
									<a href="#" data-action="order" data-value="asc" title="<?php _e( 'Order movies ascendingly', 'wpmovielibrary' ); ?>"><?php _e( 'Ascendingly', 'wpmovielibrary' ); ?> <span class="wpmolicon <# if ( 'ASC' == data.order ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="order" data-value="desc" title="<?php _e( 'Order movies descendingly', 'wpmovielibrary' ); ?>"><?php _e( 'Descendingly', 'wpmovielibrary' ); ?> <span class="wpmolicon <# if ( 'DESC' == data.order ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="order" data-value="random" title="<?php _e( 'Shuffle movies', 'wpmovielibrary' ); ?>"><?php _e( 'Random', 'wpmovielibrary' ); ?> <span class="wpmolicon <# if ( 'RANDOM' == data.order ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
							</div>
						</div>
						<div class="wpmoly-grid-settings-block">
							<div class="wpmoly-grid-settings-header"><?php _e( 'Include', 'wpmovielibrary' ); ?></div>
							<div class="wpmoly-grid-settings-section">
								<label>
									<a href="#" data-action="include" data-value="incoming" title="<?php _e( 'Include movies not released yet', 'wpmovielibrary' ); ?>"><?php _e( 'Incoming', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 1 == data.include.incoming ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="include" data-value="unrated" title="<?php _e( 'Include movies with no rating', 'wpmovielibrary' ); ?>"><?php _e( 'Not rated', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 1 == data.include.unrated ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
							</div>
						</div>
					</div>

					<div class="wpmoly-grid-settings-settings">
						<div class="wpmoly-grid-settings-block">
							<div class="wpmoly-grid-settings-header"><?php _e( 'View', 'wpmovielibrary' ); ?></div>
							<div class="wpmoly-grid-settings-section">
								<label>
									<a href="#" data-action="view" data-value="grid" title="<?php _e( 'View movies as grid', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-grid"></span>&nbsp; <?php _e( 'Grid', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 'grid' == data.view ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="view" data-value="exerpt" title="<?php _e( 'View movies as extended list', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-queue-alt"></span>&nbsp; <?php _e( 'Exerpt', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 'exerpt' == data.view ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="view" data-value="list" title="<?php _e( 'View movies as list', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-list"></span>&nbsp; <?php _e( 'List', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 'list' == data.view ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
							</div>
						</div>
						<div class="wpmoly-grid-settings-block">
							<div class="wpmoly-grid-settings-header"><?php _e( 'Display', 'wpmovielibrary' ); ?></div>
							<div class="wpmoly-grid-settings-section">
								<label>
									<a href="#" data-action="display" data-value="title" title="<?php _e( 'Show movie titles', 'wpmovielibrary' ); ?>"><?php _e( 'Title', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 1 == data.display.title ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="display" data-value="genres" title="<?php _e( 'Show movie genres', 'wpmovielibrary' ); ?>"><?php _e( 'Genres', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 1 == data.display.genres ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="display" data-value="rating" title="<?php _e( 'Show movie ratings', 'wpmovielibrary' ); ?>"><?php _e( 'Rating', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 1 == data.display.rating ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label>
									<a href="#" data-action="display" data-value="runtime" title="<?php _e( 'Show movie runtimes', 'wpmovielibrary' ); ?>"><?php _e( 'Runtime', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( 1 == data.display.runtime ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
							</div>
						</div>
						<div class="wpmoly-grid-settings-block">
							<div class="wpmoly-grid-settings-header"><?php _e( 'Advanced', 'wpmovielibrary' ); ?></div>
							<div class="wpmoly-grid-settings-section">
								<label>
									<a href="#" data-action="scroll" data-value="1" title="<?php _e( 'Automatically load more movies when reaching the end of the page', 'wpmovielibrary' ); ?>"><?php _e( 'Infinite scroll', 'wpmovielibrary' ); ?> <input type="checkbox" value="" /><span class="wpmolicon <# if ( '' == data.scroll ) { #>icon-yes-alt<# } else { #>icon-no-alt-2<# } #>"></span></a>
								</label>
								<label><?php _e( 'Movies per page', 'wpmovielibrary' ); ?> <input type="text" value="" size="3" maxlength="3" /></label>
							</div>
						</div>
					</div>

					<div class="wpmoly-grid-settings-valid">
						<a href="#" data-action="apply-settings" class="apply" title="<?php _e( 'Apply changes to the grid', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-yes"></span> <!--<?php _e( 'Apply', 'wpmovielibrary' ); ?>--></a><a href="#" data-action="cancel-settings" class="cancel" title="<?php _e( 'Reset changes', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-no"></span> <!--<?php _e( 'Cancel', 'wpmovielibrary' ); ?>--></a>
					</div>
				</div>
			</div>
