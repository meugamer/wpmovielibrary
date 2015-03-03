
						<# var status = data.status; #>
						<div class="wpmoly-status-icon"><# if ( true === status.loading ) { #><img src="<?php echo WPMOLY_URL . '/assets/img/puff.svg'; ?>" width="20" height="15" alt="" /><# } else { #><span class="wpmolicon icon-<# if ( status.error ) { #>warning<# } else { #>api<# } #>"></span><# } #></div>
						<div class="wpmoly-status-text<# if ( status.error ) { #> warning<# } #>">{{ status.message }}</div>
