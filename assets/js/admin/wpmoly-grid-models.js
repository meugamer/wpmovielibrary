
( function( $, _, Backbone, wp, wpmoly ) {

	var editor = wpmoly.editor,
	      grid = wpmoly.grid,
	     media = wp.media;

	compare = function( a, b, ac, bc ) {

		if ( _.isEqual( a, b ) ) {
			return ac === bc ? 0 : (ac > bc ? -1 : 1);
		} else {
			return a > b ? -1 : 1;
		}
	};

	/**
	 * grid.Model.Movies
	 *
	 * A collection of movies.
	 *
	 * This Model is a complete copy of editor.Model.Movies, adapted
	 * to movies.
	 *
	 * @class
	 * @augments Backbone.Collection
	 *
	 * @param {array}  [models]                Models to initialize with the collection.
	 * @param {object} [options]               Options hash for the collection.
	 * @param {string} [options.props]         Options hash for the initial query properties.
	 * @param {string} [options.props.order]   Initial order (ASC or DESC) for the collection.
	 * @param {string} [options.props.orderby] Initial attribute key to order the collection by.
	 * @param {string} [options.props.query]   Whether the collection is linked to a movies query.
	 * @param {string} [options.observe]
	 * @param {string} [options.filters]
	 *
	 */
	grid.Model.Movies = Backbone.Collection.extend({

		model: editor.Model.Movie,

		/**
		 * @param {Array} [models=[]] Array of models used to populate the collection.
		 * @param {Object} [options={}]
		 */
		initialize: function( models, options ) {

			options = options || {};

			this.props   = new Backbone.Model();
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
		 * @access private
		 */
		_changeOrder: function() {

			if ( this.comparator ) {
				this.sort();
			}
		},
		/**
		 * Set the default comparator only when the `orderby` property is set.
		 *
		 * @access private
		 *
		 * @param {Backbone.Model} model
		 * @param {string} orderby
		 */
		_changeOrderby: function( model, orderby ) {

			// If a different comparator is defined, bail.
			if ( this.comparator && this.comparator !== grid.Model.Movies.comparator ) {
				return;
			}

			if ( orderby && 'post__in' !== orderby ) {
				this.comparator = grid.Model.Movies.comparator;
			} else {
				delete this.comparator;
			}
		},
		/**
		 * If the `query` property is set to true, query the server using
		 * the `props` values, and sync the results to this collection.
		 *
		 * @access private
		 *
		 * @param {Backbone.Model} model
		 * @param {Boolean} query
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
		 * @access private
		 *
		 * @param {Backbone.Model} model
		 */
		_changeFilteredProps: function( model ) {

			// If this is a query, updating the collection will be handled by
			// `this._requery()`.
			if ( this.props.get( 'query' ) ) {
				return;
			}

			var changed = _.chain( model.changed ).map( function( t, prop ) {
				var filter = grid.Model.Movies.filters[ prop ],
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
				this._source = new grid.Model.Movies( this.models );
			}

			this.reset( this._source.filter( this.validator, this ) );
		},

		validateDestroyed: false,

		/**
		 * Checks whether an movie is valid.
		 *
		 * @param {editor.Model.Movie} movie
		 * @returns {Boolean}
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
		 * @param {editor.Model.Movie} movie
		 * @param {Object} options
		 * @returns {editor.Model.Movies} Returns itself to allow chaining
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
		 * @param {editor.Model.Movies} movies
		 * @param {object} [options={}]
		 *
		 * @fires editor.Model.Movies#reset
		 *
		 * @returns {editor.Model.Movies} Returns itself to allow chaining
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
		 * @param    object    Instance of editor.Model.Movies, the movies collection to observe.
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
		 * @param {editor.Model.Movies} The movies collection to stop observing.
		 * @returns {editor.Model.Movies} Returns itself to allow chaining
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
		 * @access private
		 *
		 * @param    object    Instance of editor.Model.Movie
		 * @param    object    Instance of editor.Model.Movies
		 * @param    object    options
		 *
		 * @return   object    Returns itself to allow chaining
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
		 * @access private
		 *
		 * @param {editor.Model.Movies} movies
		 * @param {Object} options
		 * @returns {editor.Model.Movies} Returns itself to allow chaining
		 */
		_validateAllHandler: function( movies, options ) {

			return this.validateAll( movies, options );
		},

		/**
		 * Start mirroring another movies collection, clearing out any models already
		 * in the collection.
		 *
		 * @param {editor.Model.Movies} The movies collection to mirror.
		 * @returns {editor.Model.Movies} Returns itself to allow chaining
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
		 * Only works if the collection is mirroring a Query grid.Model.Movies collection,
		 * and forwards to its `more` method. This collection class doesn't have
		 * server persistence by itself.
		 *
		 * @param {object} options
		 * @returns {Promise}
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
		 * Only works if the collection is mirroring a Query grid.Model.Movies collection,
		 * and forwards to its `hasMore` method. This collection class doesn't have
		 * server persistence by itself.
		 *
		 * @returns {boolean}
		 */
		hasMore: function() {

			return this.mirroring ? this.mirroring.hasMore() : false;
		},

		/**
		 * A custom AJAX-response parser.
		 *
		 * See trac ticket #24753
		 *
		 * @param {Object|Array} resp The raw response Object/Array.
		 * @param {Object} xhr
		 * @returns {Array} The array of model attributes to be added to the collection
		 */
		parse: function( resp, xhr ) {

			if ( _.isObject( resp ) && false === resp instanceof editor.Model.Movie ) {
				return _.toArray( resp );
			}

			if ( ! _.isArray( resp ) ) {
				resp = [resp];
			}
			
			resp = _.map( resp, function( attrs, id ) {

				var attributes, id, model, post, meta, details;

				if ( false === attrs instanceof editor.Model.Movie ) {
					attributes = attrs;
				} else {
					attributes = attrs.attributes;
				}

				     id = attributes.post.post_id;
				  model = _.extend( new editor.Model.Movie,   { id: id } ),
				   post = _.extend( new editor.Model.Post,    { id: id } ),
				   meta = _.extend( new editor.Model.Meta,    { id: id } ),
				details = _.extend( new editor.Model.Details, { id: id } );

				model.set( {
					post:    post.set(    _.pick( attributes.post    || {}, _.keys( post.defaults ) ) ),
					meta:    meta.set(    _.pick( attributes.meta    || {}, _.keys( meta.defaults ) ) ),
					details: details.set( _.pick( attributes.details || {}, _.keys( details.defaults ) ) ),
					nonces:  attributes.nonces || {}
				} );

				return grid.Model.Movies.all.push( model );
			});

			return resp;
		},

		/**
		 * If the collection is a query, create and mirror a grid.Model.Movies Query collection.
		 *
		 * @access private
		 */
		_requery: function( refresh ) {

			var props;
			if ( this.props.get( 'query' ) ) {
				props = this.props.toJSON();
				props.cache = ( true !== refresh );
				var query = grid.Model.Query.get( props );
				
				this.mirror( query );
			}
		},

		/**
		 * If this collection is sorted by `menuOrder`, recalculates and saves
		 * the menu order to the database.
		 *
		 * @returns {undefined|Promise}
		 */
		saveMenuOrder: function() {
			if ( 'menuOrder' !== this.props.get( 'orderby' ) ) {
				return;
			}

			// Removes any uploading movies, updates each movie's
			// menu order, and returns an object with an { id: menuOrder }
			// mapping to pass to the request.
			var movies = this.chain().filter( function( movie ) {
				return ! _.isUndefined( movie.id );
			}).map( function( movie, index ) {
				// Indices start at 1.
				index = index + 1;
				movie.set( 'menuOrder', index );
				return [ movie.id, index ];
			}).object().value();

			if ( _.isEmpty( movies ) ) {
				return;
			}

			return media.post( 'save-movie-order', {
				nonce:       media.model.settings.post.nonce,
				post_id:     media.model.settings.post.id,
				movies: movies
			});
		}
	}, {
		/**
		 * A function to compare two movie models in an movies collection.
		 *
		 * Used as the default comparator for instances of editor.Model.Movies
		 * and its subclasses. @see editor.Model.Movies._changeOrderby().
		 *
		 * @static
		 *
		 * @param {Backbone.Model} a
		 * @param {Backbone.Model} b
		 * @param {Object} options
		 * @returns {Number} -1 if the first model should come before the second,
		 *    0 if they are of the same rank and
		 *    1 if the first model should come after.
		 */
		comparator: function( a, b, options ) {

			var key = this.props.get( 'orderby' ),
			  order = this.props.get( 'order' ) || 'DESC',
			     ac = a.cid,
			     bc = b.cid;

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

			return ( 'DESC' === order ) ? compare( a, b, ac, bc ) : compare( b, a, bc, ac );
		},

		/**
		 * @namespace
		 */
		filters: {

			/**
			 * @static
			 * Note that this client-side searching is *not* equivalent
			 * to our server-side searching.
			 *
			 * @param {editor.Model.Movie} movie
			 *
			 * @this grid.Model.Movies
			 *
			 * @returns {Boolean}
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
			 * @static
			 * @param {editor.Model.Movie} movie
			 *
			 * @this grid.Model.Movies
			 *
			 * @returns {Boolean}
			 */
			type: function( movie ) {

				var type = this.props.get( 'type' );
				return ! type || -1 !== type.indexOf( movie.get( 'type' ) );
			},

			/**
			 * @static
			 * @param {editor.Model.Movie} movie
			 *
			 * @this grid.Model.Movies
			 *
			 * @returns {Boolean}
			 */
			uploadedTo: function( movie ) {

				var uploadedTo = this.props.get( 'uploadedTo' );
				if ( _.isUndefined( uploadedTo ) ) {
					return true;
				}

				return uploadedTo === movie.get( 'uploadedTo' );
			},

			/**
			 * @static
			 * @param {editor.Model.Movie} movie
			 *
			 * @this grid.Model.Movies
			 *
			 * @returns {Boolean}
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
	 * A collection of all attachments that have been fetched from the server.
	 *
	 * @static
	 * @member {grid.Model.Movies}
	 */
	grid.Model.Movies.all = new grid.Model.Movies();

	/**
	 * Shorthand for creating a new Movies Query.
	 *
	 * @param {object} [props]
	 * @returns {grid.Model.Movies}
	 */
	grid.query = function( props ) {
		return new grid.Model.Movies( null, {
			props: _.extend( _.defaults( props || {}, { orderby: 'date' } ), { query: true } )
		} );
	};

	/**
	 * grid.Model.Query
	 *
	 * A collection of movies that match the supplied query arguments.
	 *
	 * Note: Do NOT change this.args after the query has been initialized.
	 *       Things will break.
	 *
	 * @class
	 * @augments wp.media.model.Movies
	 * @augments Backbone.Collection
	 *
	 * @param {array}  [models]                      Models to initialize with the collection.
	 * @param {object} [options]                     Options hash.
	 * @param {object} [options.args]                Movies query arguments.
	 * @param {object} [options.args.posts_per_page]
	 */
	grid.Model.Query = grid.Model.Movies.extend({

		/**
		 * @global wp.Uploader
		 *
		 * @param {array}  [models=[]]  Array of initial models to populate the collection.
		 * @param {object} [options={}]
		 */
		initialize: function( models, options ) {

			var allowed;

			options = options || {};
			grid.Model.Movies.prototype.initialize.apply( this, arguments );

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

			// Observe the central `wp.Uploader.queue` collection to watch for
			// new matches for the query.
			//
			// Only observe when a limited number of query args are set. There
			// are no filters for other properties, so observing will result in
			// false positives in those queries.
			/*allowed = [ 's', 'order', 'orderby', 'posts_per_page', 'post_mime_type', 'post_parent' ];
			if ( wp.Uploader && _( this.args ).chain().keys().difference( allowed ).isEmpty().value() ) {
				this.observe( wp.Uploader.queue );
			}*/
		},
		/**
		 * Whether there are more movies that haven't been sync'd from the server
		 * that match the collection's query.
		 *
		 * @returns {boolean}
		 */
		hasMore: function() {

			return this._hasMore;
		},

		/**
		 * Fetch more movies from the server for the collection.
		 *
		 * @param   {object}  [options={}]
		 * @returns {Promise}
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
				if ( _.isEmpty( resp ) || -1 === this.args.posts_per_page || resp.length < this.args.posts_per_page ) {
					query._hasMore = false;
				}
			});
		},

		/**
		 * Overrides Backbone.Collection.sync
		 * Overrides grid.Model.Movies.sync
		 *
		 * @param {String} method
		 * @param {Backbone.Model} model
		 * @param {Object} [options={}]
		 * @returns {Promise}
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
				/**
				 * Call grid.Model.Movies.sync or Backbone.sync
				 */
				fallback = grid.Model.Movies.prototype.sync ? grid.Model.Movies.prototype : Backbone;
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
			posts_per_page: 40
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
			'search':    's',
			'type':      'post_mime_type',
			'perPage':   'posts_per_page',
			'menuOrder': 'menu_order',
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
		 * @static
		 * @method
		 *
		 * @param {object} [props]
		 * @param {Object} [props.cache=true]   Whether to use the query cache or not.
		 * @param {Object} [props.order]
		 * @param {Object} [props.orderby]
		 * @param {Object} [props.include]
		 * @param {Object} [props.exclude]
		 * @param {Object} [props.s]
		 * @param {Object} [props.post_mime_type]
		 * @param {Object} [props.posts_per_page]
		 * @param {Object} [props.menu_order]
		 * @param {Object} [props.post_parent]
		 * @param {Object} [props.post_status]
		 * @param {Object} [options]
		 *
		 * @returns {grid.Model.Query} A new Movies Query collection.
		 */
		get: (function(){
			/**
			 * @static
			 * @type Array
			 */
			var queries = [];

			/**
			 * @returns {Query}
			 */
			return function( props, options ) {

				var args = {},
				 orderby = grid.Model.Query.orderby,
				defaults = grid.Model.Query.defaultProps,
				   query,
				   cache = !! props.cache || _.isUndefined( props.cache );

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

					args[ grid.Model.Query.propmap[ prop ] || prop ] = value;
				});

				// Fill any other default query args.
				_.defaults( args, grid.Model.Query.defaultArgs );

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
					query = new grid.Model.Query( [], _.extend( options || {}, {
						props: props,
						args:  args
					} ) );
					queries.push( query );
				}

				return query;
			};
		}())
	});

	grid.controller.Library = Backbone.Model.extend({

		/**
		 * If a library isn't provided, query all media items.
		 * If a selection instance isn't provided, create one.
		 *
		 * @since 3.5.0
		 */
		initialize: function() {

			if ( ! this.get( 'library' ) ) {
				this.set( 'library', grid.query() );
			}

			//this.on( 'ready', this.ready, this );
		},

		ready: function() {

			//console.log( 'Go!' );
			
		}

	});

}( jQuery, _, Backbone, wp, wpmoly ) );
