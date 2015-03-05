
( function( $, _, Backbone, wp, wpmoly ) {

	var grid = wpmoly.grid || {}, media = wp.media;

	/**
	 * WPMOLY Admin Movie Grid Menu View
	 * 
	 * This View renders the Admin Movie Grid Menu.
	 * 
	 * @since    2.2
	 */
	grid.View.Menu = media.View.extend({

		id: 'grid-menu',

		template: media.template( 'wpmoly-grid-menu' ),

		/*events: {
			'click a':              'preventDefault',
			'click .view-switch a': 'switchView',
		},*/

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 *
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.frame = this.options.frame;
		},

		/**
		 * Render the Menu
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			this.$el.html( this.template( this.frame.mode() ) );

			return this;
		},

		/**
		 * Prevent click events default effect
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.2
		 */
		switchView: function( event ) {

			var mode = event.currentTarget.dataset.mode;
			this.frame.mode( mode );
		},

		/**
		 * Prevent click events default effect
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.2
		 */
		preventDefault: function( event ) {

			event.preventDefault();
		}

	});

	grid.View.ContentGrid = media.View.extend({

		id: 'grid-content-grid',

		template: media.template( 'wpmoly-grid-content-grid' ),

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function() {

			_.defaults( this.options, {
				frame:   {},
				library: {}
			} );

			this.library = this.options.library;
			this.frame   = this.options.frame;

			this.collection = this.library;

			this.prepare();
		},

	});

	grid.View.ContentList   = media.View.extend({

		el: '#grid-content-list',

		render: function() {
			this.$el.html( this.$( this.el ).html() );
			this.$el.find( '> *' ).show();
		}
	});

	grid.View.ContentExerpt = media.View.extend({

		id: 'grid-content-exerpt',

		template: media.template( 'wpmoly-grid-content-exerpt' ),
	});

	grid.View.ContentImport = media.View.extend({

		id: 'grid-content-import',

		template: media.template( 'wpmoly-grid-content-import' ),
	});

	/**
	 * WPMOLY Admin Movie Grid View
	 * 
	 * This View renders the Admin Movie Grid.
	 * 
	 * @since    2.2
	 */
	grid.View.Frame = media.View.extend({

		_mode: 'grid',

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function() {

			this._createRegions();
			this._createStates();
		},

		/**
		 * Create the frame's regions.
		 * 
		 * @since    2.2
		 */
		_createRegions: function() {

			// Clone the regions array.
			this.regions = this.regions ? this.regions.slice() : [];

			// Initialize regions.
			_.each( this.regions, function( region ) {
				this[ region ] = new media.controller.Region({
					view:     this,
					id:       region,
					selector: '.grid-frame-' + region
				});
			}, this );
		},
	
		/**
		 * Create the frame's states.
		 * 
		 * @since    2.2
		 */
		_createStates: function() {

			// Create the default `states` collection.
			this.states = new Backbone.Collection( null, {
				model: media.controller.State
			});

			// Ensure states have a reference to the frame.
			this.states.on( 'add', function( model ) {
				model.frame = this;
				model.trigger('ready');
			}, this );

			if ( this.options.states ) {
				this.states.add( this.options.states );
			}
		},

		/**
		 * 
		 */
		render: function() {

			// Activate the default state if no active state exists.
			if ( ! this.state() && this.options.state ) {
				this.setState( this.options.state );
			}

			return media.View.prototype.render.apply( this, arguments );
		}

	});

	// Make the `Frame` a `StateMachine`.
	_.extend( grid.View.Frame.prototype, media.controller.StateMachine.prototype );

	/**
	 * WPMOLY Admin Movie Grid View
	 * 
	 * This View renders the Admin Movie Grid.
	 * 
	 * @since    2.2
	 */
	grid.View.GridFrame = grid.View.Frame.extend({

		id: 'movie-grid-frame',

		tagName: 'div',

		className: 'movie-grid',

		template: media.template( 'wpmoly-grid-frame' ),

		regions: [ 'menu', 'content' ],

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 *
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			grid.View.Frame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				//selection: [],
				mode:     'grid',
				library:   {},
				state:    'library'
			});

			this.createStates();
			this.bindHandlers();

			this.preRender();
			this.render();
			this.postRender();
		},

		/**
		 * Bind events
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		bindHandlers: function() {

			this.on( 'change:mode', this.render, this );

			this.on( 'menu:create:grid', this.createMenu, this );
			this.on( 'menu:create:list', this.createMenu, this );
			this.on( 'menu:create:exerpt', this.createMenu, this );
			this.on( 'menu:create:import', this.createMenu, this );
			this.on( 'content:create:grid', this.createContentGrid, this );
			this.on( 'content:create:list', this.createContentList, this );
			this.on( 'content:create:exerpt', this.createContentExerpt, this );
			this.on( 'content:create:import', this.createContentImport, this );

			return this;
		},

		/**
		 * Create the default states on the frame.
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createStates: function() {

			var options = this.options;

			if ( this.options.states ) {
				return;
			}

			// Add the default states.
			this.states.add([
				// Main states.
				new grid.controller.Library({
					id:     'library',
					library: grid.query( options.library ),
				})
			]);

			return this;
		},

		/**
		 * Create the Menu View
		 * 
		 * This Content View show the WPMOLY 2.2 Movie Grid view
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Region
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createMenu: function( region ) {

			region.view = new grid.View.Menu( { frame: this } );
		},

		/**
		 * Create the Content Grid View
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Region
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createContentGrid: function( region ) {

			var state = this.state();

			region.view = new grid.View.ContentGrid({
				frame:   this,
				library: state.get( 'library' )
			});
		},

		/**
		 * Create the Content List View
		 * 
		 * This Content View show the basic, WPish Post List Table.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Region
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createContentList: function( region ) {

			region.view = new grid.View.ContentList( { frame: this } );
		},

		/**
		 * Create the Content Exerpt View
		 * 
		 * This Content View shows a larger Grid including some additional
		 * data from Movies.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Region
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createContentExerpt: function( region ) {

			region.view = new grid.View.ContentExerpt( { frame: this } );
		},

		/**
		 * Create the Content Importer View
		 * 
		 * This Content View include the WPMOLY < 2.2 Importer.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Region
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createContentImport: function( region ) {

			region.view = new grid.View.ContentImport( { frame: this } );
		},

		/**
		 * 
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		preRender: function() {

			$( '.wrap' ).append( '<div id="grid-content-list"></div>' );
			$( '.wrap > *' ).not( 'h2' ).appendTo( '#grid-content-list' );

			return this;
		},

		/**
		 * 
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			grid.View.Frame.prototype.render.apply( this, arguments );

			var options = this.options;

			this.$el.html( this.template() );
			this.el.className = 'mode-' + options.mode;

			_.each( this.regions, function( region ) {
				this[ region ].mode( options.mode );
			}, this );

			return this;
		},

		/**
		 * 
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		postRender: function() {

			this.$el.appendTo( $( '.wrap' ) );

			$( '#grid-content-list' ).appendTo( this.$( '.grid-frame-content' ) );

			return this;
		},

		/**
		 * 
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		mode: function( mode ) {

			var options = this.options;

			if ( ! mode )
				return options.mode;

			if ( mode === options.mode )
				return this;

			options.mode = mode;
			this.trigger( 'change:mode', mode );

			return this;
		}

	});

	

}( jQuery, _, Backbone, wp, wpmoly ) );
