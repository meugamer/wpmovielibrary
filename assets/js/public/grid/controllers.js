
var grid = wpmoly.grid,
   media = wp.media,
       $ = Backbone.$;

_.extend( grid.controller, {

	Settings: Backbone.Model.extend({

		orderby: [ 'title', 'date', 'release_date', 'rating' ],

		order:   [ 'asc', 'desc', 'random' ],

		initialize: function( options ) {

			var options = options || {};

			this.pages = new Backbone.Model({
				current: options.pages.current || 0,
				total:   options.pages.total   || 0,
				prev:    options.pages.prev    || 0,
				next:    options.pages.next    || 0
			})
			
			_.defaults( options, {
				// Library options
				number:           options.number           || 24,
				orderby:          options.orderby          || 'date',
				order:            options.order            || 'DESC',
				paged:            options.paged            || '1',
				letter:           options.letter           || '',
				category:         options.category         || '',
				tag:              options.tag              || '',
				collection:       options.collection       || '',
				actor:            options.actor            || '',
				genre:            options.genre            || '',
				meta:             options.meta             || '',
				detail:           options.detail           || '',
				value:            options.value            || '',

				// Grid Filtering
				include_incoming: options.include_incoming || true,
				include_unrated:  options.include_unrated  || true,

				// Grid Display
				show_title:       options.show_title       || true,
				show_year:        options.show_year        || true,
				show_genres:      options.show_genres      || false,
				show_rating:      options.show_rating      || false,
				show_runtime:     options.show_runtime     || false,
				scroll:           options.scroll           || false,
				view:             options.view             || 'grid',
				columns:          options.columns          || 4,
				rows:             options.rows             || 6
			} );
			this.set( options );
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
		},

		prev: function() {

			this.query.prev();
		},

		next: function() {

			this.query.next();
		},

		page: function( page ) {

			this.query.query( { paged: parseInt( page ) } );
		}
	})
} );
