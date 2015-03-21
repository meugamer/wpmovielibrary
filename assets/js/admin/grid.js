
( function( $, _, Backbone, wp, wpmoly ) {

	/**
	 * wpmoly.importer
	 * 
	 * The base object for the Movie Importer Frame.
	 * 
	 * @since    2.2
	 */
	_.extend( wpmoly.importer, {
		controller: {},
		frame:      {},
		model:      {},
		view:       {}
	} );

	/**
	 * wpmoly.grid
	 * 
	 * The base object for the Movie Grid.
	 * 
	 * @since    2.2
	 */
	_.extend( wpmoly.grid, {
		controller: {},
		frame:      {},
		model:      {},
		view:       {}
	},
	{
		/**
		 * Initialize the Grid.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		run: function() {

			$( '.wrap > *' ).not( 'h2' ).hide();

			var mode = wpmoly.parseSearchQuery().mode;

			this.frame = new this.view.GridFrame({ mode: mode });
		}
	} );

	/**
	 * wpmoly.editor
	 * 
	 * The base object for the Movie Editor. Used in single edit and grid
	 * view to manipulate movie objects and movies collections.
	 * 
	 * @since    2.2
	 */
	_.extend( wpmoly.editor, {
		controller: {},
		frame:      {},
		models:     {},
		views:      {},
		model:      {},
		view:       {}
	},
	{
		/**
		 * Initialize the Grid.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		run: function() {

			var mode = wpmoly.parseSearchQuery().mode;

			// If we're in list view, use the existing HTML Table to
			// fill the movies collection
			if ( 'list' == mode ) {

				var movies = [];
				_.each( document.querySelectorAll( '#the-list tr' ), function( movie ) {
					var id = movie.id.replace( 'post-', '' );
					movies.push( _.extend( new this.model.Movie, { id: id } ) );
				} );

				this.models.movies = new this.model.Movies;
				this.models.movies.add( movies );
			}
			// Not in list view, use the library
			else {
				this.models.movies = wpmoly.grid.frame.state().get( 'library' );
			}

			this.views.movies = new this.view.Movies;
		}
	} );

}( jQuery, _, Backbone, wp, wpmoly ) );
