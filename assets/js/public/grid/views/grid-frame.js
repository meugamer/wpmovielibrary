
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

	props: new Backbone.Model,

	pages: new Backbone.Model,

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

		this.createStates();
		this.bindHandlers();

		this.props.set({
			mode:   this.options.mode,
			scroll: this.options.scroll
		});

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

		this.on( 'menu:create:frame',       this.createMenu, this );
		this.on( 'menu:create:grid',        this.createMenu, this );
		this.on( 'content:create:grid',     this.createContentGrid, this );
		this.on( 'content:create:frame',    this.createContentGrid, this );
		this.on( 'pagination:create:grid',  this.createPaginationMenu, this );
		this.on( 'pagination:create:frame', this.createPaginationMenu, this );

		this.props.on( 'change:mode',       this._setMode, this );
		this.props.on( 'change:scroll',     this._setScroll, this );

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
				library: grid.query( options.library, this )
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

		this.gridmenu = region.view = new grid.view.Menu({
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

		this.gridcontent = region.view = new grid.view.ContentGrid({
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

		this.gridpagination = region.view = new grid.view.PaginationMenu({
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

		this.props.set({ mode: mode });

		return this;
	},

	_setMode: function( model ) {

		var mode = model.changed.mode;

		if ( mode != this._previousMode ) {
			this.trigger( 'deactivate:' + this._previousMode );
			this.trigger( 'activate:' + mode );
		}

		this._previousMode = this._mode;
		this._mode         = model.changed.mode;

		this.render();

		return this;
	},

	_setScroll: function( model ) {

		this._scroll = model.changed.scroll;

		return this;
	}

});
