
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

/**
 * Single grid item view.
 * 
 * This is a generic view, it can be extended to add specific per-node-type
 * features.
 * 
 * @since    3.0
 * 
 * @param    {object}    [options]             View options.
 * @param    {object}    options.model         View related Backbone.Model object.
 * @param    {object}    options.controller    Grid controller.
 */
Grid.Node = wp.Backbone.View.extend({

	/**
	 * Initialize the View.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    options
	 * 
	 * @return   void
	 */
	initialize: function( options ) {

		this.model = options.model || {};
		this.controller = options.controller || {};

		this.template = this.setTemplate();
	},

	/**
	 * Set the View template based on settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   wp.template()
	 */
	setTemplate: function() {

		var type = this.controller.settings.get( 'type' ),
		    mode = this.controller.settings.get( 'mode' ),
		template = 'wpmoly-grid-' + type + '-' + mode;

		return wp.template( template );
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
		   className = [ 'node' ];

		if ( 'movie' === settings.get( 'type' ) ) {
			className.push( 'post-node' );
		} else if ( _.contains( [ 'actor', 'collection', 'genre' ], settings.get( 'type' ) ) ) {
			className.push( 'term-node' );
		}

		className.push( settings.get( 'type' ) );

		this.className = className.join( ' ' );

		this.$el.addClass( this.className );

		return this;
	},

	/**
	 * Render the View.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		var data = {
			node     : this.model,
			settings : this.controller.settings
		};

		this.setClassName();

		this.$el.html( this.template( data ) );
	}

});

/**
 * Single list-mode grid item view.
 * 
 * Simply changes the View's tagName property to 'li'.
 * 
 * @since    3.0
 * 
 * @param    {object}    [options]             View options.
 * @param    {object}    options.model         View related Backbone.Model object.
 * @param    {object}    options.controller    Grid controller.
 */
Grid.ListNode = Grid.Node.extend({

	tagName: 'li'

});

/**
 * Grid items container view.
 * 
 * This is a generic view, it can be extended to add specific per-node-type
 * features.
 * 
 * @since    3.0
 * 
 * @param    {object}    [options]             View options.
 * @param    {object}    options.controller    Grid controller.
 * @param    {object}    options.collection    Grid collection.
 */
Grid.Nodes = wp.Backbone.View.extend({

	className : 'grid-content-inner loading',

	/**
	 * Initialize the View.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    options
	 * 
	 * @return   void
	 */
	initialize: function( options ) {

		this.controller = options.controller || {};
		this.collection = this.controller.query.collection;

		this.$window  = wpmoly.$( window );
		this.resizeEvent = 'resize.grid-' + this.controller.uniqid;

		this.settings = this.controller.settings;
		this.rendered = false;

		this.nodes = {};

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

		_.bindAll( this, 'adjust' );

		this.on( 'ready', this.adjust );

		// Adjust subviews dimensions on resize
		this.$window.off( this.resizeEvent ).on( this.resizeEvent, _.debounce( this.adjust, 50 ) );

		// Add views for new models
		this.listenTo( this.collection, 'add', this.addNode );
		this.listenTo( this.collection, 'remove', this.removeNode );

		// Set grid as loading when reset
		this.listenTo( this.collection, 'reset',   this.loading );
		this.listenTo( this.collection, 'request', this.loading );
		this.listenTo( this.collection, 'sync',    this.loaded );

		// Set grid as loaded when fetch is done
		this.listenTo( this.collection, 'sync', _.debounce( this.adjust, 50 ) );

		this.listenTo( this.controller.query, 'fetch:failed', this.notifyError );

		/*this.listenTo( this.controller.settings, 'change:list_columns', function( model, value, options ) {
			this.$el.attr( 'data-columns', value );
		} );*/
	},

	/**
	 * Notifiy API request errors.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    collection
	 * @param    object    xhr
	 * @param    object    options
	 * 
	 * @return    Returns itself to allow chaining.
	 */
	notifyError: function( collection, xhr, options ) {

		var message;
		if ( ! _.isUndefined( xhr.responseJSON.message ) ) {
			message = xhr.responseJSON.message;
			if ( ! _.isEmpty( xhr.responseJSON.data.params ) ) {
				message += '<br />';
				_.each( xhr.responseJSON.data.params, function( param ) {
					message += '<small>' + param + '</small>';
				} );
			}
		} else {
			message = wpmolyL10n.restAPIError;
		}

		this.$el.html( '<div class="wpmoly error notice"><div class="notice-content"><p>' + message + '</p></div><div class="notice-footnote">' + wpmolyL10n.restAPIErrorFootnote + '</div></div>' );

		this.loaded();

		return this;
	},

	/**
	 * Add a new subview.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    model
	 * @param    object    collection
	 * 
	 * @return    Returns itself to allow chaining.
	 */
	addNode: function( model, collection ) {

		var node = model.get( 'id' ),
		nodeType = Grid.Node;

		if ( 'list' === this.controller.settings.get( 'mode' ) ) {
			nodeType = Grid.ListNode;
		}

		if ( ! this.nodes[ node ] ) {
			this.nodes[ node ] = new nodeType({
				controller : this.controller,
				collection : collection,
				model      : model
			});
		}

		this.views.add( this.nodes[ node ] );

		return this;
	},

	/**
	 * Add an existing subview.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    model
	 * @param    object    collection
	 * @param    object    options
	 * 
	 * @return    Returns itself to allow chaining.
	 */
	removeNode: function( model, collection, options ) {

		var node = model.get( 'id' );

		if ( this.nodes[ node ] ) {
			this.nodes[ node ].remove();
		}

		return this;
	},

	/**
	 * Set grid as loading.
	 * 
	 * @since    3.0
	 * 
	 * @return    Returns itself to allow chaining.
	 */
	loading: function() {

		wpmoly.$( 'body,html' ).animate( { scrollTop : this.views.parent.$el.offset().top - 48 }, 250 );

		this.$el.addClass( 'loading' );

		return this;
	},

	/**
	 * Set grid as loaded.
	 * 
	 * @since    3.0
	 * 
	 * @return    Returns itself to allow chaining.
	 */
	loaded: function() {

		this.$el.removeClass( 'loading' );

		return this;
	},

	/**
	 * Adjust content nodes to fit the grid.
	 * 
	 * Should be extended.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	adjust: function() {

		return this;
	},

	/**
	 * Render the View.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		wp.Backbone.View.prototype.render.apply( this, arguments );

		this.$el.addClass( this.controller.settings.get( 'mode' ) );
	}

});

/**
 * Grid items grid-mode container view.
 * 
 * Override this.adjust() to automatically fit items on page resizing.
 * 
 * @since    3.0
 * 
 * @param    {object}    [options]             View options.
 * @param    {object}    options.controller    Grid controller.
 * @param    {object}    options.collection    Grid collection.
 */
Grid.NodesGrid = Grid.Nodes.extend({

	/**
	 * Adjust content nodes to fit the grid.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	adjust: function() {

		var columns = this.controller.settings.get( 'columns' ),
		       rows = this.controller.settings.get( 'rows' ),
		 idealWidth = this.controller.settings.get( 'column_width' ),
		 innerWidth = this.$el.width()
		      ratio = 1.25;

		console.log( this.controller.settings );
		if ( 'movie' === this.settings.get( 'type' ) ) {
			ratio = 1.5;
		}

		if ( ( Math.floor( innerWidth / columns ) - 8 ) < idealWidth ) {
			--columns;
		}

		this.columnWidth  = Math.floor( innerWidth / columns ) - 8;
		this.columnHeight = Math.floor( this.columnWidth * ratio );

		this.$el.addClass( columns + '-columns' );

		this.$( '.node' ).addClass( 'adjusted' ).css({
			width : this.columnWidth
		});

		this.$( '.node-thumbnail' ).addClass( 'adjusted' ).css({
			height : this.columnHeight,
			width  : this.columnWidth
		});
	}
});

/**
 * Grid items list-mode container view.
 * 
 * Override this.adjust() to automatically fit column number on page resizing.
 * 
 * @since    3.0
 * 
 * @param    {object}    [options]             View options.
 * @param    {object}    options.controller    Grid controller.
 * @param    {object}    options.collection    Grid collection.
 */
Grid.NodesList = Grid.Nodes.extend({

	tagName: 'ul',

	/**
	 * Adjust content nodes to fit the grid.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	adjust: function() {

		var columns = this.controller.settings.get( 'list_columns' ),
		 idealWidth = this.controller.settings.get( 'column_width' ),
		 innerWidth = this.$el.width();

		if ( ( Math.floor( innerWidth / columns ) - 8 ) < idealWidth ) {
			--columns;
		}

		this.columnWidth  = Math.floor( innerWidth / columns ) - 8;

		this.$el.addClass( 'nodes-' + columns + '-columns-list' );
	},

	/**
	 * Render the View.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		Grid.Nodes.prototype.render.apply( this, arguments );

		this.$el.addClass( 'nodes-list' );
	}

});

/**
 * Grid items container view.
 * 
 * @since    3.0
 * 
 * @param    {object}    [options]             View options.
 * @param    {object}    options.controller    Grid controller.
 * @param    {object}    options.collection    Grid collection.
 */
Grid.NodesArchives = Grid.Nodes.extend({

	
});
