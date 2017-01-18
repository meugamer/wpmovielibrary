
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view.Library, {

	ContentLatest: wp.Backbone.View.extend({

		className: 'wpmoly library content-latest inner-menu',

		template: wp.template( 'wpmoly-library-content-latest' ),

		events: {
			
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			var options = options || {};

			this.controller = options.controller || {};

		}

	})
} );
