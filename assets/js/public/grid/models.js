
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

		args: {
			posts_per_page: 4,
			paged:          1
		},

		initialize: function( models, options ) {

			var options = options || {};
			this.controller = options.controller || {};

			this.pages = new Backbone.Model({
				current: 0,
				total:   0,
				prev:    0,
				next:    0
			});
		},

		query: function() {

			this.sync( 'read', {}, { data: this.props.toJSON() } );
		},

		sync: function( method, model, options ) {

			// Overload the read method so Attachment.fetch() functions correctly.
			if ( 'read' === method ) {

				options = options || {};
				options.context = this;

				var args = _.extend( this.args, options.data || {} );
				options.data = {
					action:  'wpmoly_query_movies'
				};
				options.data.query = args;

				options.success = function( resp ) {
					var movies = this.parse( resp );
					if ( ! this.controller.get( 'scroll' ) ) {
						this.reset();
					}
					this.add( movies );
				};

				return wp.media.ajax( options );

			// Otherwise, fall back to Backbone.sync()
			} else {
				return Backbone.sync.apply( this, arguments );
			}
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
	})
} );

grid.model.Movies.all = new grid.model.Movies();

/* Required files:
 * - ./models/movie.js
 * - ./models/movies.js
 * - ./models/moviequery.js
 */
