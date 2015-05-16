
/**
 * grid.model.Movies
 * 
 * A collection of movies.
 * 
 * This collection has no persistence with the server without supplying
 * 'options.props.query = true', which will mirror the collection
 * to an Movies Query collection - @see grid.model.Movies.mirror().
 * 
 * @since    2.1.5
 * 
 * @param    array     [models]                Models to initialize with the collection.
 * @param    object    [options]               Options hash for the collection.
 * @param    string    [options.props]         Options hash for the initial query properties.
 * @param    string    [options.props.order]   Initial order (ASC or DESC) for the collection.
 * @param    string    [options.props.orderby] Initial attribute key to order the collection by.
 * @param    string    [options.props.query]   Whether the collection is linked to an movies query.
 * @param    string    [options.observe]
 * @param    string    [options.filters]
 *
 */
grid.model.Movies = Backbone.Collection.extend({

	model: grid.model.Movie,

	/**
	 * Initialize the Collection
	 * 
	 * @since    2.1.5
	 * 
	 * @param    array     [models=[]] Array of models used to populate the collection.
	 * @param    object    [options={}]
	 * @param    object    grid.view.Frame
	 * 
	 * @return   void
	 */
	initialize: function( models, options, controller ) {

		options = options || {};

		// this.controller is the Frame View.
		this.controller = controller;

		this.props = new Backbone.Model();
		this.pages = new Backbone.Model({
			current: 0,
			total:   0,
			prev:    0,
			next:    0
		});
		this.filters = options.filters || {};

		// Bind default `change` events to the `props` model.
		this.props.on( 'change', this._changeFilteredProps, this );

		this.props.on( 'change:order',   this._changeOrder,   this );
		this.props.on( 'change:orderby', this._changeOrderby, this );
		this.props.on( 'change:query',   this._changeQuery,   this );

		this.props.set( _.defaults( options.props || {} ) );

		if ( options.observe ) {
			this.observe( options.observe );
		}
	},

	/**
	 * Sort the collection when the order attribute changes.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   void
	 */
	_changeOrder: function() {

		if ( this.comparator ) {
			this.sort();
		}
	},

	/**
	 * Set the default comparator only when the `orderby` property is set.
	 *
	 * @since    2.1.5
	 *
	 * @param    object    model
	 * @param    string    orderby
	 * 
	 * @return   void
	 */
	_changeOrderby: function( model, orderby ) {

		// If a different comparator is defined, bail.
		if ( this.comparator && this.comparator !== grid.model.Movies.comparator ) {
			return;
		}

		if ( orderby && 'post__in' !== orderby ) {
			this.comparator = grid.model.Movies.comparator;
		} else {
			delete this.comparator;
		}
	},

	/**
	 * If the `query` property is set to true, query the server using
	 * the `props` values, and sync the results to this collection.
	 *
	 * @since    2.1.5
	 *
	 * @param    object     model
	 * @param    boolean    query
	 * 
	 * @return   void
	 */
	_changeQuery: function( model, query ) {

		if ( query ) {
			this.props.on( 'change', this._requery, this );
			this._requery();
		} else {
			this.props.off( 'change', this._requery, this );
		}
	},

	/**
	 * Change the filter properties.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   void
	 * 
	 * @param    object    model
	 */
	_changeFilteredProps: function( model ) {

		// If this is a query, updating the collection will be handled by
		// `this._requery()`.
		if ( this.props.get( 'query' ) ) {
			return;
		}

		var changed = _.chain( model.changed ).map( function( t, prop ) {
			var filter = grid.model.Movies.filters[ prop ],
				term = model.get( prop );

			if ( ! filter ) {
				return;
			}

			if ( term && ! this.filters[ prop ] ) {
				this.filters[ prop ] = filter;
			} else if ( ! term && this.filters[ prop ] === filter ) {
				delete this.filters[ prop ];
			} else {
				return;
			}

			// Record the change.
			return true;
		}, this ).any().value();

		if ( ! changed ) {
			return;
		}

		// If no `Movies` model is provided to source the searches
		// from, then automatically generate a source from the existing
		// models.
		if ( ! this._source ) {
			this._source = new grid.model.Movies( this.models );
		}

		this.reset( this._source.filter( this.validator, this ) );
	},

	validateDestroyed: false,

	/**
	 * Checks whether an movie is valid.
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    grid.model.Movie
	 * 
	 * @return   boolean
	 */
	validator: function( movie ) {

		if ( ! this.validateDestroyed && movie.destroyed ) {
			return false;
		}

		return _.all( this.filters, function( filter ) {
			return !! filter.call( this, movie );
		}, this );
	},

	/**
	 * Add or remove an movie to the collection depending on its validity.
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    grid.model.Movie
	 * @param    object    options
	 * 
	 * @return   Returns itself to allow chaining
	 */
	validate: function( movie, options ) {

		var     valid = this.validator( movie ),
		hasAttachment = !! this.get( movie.cid );

		if ( ! valid && hasAttachment ) {
			this.remove( movie, options );
		} else if ( valid && ! hasAttachment ) {
			this.add( movie, options );
		}

		return this;
	},

	/**
	 * Add or remove all movies from another collection depending on each one's validity.
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    grid.model.Movies
	 * @param    object    [options={}]
	 * 
	 * @return   Return itself to allow chaining
	 */
	validateAll: function( movies, options ) {

		options = options || {};

		_.each( movies.models, function( movie ) {
			this.validate( movie, { silent: true });
		}, this );

		if ( ! options.silent ) {
			this.trigger( 'reset', this, options );
		}
		return this;
	},

	/**
	 * Start observing another movies collection change events
	 * and replicate them on this collection.
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    The movies collection to observe.
	 * 
	 * @return   Return itself to allow chaining.
	 */
	observe: function( movies ) {

		this.observers = this.observers || [];
		this.observers.push( movies );

		movies.on( 'add change remove', this._validateHandler, this );
		movies.on( 'reset', this._validateAllHandler, this );

		// Replicate pagination changes on the observer's controller
		// (ie the View). Dirty coding, but that's only thing that works
		// so far.
		movies.pages.on( 'change', function( model ) {
			movies.observer.controller.pages.set( model.changed );
		}, this );

		this.validateAll( movies );
		return this;
	},

	/**
	 * Stop replicating collection change events from another movies collection.
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    The movies collection to stop observing.
	 * 
	 * @return   Return itself to allow chaining
	 */
	unobserve: function( movies ) {

		if ( movies ) {
			movies.off( null, null, this );
			this.observers = _.without( this.observers, movies );

		} else {
			_.each( this.observers, function( movies ) {
				movies.off( null, null, this );
			}, this );
			delete this.observers;
		}

		return this;
	},

	/**
	 * Single validation handler
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    grid.model.Movie
	 * @param    object    grid.model.Movies
	 * @param    object    options
	 *
	 * @return   Return itself to allow chaining
	 */
	_validateHandler: function( movie, movies, options ) {

		// If we're not mirroring this `movies` collection,
		// only retain the `silent` option.
		options = movies === this.mirroring ? options : {
			silent: options && options.silent
		};

		return this.validate( movie, options );
	},

	/**
	 * General validation handler
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    grid.model.Movies
	 * @param    object    options
	 * 
	 * @return   Return itself to allow chaining
	 */
	_validateAllHandler: function( movies, options ) {
		return this.validateAll( movies, options );
	},

	/**
	 * Start mirroring another movies collection, clearing out any models already
	 * in the collection.
	 * 
	 * @since    2.1.5
	 *
	 * @param    object   The movies collection to mirror.
	 * 
	 * @return   Return itself to allow chaining
	 */
	mirror: function( movies ) {

		if ( this.mirroring && this.mirroring === movies ) {
			return this;
		}

		this.unmirror();
		this.mirroring = movies;

		movies.observer = this;

		// Clear the collection silently. A `reset` event will be fired
		// when `observe()` calls `validateAll()`.
		this.reset( [], { silent: true } );
		this.observe( movies );

		return this;
	},

	/**
	 * Stop mirroring another movies collection.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   void
	 */
	unmirror: function() {

		if ( ! this.mirroring ) {
			return;
		}

		this.unobserve( this.mirroring );
		delete this.mirroring;
	},

	/**
	 * Jump to the next page in the collection
	 * 
	 * @since    2.1.5
	 * 
	 * @return   void
	 */
	_next: function() {

		this.props.set({ paged: this.mirroring.pages.get( 'next' ) });
	},

	/**
	 * Jump to the previous page in the collection
	 * 
	 * @since    2.1.5
	 * 
	 * @return   void
	 */
	_prev: function() {

		this.props.set({ paged: this.mirroring.pages.get( 'prev' ) });
	},

	/**
	 * Jump to a specific page in the collection
	 * 
	 * @since    2.1.5
	 * 
	 * @param    int    Page number to jump to
	 * 
	 * @return   void
	 */
	_page: function( page ) {

		if ( _.isUndefined( page ) ) {
			return;
		}

		if ( ! page || page > this.mirroring.pages.get( 'total' ) ) {
			return;
		}

		this.props.set({ paged: page });
	},

	/**
	 * Retrive more movies from the server for the collection.
	 *
	 * Only works if the collection is mirroring a Query Attachments collection,
	 * and forwards to its `more` method. This collection class doesn't have
	 * server persistence by itself.
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    options
	 * 
	 * @return   Promise
	 */
	more: function( options ) {

		var deferred = jQuery.Deferred(),
		   mirroring = this.mirroring,
		      movies = this;

		if ( ! mirroring || ! mirroring.more ) {
			return deferred.resolveWith( this ).promise();
		}

		// If we're mirroring another collection, forward `more` to
		// the mirrored collection. Account for a race condition by
		// checking if we're still mirroring that collection when
		// the request resolves.
		mirroring.more( options ).done( function() {
			if ( this === movies.mirroring ) {
				deferred.resolveWith( this );
			}
		});

		return deferred.promise();
	},

	/**
	 * Whether there are more movies that haven't been sync'd from the server
	 * that match the collection's query.
	 *
	 * Only works if the collection is mirroring a Query Attachments collection,
	 * and forwards to its `hasMore` method. This collection class doesn't have
	 * server persistence by itself.
	 * 
	 * @since    2.1.5
	 *
	 * @return   boolean
	 */
	hasMore: function() {
		return this.mirroring ? this.mirroring.hasMore() : false;
	},
	
	/**
	 * Custom AJAX-response parser.
	 * 
	 * @since    2.1.5
	 *
	 * @param    object|array    The raw response Object/Array.
	 * @param    object          XHR request
	 * 
	 * @return   array           The array of model attributes to be added to the collection
	 */
	parse: function( resp, xhr ) {

		// Set pagination data
		if ( ! _.isUndefined( resp.pages ) ) {

			var pages = {
				current: resp.pages.current,
				prev:    Math.max( 0, resp.pages.current - 1 ),
				next:    Math.min( resp.pages.total, resp.pages.current + 1 ),
				total:   resp.pages.total,
				posts:   resp.pages.posts
			};

			this.pages.set( pages );
			if ( this.mirroring ) {
				this.mirroring.pages.set( pages );
			}

			delete resp.pages;
		}

		if ( _.isObject( resp ) && false === resp instanceof grid.model.Movie ) {
			return _.toArray( resp );
		}

		if ( ! _.isArray( resp ) ) {
			resp = [resp];
		}

		return _.each( resp, function( attrs ) {

			var id, movie, newAttributes;

			if ( attrs instanceof Backbone.Model ) {
				id = attrs.get( 'id' );
				attrs = attrs.attributes;
			} else {
				id = attrs.id;
			}

			movie = grid.model.Movie.get( id );
			newAttributes = movie.parse( attrs, xhr );

			if ( ! _.isEqual( movie.attributes, newAttributes ) ) {
				movie.set( newAttributes );
			}

			return movie;
		});
	},

	/**
	 * If the collection is a query, create and mirror an Attachments Query collection.
	 * 
	 * @since    2.1.5
	 * 
	 * @param    boolean    Refresh cache
	 * 
	 * @return   void
	 */
	_requery: function( refresh ) {

		var props, query;

		if ( this.props.get('query') ) {
			props = this.props.toJSON();
			props.cache = ( true !== refresh );
			query = grid.model.Query.get( props );
			this.mirror( query );
			this.pages = query.pages;
		}
	}
}, {
	/**
	 * A function to compare two movie models in an movies collection.
	 *
	 * Used as the default comparator for instances of grid.model.Movies
	 * and its subclasses. @see grid.model.Movies._changeOrderby().
	 * 
	 * @since    2.1.5
	 *
	 * @static
	 *
	 * @param    object    a
	 * @param    object    b
	 * @param    object    options
	 * 
	 * @return   int       -1 if the first model should come before the second,
	 *                     0 if they are of the same rank and
	 *                     1 if the first model should come after.
	 */
	comparator: function( a, b, options ) {
		var key   = this.props.get('orderby'),
			order = this.props.get('order') || 'DESC',
			ac    = a.cid,
			bc    = b.cid;

		a = a.get( key );
		b = b.get( key );

		if ( 'date' === key || 'modified' === key ) {
			a = a || new Date();
			b = b || new Date();
		}

		// If `options.ties` is set, don't enforce the `cid` tiebreaker.
		if ( options && options.ties ) {
			ac = bc = null;
		}

		return ( 'DESC' === order ) ? wp.media.compare( a, b, ac, bc ) : wp.media.compare( b, a, bc, ac );
	},

	filters: {

		/**
		 * Filter by search
		 * 
		 * Note that this client-side searching is *not* equivalent
		 * to our server-side searching.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    grid.model.Movie
		 *
		 * @return   boolean
		 */
		search: function( movie ) {

			if ( ! this.props.get( 'search' ) ) {
				return true;
			}

			return _.any([ 'title', 'filename', 'description', 'caption', 'name' ], function( key ) {
				var value = movie.get( key );
				return value && -1 !== value.search( this.props.get( 'search' ) );
			}, this );
		},

		/**
		 * Filter by status
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    grid.model.Movie
		 *
		 * @return   boolean
		 */
		status: function( movie ) {

			var status = this.props.get( 'status' );
			if ( _.isUndefined( status ) ) {
				return true;
			}

			return status === movie.get( 'status' );
		}
	}
});
