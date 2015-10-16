
var grid = wpmoly.grid,
   media = wp.media,
       $ = Backbone.$
hasTouch = ( 'ontouchend' in document );

_.extend( grid.view, {

	Menu: media.View.extend({

		className: 'wpmoly-grid-menu',

		template: media.template( 'wpmoly-grid-menu' ),

		events: {
			'click a':                                'preventDefault',

			'click a[data-action="openmenu"]':        'toggle_menu',

			//'click a[data-action="scroll"]':          'setScroll',

			'click a[data-action="orderby"]':         'orderby',
			'click a[data-action="order"]':           'order',
			'click a[data-action="letter"]':          'letter',
			'click a[data-action="filter"]':          'filter',
			'click a[data-action="display"]':         'display',
			//'click a[data-action="view"]':            'view',

			'click a[data-action="apply-settings"]':  'apply',
			'click a[data-action="cancel-settings"]': 'cancel',
			'click a[data-action="reload-settings"]': 'reload',

			'click .wpmoly-grid-settings-container':  'stopPropagation',
			'click .grid-menu-settings':              'stopPropagation',
		},

		icons: {
			yes: 'icon-yes-alt',
			no:  'icon-no-alt-2'
		},

		/**
		 * Initialize the View
		 * 
		 * @since    2.1.5
		 *
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			_.defaults( this.options, {
				refreshSensitivity: hasTouch ? 300 : 200
			} );

			this._mode   = '';

			this.frame      = this.options.frame;
			this.library    = this.options.library;
			this.controller = this.frame.controller;

			this.$window = $( window );
			this.$body   = $( document.body );
			this.$waitee = $( 'body.waitee' );
		},

		/**
		 * Open or close the submenu related to the menu link clicked
		 * 
		 * @since    2.1.5
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @return   void
		 */
		toggle_menu: function( event ) {

			var $elem = this.$( event.currentTarget ),
			 $submenu = this.$( '.wpmoly-grid-settings-container' ),
			     mode = $elem.attr( 'data-value' );

			if ( ! this.$el.hasClass( 'open' ) ) {
				$elem.addClass( 'active' );
				this.open( mode );
			} else {
				if ( 'settings' == mode && this.$el.hasClass( 'mode-content' ) ) {
					this.$el.removeClass( 'mode-content' ).addClass( 'mode-settings' );
				} else if ( 'content' == mode && this.$el.hasClass( 'mode-settings' ) ) {
					this.$el.removeClass( 'mode-settings' ).addClass( 'mode-content' );
				} else {
					$elem.removeClass( 'active' );
					this.close();
				}
			}

			event.stopPropagation();
		},

		/**
		 * Open the submenu and set its mode
		 * 
		 * @since    2.1.5
		 *
		 * @param    string    Submenu mode, 'content' of 'settings'
		 * 
		 * @return   void
		 */
		open: function( mode ) {

			this.mode( mode );

			if ( this.$body.hasClass( 'waitee' ) ) {
				return;
			}

			// Close the submenu when clicking elsewhere
			var self = this;
			this.$body.addClass( 'waitee' ).one( 'click', function() {
				self.close();
			});
		},

		/**
		 * Update the view's mode
		 * 
		 * @since    2.1.5
		 * 
		 * @param    string    Mode
		 * 
		 * @return   void
		 */
		mode: function( mode ) {

			this._mode = mode;
			this.render();
		},

		/**
		 * Close the submenu
		 * 
		 * @since    2.1.5
		 * 
		 * @return   void
		 */
		close: function() {

			this.$el.removeClass( 'mode-content mode-settings open' );
			this.$( '.grid-menu-action.active' ).removeClass( 'active' );
			this.$waitee.unbind( 'click' );
			this.$waitee.removeClass( 'waitee' );
		},

		/**
		 * Handle ordering change menu (orderby)
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    JS 'Click' Event
		 * 
		 * @return   void
		 */
		orderby: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			if ( ! _.contains( this.controller.orderby, value ) ) {
				return;
			}

			this.controller.set( { orderby: value, paged: 1 }, { silent: true } );
			this.render();
		},

		/**
		 * Handle ordering change menu (order)
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    JS 'Click' Event
		 * 
		 * @return   void
		 */
		order: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			if ( ! _.contains( this.controller.order, value ) ) {
				return;
			}

			this.controller.set( { order: value.toUpperCase(), paged: 1 }, { silent: true } );
			this.render();
		},

		/**
		 * Handle letter filtering
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    JS 'Click' Event
		 * 
		 * @return   void
		 */
		letter: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' )
			    regex = new RegExp('^[a-z0-9#]', 'i');

			if ( 1 < value.length ) {
				value = value.substr( 0, 1 );
			}

			if ( ! regex.test( value ) ) {
				return;
			}

			this.controller.set( { letter: value.toUpperCase() }, { silent: true } );
			this.render();
		},

		/**
		 * Handle display alterations
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    JS 'Click' Event
		 * 
		 * @return   void
		 */
		display: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' ),
			    check = $elem.attr( 'data-check' );
			    check = '1' === check;

			if ( ! _.contains( this.controller.display, value ) ) {
				return;
			}

			this.controller.set( 'show_' + value, ! check, { silent: true } );
			this.render();
		},

		/**
		 * Apply the settings.
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.1.5
		 */
		apply: function( event ) {

			var self = this,
			callback = this.library.update();

			if ( ! _.isPromise( callback ) ) {
				return this;
			}

			// Loading...
			this.frame.$el.addClass( 'loading' );
			// Deferring
			this.dfd = callback.done( function() {
				self.frame.$el.removeClass( 'loading' );
			} );

			this.close();
		},

		/**
		 * Cancel the settings.
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.1.5
		 */
		cancel: function( event ) {

			this.close();
		},

		/**
		 * Reload the grid with default settings.
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.1.5
		 */
		reload: function() {

			this.controller.reset();
			this.close();
		},

		/**
		 * Prevent click events default effect
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.1.5
		 */
		switchView: function( event ) {

			var mode = event.currentTarget.dataset.mode;
			this.frame.mode( mode );
		},

		/**
		 * Render the Menu
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			var options = {
				mode:    this._mode,
				scroll:  this.frame._scroll,
				view:    this.frame.mode(),
				orderby: this.controller.get( 'orderby' ),
				order:   this.controller.get( 'order' ),
				include: {
					incoming: this.controller.get( 'include_incoming' ),
					unrated:  this.controller.get( 'include_unrated' ),
				},
				display: {
					title:   this.controller.get( 'show_title' ),
					genre:   this.controller.get( 'show_genre' ),
					year:    this.controller.get( 'show_year' ),
					rating:  this.controller.get( 'show_rating' ),
					runtime: this.controller.get( 'show_runtime' ),
					number:  this.controller.get( 'number' ),
					columns: this.controller.get( 'columns' ),
					rows:    this.controller.get( 'rows' )
				}
			};
			this.$el.html( this.template( options ) );

			if ( ! _.isEmpty( this._mode ) ) {
				this.$el.addClass( 'open mode-' + options.mode );
			}

			this.views.render();

			return this;
		},

		/**
		 * Prevent click events default effect
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.1.5
		 */
		preventDefault: function( event ) {

			event.preventDefault();
		},

		/**
		 * Stop Click Event Propagation
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.1.5
		 */
		stopPropagation: function( event ) {

			event.stopPropagation();
		}
	}),

	/**
	* Movie Grid Menu Pagination SubView
	* 
	* This View renders the Movie Grid Menu Pagination subview.
	* 
	* @since    2.1.5
	*/
	Pagination: media.View.extend({

		className: 'wpmoly-grid-menu',

		template: media.template( 'wpmoly-grid-pagination' ),

		events: {
			'click a':                              'preventDefault',

			'click a[data-action="prev"]':          'prev',
			'click a[data-action="next"]':          'next',
			'change input[data-action="browse"]':   'browse',

			'click .grid-pagination-settings':      'stopPropagation'
		},

		/**
		 * Initialize the View
		 * 
		 * @since    2.1.5
		 *
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.library    = this.options.library;
			this.frame      = this.options.frame;
			this.controller = this.options.controller;

			this.$body = $( 'body' );

			this.library.collection.pages.on( 'change', this.render, this );

			//this.frame.props.on( 'change:scroll', this.render, this );
		},

		/**
		 * Go to the previous results page.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Return itself to allow chaining
		 */
		prev: function() {

			// Access this from deferred
			var self = this,
			callback = this.library.prev();

			if ( ! _.isPromise( callback ) ) {
				return this;
			}

			// Loading...
			this.frame.$el.addClass( 'loading' );
			// Deferring
			this.dfd = callback.done( function() {
				self.frame.$el.removeClass( 'loading' );
				//self.setColumns();
				//self.scroll;
			} );

			return this;
		},

		/**
		 * Go to the next results page.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Return itself to allow chaining
		 */
		next: function() {

			// Access this from deferred
			var self = this,
			callback = this.library.next();

			if ( ! _.isPromise( callback ) ) {
				return this;
			}

			// Loading...
			this.frame.$el.addClass( 'loading' );
			// Deferring
			this.dfd = callback.done( function() {
				self.frame.$el.removeClass( 'loading' );
				//self.setColumns();
				//self.scroll;
			} );

			return this;
		},

		/**
		 * Go to a specific results page.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Return itself to allow chaining
		 */
		browse: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.val() || 1,
			     self = this,
			 callback = this.library.page( value );

			if ( ! _.isPromise( callback ) ) {
				return this;
			}

			// Loading...
			this.frame.$el.addClass( 'loading' );
			// Deferring
			this.dfd = callback.done( function() {
				self.frame.$el.removeClass( 'loading' );
				//self.setColumns();
				//self.scroll;
			} );

			return this;
		},

		/**
		 * Render the Menu
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			/*if ( false !== this.frame._scroll ) {
				this.$el.hide();
				return this;
			} else {
				this.$el.show();
			}*/

			var options = {
				current: this.library.collection.pages.get( 'current' ),
				total:   this.library.collection.pages.get( 'total' ),
				prev:    this.library.collection.pages.get( 'prev' ),
				next:    this.library.collection.pages.get( 'next' )
			};

			this.$el.html( this.template( options ) );

			return this;
		},

		/**
		 * Prevent click events default effect
		 *
		 * @param    object    JS 'Click' Event
		 * 
		 * @since    2.1.5
		 */
		preventDefault: function( event ) {

			event.preventDefault();
		},
	}),

	Movie: media.View.extend({

		tagName:   'li',

		className: 'attachment movie',

		template:  media.template( 'wpmoly-movie' ),

		/**
		 * Initialize the View
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.frame      = this.options.frame;
			this.library    = this.options.library;
			this.controller = this.frame.controller;

			this.grid = this.options.grid || {};
		},

		/**
		 * Render the View
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			var data = this.model.toJSON(),
			  rating = parseFloat( this.model.get( 'details' ).rating ),
			    star = 'empty',
			settings = this.controller;

			if ( '' != rating ) {
				if ( 3.5 < rating ) {
					star = 'filled';
				} else if ( 2 < rating ) {
					star = 'half';
				}
			}

			data.meta.year = new Date( data.meta.release_date ).getFullYear();

			this.$el.html(
				this.template( _.extend( data, {
					size: {
						height: this.grid.thumbnail_height || '',
						width:  this.grid.thumbnail_width  || ''
					},
					details: _.extend( this.model.get( 'details' ), { star: star } ),
					display: {
						title:   settings.get( 'show_title' ),
						year:    settings.get( 'show_year' ),
						genre:   settings.get( 'show_genre' ),
						rating:  settings.get( 'show_rating' ),
						runtime: settings.get( 'show_runtime' )
					}
				} ) )
			);

			return this;
		}

	})
},
{
	Content: media.View.extend({

		id: _.uniqueId( 'grid-content-grid-' ),

		tagName:   'ul',

		className: 'attachments movies',

		_views: [],

		/**
		 * Initialize the View
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			_.defaults( this.options, {
				resize:             true,
				idealColumnWidth:   $( window ).width() < 640 ? 135 : 180,
				refreshSensitivity: hasTouch ? 300 : 200,
				refreshThreshold:   3,
				resizeEvent:        'resize.grid-content-columns'
			} );

			this.frame      = this.options.frame;
			this.library    = this.options.library;
			this.controller = this.frame.controller;

			// Add new views for new movies
			this.library.on( 'add', function( movie ) {
				this.views.add( this.create_subview( movie ) );
			}, this );

			// Re-render the view when library is emptied
			this.library.on( 'reset', function() {
				_.map( this.views.all(), function( view ) {
					view.remove();
				}, this );
			}, this );

			// Event handlers
			_.bindAll( this, 'set_columns' );

			$( window ).off( this.options.resizeEvent ).on( this.options.resizeEvent, _.debounce( this.set_columns, 50 ) );

			// Call this.set_columns() after this view has been rendered in the DOM so
			// attachments get proper width applied.
			_.defer( this.set_columns, this );
		},

		/**
		 * Build a view for each movie added to the library.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		create_subview: function( movie ) {

			var view = new grid.view.Movie({
				model: movie,
				grid:  this,
				frame: this.frame
			});

			return this._views[ movie.cid ] = view;
		},

		/**
		 * Calcul the best number of columns to use and resize thumbnails
		 * to fit correctly.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   void
		 */
		set_columns: function() {

			var prev = this.columns,
			   width = this.$el.width();

			if ( width ) {

				this.columns = Math.min( Math.round( width / this.options.idealColumnWidth ), 12 ) || 1;

				this.thumbnail_width  = Math.round( width / this.columns );
				this.thumbnail_height = Math.round( this.thumbnail_width * 1.6 );

				if ( ! prev || prev !== this.columns ) {
					this.$el.closest( '.grid-frame-content' ).attr( 'data-columns', this.columns );
				}
			}

			this.fix_thumbnails( force = true );
		},

		/**
		 * Fix movie thumbnails height to display properly in the grid.
		 * 
		 * If the force parameter is set to true every movie in the 
		 * grid will be resized; it set to false only movies not already
		 * resized will be considered.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    boolean    force resize
		 * 
		 * @return   void
		 */
		fix_thumbnails: function( force ) {

			
			if ( ! this.library.collection.length ) {
				return;
			}

			if ( true === force ) {
				var $li = this.$( 'li' ),
				 $items = $li.find( '.movie-preview' );

				$items.css( { width: '', height: '' } );
				$li.css( { width: '' } );
			} else {
				var $li = this.$( 'li' ).not( '.resized' ),
				 $items = $li.find( '.movie-preview' );
			}

			this.thumbnail_width  = Math.floor( this.$( 'li:first' ).width() - 1 );
			this.thumbnail_height = Math.floor( this.thumbnail_width * 1.5 );

			$li.addClass( 'resized' ).css({
				width: this.thumbnail_width
			});
			$items.css({
				width:  this.thumbnail_width - 8,
				height: this.thumbnail_height - 12
			});
		},
	})
},
{
	Grid: media.View.extend({

		_mode: '',

		template: media.template( 'wpmoly-grid-frame' ),

		pages: new Backbone.Model,

		/**
		 * Initialize the View
		 * 
		 * @since    2.1.5
		 * 
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			var options = options || {};

			this.load = this.$el.attr( 'data-backbone' ) || 'no';
			this.render();

			// Set controller
			this.controller = options.controller;
			this.library    = new grid.controller.Query({ controller: this.controller });

			this._mode = this.controller.get( 'view' );

			// Set regions
			this.set_regions();
		},

		/**
		 * Build the regions.
		 * 
		 * @since    2.1.5
		 * 
		 * @return   void
		 */
		set_regions: function() {

			this.menu = new grid.view.Menu({
				frame:      this,
				library:    this.library,
				controller: this.controller
			});

			this.pagination = new grid.view.Pagination({
				frame:      this,
				library:    this.library,
				controller: this.controller
			});

			this.content = new grid.view.Content({
				frame:      this,
				library:    this.library,
				controller: this.controller
			});

			this.views.add( '.grid-frame-menu',       this.menu );
			this.views.add( '.grid-frame-pagination', this.pagination );
			this.views.add( '.grid-frame-content',    this.content );
		},

		/**
		 * Switch mode.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    string    Mode
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		mode: function( mode ) {

			if ( ! mode ) {
				return this._mode;
			}

			if ( mode === this._mode ) {
				return this;
			}

			this.controller.set({ view: mode });

			return this;
		}
	})
} );
