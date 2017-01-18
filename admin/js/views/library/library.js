
wpmoly = window.wpmoly || {};

wpmoly.view.Library = {};

_.extend( wpmoly.view.Library, {

	Library: wp.Backbone.View.extend({

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};

			this.set_regions();
		},

		/**
		 * Set Regions (subviews).
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		set_regions: function() {

			this.menu = new wpmoly.view.Library.Menu({ controller: this.controller });
			this.content = {
				latest    : new wpmoly.view.Library.ContentLatest({ controller: this.controller }),
				favorites : new wpmoly.view.Library.ContentFavorites({ controller: this.controller }),
				import    : new wpmoly.view.Library.ContentImport({ controller: this.controller }),
			}

			this.views.set( '#wpmoly-library-menu', this.menu );

			var mode = this.controller.get( 'mode' );
			if ( this.content[ mode ] ) {
				this.views.set( '#wpmoly-library-content', this.content[ mode ] );
			}
		}
	})
} );
