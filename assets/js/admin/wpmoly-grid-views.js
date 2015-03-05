
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

			this.$el.html( this.template( this.frame._mode ) );

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

			this._mode = this.options.mode;

			this.createRegions();
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
		 * Create the View's Regions
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createRegions: function() {

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

			region.view = new grid.View.ContentGrid( { frame: this } );
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

			this.$el.html( this.template() );
			this.el.className = 'mode-' + this._mode;

			_.each( this.regions, function( region ) {
				this[ region ].mode( this._mode );
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

			if ( ! mode )
				return this._mode;

			if ( mode === this._mode )
				return this;

			this._mode = mode;
			this.trigger( 'change:mode', mode );

			return this;
		}

	});

	

}( jQuery, _, Backbone, wp, wpmoly ) );
