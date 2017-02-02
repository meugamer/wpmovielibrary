
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Query: Backbone.Model.extend({

		defaults: {
			order   : 'DESC',
			orderby : 'post_date',
			letter  : ''
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

			var options = options || {};
			this.total_page   = options.total_page || '';
			this.current_page = options.current_page || '';
		}

	}),

} );
