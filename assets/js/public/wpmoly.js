
wpmoly = {};

( function( $, _, Backbone, wp ) {

	_.extend( wpmoly, {

		controller: {},

		model: {},

		view: {},

		grid: {},

		headbox: {},

		widgets: {}
	},
	{
		compare: function( a, b, ac, bc ) {

			if ( _.isEqual( a, b ) ) {
				return ac === bc ? 0 : (ac > bc ? -1 : 1);
			} else {
				return a > b ? -1 : 1;
			}
		},

		run: function() {

			$( '.hide-if-js' ).hide();
			$( '.hide-if-no-js' ).removeClass( 'hide-if-no-js' );

			this.grid.run();
			this.widgets.run();
		}
	} );

	_.extend( wpmoly.grid, {

		model: {},

		view: {},

		frames: []
	},
	{
		run: function() {

			var grids = document.querySelectorAll( 'div.wpmoly.movies.grid' );
			if ( ! grids.length ) {
				return;
			}

			_.map( grids, function( grid ) {

				grid.id = _.uniqueId( grid.id + '-' );

				return this.frames.push(
					new this.view.Grid({
						el: '#' + grid.id,
						//scroll: true
					})
				);
			}, this );
		}
	} );

	_.extend( wpmoly.widgets,{

		run: function() {

			$( 'select.wpmoly.list' ).change( function() {
				if ( this.options[ this.selectedIndex ].value.length > 0 ) {
					location.href = this.options[ this.selectedIndex ].value;
				}
			} );
		}
	} );

}( jQuery, _, Backbone, wp ) );

jQuery( document ).ready( function() {
	wpmoly.run();
} );
