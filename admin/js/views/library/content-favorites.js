
wpmoly = window.wpmoly || {};

_.extend( wpmoly.view.Library, {

	ContentFavorites: wp.Backbone.View.extend({

		className: 'wpmoly library content-import inner-menu',

		template: wp.template( 'wpmoly-library-content-import' ),

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
