
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	grids = wpmoly.grids = {

		runned: false,

		views: {},
 
		run: function() {

			var grids = document.querySelectorAll( '[data-grid]' );
			_.each( grids, function( grid ) {

				var controller = new wpmoly.controller.Grid(
					{
						post_id  : grid.dataset.grid
					},
					window[ '_wpmoly_grid_' + grid.dataset.grid ]
				);

				var view = new wpmoly.view.Grid.Grid({
					el         : grid,
					controller : controller
				});

				wpmoly.grids.views[ grid.dataset.grid ] = view;
			} );
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.grids );
