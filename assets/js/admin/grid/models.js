
var editor = wpmoly.editor,
      grid = wpmoly.grid,
     media = wp.media,
         $ = Backbone.$;

/**
 * wpmoly.grid.model.Movies
 *
 * A collection of movies.
 *
 * This Model is a complete copy of wp.media.model.Attachments, adapted to movies.
 *
 * @since    2.2
 */
grid.model.Movies = Backbone.Collection.extend({

	model: editor.model.Movie,

	/**
	 * Initialize the Model.
	 * 
	 * @since    2.2
	 * 
	 * @param    array     Array of models used to populate the collection.
	 * @param    object    Options
	 * 
	 * @return   void
	 */
	initialize: function( models, options ) {

		options = options || {};

		this.props   = new Backbone.Model();
		this.filters = options.filters || {};

		// Bind default `change` events to the `props` model.
		this.props.on( 'change',         this._changeFilteredProps, this );

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
	 * @since    2.2
	 *
	 * @access   private
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
	 * @since    2.2
	 *
	 * @access   private
	 *
	 * @param    object    Backbone.Model
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
	 * @since    2.2
	 *
	 * @access   private
	 *
	 * @param    object     Backbone.Model
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
	 * Update the filters when any props is changed.
	 * 
	 * @since    2.2
	 * 
	 * @access   private
	 * 
	 * @param    object    Backbone.Model
	 * 
	 * @return   void
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
	 * @since    2.2
	 * 
	 * @param    object    Instance of editor.model.Movie, the movie to validate
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
	 * @since    2.2
	 *
	 * @param    object    Instance of editor.model.Movie, the movie to validate
	 * @param    object    Options
	 * 
	 * @return   Return itself to allow chaining
	 */
	validate: function( movie, options ) {

		var valid = this.validator( movie ),
		 hasMovie = !! this.get( movie.cid );

		if ( ! valid && hasMovie ) {
			this.remove( movie, options );
		} else if ( valid && ! hasMovie ) {
			this.add( movie, options );
		}

		return this;
	},

	/**
	 * Add or remove all movies from another collection depending on each one's validity.
	 * 
	 * @since    2.2
	 *
	 * @param    object    Instance of editor.model.Movies, the movies collection to validate.
	 * @param    object    Options
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
	 * @since    2.2
	 *
	 * @param    object    Instance of editor.model.Movies, the movies collection to observe.
	 * 
	 * @return   object    Returns itself to allow chaining.
	 */
	observe: function( movies ) {

		this.observers = this.observers || [];
		this.observers.push( movies );

		movies.on( 'add change remove', this._validateHandler, this );
		movies.on( 'reset', this._validateAllHandler, this );

		return this;
	},

	/**
	 * Stop replicating collection change events from another movies collection.
	 * 
	 * @since    2.2
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
	 * 
	 * @since    2.2
	 * 
	 * @access   private
	 *
	 * @param    object    Instance of editor.model.Movie
	 * @param    object    Instance of editor.model.Movies
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
	 * Handle Movie validation. Called exclusively on collection reset.
	 * 
	 * @since    2.2
	 * 
	 * @access   private
	 * 
	 * @param    object    editor.model.Movies
	 * @param    object    Options
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
	 * @since    2.2
	 * 
	 * @param    object    The movies collection to mirror.
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
	 * @since    2.2
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
	 * Retrive more movies from the server for the collection.
	 *
	 * Only works if the collection is mirroring a Query grid.model.Movies collection,
	 * and forwards to its `more` method. This collection class doesn't have
	 * server persistence by itself.
	 * 
	 * @since    2.2
	 * 
	 * @param    object    options
	 * 
	 * @return   Promise
	 */
	more: function( options ) {

		var deferred = $.Deferred(),
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
			if ( this === movies.mirroring )
				deferred.resolveWith( this );
		});

		return deferred.promise();
	},

	/**
	 * Whether there are more movies that haven't been sync'd from the server
	 * that match the collection's query.
	 *
	 * Only works if the collection is mirroring a Query grid.model.Movies collection,
	 * and forwards to its `hasMore` method. This collection class doesn't have
	 * server persistence by itself.
	 * 
	 * @since    2.2
	 *
	 * @return   boolean
	 */
	hasMore: function() {

		return this.mirroring ? this.mirroring.hasMore() : false;
	},

	/**
	 * A custom AJAX-response parser.
	 * 
	 * @since    2.2
	 *
	 * @param    object|array    Raw response
	 * @param    object          xhr
	 * 
	 * @return   array           The array of model attributes to be added to the collection
	 */
	parse: function( resp, xhr ) {

		if ( _.isObject( resp ) && false === resp instanceof editor.model.Movie ) {
			return _.toArray( resp );
		}

		if ( ! _.isArray( resp ) ) {
			resp = [resp];
		}
		
		resp = _.map( resp, function( attrs ) {

			var attributes, id, model, post, meta, details, formatted, _post, _meta, _details, _formatted;

			if ( false === attrs instanceof editor.model.Movie ) {
				attributes = attrs;
			} else {
				attributes = attrs.attributes;
			}

			       id = attributes.post.post_id;
			    model = _.extend( new editor.model.Movie,     { id: id } ),
			     post = _.extend( new editor.model.Post,      { id: id } ),
			     meta = _.extend( new editor.model.Meta,      { id: id } ),
			  details = _.extend( new editor.model.Details,   { id: id } ),
			formatted = _.extend( new editor.model.Formatted, { id: id } );

			     _post = _.pick( attributes.post      || {}, _.keys( post.defaults ) );
			     _meta = _.pick( attributes.meta      || {}, _.keys( meta.defaults ) );
			  _details = _.pick( attributes.details   || {}, _.keys( details.defaults ) );
			_formatted = _.pick( attributes.formatted || {}, _.keys( formatted.defaults ) );

			_.extend( _post, {
				post_date: new Date( _post.post_date )
			} );

			_.extend( _meta, {
				year: new Date( _meta.release_date ).getFullYear()
			} );

			model.set( {
				post:      post.set( _post ),
				meta:      meta.set( _meta ),
				details:   details.set( _details ),
				formatted: formatted.set( _formatted ),
				nonces:    attributes.nonces || {}
			} );

			return grid.model.Movies.all.push( model );
		});

		return resp;
	},

	/**
	 * If the collection is a query, create and mirror a grid.model.Movies
	 * Query collection.
	 * 
	 * @since    2.2
	 *
	 * @access   private
	 * 
	 * @param    boolean    Refresh or don't.
	 * 
	 * @return   void
	 */
	_requery: function( refresh ) {

		var props;
		if ( this.props.get( 'query' ) ) {
			props = this.props.toJSON();
			props.cache = ( true !== refresh );
			var query = grid.model.Query.get( props );
			
			this.mirror( query );
		}
	}
}, {
	/**
	 * A function to compare two movie models in an movies collection.
	 *
	 * Used as the default comparator for instances of editor.model.Movies
	 * and its subclasses. @see editor.model.Movies._changeOrderby().
	 * 
	 * @todo Implement other sorting methods
	 * 
	 * @since    2.2
	 *
	 * @param    object    Backbone.Model
	 * @param    object    Backbone.Model
	 * @param    object    Options
	 * 
	 * @return   int       -1 if the first model should come before the second,
	 *                      0 if they are of the same rank,
	 *                      1 if the first model should come after.
	 */
	comparator: function( a, b, options ) {

		var key = this.props.get( 'orderby' ),
		    order = this.props.get( 'order' ) || 'DESC',
			ac = a.cid,
			bc = b.cid;

		if ( 'date' === key || 'modified' === key ) {
			var _key = 'post_date';
		} else {
			var _key = 'post_date';
		}

		a = a.get( 'post' )[ _key ];
		b = b.get( 'post' )[ _key ];

		if ( 'date' === key || 'modified' === key ) {
			a = a || new Date();
			b = b || new Date();
		}

		// If `options.ties` is set, don't enforce the `cid` tiebreaker.
		if ( options && options.ties ) {
			ac = bc = null;
		}

		return 0;
		return ( 'DESC' === order ) ? compare( a, b, ac, bc ) : compare( b, a, bc, ac );
	},

	/**
	 * @namespace
	 */
	filters: {

		/**
		 * Note that this client-side searching is *not* equivalent
		 * to our server-side searching.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    editor.model.Movie
		 * 
		 * @return   boolean
		 */
		search: function( movie ) {

			if ( ! this.props.get( 'search' ) ) {
				return true;
			}

			return _.any( [ 'title', 'filename', 'description', 'caption', 'name' ], function( key ) {
				var value = movie.get( key );
				return value && -1 !== value.search( this.props.get( 'search' ) );
			}, this );
		},

		/**
		 * 
		 * 
		 * @since    2.2
		 * 
		 * @param    object    editor.model.Movie
		 *
		 * @return   boolean
		 */
		type: function( movie ) {

			var type = this.props.get( 'type' );
			return ! type || -1 !== type.indexOf( movie.get( 'type' ) );
		},

		/**
		 * 
		 * @since    2.2
		 * 
		 * @param    object    editor.model.Movie
		 *
		 * @return   boolean
		 */
		uploadedTo: function( movie ) {

			var uploadedTo = this.props.get( 'uploadedTo' );
			if ( _.isUndefined( uploadedTo ) ) {
				return true;
			}

			return uploadedTo === movie.get( 'uploadedTo' );
		},

		/**
		 * 
		 * @since    2.2
		 * 
		 * @param    object    editor.model.Movie
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
 * A collection of all movies that have been fetched from the server.
 *
 * @since    2.2
 */
grid.model.Movies.all = new grid.model.Movies();

/**
 * Shorthand for creating a new Movies Query..
 *
 * @since    2.2
 *
 * @param    object    props
 * 
 * @return   grid.model.Movies
 */
grid.query = function( props ) {
	return new grid.model.Movies( null, {
		props: _.extend( _.defaults( props || {}, { orderby: 'date' } ), { query: true } )
	} );
};

/**
 * grid.model.Query
 *
 * A collection of movies that match the supplied query arguments.
 *
 * Note: Do NOT change this.args after the query has been initialized.
 *       Things will break.
 *
 * @since    2.2
 */
grid.model.Query = grid.model.Movies.extend({

	/**
	 * Initialize the Model.
	 * 
	 * @since    2.2
	 * 
	 * @param    array     Array of initial models to populate the collection.
	 * @param    object    Options
	 * 
	 * @return   void
	 */
	initialize: function( models, options ) {

		var allowed;

		options = options || {};
		grid.model.Movies.prototype.initialize.apply( this, arguments );

		this.args     = options.args;
		this._hasMore = true;
		this.created  = new Date();

		this.filters.order = function( movie ) {
			var orderby = this.props.get( 'orderby' ),
				order = this.props.get( 'order' );

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
	 * that match the collection's query. @see this.more()
	 * 
	 * @since    2.2
	 * 
	 * @return   boolean
	 */
	hasMore: function() {

		return this._hasMore;
	},

	/**
	 * Fetch more movies from the server for the collection.
	 * 
	 * @since    2.2
	 * 
	 * @param    object    options
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
			return $.Deferred().resolveWith( this ).promise();
		}

		options = options || {};
		options.remove = false;

		return this._more = this.fetch( options ).done( function( resp ) {
			if ( ! _.isArray(  ) ) {
				resp = _.toArray( resp );
			}
			// Cleverness: response is empty or has less results than requested: we've reached the end.
			if ( _.isEmpty( resp ) || -1 === this.args.posts_per_page || resp.length < this.args.posts_per_page ) {
				query._hasMore = false;
			}
		});
	},

	/**
	 * Overrides Backbone.Collection.sync and grid.model.Movies.sync
	 * 
	 * @since    2.2
	 * 
	 * @param    string    method
	 * @param    object    Backbone.Model
	 * @param    object    Options
	 * 
	 * @return   Promise
	 */
	sync: function( method, model, options ) {
		var args, fallback;

		// Overload the read method
		if ( 'read' === method ) {

			options = options || {};
			options.context = this;
			options.data = _.extend( options.data || {}, {
				action:  'wpmoly_query_movies',
				post_id: media.model.settings.post.id
			});

			// Clone the args so manipulation is non-destructive.
			args = _.clone( this.args );

			// Determine which page to query.
			if ( -1 !== args.posts_per_page ) {
				args.paged = Math.floor( this.length / args.posts_per_page ) + 1;
			}

			options.data.query = args;
			return wp.ajax.send( options );

		// Otherwise, fall back to Backbone.sync()
		} else {
			// Call grid.model.Movies.sync or Backbone.sync
			fallback = grid.model.Movies.prototype.sync ? grid.model.Movies.prototype : Backbone;
			return fallback.sync.apply( this, arguments );
		}
	}
}, {

	/**
	 * @readonly
	 */
	defaultProps: {
		orderby: 'date',
		order:   'DESC'
	},

	/**
	 * @readonly
	 */
	defaultArgs: {
		posts_per_page: 16
	},

	/**
	 * @readonly
	 */
	orderby: {
		allowed:  [ 'name', 'author', 'date', 'title', 'modified', 'uploadedTo', 'id', 'post__in', 'menuOrder' ],
		/**
		 * A map of JavaScript orderby values to their WP_Query equivalents.
		 * @type {Object}
		 */
		valuemap: {
			'id':         'ID',
			'uploadedTo': 'parent',
			'menuOrder':  'menu_order ID'
		}
	},

	/**
	 * A map of JavaScript query properties to their WP_Query equivalents.
	 *
	 * @readonly
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
	 * Creates and returns a Movies Query collection given the properties.
	 * 
	 * Caches query objects and reuses where possible.
	 * 
	 * @since    2.2
	 *
	 * @return   A new Movies Query collection.
	 */
	get: (function(){

		/**
		 * @static
		 * @type Array
		 */
		var queries = [];

		/**
		 * Run the Query.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    props
		 * @param    object    props.cache
		 * @param    object    props.order
		 * @param    object    props.orderby
		 * @param    object    props.include
		 * @param    object    props.exclude
		 * @param    object    props.s
		 * @param    object    props.post_mime_type
		 * @param    object    props.posts_per_page
		 * @param    object    props.menu_order
		 * @param    object    props.post_parent
		 * @param    object    props.post_status
		 * @param    object    options
		 * 
		 * @return   grid.model.Query
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
				query = new grid.model.Query( [], _.extend( options || {}, {
					props: props,
					args:  args
				} ) );
				queries.push( query );
			}

			return query;
		};
	}())
});