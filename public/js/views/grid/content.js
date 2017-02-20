
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

Grid.Node = wp.Backbone.View.extend({

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

		this.model = options.model || {};
		this.controller = options.controller || {};

		this.template = this.setTemplate();
	},

	/**
	 * Set the View template based on settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   wp.template()
	 */
	setTemplate: function() {

		var type = this.controller.settings.get( 'type' ),
		    mode = this.controller.settings.get( 'mode' ),
		template = 'wpmoly-grid-' + type + '-' + mode;

		return wp.template( template );
	},

	/**
	 * Set $el class names depending on settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	setClassName: function() {

		var settings = this.controller.settings,
		   className = [ 'node' ];

		if ( 'movie' === settings.get( 'type' ) ) {
			className.push( 'post-node' );
		} else if ( _.contains( [ 'actor', 'collection', 'genre' ], settings.get( 'type' ) ) ) {
			className.push( 'term-node' );
		}

		className.push( settings.get( 'type' ) );

		this.className = className.join( ' ' );

		this.$el.addClass( this.className );

		return this;
	},

	/**
	 * Render the View.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		var data = {
			movie    : this.model,
			settings : this.settings
		};

		this.setClassName();

		this.$el.html( this.template( data ) );
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
			this.collection = this.controller.collection;

			this.$window  = wpmoly.$( window );
			this.resizeEvent = 'resize.grid-' + this.controller.get( 'post_id' );

			this.settings = this.controller.settings;
			this.rendered = false;

			this.nodes = {};

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

			// Adjust subviews dimensions on resize
			this.$window.off( this.resizeEvent ).on( this.resizeEvent, _.debounce( this.adjust, 50 ) );

			// Add views for new models
			this.listenTo( this.collection, 'add', this.addNode );

			// Set grid as loading when reset
			this.listenTo( this.collection, 'reset', this.loading );

			// Set grid as loaded when fetch is done
			this.listenTo( this.controller, 'fetch:stop', this.loaded );
			this.listenTo( this.controller, 'fetch:stop', _.debounce( this.adjust, 50 ) );

			/*this.listenTo( this.controller.settings, 'change:list_columns', function( model, value, options ) {
				this.$el.attr( 'data-columns', value );
			} );*/
		},

		/**
		 * Add a new subview.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * @param    object    collection
		 * 
		 * @return    Returns itself to allow chaining.
		 */
		addNode: function( model, collection ) {

			var id = model.get( 'id' );

			if ( ! this.nodes[ id ] ) {
				this.nodes[ id ] = new Grid.Node({
					controller : this.controller,
					collection : collection,
					model      : model
				});
			}

			this.views.add( this.nodes[ id ] );

			return this;
		},

		/**
		 * Set grid as loading.
		 * 
		 * @since    3.0
		 * 
		 * @return    Returns itself to allow chaining.
		 */
		loading: function() {

			this.$el.addClass( 'loading' );

			return this;
		},

		/**
		 * Set grid as loaded.
		 * 
		 * @since    3.0
		 * 
		 * @return    Returns itself to allow chaining.
		 */
		loaded: function() {

			this.$el.removeClass( 'loading' );

			return this;
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
		adjust: function() {

			return this;
		},

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
