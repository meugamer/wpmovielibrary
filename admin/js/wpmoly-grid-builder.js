
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	gridbuilder = wpmoly.gridbuilder = {

		/**
		 * Create the Grid controller.
		 * 
		 * @since    3.0
		 * 
		 * @return   controller
		 */
		createGrid : function() {

			var grid = document.querySelector( '[data-grid]' ),
			 post_id = document.querySelector( '#post_ID' ),
			 content = grid.querySelector( '.grid-content-inner' ),
			    json = grid.querySelector( '.grid-json' );

			var options = JSON.parse( json.textContent || '{}' );

			var controller = new wpmoly.controller.Grid(
				{
					post_id : post_id.value
				},
				_.extend( options, {
					prefetch : true,
					settings : _.extend( options.settings, {
						enable_pagination : false,
						customs_control   : false,
						settings_control  : false
					} )
				} )
			);

			var view = new wpmoly.view.Grid.Grid({
				el         : grid,
				content    : content,
				controller : controller
			});

			return gridbuilder.grid = controller;
		},

		/**
		 * Create the Builder controller.
		 * 
		 * @since    3.0
		 * 
		 * @return   controller
		 */
		createBuilder : function() {

			var post_id = document.querySelector( '#post_ID' ),
			    builder = document.querySelector( '#wpmoly-grid-builder' ),
			      nonce = builder.querySelector( '#wpmoly_save_grid_setting_nonce' );

			var controller = new wpmoly.controller.GridBuilder( {
				post_id : post_id.value,
				nonce   : nonce.value
			}, {
				preview : gridbuilder.grid
			} );

			var view = new wpmoly.view.Grid.Builder({
				el         : builder,
				controller : controller
			});

			return gridbuilder.controller = controller;
		},

		/**
		 * Ready the Grid controller.
		 * 
		 * @since    3.0
		 * 
		 * @return   controller
		 */
		readyGrid : function() {

			return gridbuilder.grid.ready();
		},

		/**
		 * Ready the Builder controller.
		 * 
		 * @since    3.0
		 * 
		 * @return   controller
		 */
		readyBuilder : function() {

			return gridbuilder.controller.ready();
		},

		/**
		 * Create controllers.
		 *
		 * This should be called after the REST API Backbone client has
		 * been loaded.
		 *
		 * Use a Deferred object to properly create the controllers
		 * before rendering the views.
		 *
		 * @see wp.api.loadPromise.done()
		 *
		 * @since    3.0
		 *
		 * @return   array    Controllers list.
		 */
		load : function() {

			var dfd = jQuery.Deferred();

			dfd.done(
				gridbuilder.createGrid,
				gridbuilder.createBuilder,
				gridbuilder.readyGrid,
				gridbuilder.readyBuilder,
				function() {
					return gridbuilder = {
						controller : gridbuilder.controller,
						grid       : gridbuilder.grid
					};
				}
			);

			dfd.resolve();

			return dfd.promise();
		},

		/**
		 * Run Forrest, run!
		 *
		 * Load the REST API Backbone client before loading all
		 * controllers.
		 *
		 * @see wp.api.loadPromise.done()
		 *
		 * @since    3.0
		 */
		run : function() {

			if ( ! wp.api ) {
				return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
			}

			wp.api.loadPromise.done( this.load );
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.gridbuilder );
