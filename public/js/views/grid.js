
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

		this.render();

		this.set_regions();

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

		// Switch themes
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
	set_regions: function() {

		var settings = this.controller.settings,
		     preview = this.controller.preview,
		        mode = this.controller.settings.get( 'mode' ),
		     options = { controller : this.controller };

		if ( preview || settings.get( 'settings_control' ) || settings.get( 'customs_control' ) ) {
			this.menu = new wpmoly.view.Grid.Menu( options );
			this.views.set( '.grid-menu.settings-menu', this.menu );
		}

		if ( preview || settings.get( 'enable_pagination' ) ) {
			this.pagination = new wpmoly.view.Grid.Pagination( options );
			this.views.set( '.grid-menu.pagination-menu', this.pagination );
		}

		if ( settings.get( 'settings_control' ) ) {
			this.settings = new wpmoly.view.Grid.Settings( options );
			this.views.set( '.grid-settings', this.settings );
		}

		if ( settings.get( 'customs_control' ) ) {
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

		this.$el.prop( 'id', 'wpmoly-' + this.controller.uniqid );

		this.$el.addClass( 'grid-' + this.controller.get( 'post_id' ), this.controller.uniqid );
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
