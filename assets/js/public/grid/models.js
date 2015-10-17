
var grid = wpmoly.grid,
   media = wp.media,
       $ = Backbone.$;

_.extend( grid.model, {

	/**
	* WPMOLY Backbone Movie Model
	* 
	* Stores a movie's post data, metadata and details
	* 
	* @since    2.1.5
	*/
	Movie: Backbone.Model.extend({

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
	})
},
{
	Movies: Backbone.Collection.extend({

		query_args: [ 'number', 'orderby', 'order', 'paged', 'letter', 'category', 'tag', 'collection', 'actor', 'genre', 'meta', 'detail', 'value', 'incoming', 'unrated' ],

		// More movies to load?
		has_more: true,

		/**
		 * Initialize the Model
		 * 
		 * @since    2.1.5
		 *
		 * @param    object    Models
		 * @param    object    Options
		 * 
		 * @return   void
		 */
		initialize: function( models, options ) {

			var options = options || {};
			this.controller = options.controller || {};
			this.pages      = this.controller.pages || new Backbone.Model({
				current: 0,
				total:   0,
				prev:    0,
				next:    0
			});
			this.pages.on( 'change', function() {
				this.has_more = _.isEqual( this.pages.current, this.pages.total );
			}, this );
		},

		/**
		 * Query movies
		 * 
		 * @since    2.1.5
		 * 
		 * @return   XHR|boolean
		 */
		query: function( options ) {

			return this.sync( 'read', {}, { data: options } );
		},

		/**
		 * Query previous page
		 * 
		 * @since    2.1.5
		 * 
		 * @return   XHR|boolean
		 */
		prev: function() {

			var page = Math.max( 0, this.pages.get( 'current' ) - 1 );

			return this.query( { paged: page } );
		},

		/**
		 * Query next page
		 * 
		 * @since    2.1.5
		 * 
		 * @return   XHR|boolean
		 */
		next: function() {

			//console.log( this.pages.get( 'next' ) );
			/*if ( ! this.pages.get( 'next' ) ) {
				return false;
			}*/
			var page = Math.min( this.pages.get( 'current' ) + 1, this.pages.get( 'total' ) );

			return this.query( { paged: page } );
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
		/*more: function( options ) {

			if ( ! this.has_more ) {
				return false;
			}

			var data = this.props.toJSON();
			    data.paged = this.pages.get( 'next' );

			return this.query( { data: data } );
		},*/

		/**
		 * Overrides Backbone.Collection.sync
		 * Overrides grid.model.Movies.sync
		 * 
		 * Implement a caching system to avoid running the same query
		 * twice.
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

			// Overload the read method so Attachment.fetch() functions correctly.
			if ( 'read' === method ) {

				options = options || {};
				options.context = this;

				var query = {};
				_.each( this.query_args, function( arg ) {
					query[ arg ] = this.controller.query.get( arg ) || null;
				}, this );
				_.extend( query, options.data || {} );
				console.log( query );

				// check if there's a cached result for the query
				var cache_id = this.cache.exists( query );
				if ( cache_id ) {
					var data = _.clone( this.cache.get( cache_id ) );
					return this.success( data );
				}

				options.data = {
					action:  'wpmoly_query_movies',
					query: query
					//nonce: ''
				};

				options.success = function( data ) {

					// cache the request result
					this.cache.set( _.uniqueId( 'q' ), query, data );

					// actually handle the result
					this.success( data );
				};

				return wp.ajax.send( options );

			// Otherwise, fall back to Backbone.sync()
			} else {
				return Backbone.sync.apply( this, arguments );
			}
		},

		/**
		 * Custom success function for AJAX.
		 * 
		 * Handle the AJAX response independently from the AJAX call 
		 * per-se. Useful to provide caching.
		 * 
		 * @since    2.1.5
		 *
		 * @param    object|array    The raw response Object/Array.
		 * 
		 * @return   void
		 */
		success: function( data ) {

			var movies = this.parse( data );
			if ( ! this.controller.get( 'scroll' ) ) {
				this.reset();
			}

			return this.add( movies );
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
		parse: function( data, xhr ) {

			// Set pagination data
			if ( ! _.isUndefined( data.pages ) ) {

				var pages = {
					current: data.pages.current,
					prev:    Math.max( 0, data.pages.current - 1 ),
					next:    Math.min( data.pages.total, data.pages.current + 1 ),
					total:   data.pages.total,
					posts:   data.pages.posts
				};

				this.pages.set( pages );
				/*if ( this.mirroring ) {
					this.mirroring.pages.set( pages );
				}*/

				delete data.pages;
			}

			if ( _.isObject( data ) && false === data instanceof grid.model.Movie ) {
				return _.toArray( data );
			}

			return data;
		},

		/**
		 * Query Caching object.
		 * 
		 * Store the queries parameters and results to avoid running a
		 * query twice.
		 * 
		 * @since    2.1.5
		 */
		cache: {

			// Cached query parameters
			queries: {},

			// Cached query results
			results: {},

			/**
			 * Add a query to the cache.
			 * 
			 * @since    2.1.5
			 * 
			 * @param    int       Cache ID
			 * @param    object    Query parameters
			 * @param    object    Query results
			 * 
			 * @return   string    Cache ID
			 */
			set: function( id, query, results ) {

				if ( ! this.exists( query ) && ! this.queries.hasOwnProperty( id ) ) {
					this.queries[ id ] = _.clone( query );
					this.results[ id ] = _.clone( results );
				}

				return id;
			},

			/**
			 * Retrieve the cached data corresponding to a cache ID,
			 * empty object if no matching result could be found.
			 * 
			 * @since    2.1.5
			 * 
			 * @param    int    Cache ID
			 * 
			 * @return   object
			 */
			get: function( id ) {

				return this.queries.hasOwnProperty( id ) ? this.results[ id ] : {};
			},

			/**
			 * Check if a query matching the submitted parameters
			 * has been cached already and return its ID, false
			 * otherwise.
			 * 
			 * @since    2.1.5
			 * 
			 * @param    object    Query parameters
			 * 
			 * @return   string    Cache ID
			 */
			exists: function( query ) {

				var id = false;
				_.each( this.queries, function( value, key ) {
					if ( _.isEqual( value, query ) ) {
						id = key;
					}
				}, this );

				return id;
			}
		}
	})
} );

grid.model.Movies.all = new grid.model.Movies();

/* Required files:
 * - ./models/movie.js
 * - ./models/movies.js
 * - ./models/moviequery.js
 */
