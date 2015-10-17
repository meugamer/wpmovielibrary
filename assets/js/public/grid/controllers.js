
var grid = wpmoly.grid,
   media = wp.media,
       $ = Backbone.$;

_.extend( grid.controller, {

	Settings: Backbone.Model.extend({

		_orderby: [ 'post_title', 'post_date', 'release_date', 'rating' ],

		_order:   [ 'asc', 'desc', 'random' ],

		_display: [ 'title', 'year', 'genre', 'rating', 'runtime' ],

		pages: new Backbone.Model,

		query: new Backbone.Model,

		display: new Backbone.Model,

		defaults: {

			// Grid Display
			display: {
				title:      true,
				year:       true,
				genre:      false,
				rating:     false,
				runtime:    false,
				scroll:     false,
				view:       'grid',
				columns:    4,
				rows:       6
			},

			// Library options
			query: {
				number:     24,
				orderby:    'post_date',
				order:      'DESC',
				paged:      '1',
				letter:     '',
				category:   '',
				tag:        '',
				collection: '',
				actor:      '',
				genre:      '',
				meta:       '',
				detail:     '',
				value:      '',
				incoming:   true,
				unrated:    true,
			}
		},

		/**
		 * Initialize the Controller
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Options
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			var options = options || {},
			    display = {},
			      query = {};

			this.pages.set({
				current: options.pages.current || 0,
				total:   options.pages.total   || 0,
				prev:    options.pages.prev    || 0,
				next:    options.pages.next    || 0
			});

			_.each( this.defaults.display, function( value, key ) {
				display[ key ] = options.display[ key ] || value;
			}, this );

			_.each( this.defaults.query, function( value, key ) {
				query[ key ] = options.query[ key ] || value;
			}, this );

			this.display.set( display );
			this.query.set( query );
		}
	})
} );

_.extend( grid.controller, {

	Query: Backbone.Model.extend({

		/**
		 * Initialize the Controller
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Options
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.settings = options.controller;
			this.settings.on( 'change', this.update, this );

			this.collection = new grid.model.Movies( [], { controller: this.settings } );
			this.listenTo( this.collection, 'add',    this._add );
			this.listenTo( this.collection, 'remove', this._remove );
			this.listenTo( this.collection, 'change', this._change );
			this.listenTo( this.collection, 'reset',  this._reset );
		},

		/**
		 * Replicate the collection's 'add' event the controller.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Model
		 * @param    object    Collection
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		_add: function( model, collection, options ) {

			return this._reroute( 'add', model, collection, options );
		},

		/**
		 * Replicate the collection's 'remove' event the controller.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Model
		 * @param    object    Collection
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		_remove: function( model, collection, options ) {

			return this._reroute( 'remove', model, collection, options );
		},

		/**
		 * Replicate the collection's 'change' event the controller.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Model
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		_change: function( model, options ) {

			return this._reroute( 'change', model, options );
		},

		/**
		 * Replicate the collection's 'reset' event the controller.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Collection
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		_reset: function( collection, options ) {

			return this._reroute( 'reset', collection, options );
		},

		/**
		 * Replicate an event the controller.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Event
		 * @param    object    Model
		 * @param    object    Collection
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		_reroute: function( event, model, collection, options ) {

			return this.trigger( event, model || {}, collection || {}, options || {} );
		},

		/**
		 * Update the collection.
		 * 
		 * Should be used when settings were changed from the menu view
		 * using {silent:true}.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Model
		 * @param    object    Collection
		 * @param    object    Options
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		update: function( model, value, options ) {

			return this.collection.query();
		},

		/**
		 * Go back to the previous page.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		prev: function() {

			return this.collection.prev();
		},

		/**
		 * Go to the next page.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		next: function() {

			return this.collection.next();
		},

		/**
		 * Jump to a specific page.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		page: function( page ) {

			return this.collection.query( { paged: parseInt( page ) } );
		}
	})
} );
