
		<input type="hidden" name="_wpmoly_grid_type" value="{{ data.meta.type }}" />
		<input type="hidden" name="_wpmoly_grid_mode" value="{{ data.meta.mode }}" />
		<input type="hidden" name="_wpmoly_grid_theme" value="{{ data.meta.theme }}" />

		<div id="grid-types" class="block supported-grid-types active">
			<h4><?php _e( 'Select a type of grid', 'wpmovielibrary' ); ?></h4>
			<div class="block-inner clearfix">
			<# _.each( data.support, function( type, type_id ) { #>
				<button type="button" data-action="grid-type" data-value="{{ type_id }}" title="{{ type.label }}" class="<# if ( type_id == data.meta.type ) { #>active<# } #>">
					<span class="{{ type.icon }}"></span>
					<span class="label">{{ type.label }}</span>
				</button>
			<# } ); #>
			</div>
		</div>

		<# _.each( data.support, function( type, type_id ) { #>
			<# if ( type_id == data.meta.type ) { #>
		<div id="{{ type_id }}-grid-modes" class="block supported-grid-modes active">
			<h4><?php _e( 'Select a grid mode', 'wpmovielibrary' ); ?></h4>
			<div class="block-inner clearfix">
			<# _.each( data.support[ type_id ].modes, function( mode, mode_id ) { #>
				<button type="button" data-action="grid-mode" data-value="{{ mode_id }}" title="{{ mode.label }}" class="<# if ( mode_id == data.meta.mode ) { #>active<# } #>">
					<span class="{{ mode.icon }}"></span>
					<span class="label">{{ mode.label }}</span>
				</button>
			<# } ); #>
			</div>
		</div>
			<# } #>
		<# } ); #>

		<# _.each( data.support, function( type, type_id ) { #>
			<# if ( type_id == data.meta.type ) { #>
				<# _.each( data.support[ type_id ].modes, function( mode, mode_id ) { #>
					<# if ( mode_id == data.meta.mode ) { #>
		<div id="{{ type_id }}-grid-{{ mode_id }}-mode-themes" class="block supported-grid-themes active">
			<h4><?php _e( 'Select a theme', 'wpmovielibrary' ); ?></h4>
			<div class="block-inner clearfix">
						<# _.each( data.support[ type_id ].modes[ mode_id ].themes, function( theme, theme_id ) { #>
				<button type="button" data-action="grid-theme" data-value="{{ theme_id }}" title="{{ theme.label }}" class="<# if ( theme_id == data.meta.theme ) { #>active<# } #>">
					<span class="{{ theme.icon }}"></span>
					<span class="label">{{ theme.label }}</span>
				</button>
						<# } ); #>
					<# } #>
			</div>
		</div>
				<# } ); #>
			<# } #>
		<# } ); #>
