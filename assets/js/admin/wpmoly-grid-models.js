
( function( $, _, Backbone, wp, wpmoly ) {

	var grid = wpmoly.grid = function() {

		$( '.wrap > *' ).not( 'h2' ).hide();

		var search = wpmoly.parseSearchQuery(),
		      mode = search.mode || 'grid';

		grid.frame = new grid.View.Frame( { mode: mode } );
	};

	_.extend( grid, { controller: {}, models: {}, views: {}, Model: {}, View: {} } );

	grid.controller.State = Backbone.Model.extend({

		_previousMode: '',

		defaults: {
			mode: 'grid',
			modes: [ 'grid', 'exerpt', 'list', 'import' ]
		},

		setMode: function( mode ) {

			if ( mode == this.get( 'mode' ) || ! _.isDefined( this.defaults.modes[ mode ] ) ) {
				return false;
			}

			this._previousMode = this.get( 'mode' );
			this.set( { mode: mode } );
		}

		
	});

}( jQuery, _, Backbone, wp, wpmoly ) );
