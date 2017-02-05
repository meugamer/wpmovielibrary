
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Settings: Backbone.Model.extend({

		defaults: function() { return {
			type            : '',
			mode            : '',
			theme           : '',
			preset          : '',
			columns         : 5,
			rows            : 4,
			column_width    : 160,
			row_height      : 240,
			show_menu       : 1,
			mode_control    : 0,
			content_control : 0,
			display_control : 0,
			order_control   : 1,
			show_pagination : 1
		} },

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

			var options = options || {};
		}

	}),

} );
