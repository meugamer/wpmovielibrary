
		<div id="wpmoly-posters" class="wpmoly-posters">

			<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

			<?php echo wpmoly_nonce_field( 'upload-movie-poster', $referer = false ); ?>
			<?php echo wpmoly_nonce_field( 'load-movie-posters', $referer = false ); ?>

			<div id="wpmoly-posters-preview" class="hide-if-no-js">
				<textarea id="wpmoly-imported-posters-json" style="display:none"><?php echo $data ?></textarea>
				<ul id="wpmoly-imported-posters" class="attachments ui-sortable ui-sortable-disabled" tabindex="-1">

<?php /*foreach ( $posters as $poster ) : ?>
					<li class="wpmoly-poster wpmoly-imported-poster">
						<a class="open-editor" href="<?php echo $poster['sizes']['medium']['url'] ?>" data-id="<?php echo $poster['id'] ?>">
							<div class="js--select-attachment type-image <?php echo $poster['type'] . $poster['format'] ?>">
								<div class="thumbnail">
									<div class="centered"><img src="<?php echo $poster['sizes']['medium']['url'] ?>" draggable="false" alt=""></div>
								</div>
							</div>
						</a>
					</li>

<?php endforeach;*/ ?>

					<li class="wpmoly-poster"><a href="#" id="wpmoly-load-posters" title="<?php _e( 'Load Posters', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-plus"></span></a></li>

				</ul>
			</div>
			<div style="clear:both"></div>

		</div>
