
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
			var orderby = this.props.get( 'orderby' ),
			      order = this.props.get( 'order' );

			if ( ! this.comparator ) {
				return true;
			}

			// We want any items that can be placed before the last
			// item in the set. If we add any items after the last
			// item, then we can't guarantee the set is complete.
			if ( this.length ) {
				return 1 !== this.comparator( movie, this.last(), { ties: true } );

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
			var calc = Math.round( this.length / args.posts_per_page ) + 1;
			if ( ! _.isUndefined( options.paged ) ) {
				args.paged = options.paged;
				delete options.paged;
			} else if ( ( ! _.isUndefined( args.paged ) && args.paged <= calc ) ||
			            (   _.isUndefined( args.paged ) && -1 !== args.posts_per_page ) ) {
				args.paged = calc;
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
			if ( 'DESC' !== props.order && 'ASC' !== props.order && 'RANDOM' !== props.order ) {
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
grid.query = function( props, controller ) {
	return new grid.model.Movies( null, {
		props: _.extend( _.defaults( props || {}, { orderby: 'date' } ), { query: true } )
	}, controller );
};
