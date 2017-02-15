
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	grids = wpmoly.grids = {

		runned: false,

		views: {},
 
		run: function() {

			var grids = document.querySelectorAll( '[data-grid]' );
			_.each( grids, function( grid ) {

				var post_id = parseInt( grid.dataset.grid ),
				 controller = new wpmoly.controller.Grid(
					{
						post_id : post_id
					},
					window[ '_wpmoly_grid_' + post_id ]
				);

				// Temporarily disable grid customs menu
				controller.settings.set( { customs_control: false }, { silent: true } );

				var view = new wpmoly.view.Grid.Grid({
					el         : grid,
					controller : controller
				});

				wpmoly.grids.views[ post_id ] = view;
			} );
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.grids );
