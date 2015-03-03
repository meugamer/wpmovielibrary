
			<img width="{{ data.attachment.sizes.medium.width }}" height="{{ data.attachment.sizes.medium.height }}" src="{{ data.attachment.sizes.medium.url }}" class="attachment-medium" alt="{{ data.attachment.title }}" />
			<div class="wpmoly-imported-attachment-menu">
				<a href="#" class="wpmoly-imported-attachment-menu-toggle"><span class="wpmolicon icon-ellipsis-h"></span></a>
				<div class="wpmoly-imported-attachment-menu-inner">
					<ul>
						<li>Options</li>
						<li><a class="wpmoly-imported-attachment-menu-edit" href="<?php echo admin_url( '/upload.php?item=' ) ?>{{ data.attachment.id }}" target="_blank"><span class="wpmolicon icon-edit-page"></span>&nbsp; <?php _e( 'Edit' ) ?></a></li>
						<li><a class="wpmoly-imported-attachment-menu-delete" href="#"><span class="wpmolicon icon-trash"></span>&nbsp; <?php _e( 'Delete' ) ?></a></a></li>
<# if ( 'poster' == data.type ) { #>
						<li><a class="wpmoly-imported-attachment-menu-featured" href="#"><span class="wpmolicon icon-poster"></span>&nbsp; <?php _e( 'Featured' ) ?></a></a></li>
<# } #>
					</ul>
				</div>
			</div>
