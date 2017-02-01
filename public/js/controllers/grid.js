
wpmoly = window.wpmoly || {};

_.extend( wpmoly.controller, {

	Grid: Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			this.settings = new Backbone.Model( options.settings || {} );
		},

		prev: function() {

			var current = this.settings.get( 'current_page' ),
			      total = this.settings.get( 'total_page' )
			       prev = Math.max( 1, current - 1 );

			if ( current != prev ) {
				this.settings.set({ current : prev });
			}
		},

		next: function() {

			var current = this.settings.get( 'current_page' ),
			      total = this.settings.get( 'total_page' )
			       next = Math.min( current + 1, total );

			if ( current != next ) {
				this.settings.set({ current : next });
			}
		}
	})
} );
