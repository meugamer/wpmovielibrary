
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

_.extend( Grid, {

	Nodes: wp.Backbone.View.extend({

		className : 'grid-content-inner',

		/**
		* Initialize the View.
		* 
		* @since    3.0
		* 
		* @param    object    options
		* 
		* @return   void
		*/
		initialize: function( options ) {

			this.controller = options.controller || {};
			
			this.$window  = wpmoly.$( window );
			this.resizeEvent = 'resize.grid-' + this.controller.get( 'post_id' );

			this.settings = this.controller.settings;
			this.rendered = false;

			this.bindEvents();
		},

		/**
		 * Bind events.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		bindEvents: function() {

			_.bindAll( this, 'adjust' );

			this.on( 'ready', this.adjust );

			this.$window.off( this.resizeEvent ).on( this.resizeEvent, _.debounce( this.adjust, 50 ) );
		},

		/**
		 * Adjust content nodes to fit the grid.
		 * 
		 * Should be extended.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		adjust: function() {},

		/**
		* Render the View.
		* 
		* @since    3.0
		* 
		* @return   Returns itself to allow chaining.
		*/
		render: function() {

			if ( ! this.rendered ) {

				var grid_id = this.controller.get( 'post_id' ),
				   $content = wpmoly.$( '[data-grid="' + grid_id + '"] .grid-content' );

				this.$el.html( $content.html() );

				this.rendered = true;
			}

			return this;
		}

	})

} );

_.extend( Grid, {

	NodesGrid: Grid.Nodes.extend({

		/**
		 * Adjust content nodes to fit the grid.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		adjust: function() {

			var columns = this.settings.get( 'columns' ),
			       rows = this.settings.get( 'rows' ),
			 idealWidth = this.settings.get( 'column_width' ),
			 innerWidth = this.$el.width();

			if ( 'movie' === this.settings.get( 'type' ) ) {

				if ( ( Math.floor( innerWidth / columns ) - 8 ) < idealWidth ) {
					--columns;
				}

				this.columnWidth  = Math.floor( innerWidth / columns ) - 8;
				this.columnHeight = Math.floor( this.columnWidth * 1.5 );
			}

			this.$( '.node' ).addClass( 'adjusted' ).css({
				width : this.columnWidth
			});

			this.$( '.node-poster' ).addClass( 'adjusted' ).css({
				height : this.columnHeight,
				width  : this.columnWidth
			});
		}
	}),

	NodesList: Grid.Nodes.extend({

		
	}),

	NodesArchives: Grid.Nodes.extend({

		
	})

} );
