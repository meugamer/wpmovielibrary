
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
			show_runtime:     true,

			scroll:           false,
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

		queries: [],

		initialize: function( options ) {

			this.settings = options.controller;
			this.settings.on( 'change', this.update, this );
			/*this.settings.on( 'change:order',   this.update, this );
			this.settings.on( 'change:orderby', this.update, this );
			this.settings.on( 'change:paged',   this.update, this );*/

			this.props = new Backbone.Model;
			this.props.on( 'change', this.get, this );

			this.query = new grid.model.Movies( [], { controller: this.settings } );
			this.listenTo( this.query, 'add',    this.add );
			this.listenTo( this.query, 'remove', this.remove );
			this.listenTo( this.query, 'change', this.change );
			this.listenTo( this.query, 'reset',  this.reset );
		},

		add: function( model, collection, options ) {

			return this.reroute( 'add', model, collection, options );
		},

		remove: function( model, collection, options ) {

			return this.reroute( 'remove', model, collection, options );
		},

		change: function( model, options ) {

			return this.reroute( 'change', model, options );
		},

		reset: function( collection, options ) {

			return this.reroute( 'reset', collection, options );
		},

		reroute: function( event, model, collection, options ) {

			return this.trigger( event, model || {}, collection || {}, options || {} );
		},

		update: function( model, value, options ) {

			this.props.set( model.changed );
		},

		get: function( model, value, options ) {

			this.query.props = model;
			this.query.query();
		}
	})
} );
