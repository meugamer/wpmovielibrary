
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid = {};

_.extend( Grid, {

	Grid: wp.Backbone.View.extend({

		events: {
			/*'click [data-action="grid-type"]'  : 'setType',
			'click [data-action="grid-mode"]'  : 'setMode',
			'click [data-action="grid-theme"]' : 'setTheme'*/
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};

			this.set_regions();
		},

		set_regions: function() {

			this.menu       = new wpmoly.view.Grid.Menu({ controller: this.controller });
			this.pagination = new wpmoly.view.Grid.Pagination({ controller: this.controller });
			this.settings   = new wpmoly.view.Grid.Settings({ controller: this.controller });
			

			this.views.set( '.grid-settings', this.settings );
			this.views.set( '.grid-menu.settings-menu', this.menu );
			this.views.set( '.grid-menu.pagination-menu', this.pagination );
		}

	})

} );
