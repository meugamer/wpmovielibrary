
var grid = wpmoly.grid,
   media = wp.media,
       $ = Backbone.$;

_.extend( grid.controller, {

	Settings: Backbone.Model.extend({

		orderby: [ 'title', 'date', 'release_date', 'rating' ],

		order:   [ 'asc', 'desc', 'random' ],

		defaults: {
			// Grid Content
			orderby:          'title',
			order:            'asc',
			paged:            1,

			// Grid Filtering
			include_incoming: true,
			include_unrated:  true,

			// Grid Display
			show_title:       true,
			show_genres:      false,
			show_rating:      true,
			show_runtime:     true
		},

		update: function() {

			this.props.set({
				orderby: this.get( 'orderby' ),
				order:   this.get( 'order' ),
			});
		},

		reset: function() {

			this.props.set({
				orderby: this.defaults.orderby,
				order:   this.defaults.order,
			});
		}
	})
} );

_.extend( grid.controller, {

	Query: Backbone.Model.extend({

		query: {},

		queries: [],

		initialize: function( options ) {

			this.settings = options.controller;
			this.settings.on( 'change:order',   this.update, this );
			this.settings.on( 'change:orderby', this.update, this );
			this.settings.on( 'change:paged',   this.update, this );

			this.props = new Backbone.Model;
			this.props.on( 'change', this.get, this );
		},

		update: function( model, value, options ) {

			this.props.set( model.changed );
		},

		get: function( model, value, options ) {

			this.query = new grid.model.Movies;
			this.query.props = model;

		}
	})
} );
