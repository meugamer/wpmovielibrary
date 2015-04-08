
var grid = wpmoly.grid,
  editor = wpmoly.editor,
importer = wpmoly.importer,
   media = wp.media,
hasTouch = ( 'ontouchend' in document );

/**
 * Movie Grid Menu Pagination SubView
 * 
 * This View renders the Movie Grid Menu Pagination subview.
 * 
 * @since    2.1.5
 */
grid.view.PaginationMenu = wp.Backbone.View.extend({

	className: 'wpmoly-grid-menu',

	template: wp.template( 'wpmoly-grid-pagination' ),

	events: {
		'click a':                              'preventDefault',

		'click a[data-action="openmenu"]':      'toggleSettings',
		'click a[data-action="applysettings"]': 'updateSettings',

		'click a[data-action="scroll"]':        'setScroll',
		'click a[data-action="prev"]':          'prev',
		'click a[data-action="next"]':          'next',
		'change input[data-action="browse"]':   'browse',
		'keypress input[data-action="browse"]': 'browse',

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

		this.library = this.options.library;
		this.frame   = this.options.frame;

		this.pages   = this.library.pages;
		this.scroll  = this.frame._scroll;

		this.$body = $( 'body' );

		this.library.props.on( 'change:paged', this.render, this );
		this.library.props.on( 'change:posts_per_page', this.render, this );
		this.library.pages.once( 'change', this.render, this );
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
	toggleSettings: function( event ) {

		// Open settings panel
		if ( ! this.$el.hasClass( 'settings' ) ) {
			this.$el.addClass( 'settings' );
		}

		event.stopPropagation();

		if ( this.$body.hasClass( 'waitee' ) ) {
			return;
		}

		// Close the submenu when clicking elsewhere
		var self = this;
		this.$body.addClass( 'waitee' ).one( 'click', function() {
			self.$el.removeClass( 'settings' );
			self.$body.removeClass( 'waitee' );
		});
	},

	updateSettings: function() {

		var scroll = parseInt( this.$( 'input[data-value="scroll"]' ).val() ) || 0,
		   perpage = parseInt( this.$( 'input[data-action="perpage"]' ).val() ) || 0,
		     props = {},
		  defaults = {
			perpage: this.library.props.get( 'posts_per_page' ),
			scroll:  this.frame._scroll
		   };

		if ( scroll && scroll !== defaults.scroll ) {
			this.frame._scroll = Boolean( scroll );
		}

		if ( perpage && perpage !== defaults.perpage ) {
			this.library.props.set({ posts_per_page: perpage });
		} else if ( scroll && scroll !== defaults.scroll ) {
			this.frame.content.scroll();
		}

		this.$el.removeClass( 'settings' );
	},

	setScroll: function( event ) {

		var $elem = this.$( event.currentTarget ),
		   $input = this.$( 'input[data-value="scroll"]' ),
		   scroll = parseInt( $elem.attr( 'data-value' ) );

		if ( 1 === scroll ) {
			$input.val( 1 );
		} else {
			$input.val( 0 );
		}

		this.$( 'a[data-action="scroll"].selected' ).removeClass( 'selected' );
		$elem.addClass( 'selected' );
	},

	/**
	 * Go to the previous results page.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Return itself to allow chaining
	 */
	prev: function() {

		this.library._prev();

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

		this.library._next();

		return this;
	},

	/**
	 * Go to a specific results page.
	 * 
	 * Handle multiple JS events: Click, Change and Keypress. Clicks concern 
	 * the pagination's 'previous' and 'next' links; Change handle modifications
	 * of the page input whilst Keypress handle an 'Enter' hit on the page
	 * input.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Return itself to allow chaining
	 */
	browse: function( event ) {

		var $elem = this.$( event.currentTarget ),
		    value;

		if ( 'click' == event.type ) {
			if ( 'next' == value ) {
				this.library._next();
			} else if ( 'prev' == value ) {
				this.library._prev();
			} else {
				return this;
			}
		} else if ( 'change' == event.type || ( 'keypress' == event.type && 13 === ( event.charCode || event.keyCode ) ) ) {
			value = $elem.val() || 1 ;
			this.library._page( value );
		} else {
			return this;
		}

		return this;
	},

	/**
	 * Render the Menu
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function( a ) {

		var total = this.library.pages.get( 'total' ),
		  options = {
			scroll:  this.scroll,
			current: this.library.props.get( 'paged' ),
			total:   total,
			prev:    this.library.props.get( 'paged' ) - 1,
			next:    this.library.props.get( 'paged' ) + 1
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
});

/**
 * Movie Grid Menu View
 * 
 * This View renders the Movie Grid Menu.
 * 
 * @since    2.1.5
 */
grid.view.Menu = wp.Backbone.View.extend({

	className: 'wpmoly-grid-menu',

	template: wp.template( 'wpmoly-grid-menu' ),

	events: {
		'click a':                            'preventDefault',
		'click a[data-action="openmenu"]':    'toggleSubMenu',
		'click a[data-action="opensearch"]':  'toggleSearch',

		'click a[data-action="expand"]':      'expand',
		'click a[data-action="orderby"]':     'orderby',
		'click a[data-action="order"]':       'order',
		'click a[data-action="filter"]':      'filter',
		'click a[data-action="view"]':        'view',

		'click .grid-menu-search-container':  'stopPropagation',

	},

	defaults: {
		orderby: [ 'title', 'date', 'release_date', 'rating' ],
		order:   [ 'asc', 'desc' ]
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

		this.frame   = this.options.frame;
		this.model   = this.options.model;
		this.library = this.options.library;

		this.$window = $( window );
		this.$body   = $( document.body );

		// Throttle the scroll handler and bind this.
		this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

		this.$window.on( 'scroll', this.scroll );
	},

	/**
	 * Handle infinite scroll into the current View
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	scroll: function( event ) {

		var   $el = this.$el.parent( '.grid-frame-menu' ),
		   $frame = this.frame.$el,
		scrollTop = this.$window.scrollTop();

		if ( ! $frame.hasClass( 'menu-fixed' ) && $frame.offset().top <= ( scrollTop + 48 ) ) {
			$frame.addClass( 'menu-fixed' );
			$el.css({ width: $frame.width() });
		} else if ( $frame.hasClass( 'menu-fixed' ) && $frame.offset().top > ( scrollTop + 48 ) ) {
			$frame.removeClass( 'menu-fixed' );
			$el.css({ width: '' });
		}
	},

	/**
	 * Change the frame mode fo 'frame', a modal-like full view
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	expand: function( event ) {

		var elem = this.$( event.currentTarget ) || {},
		   value = elem.attr( 'data-value' ),
		    mode;

		if ( 'enlarge' == value ) {
			mode = 'frame';
		} else if ( 'shrink' == value ) {
			mode = 'grid';
		}

		this.frame.mode( mode );
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
	toggleSubMenu: function( event ) {

		var $elem = this.$( event.currentTarget );
		 $submenu = $elem.parents( '.wpmoly-grid-submenu' );

		// Close other submenus
		this.$el.removeClass( 'submenu-open' );
		this.$( '.wpmoly-grid-submenu.open' ).removeClass( 'open' );

		// Open current submenu
		this.$el.addClass( 'submenu-open' );
		$submenu.addClass( 'open' );

		event.stopPropagation();

		if ( this.$body.hasClass( 'waitee' ) ) {
			return;
		}

		// Close the submenu when clicking elsewhere
		var self = this;
		this.$body.addClass( 'waitee' ).one( 'click', function() {
			$submenu.removeClass( 'open' );
			self.$el.removeClass( 'submenu-open' );
			self.$body.removeClass( 'waitee' );
		});

	},

	/**
	 * Open or close the search menu
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	toggleSearch: function( event ) {

		var $elem = this.$( event.currentTarget ),
		  $parent = $elem.parents( '.wpmoly-grid-menu-item-search' );

		// If the search menu is already opened, close it
		if ( $parent.hasClass( 'search-open' ) || this.$el.hasClass( 'submenu-open' ) ) {
			$parent.removeClass( 'search-open' );
			this.$el.removeClass( 'submenu-open' );
			return;
		}

		// Open search menu
		this.$el.addClass( 'submenu-open' );
		$parent.addClass( 'search-open' );

		event.stopPropagation();

		if ( this.$body.hasClass( 'waitee' ) ) {
			return;
		}

		// Close the search when clicking elsewhere
		var self = this;
		this.$body.addClass( 'waitee' ).one( 'click', function() {
			$parent.removeClass( 'search-open' );
			self.$el.removeClass( 'submenu-open' );
			self.$body.removeClass( 'waitee' );
		});
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

		if ( ! _.contains( this.defaults.orderby, value ) ) {
			return;
		}

		this.library.props.set({ orderby: value });

		this.$( 'a[data-action="orderby"].active' ).removeClass( 'active' );
		$elem.closest( 'a[data-action="orderby"]' ).addClass( 'active' );
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

		if ( ! _.contains( this.defaults.order, value ) ) {
			return;
		}

		this.library.props.set({ order: value.toUpperCase() });

		this.$( 'a[data-action="order"].active' ).removeClass( 'active' );
		$elem.closest( 'a[data-action="order"]' ).addClass( 'active' );
	},

	/**
	 * Handle filtering change menu
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	filter: function( event ) {

		var $elem = this.$( event.currentTarget );
		console.log( 'filter!' );
	},

	/**
	 * Handle viewing change menu
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	view: function( event ) {

		var $elem = this.$( event.currentTarget );
		console.log( 'view!' );
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
			mode:    this.frame.mode(),
			orderby: this.library.props.get( 'orderby' ),
			order:   this.library.props.get( 'order' )
		};
		this.$el.html( this.template( options ) );

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
	switchView: function( event ) {

		var mode = event.currentTarget.dataset.mode;
		this.frame.mode( mode );
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

});

/**
 * Custom attachment-like Movie View.
 * 
 * @since    2.1.5
 */
grid.view.Movie = media.View.extend({

	tagName:   'li',

	className: 'attachment movie',

	template:  media.template( 'wpmoly-movie' ),

	initialize: function() {

		this.grid = this.options.grid || {};
	},

	/**
	 * Render the Menu
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		var rating = parseFloat( this.model.get( 'details' ).rating ),
		      star = 'empty';

		if ( '' != rating ) {
			if ( 3.5 < rating ) {
				star = 'filled';
			} else if ( 2 < rating ) {
				star = 'half';
			}
		}

		this.$el.html(
			this.template( _.extend( this.model.toJSON(), {
				size: {
					height: this.grid.thumbnail_height || '',
					width:  this.grid.thumbnail_width  || ''
				},
				details: _.extend( this.model.get( 'details' ), { star: star } )
			} ) )
		);

		return this;
	}

});

/**
 * Basic grid view.
 * 
 * This displays a grid view of movies very similar to the WordPress
 * Media Library grid.
 * 
 * @since    2.1.5
 */
grid.view.ContentGrid = media.View.extend({

	id: 'grid-content-grid',

	tagName:   'ul',

	className: 'attachments movies',

	_viewsByCid: {},

	_lastPosition: 0,

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

		_.defaults( this.options, {
			resize:             true,
			idealColumnWidth:   $( window ).width() < 640 ? 135 : 180,
			refreshSensitivity: hasTouch ? 300 : 200,
			refreshThreshold:   3,
			resizeEvent:        'resize.grid-content-columns',
			subview:            grid.view.Movie
		} );

		this.options.scrollElement = this.el;

		this.model = this.options.model;
		this.frame = this.options.frame;
		this.$window = $( window );

		// Add new views for new movies
		this.collection.on( 'add', function( movie ) {
			this.views.add( this.createSubView( movie ) );
		}, this );

		// Re-render the view when collection is emptied
		this.collection.on( 'reset', function() {
			this.render();
		}, this );

		// Event handlers
		_.bindAll( this, 'setColumns' );

		// Throttle the scroll handler and bind this.
		this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

		// Handle scrolling
		$( document ).on( 'scroll', this.scroll );

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
	 * @since    2.1.5
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
	 * @since    2.1.5
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

		
		this.thumbnail_width = Math.floor( this.$( 'li:first' ).width() - 1 );
		this.thumbnail_height = Math.floor( this.thumbnail_width * 1.5 );

		$li.addClass( 'resized' ).css({
			width: this.thumbnail_width
		});
		$items.css({
			width:  this.thumbnail_width - 8,
			height: this.thumbnail_height - 12
		});
	},

	/**
	 * Create a view for a movie.
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    grid.model.Movie
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
	 * @since    2.1.5
	 * 
	 * @param    object    grid.model.Movie
	 * 
	 * @return   object    Backbone.View
	 */
	prepare: function() {

		if ( this.collection.length ) {
			this.views.set( this.collection.map( this.createSubView, this ) );
		} else {
			// Clear existing views
			this.views.unset();

			// Access this from deferred
			var self = this;
			// Loading...
			this.frame.$el.addClass( 'loading' );
			// Deferring
			this.dfd = this.collection.more().done( function() {
				self.frame.$el.removeClass( 'loading' );
				self.setColumns();
				self.scroll;
			} );
		}
	},

	/**
	 * Handle the infinite scroll.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   void
	 */
	scroll: function() {

		if ( true !== this.frame._scroll ) {
			return;
		}

		var  view = this,
		scrollTop = this.$window.scrollTop(),
		       el = this.options.scrollElement,
		    $last = this.$( 'li.movie:last' );

		// Already loading? Don't bother.
		if ( this._loading ) {
			return;
		}

		// Scroll elem is hidden or collection has no more movie
		if ( _.isUndefined( $last.offset() ) || ! $( el ).is( ':visible' ) || ! this.collection.hasMore() ) {
			this.frame.$el.removeClass( 'loading' );
			return;
		}

		// Don't go further if we're scrolling up
		if ( scrollTop <= this._lastPosition ) {
			return;
		}

		this._lastPosition = scrollTop;
		if ( scrollTop >= $last.offset().top - this.$window.height() ) {

			this._loading = true;
			this.frame.$el.addClass( 'loading' );

			var paged = this.collection.props.get( 'paged' ) || 1;
			this.dfd = this.collection.more({ paged: paged + 1 }).done( function() {
				view.frame.$el.removeClass( 'loading' );
				view._loading = false;
			} );

		} else {
			this.frame.$el.removeClass( 'loading' );
			this._loading = false;
		}
	}

});

/*grid.view.ContentExerpt = media.View.extend({

	id: 'grid-content-exerpt',

	template: media.template( 'wpmoly-grid-content-exerpt' ),
});*/

/**
 * WPMOLY Admin Movie Grid View
 * 
 * This View renders the Admin Movie Grid.
 * 
 * @since    2.1.5
 */
grid.view.Frame = media.View.extend({

	_mode: 'grid',

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

		this._createRegions();
		this._createStates();
	},

	/**
	 * Create the frame's regions.
	 * 
	 * @since    2.1.5
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
	 * @since    2.1.5
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
	 * @since    2.1.5
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
_.extend( grid.view.Frame.prototype, media.controller.StateMachine.prototype );

/**
 * WPMOLY Admin Movie Grid View
 * 
 * This View renders the Admin Movie Grid.
 * 
 * @since    2.1.5
 */
grid.view.GridFrame = grid.view.Frame.extend({

	template: media.template( 'wpmoly-grid-frame' ),

	regions: [ 'menu', 'content', 'pagination' ],

	_previousMode: '',

	_mode: '',

	_scroll: true,

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

		grid.view.Frame.prototype.initialize.apply( this, arguments );

		_.defaults( this.options, {
			mode:   'grid',
			state:  'library',
			library: {
				orderby: 'title',
				order:   'ASC',
				paged:   1
			},
			scroll: true
		});

		this.$bg   = $( '#wpmoly-grid-bg' );
		this.$body = $( document.body );
		this._mode = this.options.mode;
		this._scroll = this.options.scroll;

		this.createStates();
		this.bindHandlers();

		this.render();

		var self = this;
		this.$bg.one( 'click', function() {
			self.$body.removeClass( 'wpmoly-frame-open' );
			self.mode( 'grid' );
		} );
	},

	/**
	 * Bind events
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	bindHandlers: function() {

		//this.on( 'all', function( event ) { console.log( event ); }, this );
		this.on( 'change:mode', this.render, this );

		this.on( 'menu:create:frame', this.createMenu, this );
		this.on( 'menu:create:grid', this.createMenu, this );
		this.on( 'content:create:grid', this.createContentGrid, this );
		this.on( 'content:create:frame', this.createContentGrid, this );
		this.on( 'pagination:create:grid', this.createPaginationMenu, this );
		this.on( 'pagination:create:frame', this.createPaginationMenu, this );

		return this;
	},

	/**
	 * Create the default states on the frame.
	 * 
	 * @since    2.1.5
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
			new wpmoly.controller.State({
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
	 * @since    2.1.5
	 * 
	 * @param    object    Region
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	createMenu: function( region ) {

		var state = this.state();

		this.menu = region.view = new grid.view.Menu({
			frame:   this,
			model:   state,
			library: state.get( 'library' ),
		});
	},

	/**
	 * Create the Content Grid View
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    Region
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	createContentGrid: function( region ) {

		var state = this.state();

		this.content = region.view = new grid.view.ContentGrid({
			frame:      this,
			model:      state,
			collection: state.get( 'library' ),
			controller: this,
		});
	},

	/**
	 * Create the Menu View
	 * 
	 * This Content View show the WPMOLY 2.2 Movie Grid view
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    Region
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	createPaginationMenu: function( region ) {

		var state = this.state();

		this.pagination = region.view = new grid.view.PaginationMenu({
			frame:   this,
			model:   state,
			library: state.get( 'library' ),
		});
	},

	/**
	 * Render the View.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		grid.view.Frame.prototype.render.apply( this, arguments );

		this.$el.html( this.template() );

		if ( 'frame' == this._mode ) {
			this.$body.addClass( 'wpmoly-frame-open' );
		} else {
			this.$body.removeClass( 'wpmoly-frame-open' );
		}

		if ( '' != this._previousMode ) {
			this.$el.removeClass( 'mode-' + this._previousMode );
		}

		this.$el.addClass( 'mode-' + this._mode );

		_.each( this.regions, function( region ) {
			this[ region ].mode( this._mode );
		}, this );

		return this;
	},

	/**
	 * Switch mode.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	mode: function( mode ) {

		if ( ! mode )
			return this._mode;

		if ( mode === this._mode )
			return this;

		this._previousMode = this._mode;
		this._mode = mode;
		this.trigger( 'change:mode', mode );
		this.trigger( 'activate:' + mode );

		return this;
	}

});
