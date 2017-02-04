
wpmoly = window.wpmoly || {};

_.extend( wpmoly.model, {

	Query: Backbone.Model.extend({

		defaults: function() { return {
			order   : 'DESC',
			orderby : 'post_date',
			letter  : ''
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
			this.total_page   = parseInt( options.total_page ) || '';
			this.current_page = parseInt( options.current_page ) || '';
		}

	}),

} );
