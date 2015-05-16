
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
