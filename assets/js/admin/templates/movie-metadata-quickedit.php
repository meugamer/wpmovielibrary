
		<div class="attachment-info movie-images">
			<span class="settings-save-status">
				<span class="spinner"></span>
				<span class="saved"><?php _e( 'Saved.' ); ?></span>
			</span>
			<div class="details">
				<div class="post">
					<div class="filename"><strong><?php _e( 'Title' ); ?>&nbsp;:</strong> {{ data.post.post_title }}</div>
					<div class="filename"><strong><?php _e( 'Published on:'); ?></strong> {{ data.post.post_date }}</div>
					<div class="uploaded"><strong><?php _e( 'Author' ); ?>&nbsp;:</strong> <a href="{{ data.post.post_author_url }}">{{ data.post.post_author_name }}</a></div>
					<div class="filename"><strong><?php _e( 'Status' ); ?>&nbsp;:</strong> <# if ( undefined !== wpmoly.l10n.misc[ data.post.post_status ] ) { #>{{ wpmoly.l10n.misc[ data.post.post_status ] }} <# } else { #>−<# } #></div>
				</div>
				<div class="images">
					<div class="poster">
						<img src="{{ data.post.post_thumbnail }}" alt="" />
						<a href="{{ data.post.edit_poster }}" title="<?php _e( 'Change featured poster', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-edit"></span></a>
					</div>
					<# if ( _.isDefined( data.post.posters ) && data.post.posters.length ) { #>
					<div class="posters">
						<# _.each( data.post.posters, function( poster ) { #>
						<div class="additional-poster">
							<img src="{{ poster.url }}" alt="" />
							<a href="{{ poster.link }}" title="<?php _e( 'Edit this poster', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-edit"></span></a>
						</div>
						<# } ); if ( ! _.isNull( data.post.posters_total ) ) { #>
						<div class="additional-poster more"><a href="{{ data.post.edit_posters }}" title="<?php _e( 'View all posters', 'wpmovielibrary' ); ?>"><?php printf( '%s more', '{{ data.post.post_posters_total }}' ); ?></a></div>
						<# } #>
					</div>
					<# } #>
					<div class="sep"></div>
					<# if ( _.isDefined( data.post.images ) && data.post.images.length ) { #>
					<div class="backdrops">
						<# _.each( data.post.images, function( image ) { #>
						<div class="image">
							<img src="{{ image.url }}" alt="" />
							<a href="{{ image.link }}" title="<?php _e( 'Edit this image', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-edit"></span></a>
						</div>
						<# } ); if ( ! _.isNull( data.post.images_total ) ) { #>
						<div class="image more"><a href="{{ data.post.edit_images }}" title="<?php _e( 'View all images', 'wpmovielibrary' ); ?>"><?php printf( '%s more', '{{ data.post.post_images_total }}' ); ?></a></div>
						<# } #>
					</div>
					<# } #>
					<div style="clear:both"></div>
				</div>
			</div>
			<div class="actions">
				<a href="{{ data.post.edit_poster }}"><?php _e( 'View all posters', 'wpmovielibrary' ); ?></a> | <a href="{{ data.post.edit_images }}"><?php _e( 'View all images', 'wpmovielibrary' ); ?></a>
			</div>
		</div>
		<div class="attachment-media-view movie-metadata-view">
			<div class="movie-metadata">
				<?php echo self::render_meta_panel( 0 ); ?>
			</div>
		</div>
		<div class="attachment-info movie-details">
			<div class="settings">
				<?php echo self::render_details_panel( 0 ); ?>
			</div>

			<div class="actions">
				<a class="view-attachment" href="{{ data.link }}">Afficher la page du fichier</a>
				<# if ( data.can.save ) { #> |
					<a href="post.php?post={{ data.id }}&action=edit"> Indiquer plus de détails</a>
				<# } #>
				<# if ( ! data.uploading && data.can.remove ) { #> |
											<a class="delete-attachment" href="#">Supprimer définitivement</a>
									<# } #>
			</div>

		</div>