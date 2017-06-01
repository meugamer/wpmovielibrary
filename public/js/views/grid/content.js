
wpmoly = window.wpmoly || {};

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
wpmoly.view.GridNode = wp.Backbone.View.extend({

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

		this.listenTo( this.controller.settings, 'change:theme', this.render );

		this.on( 'prepare', this.setTemplate );
		this.on( 'prepare', this.setClassName );
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
		   theme = this.controller.settings.get( 'theme' ),
		template = 'wpmoly-grid-' + type + '-' + mode;

		if ( theme && 'default' !== theme ) {
			template += '-' + theme;
		}

		this.template = wp.template( template );

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
	 * Prepare the View rendering options.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	prepare : function() {

		var options = {
			node     : this.model,
			settings : this.controller.settings
		};

		return options;
	},

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
wpmoly.view.GridListNode = wpmoly.view.GridNode.extend({

	tagName: 'li'

});

wpmoly.view.GridArchiveNode = wpmoly.view.GridNode.extend({

	

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
wpmoly.view.GridNodes = wp.Backbone.View.extend({

	className : 'grid-content-inner',

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

		// Remove no-JS content
		//this.listenToOnce( this.collection, 'add', this.preEmpty );

		// Add views for new models
		this.listenTo( this.collection, 'add',    this.addNode );
		this.listenTo( this.collection, 'remove', this.removeNode );

		// Set grid as loading when reset
		this.listenTo( this.collection, 'reset',   this.loading );
		this.listenTo( this.collection, 'request', this.loading );
		this.listenTo( this.collection, 'sync',    this.loaded );

		// Set grid as loaded when fetch is done
		this.listenTo( this.collection, 'sync', _.debounce( this.adjust, 50 ) );

		// Notify query errors
		this.listenTo( this.controller.query, 'fetch:failed', this.notifyError );

		// Switch themes
		this.listenTo( this.controller.settings, 'change:theme', this.adjust );
	},

	/**
	 * Notify API request errors.
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
		nodeType = wpmoly.view.GridNode;

		if ( 'list' === this.controller.getMode() ) {
			nodeType = wpmoly.view.GridListNode;
		} else if ( 'list' === this.controller.getMode() ) {
			nodeType = wpmoly.view.GridArchiveNode;
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

		this.views.remove();

		this.$el.empty();
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
	 * TODO prevent this from running twice
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

		if ( ! this.collection.length ) {
			// Replace $el with pre-generated content
			this.$el.html( wpmoly.$( this.options.content || '' ).html() );
		} else {
			this.collection.each( this.addNode, this );
		}
	},

	/**
	 * Empty the $el.
	 * 
	 * Get of rid of pre-generated content.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	preEmpty: function() {

		if ( ! this.rendered ) {
			this.rendered = true;
			this.$el.empty();
		}

		return this;
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
wpmoly.view.GridNodesGrid = wpmoly.view.GridNodes.extend({

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

		if ( 'movie' === this.settings.get( 'type' ) ) {
			ratio = 1.5;
		}

		if ( ( Math.floor( innerWidth / columns ) - 8 ) > idealWidth ) {
			++columns;
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
wpmoly.view.GridNodesList = wpmoly.view.GridNodes.extend({

	tagName: 'ul',

	/**
	 * Adjust content nodes to fit the grid.
	 * 
	 * TODO handle this by UL columns rather than width
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

		wpmoly.view.GridNodes.prototype.render.apply( this, arguments );

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
wpmoly.view.GridNodesArchives = wpmoly.view.GridNodes.extend({

	
});
