
var grid = wpmoly.grid,
   media = wp.media,
       $ = Backbone.$;

/**
 * WPMOLY Backbone Movie Model
 * 
 * Stores a movie's post data, metadata and details
 * 
 * @since    2.1.5
 */
grid.model.Movie = Backbone.Model.extend({

	id: '',

	/**
	 * Convert date strings into Date objects.
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    The raw response object, typically returned by fetch()
	 * 
	 * @return   object    The modified response object, which is the attributes hash to be set on the model.
	 */
	parse: function( resp ) {

		if ( ! resp ) {
			return resp;
		}

		resp.post_date     = new Date( resp.post_date );
		resp.post_modified = new Date( resp.post_modified );

		return resp;
	}
}, {
	/**
	 * Create a new model on the static 'all' movies collection and return it.
	 *
	 * @since    2.1.5
	 * 
	 * @param    object    Movie attributes
	 * 
	 * @return   object    wpmoly.grid.model.Movie
	 */
	create: function( attrs ) {
		return grid.model.Movies.all.push( attrs );
	},

	/**
	 * Create a new model on the static 'all' movies collection and return it.
	 * 
	 * If this function has already been called for the id,
	 * it returns the specified movie.
	 * 
	 * @since    2.1.5
	 * 
	 * @param    string    A string used to identify a model.
	 * @param    object    A grid.model.Movie movie model.
	 * 
	 * @return   wpmoly.grid.model.Movie
	 */
	get: _.memoize( function( id, movie ) {
		return grid.model.Movies.all.push( movie || { id: id } );
	})
});

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
	 * 
	 * @return   void
	 */
	initialize: function( models, options ) {

		options = options || {};

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
		var valid = this.validator( movie ),
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

/**
 * wpmoly.grid.model.Query
 *
 * A collection of movies that match the supplied query arguments.
 *
 * Note: Do NOT change this.args after the query has been initialized.
 *       Things will break.
 *
 * @since    2.1.5
 *
 * @param    array     [models]                      Models to initialize with the collection.
 * @param    object    [options]                     Options hash.
 * @param    object    [options.args]                Movies query arguments.
 * @param    object    [options.args.posts_per_page]
 */
grid.model.Query = grid.model.Movies.extend({

	/**
	 * Initialize the Model.
	 * 
	 * @since    2.1.5
	 * 
	 * @param    array     [models=[]]  Array of initial models to populate the collection.
	 * @param    object    [options={}]
	 */
	initialize: function( models, options ) {
		var allowed;

		options = options || {};
		grid.model.Movies.prototype.initialize.apply( this, arguments );

		this.args     = options.args;
		this._hasMore = true;
		this.created  = new Date();

		this.filters.order = function( movie ) {
			var orderby = this.props.get('orderby'),
				order = this.props.get('order');

			if ( ! this.comparator ) {
				return true;
			}

			// We want any items that can be placed before the last
			// item in the set. If we add any items after the last
			// item, then we can't guarantee the set is complete.
			if ( this.length ) {
				return 1 !== this.comparator( movie, this.last(), { ties: true });

			// Handle the case where there are no items yet and
			// we're sorting for recent items. In that case, we want
			// changes that occurred after we created the query.
			} else if ( 'DESC' === order && ( 'date' === orderby || 'modified' === orderby ) ) {
				return movie.get( orderby ) >= this.created;

			// If we're sorting by menu order and we have no items,
			// accept any items that have the default menu order (0).
			} else if ( 'ASC' === order && 'menuOrder' === orderby ) {
				return movie.get( orderby ) === 0;
			}

			// Otherwise, we don't want any items yet.
			return false;
		};
	},

	/**
	 * Whether there are more movies that haven't been sync'd from the server
	 * that match the collection's query.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   boolean
	 */
	hasMore: function() {
		return this._hasMore;
	},

	/**
	 * Fetch more movies from the server for the collection.
	 * 
	 * @since    2.1.5
	 * 
	 * @param   object    [options={}]
	 * 
	 * @return   Promise
	 */
	more: function( options ) {

		var query = this;

		// If there is already a request pending, return early with the Deferred object.
		if ( this._more && 'pending' === this._more.state() ) {
			return this._more;
		}

		if ( ! this.hasMore() ) {
			return jQuery.Deferred().resolveWith( this ).promise();
		}

		options = options || {};
		options.remove = false;

		return this._more = this.fetch( options ).done( function( resp ) {
			if ( _.isEmpty( resp ) || -1 === this.args.posts_per_page || resp.length < this.args.posts_per_page ) {
				query._hasMore = false;
			}
		});
	},

	/**
	 * Overrides Backbone.Collection.sync
	 * Overrides grid.model.Movies.sync
	 * 
	 * @since    2.1.5
	 * 
	 * @param    string    method
	 * @param    object    model
	 * @param    object    [options={}]
	 * 
	 * @return   Promise
	 */
	sync: function( method, model, options ) {

		var args, fallback;

		// Overload the read method so Attachment.fetch() functions correctly.
		if ( 'read' === method ) {

			options = options || {};
			options.context = this;
			options.data = _.extend( options.data || {}, {
				action:  'wpmoly_query_movies'
			});

			// Clone the args so manipulation is non-destructive.
			args = _.clone( this.args );

			// Determine which page to query.
			if ( _.isUndefined( args.paged ) && -1 !== args.posts_per_page ) {
				args.paged = Math.round( this.length / args.posts_per_page ) + 1;
			}

			options.data.query = args;

			return wp.media.ajax( options );

		// Otherwise, fall back to Backbone.sync()
		} else {
			// Call grid.model.Movies.sync or Backbone.sync
			fallback = grid.model.Movies.prototype.sync ? grid.model.Movies.prototype : Backbone;
			return fallback.sync.apply( this, arguments );
		}
	}
}, {

	defaultProps: {
		orderby: 'date',
		order:   'DESC'
	},

	defaultArgs: {
		posts_per_page: 40
	},

	orderby: {
		allowed:  [ 'date', 'title', 'modified', 'meta_title', 'release_date', 'rating', 'id' ],
		valuemap: {
			'id': 'ID'
		}
	},

	/**
	 * A map of JavaScript query properties to their WP_Query equivalents.
	 * 
	 * @since    2.1.5
	 */
	propmap: {
		'search':     's',
		'type':       'post_mime_type',
		'perPage':    'posts_per_page',
		'menuOrder':  'menu_order',
		'uploadedTo': 'post_parent',
		'status':     'post_status',
		'include':    'post__in',
		'exclude':    'post__not_in'
	},

	/**
	 * Creates and returns an Movies Query collection given the properties.
	 *
	 * Caches query objects and reuses where possible.
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    [props]
	 * @param    object    [props.cache=true]   Whether to use the query cache or not.
	 * @param    object    [props.order]
	 * @param    object    [props.orderby]
	 * @param    object    [props.include]
	 * @param    object    [props.exclude]
	 * @param    object    [props.s]
	 * @param    object    [props.post_mime_type]
	 * @param    object    [props.posts_per_page]
	 * @param    object    [props.menu_order]
	 * @param    object    [props.post_parent]
	 * @param    object    [props.post_status]
	 * @param    object    [options]
	 *
	 * @return   object    wpmoly.model.Query A new Movies Query collection.
	 */
	get: (function(){

		var queries = [];

		/**
		 * Return a new Query
		 * 
		 * @since    2.1.5
		 * 
		 * @return   object    Query
		 */
		return function( props, options ) {

			var args = {},
			 orderby = grid.model.Query.orderby,
			defaults = grid.model.Query.defaultProps,
			   cache = !! props.cache || _.isUndefined( props.cache ),
			   query;

			// Remove the `query` property. This isn't linked to a query,
			// this *is* the query.
			delete props.query;
			delete props.cache;

			// Fill default args.
			_.defaults( props, defaults );

			// Normalize the order.
			props.order = props.order.toUpperCase();
			if ( 'DESC' !== props.order && 'ASC' !== props.order ) {
				props.order = defaults.order.toUpperCase();
			}

			// Ensure we have a valid orderby value.
			if ( ! _.contains( orderby.allowed, props.orderby ) ) {
				props.orderby = defaults.orderby;
			}

			_.each( [ 'include', 'exclude' ], function( prop ) {
				if ( props[ prop ] && ! _.isArray( props[ prop ] ) ) {
					props[ prop ] = [ props[ prop ] ];
				}
			} );

			// Generate the query `args` object.
			// Correct any differing property names.
			_.each( props, function( value, prop ) {
				if ( _.isNull( value ) ) {
					return;
				}

				args[ grid.model.Query.propmap[ prop ] || prop ] = value;
			});

			// Fill any other default query args.
			_.defaults( args, grid.model.Query.defaultArgs );

			// `props.orderby` does not always map directly to `args.orderby`.
			// Substitute exceptions specified in orderby.keymap.
			args.orderby = orderby.valuemap[ props.orderby ] || props.orderby;

			// Search the query cache for a matching query.
			if ( cache ) {
				query = _.find( queries, function( query ) {
					return _.isEqual( query.args, args );
				});
			} else {
				queries = [];
			}

			// Otherwise, create a new query and add it to the cache.
			if ( ! query ) {
				var options = _.extend( options || {}, {
					props: props,
					args:  args
				} );
				
				query = new grid.model.Query( [], options );
				queries.push( query );
			}

			return query;
		};
	}())
});

/**
 * A collection of all attachments that have been fetched from the server.
 * 
 * @since    2.1.5
 */
grid.model.Movies.all = new grid.model.Movies();

/**
 * wpmoly.grid.query
 *
 * Shorthand for creating a new Movies Query.
 * 
 * @since    2.1.5
 *
 * @param    object    [props]
 * 
 * @return   object    grid.model.Movies
 */
grid.query = function( props ) {
	return new grid.model.Movies( null, {
		props: _.extend( _.defaults( props || {}, { orderby: 'date' } ), { query: true } )
	});
};