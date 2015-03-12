
( function( $, _, Backbone, wp, wpmoly ) {

	var grid = wpmoly.grid,
	  editor = wpmoly.editor,
	   media = wp.media,
	hasTouch = ( 'ontouchend' in document );

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

	/**
	 * Custom attachment-like Movie View.
	 * 
	 * @since    2.2
	 */
	grid.View.Movie = media.View.extend({

		tagName:   'li',

		className: 'attachment movie',

		template:  media.template( 'wpmoly-movie' ),

		events: {
			'click a':               'preventDefault',
			'click a.edit-movie':    'editMovie',
			'click a.preview-movie': 'previewMovie',
		},

		initialize: function() {

			this.grid = this.options.grid || {};
		},

		render: function() {

			var rating = parseFloat( this.model.get( 'details' ).get( 'rating' ) ),
			      star = 'empty';

			if ( '' != rating ) {
				if ( 3.5 < rating ) {
					star = 'filled';
				} else if ( 2 < rating ) {
					star = 'half';
				}
			}

			this.$el.html(
				this.template({
					post:    this.model.get( 'post' ).toJSON(),
					meta:    this.model.get( 'meta' ).toJSON(),
					details: _.extend( this.model.get( 'details' ).toJSON(), { star: star } ),
					size:    {
						height: this.grid.thumbnail_height || '',
						width:  this.grid.thumbnail_width  || ''
					}
				})
			);

			return this;
		},

		editMovie: function( event ) {

			var id = this.$( event.currentTarget ).attr( 'data-id' );
			    id = parseInt( id );

			editor.views.movies.openMovieModal( event, id, 'edit-movie' );
		},

		previewMovie: function( event ) {

			var id = this.$( event.currentTarget ).attr( 'data-id' );
			    id = parseInt( id );

			editor.views.movies.openMovieModal( event, id, 'preview-movie' );
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

	/**
	 * Basic grid view.
	 * 
	 * This displays a grid view of movies very similar to the WordPress
	 * Media Library grid.
	 * 
	 * @since    2.2
	 */
	grid.View.ContentGrid = media.View.extend({

		id: 'grid-content-grid',

		tagName:   'ul',

		className: 'attachments movies',

		_viewsByCid: {},

		_lastPosition: 0,

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
				resize:             true,
				idealColumnWidth:   $( window ).width() < 640 ? 135 : 180,
				refreshSensitivity: hasTouch ? 300 : 200,
				refreshThreshold:   3,
				scrollElement:      document,
				resizeEvent:        'resize.grid-content-columns',
				subview:            grid.View.Movie
			} );

			this.model = this.options.model;
			this.frame = this.options.frame;
			this.$window = $( window );

			// Add new views for new movies
			this.collection.on( 'add', function( movie ) {
				this.views.add( this.createSubView( movie ) );
			}, this );

			// Re-render the view when collection is emptied
			this.collection.on( 'reset', this.render, this );

			// Event handlers
			_.bindAll( this, 'setColumns' );

			// Throttle the scroll handler and bind this.
			this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

			// Handle scrolling
			$( this.options.scrollElement ).on( 'scroll', this.scroll );

			// Detect Window resize to readjust thumbnails
			if ( this.options.resize ) {
				this.$window.off( this.options.resizeEvent ).on( this.options.resizeEvent, _.debounce( this.setColumns, 50 ) );
			}

			// Determine optimal columns number and adjust thumbnails
			if ( this.options.resize ) {
				this.controller.on( 'open', this.setColumns );
				// Call this.setColumns() after this view has been rendered in the DOM so
				// attachments get proper width applied.
				_.defer( this.setColumns, this );
			}
		},

		/**
		 * Calcul the best number of columns to use and resize thumbnails
		 * to fit correctly.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		setColumns: function() {

			var prev = this.columns,
			   width = this.$el.width();

			if ( width ) {
				this.columns = Math.min( Math.round( width / this.options.idealColumnWidth ), 12 ) || 1;

				if ( ! prev || prev !== this.columns ) {
					this.$el.closest( '.grid-frame-content' ).attr( 'data-columns', this.columns );
				}
			}

			this.fixThumbnails( force = true );
		},

		/**
		 * Fix movie thumbnails height to display properly in the grid.
		 * 
		 * If the force parameter is set to true every movie in the 
		 * grid will be resized; it set to false only movies not already
		 * resized will be considered.
		 * 
		 * @since    2.2
		 * 
		 * @param    boolean    force resize
		 * 
		 * @return   void
		 */
		fixThumbnails: function( force ) {

			if ( ! this.collection.length )
				return;

			if ( true === force ) {
				var $li = this.$( 'li' ),
				 $items = $li.find( '.movie-preview' );

				$items.css( { width: '', height: '' } );
				$li.css( { width: '' } );
			} else {
				var $li = this.$( 'li' ).not( '.resized' ),
				 $items = $li.find( '.movie-preview' );
			}

			this.thumbnail_width  = this.$( 'li:first' ).width() - 26;
			this.thumbnail_height = Math.floor( this.thumbnail_width * 1.5 );

			$li.addClass( 'resized' ).css({
				width: this.thumbnail_width
			});
			$items.css({
				width: this.thumbnail_width,
				height: this.thumbnail_height
			});
		},

		/**
		 * Create a view for a movie.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    grid.Model.Movie
		 * 
		 * @return   object    Backbone.View
		 */
		createSubView: function( movie ) {

			var view = new this.options.subview({
				grid:       this,
				controller: this.controller,
				model:      movie,
				collection: this.collection
			});

			if ( ! _.isUndefined( this.thumbnail_width ) ) {
				view.$el.css({
					width: this.thumbnail_width
				});
			}

			return this._viewsByCid[ movie.cid ] = view;
		},

		/**
		 * Prepare the view. If the collection is already set, create
		 * views for each movie. If the collection is empty, fill it.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    grid.Model.Movie
		 * 
		 * @return   object    Backbone.View
		 */
		prepare: function() {

			if ( this.collection.length ) {
				//this.views.set( this.collection.map( this.options.subview, this ) );
				this.collection.map( this.createSubView, this );
			} else {
				// Clear existing views
				this.views.unset();

				// Access this from deferred
				var self = this;
				// Loading...
				this.$el.addClass( 'loading' );
				// Deferring
				this.dfd = this.collection.more().done( function() {
					self.$el.removeClass( 'loading' );
					self.setColumns();
					self.scroll;
				} );
			}
		},

		/**
		 * Handle the infinite scroll.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		scroll: function() {

			var  view = this,
			       el = document.body,
			scrollTop = this.$window.scrollTop();

			// Already loading? Don't bother.
			if ( this._loading ) {
				return;
			}

			// Scroll elem is hidden or collection has no more movie
			if ( ! $( el ).is( ':visible' ) || ! this.collection.hasMore() ) {
				this.$el.removeClass( 'loading' );
				return;
			}

			// Don't go further if we're scrolling up
			if ( scrollTop <= this._lastPosition ) {
				return;
			}

			this._lastPosition = scrollTop;
			if ( scrollTop >= ( el.scrollHeight - el.clientHeight - 100 ) ) {

				this._loading = true;
				this.$el.addClass( 'loading' );

				this.dfd = this.collection.more().done( function() {
					view.$el.removeClass( 'loading' );
					view._loading = false;
				} );

			} else {
				this.$el.removeClass( 'loading' );
				this._loading = false;
			}
		}

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
				model.trigger( 'ready' );
			}, this );

			if ( this.options.states ) {
				this.states.add( this.options.states );
			}
		},

		/**
		 * Render the View.
		 * 
		 * @since    2.2
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
				mode:  'grid',
				state: 'library'
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
					id:      'library',
					library: grid.query( options.library )
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
				frame:      this,
				model:      state,
				collection: state.get( 'library' ),
				controller: this,
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
		 * Run actions before rendering.
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
		 * Render the View.
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
		 * Run actions after rendering.
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
		 * Switch mode.
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
