
( function( $, _, Backbone, wp, wpmoly ) {

	var grid = wpmoly.grid = function() {

		$( '.wrap > *' ).not( 'h2' ).hide();

		var search = wpmoly.parseSearchQuery(),
		      mode = search.mode || 'grid';

		grid.frame = new grid.View.Frame( { mode: mode } );
	};

	_.extend( grid, { controller: {}, models: {}, views: {}, Model: {}, View: {} } );

	

}( jQuery, _, Backbone, wp, wpmoly ) );
