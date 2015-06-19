
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
		//$( document ).on( 'scroll', this.scroll );

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

			this.dfd = this.collection.more().done( function() {
				view.frame.$el.removeClass( 'loading' );
				view._loading = false;
			} );

		} else {
			this.frame.$el.removeClass( 'loading' );
			this._loading = false;
		}
	}

});
