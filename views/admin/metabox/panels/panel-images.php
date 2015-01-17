
		<div id="wpmoly-images" class="wpmoly-images">

			<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

			<?php echo wpmoly_nonce_field( 'upload-movie-image', $referer = false ); ?>
			<?php echo wpmoly_nonce_field( 'load-movie-images', $referer = false ); ?>
			<input type="hidden" id="wp-version" value="<?php echo $version ?>" />

			<div id="wpmoly-backdrops-preview" class="hide-if-no-js">
				<ul id="wpmoly-imported-backdrops" class="attachments ui-sortable ui-sortable-disabled" tabindex="-1">

<?php foreach ( $images as $image ) : ?>
					<li class="wpmoly-backdrop wpmoly-imported-backdrop">
						<a class="open-editor" href="<?php echo $image['link'] ?>" data-id="<?php echo $image['id'] ?>">
							<div class="js--select-attachment type-image <?php echo $image['type'] . $image['format'] ?>">
								<div class="thumbnail">
									<div class="centered"><img src="<?php echo $image['image'][0] ?>" draggable="false" alt=""></div>
								</div>
							</div>
						</a>
					</li>

<?php endforeach; ?>

					<li class="wpmoly-backdrop wpmoly-imported-backdrop"><a href="#" id="wpmoly-load-backdrops"><?php _e( 'Load Images', 'wpmovielibrary' ); ?></a></li>

				</ul>
			</div>
			<div style="clear:both"></div>

		</div>
