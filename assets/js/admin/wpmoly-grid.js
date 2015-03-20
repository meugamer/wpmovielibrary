
( function( $, _, Backbone, wp, wpmoly ) {

	var search = wpmoly.parseSearchQuery(),
	      mode = search.mode || 'grid'
	  importer = wpmoly.importer || {};

	var grid = wpmoly.grid = function() {

		$( '.wrap > *' ).not( 'h2' ).hide();

		grid.frame = new grid.View.GridFrame( { mode: mode } );
	};

	var editor = wpmoly.editor = function() {

		if ( 'list' == mode ) {

			var movies = [];
			_.each( document.querySelectorAll( '#the-list tr' ), function( movie ) {
				var id = movie.id.replace( 'post-', '' );
				movies.push( _.extend( new editor.Model.Movie, { id: id } ) );
			} );

			editor.models.movies = new editor.Model.Movies;
			editor.models.movies.add( movies );
		} else {
			editor.models.movies = grid.frame.state().get( 'library' );
		}

		editor.views.movies = new editor.View.Movies;
	};

	_.extend( editor  , { controller: {}, models: {}, views: {}, Model: {}, View: {} } );
	_.extend( grid    , { controller: {}, models: {}, views: {}, Model: {}, View: {} } );

}( jQuery, _, Backbone, wp, wpmoly ) );
