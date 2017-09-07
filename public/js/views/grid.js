wpmoly = window.wpmoly || {};

wpmoly.view.Grid = wp.Backbone.View.extend({

	template : wp.template( 'wpmoly-grid' ),

	/**
	 * Initialize the View.
	 *
	 * @since    3.0
	 */
	initialize : function( options ) {

		this.controller = options.controller || {};

		// Wait for the controller's signal to render.
		this.listenToOnce( this.controller, 'ready', this.render );
		this.listenToOnce( this.controller, 'ready', this.setRegions );

		// Change Theme.
		this.listenTo( this.controller.settings, 'change:mode',  this.setNodesView );
		this.listenTo( this.controller.settings, 'change:theme', this.changeTheme );

		// Prepare $el.
		this.on( 'prepare', this.setClassName );
		this.on( 'prepare', this.setUniqueId );
	},

	/**
	 * Update the grid classes on theme change.
	 *
	 * @since    3.0
	 *
	 * @return   Returns itself to allow chaining.
	 */
	changeTheme : function( model, options ) {

		this.$el.removeClass( 'theme-' + model.previous( 'theme' ) );

		this.$el.addClass( 'theme-' + model.get( 'theme' ) );

		return this;
	},

	/**
	 * Set subviews.
	 *
	 * @since    3.0
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setRegions : function() {

		this.setMenuView();
		this.setSettingsView();
		this.setCustomsView();
		this.setPaginationView();
		this.setNodesView();

		return this;
	},

	/**
	 * Set menu subview.
	 *
	 * @since    3.0
	 *
	 * @param    object    options
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setMenuView : function( options ) {

		if ( this.menu && ! options.silent ) {
			this.menu.remove();
		}

		if ( this.controller.canEdit() || this.controller.canCustomize() ) {
			this.menu = new wpmoly.view.GridMenu( { controller : this.controller } );
			this.views.set( '.grid-menu.settings-menu', this.menu );
		}

		return this;
	},

	/**
	 * Set settings subview.
	 *
	 * @since    3.0
	 *
	 * @param    object    options
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setSettingsView : function( options ) {

		if ( this.settings && ! options.silent ) {
			this.settings.remove();
		}

		if ( this.controller.canEdit() ) {
			this.settings = new wpmoly.view.GridSettings( { controller : this.controller } );
			this.views.set( '.grid-settings', this.settings );
		}

		return this;
	},

	/**
	 * Set customization subview.
	 *
	 * @since    3.0
	 *
	 * @param    object    options
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setCustomsView : function( options ) {

		if ( this.customs && ! options.silent ) {
			this.customs.remove();
		}

		if ( this.controller.canCustomize() ) {
			this.customs = new wpmoly.view.GridCustoms( { controller : this.controller } );
			this.views.set( '.grid-customs', this.customs );
		}

		return this;
	},

	/**
	 * Set pagination subview.
	 *
	 * @since    3.0
	 *
	 * @param    object    options
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setPaginationView : function( options ) {

		if ( this.pagination && ! options.silent ) {
			this.pagination.remove();
		}

		if ( this.controller.canBrowse() ) {
			this.pagination = new wpmoly.view.GridPagination( { controller : this.controller } );
			this.views.set( '.grid-menu.pagination-menu', this.pagination );
		}

		return this;
	},

	/**
	 * Set content subview.
	 *
	 * The content is set to use the original grid content generated on the
	 * server to avoid reloading the grid directly on page load.
	 *
	 * @since    3.0
	 *
	 * @param    object    options
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setNodesView : function( options ) {

		if ( this.content && ! options.silent ) {
			this.content.remove();
		}

		var mode = this.controller.getMode(),
		 options = { controller : this.controller };

		// Use server-generated grid content first
		_.extend( options, { content : this.options.content } );

		if ( 'grid' === mode ) {
			this.content = new wpmoly.view.GridNodesGrid( options );
		} else if ( 'list' === mode ) {
			this.content = new wpmoly.view.GridNodesList( options );
		} else if ( 'archives' === mode ) {
			this.content = new wpmoly.view.GridNodesArchives( options );
		} else {
			this.content = new wpmoly.view.GridNodes( options );
		}

		this.views.set( '.grid-content', this.content );

		return this;
	},

	/**
	 * Set $el class names depending on settings.
	 *
	 * @since    3.0
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setClassName : function() {

		var settings = this.controller.settings,
		    className = [ 'wpmoly' ];

		className.push( settings.get( 'type' ) );
		className.push( settings.get( 'mode' ) );
		className.push( 'theme-' + settings.get( 'theme' ) );

		this.className = className.join( ' ' );

		this.$el.addClass( this.className );

		return this;
	},

	/**
	 * Set a unique ID for $el.
	 *
	 * @since    3.0
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setUniqueId : function() {

		var post_id = this.controller.get( 'post_id' ),
		    grid_id = 'grid-' + post_id;

		if ( ! this.uniqid ) {
			this.uniqid = _.uniqueId( grid_id + '-' );
		}

		this.$el.prop( 'id', 'wpmoly-' + this.uniqid );

		this.$el.addClass( grid_id, this.uniqid );
	},

	/**
	 * Render the view.
	 *
	 * @since    3.0
	 *
	 * @return   Returns itself to allow chaining.
	 */
	/*render : function() {

		this.setClassName();
		this.setUniqueId();

		this.$el.html( this.template() );

		return this;
	}*/

});
