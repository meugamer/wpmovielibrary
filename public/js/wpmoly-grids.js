
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	grids = wpmoly.grids = {

		/**
		 * List of Grid controllers.
		 *
		 * @var    array
		 */
		controllers : [],

		/**
		 * Create a new Grid controller from an HTML Node.
		 *
		 * Use 'data-grid' attribute and '.grid-json' subNode to build
		 * a Backbone-powered Grid.
		 *
		 * @since    3.0
		 *
		 * @param    Element    grid Grid Element.
		 *
		 * @return   object     New Grid controller.
		 */
		createGrid : function( grid ) {

			var json = grid.querySelector( '.grid-json' ),
			 content = grid.querySelector( '.grid-content-inner' ),
			 post_id = grid.getAttribute( 'data-grid' );

			var controller = new wpmoly.controller.Grid(
				{
					post_id : parseInt( post_id )
				},
				JSON.parse( json.textContent || '{}' )
			);

			var view = new wpmoly.view.Grid({
				el         : grid,
				content    : content,
				controller : controller
			});

			return controller;
		},

		/**
		 * Ready a Grid controller.
		 *
		 * @since    3.0
		 *
		 * @param    object    grid Backbone Model
		 *
		 * @return   object    Grid controller
		 */
		readyGrid : function( grid ) {

			return grid.ready();
		},

		/**
		 * Create Grid controllers.
		 *
		 * Loop through the grid Nodes to create the corresponding Grid
		 * controllers.
		 *
		 * @since    3.0
		 *
		 * @return   array    List of Grid controllers.
		 */
		createGrids : function() {

			wpmoly.grids.controllers = _.map(
				document.querySelectorAll( '[data-grid]' ),
				wpmoly.grids.createGrid
			);

			return wpmoly.grids.controllers;
		},

		/**
		 * Ready the Grid controllers.
		 *
		 * Loop through the grids to trigger views rendering and data
		 * prefetching.
		 *
		 * @since    3.0
		 *
		 * @return   array    List of Grid controllers.
		 */
		readyGrids : function() {

			wpmoly.grids.controllers = _.map(
				wpmoly.grids.controllers,
				wpmoly.grids.readyGrid
			);

			return wpmoly.grids.controllers;
		},

		/**
		 * Create controllers.
		 *
		 * This should be called after the REST API Backbone client has
		 * been loaded.
		 *
		 * Use a Deferred object to properly create the Grid controllers
		 * before rendering the views.
		 *
		 * @see wp.api.loadPromise.done()
		 *
		 * @since    3.0
		 *
		 * @return   array    List of Grid controllers.
		 */
		load : function() {

			var grids = wpmoly.grids,
			      dfd = jQuery.Deferred();

			dfd.done(
				grids.createGrids,
				grids.readyGrids,
				function() {
					return wpmoly.grids = wpmoly.grids.controllers;
				}
			);

			dfd.resolve();

			return dfd.promise();
		},

		/**
		 * Run Forrest, run!
		 *
		 * Load the REST API Backbone client before loading all Grid
		 * controllers.
		 *
		 * @see wp.api.loadPromise.done()
		 *
		 * @since    3.0
		 */
		run: function() {

			if ( ! wp.api ) {
				return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
			}

			wp.api.loadPromise.done( this.load );
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.grids );
