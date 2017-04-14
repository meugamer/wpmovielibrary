
wpmoly = window.wpmoly || {};

wpmoly.view.Grid = wpmoly.view.Grid || {};

wpmoly.view.Grid.Grid = wp.Backbone.View.extend({

	template: wp.template( 'wpmoly-grid' ),

	/**
	 * Initialize the View.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	initialize: function( options ) {

		this.controller = options.controller || {};

		// Wait for the controller's signal to render.
		this.listenToOnce( this.controller, 'ready', this.render );
		this.listenToOnce( this.controller, 'ready', this.setRegions );

		// Change Theme.
		this.listenTo( this.controller.settings, 'change:theme', this.changeTheme );
	},

	/**
	 * Update the grid classes on theme change.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	changeTheme: function( model, options ) {

		this.$el.removeClass( 'theme-' + model.previous( 'theme' ) );

		this.$el.addClass( 'theme-' + model.get( 'theme' ) );

		return this;
	},

	/**
	 * Set subviews.
	 * 
	 * The content is set to use the original grid content generated on the
	 * server to avoid reloading the grid directly on page load.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	setRegions: function() {

		var mode = this.controller.getMode(),
		 options = { controller : this.controller };

		if ( this.controller.canEdit() || this.controller.canCustomize() ) {
			this.menu = new wpmoly.view.Grid.Menu( options );
			this.views.set( '.grid-menu.settings-menu', this.menu );
		}

		if ( this.controller.canBrowse() ) {
			this.pagination = new wpmoly.view.Grid.Pagination( options );
			this.views.set( '.grid-menu.pagination-menu', this.pagination );
		}

		if ( this.controller.canEdit() ) {
			this.settings = new wpmoly.view.Grid.Settings( options );
			this.views.set( '.grid-settings', this.settings );
		}

		if ( this.controller.canCustomize() ) {
			this.customs = new wpmoly.view.Grid.Customs( options );
			this.views.set( '.grid-customs', this.customs );
		}

		// Use server-generated grid content first
		_.extend( options, { content : this.options.content } );

		if ( 'grid' === mode ) {
			this.content = new wpmoly.view.Grid.NodesGrid( options );
		} else if ( 'list' === mode ) {
			this.content = new wpmoly.view.Grid.NodesList( options );
		} else if ( 'archives' === mode ) {
			this.content = new wpmoly.view.Grid.NodesArchives( options );
		} else {
			this.content = new wpmoly.view.Grid.Nodes( options );
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
	setClassName: function() {

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
	setUniqueId: function() {

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
	render: function() {

		this.setClassName();
		this.setUniqueId();

		this.$el.html( this.template() );

		return this;
	}

});
