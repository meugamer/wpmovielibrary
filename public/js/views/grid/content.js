
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

Grid.Node = wp.Backbone.View.extend({

	className : 'node post-node',

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

		this.model = options.model;
	},

	/**
	 * Render the View.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		var link = this.model.get( 'link' ),
		   title = this.model.get( 'title' ) || this.model.get( 'name' );

		this.$el.html( '<div class="node-title"><a href="' + link + '">' + ( title.rendered || title ) + '</a></div>' );
	}

});

_.extend( Grid, {

	Nodes: wp.Backbone.View.extend({

		className : 'grid-content-inner loading',

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

			this.listenTo( this.controller.collection, 'add', function( model, collection ) {
				this.views.add( new Grid.Node( { model: model } ) );
			} );

			this.listenTo( this.controller, 'fetch:stop', function() {
				this.$el.removeClass( 'loading' );
			} );

			this.listenTo( this.controller.settings, 'change:list_columns', function( model, value, options ) {
				this.$el.attr( 'data-columns', value );
			} );
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

			wp.Backbone.View.prototype.render.apply( this, arguments );

			wpmoly.$( this.views.selector ).addClass( this.controller.settings.get( 'mode' ) );
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

			var columns = this.controller.settings.get( 'columns' ),
			       rows = this.controller.settings.get( 'rows' ),
			 idealWidth = this.controller.settings.get( 'column_width' ),
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
