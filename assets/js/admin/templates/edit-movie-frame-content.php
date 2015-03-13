
		<div class="attachment-info movie-info movie-images">
			<div class="container">
				<span class="settings-save-status">
					<span class="spinner"></span>
					<span class="saved"><?php _e( 'Saved.' ); ?></span>
				</span>
				<# var post = data.post.attributes || {},
				       meta = data.meta.attributes || {},
				    details = data.details.attributes || {}; #>
				<div class="details">
					<div class="post">
						<div class="filename"><strong><?php _e( 'Title' ); ?>&nbsp;:</strong> {{ post.post_title }}</div>
						<div class="filename"><strong><?php _e( 'Published on:'); ?></strong> {{ post.post_date }}</div>
						<div class="uploaded"><strong><?php _e( 'Author' ); ?>&nbsp;:</strong> <a href="{{ post.post_author_url }}">{{ post.post_author_name }}</a></div>
						<div class="filename"><strong><?php _e( 'Status' ); ?>&nbsp;:</strong> <# if ( _.isDefined( wpmoly.l10n.misc[ post.post_status ] ) ) { #>{{ wpmoly.l10n.misc[ post.post_status ] }} <# } else { #>−<# } #></div>
					</div>
					<div class="images">
						<div class="poster">
							<img src="{{ post.post_thumbnail }}" alt="" />
							<a href="{{ post.edit_poster }}" title="<?php _e( 'Change featured poster', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-edit"></span></a>
						</div>
						<# if ( _.isDefined( post.posters ) && post.posters.length ) { #>
						<div class="posters">
							<# _.each( post.posters, function( poster, i ) { #>
							<div class="additional-poster">
								<img src="{{ poster.url }}" alt="" />
								<a href="{{ poster.link }}" title="<?php _e( 'Edit this poster', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-edit"></span></a>
							</div>
							<# } ); if ( ! _.isNull( post.posters_total ) ) { #>
							<div class="additional-poster more"><a href="{{ post.edit_posters }}" title="<?php _e( 'View all posters', 'wpmovielibrary' ); ?>"><?php printf( '%s more', '{{ post.posters_total }}' ); ?></a></div>
							<# } #>
						</div>
						<# } #>
						<div class="sep"></div>
						<# if ( _.isDefined( post.images ) && post.images.length ) { #>
						<div class="backdrops">
							<# _.each( post.images, function( image ) { #>
							<div class="image">
								<img src="{{ image.url }}" alt="" />
								<a href="{{ image.link }}" title="<?php _e( 'Edit this image', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-edit"></span></a>
							</div>
							<# } ); if ( ! _.isNull( post.images_total ) ) { #>
							<div class="image more"><a href="{{ post.edit_images }}" title="<?php _e( 'View all images', 'wpmovielibrary' ); ?>"><?php printf( '%s more', '{{ post.images_total }}' ); ?></a></div>
							<# } #>
						</div>
						<# } #>
						<div style="clear:both"></div>
					</div>
				</div>
				<div class="actions">
					<a href="{{ post.edit_posters }}"><?php _e( 'View all posters', 'wpmovielibrary' ); ?></a> | <a href="{{ post.edit_images }}"><?php _e( 'View all images', 'wpmovielibrary' ); ?></a>
				</div>
			</div>
		</div>
		<div class="attachment-media-view movie-metadata-view">
			<div class="container">
				<div class="movie-metadata">
					<?php echo WPMOLY_Edit_Movies::render_meta_panel( 0 ); ?>
				</div>
			</div>
		</div>
		<div class="attachment-info movie-info movie-details">
			<div class="container">
				<div class="settings">
					<?php echo WPMOLY_Edit_Movies::render_details_panel( 0 ); ?>
				</div>
			</div>
		</div>
		<# _.each( data.nonces, function( nonce, key ) { #>
		<input type="hidden" id="_wpmolynonce_{{ key }}" value="{{ nonce }}" />
		<# } ); #>
