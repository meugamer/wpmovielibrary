
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Settings: Backbone.Model.extend({

		defaults: {
			type              : '',
			mode              : 'grid',
			theme             : 'default',
			preset            : '',
			columns           : 5,
			rows              : 4,
			column_width      : 160,
			row_height        : 240,
			list_columns      : 3,
			list_column_width : 240,
			list_rows         : 8,
			enable_ajax       : 1,
			enable_pagination : 1,
			settings_control  : 1,
			custom_letter     : 1,
			custom_order      : 1,
			customs_control   : 0,
			custom_mode       : 0,
			custom_content    : 0,
			custom_display    : 0
		},

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 * 
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			var options = options || {},
			   booleans = [ 'enable_ajax', 'enable_pagination', 'settings_control', 'custom_letter', 'custom_order', 'customs_control', 'custom_mode', 'custom_content', 'custom_display' ],
			   integers = [ 'columns', 'rows', 'column_width', 'row_height', 'list_columns', 'list_column_width', 'list_rows' ];

			_.each( attributes, function( value, key ) {
				if ( _.isNull( value ) && _.has( this.defaults, key ) ) {
					this.set( key, this.defaults[ key ], { silent : true } );
				} else if ( ! _.isNull( value ) ) {
					if ( _.contains( booleans, key ) ) {
						this.set( key, !! value, { silent : true } );
					} else if ( _.contains( integers, key ) ) {
						this.set( key, parseInt( value ) || this.defaults[ key ], { silent : true } );
					}
				}
			}, this );
		}

	}),

} );
