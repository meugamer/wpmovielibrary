
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	grids = wpmoly.grids = {

		runned: false,

		views: {},

		_run: function() {

			var grids = document.querySelectorAll( '[data-grid]' );
			_.each( grids, function( grid ) {

				var $grid = wpmoly.$( grid ),
				    $json = $grid.find( '.grid-json' ),
				 $content = $grid.find( '.grid-content-inner' ),
				 settings = $json.text() || '{}';

				var post_id = parseInt( grid.dataset.grid ),
				 controller = new wpmoly.controller.Grid( { post_id : post_id }, JSON.parse( settings ) );

				// Temporarily disable grid customs menu
				controller.settings.set( { customs_control: false }, { silent: true } );

				var view = new wpmoly.view.Grid.Grid({
					el         : grid,
					controller : controller,
					content    : $content
				});

				wpmoly.grids.views[ post_id ] = view;
			} );
		},

		run: function() {

			if ( ! wp.api ) {
				return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
			}

			wp.api.loadPromise.done( this._run );
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.grids );
