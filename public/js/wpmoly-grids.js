
window.wpmoly = window.wpmoly || {};

( function( $, _, Backbone ) {

	/**
	 * Create a new Grid instance.
	 *
	 * @since    3.0
	 *
	 * @param    {Element}    grid Grid DOM element.
	 *
	 * @return   {object}     Grid instance.
	 */
	var Grid = function( grid ) {

		// Set a unique grid ID to the grid element.
		grid.id  = _.uniqueId( grid.id + '-' );

		// Store unique and post ID.
		var grid_id = parseInt( grid.getAttribute( 'data-grid' ) ),
		   selector = grid.id;

		// Grid controller.
		var controller = new Grids.controller.Grid(
			{
				post_id : this.id
			},
			JSON.parse( grid.querySelector( '.grid-json' ).textContent || '{}' )
		);

		// Grid view.
		var view = new Grids.view.Grid({
			el         : grid,
			content    : grid.querySelector( '.grid-content-inner' ),
			controller : controller
		});

		var query    = controller.query;
		var settings = controller.settings;

		/**
		 * Grid instance.
		 *
		 * Provide a set of useful functions to interact with the Grid
		 * without directly calling controllers and views.
		 *
		 * @since    3.0
		 */
		var grid = {

			/**
			 * Grid ID.
			 *
			 * @since    3.0
			 *
			 * @var      int
			 */
			grid_id : grid_id,

			/**
			 * Grid selector.
			 *
			 * @since    3.0
			 *
			 * @var      string
			 */
			selector : selector,

			/**
			 * Retrieve grid type.
			 *
			 * @since    3.0
			 *
			 * @return   {string}
			 */
			getType : function() {

				return settings.get( 'type' );
			},

			/**
			 * Retrieve grid mode.
			 *
			 * @since    3.0
			 *
			 * @return   {string}
			 */
			getMode : function() {

				return settings.get( 'mode' );
			},

			/**
			 * Retrieve grid theme.
			 *
			 * @since    3.0
			 *
			 * @return   {string}
			 */
			getTheme : function() {

				return settings.get( 'theme' );
			},

			/**
			 * Set grid type.
			 *
			 * @since    3.0
			 *
			 * @param    {string}    type New grid type.
			 *
			 * @return   {object}
			 */
			setType : function( type ) {

				return settings.set({
					type  : type,
					mode  : 'grid',
					theme : 'default',
				});
			},

			/**
			 * Set grid mode.
			 *
			 * @since    3.0
			 *
			 * @param    {string}    mode New grid mode.
			 *
			 * @return   {object}
			 */
			setMode : function( mode ) {

				return settings.set({
					mode  : mode,
					theme : 'default',
				});
			},

			/**
			 * Set grid theme.
			 *
			 * @since    3.0
			 *
			 * @param    {string}    theme New grid theme.
			 *
			 * @return   {object}
			 */
			setTheme : function( theme ) {

				return settings.set( { theme : theme } );
			},

			/**
			 * Retrieve grid settings.
			 *
			 * If no specific property is passed, return the full
			 * settings array.
			 *
			 * @since    3.0
			 *
			 * @param    {string}    property Grid setting name.
			 *
			 * @return   {mixed}
			 */
			get : function( attribute ) {

				if ( ! attribute ) {
					return settings.attributes;
				}

				return settings.get( attribute );
			},

			/**
			 * Set grid settings.
			 *
			 * @since    3.0
			 *
			 * @param    {object}    attributes Settings list.
			 *
			 * @return   {mixed}
			 */
			set : function( attributes ) {

				return settings.set( attributes );
			},

			/**
			 * Render the grid.
			 *
			 * @since    3.0
			 *
			 * @return   {object}    View
			 */
			refresh : function() {

				return view.setRegions();
			},

			/**
			 * Reload the grid content.
			 *
			 * @since    3.0
			 *
			 * @return   {Promise}
			 */
			reload : function() {

				return query.fetch();
			},

			/**
			 * Restore the grid to the default content.
			 *
			 * @since    3.0
			 *
			 * @return   {object}
			 */
			reset : function() {

				return query.fetch();
			},

		};

		return grid;
	};

	/**
	 * Grids Wrapper.
	 *
	 * Store controllers, views and grids objects.
	 *
	 * @since    3.0
	 */
	var Grids = wpmoly.grids = {

		/**
		 * List of grid instances.
		 *
		 * This should not be used directly. Use Grids.get()
		 * instead.
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		grids : [],

		/**
		 * List of grid models.
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		model : {},

		/**
		 * List of grid controllers.
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		controller : {},

		/**
		 * List of grid views.
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		view : {},

		/**
		 * Retrieve Grid instances.
		 *
		 * Grids can have multiple instances. Use Grid.find() to retrieve
		 * a specific instance.
		 *
		 * @since    3.0
		 *
		 * @param    {int}       grid_id Grid ID.
		 *
		 * @return   {array}     List of Grid instances.
		 */
		get : function( grid_id ) {

			return _.where( this.grids, { grid_id : grid_id } );
		},

		/**
		 * Retrieve a Grid instance.
		 *
		 * Grids can have multiple instances. Use Grid.get() to retrieve
		 * a list of all instances for a specific Grid.
		 *
		 * @since    3.0
		 *
		 * @param    {string}    selector Grid unique identifier.
		 *
		 * @return   {Grid}      Grid instance.
		 */
		find : function( selector ) {

			return _.find( this.grids, { selector : selector } );
		},

		/**
		 * Add a Grid instance.
		 *
		 * @since    3.0
		 *
		 * @param    {string}    grid Grid unique identifier.
		 *
		 * @return   {Grid}      Grid instance.
		 */
		add : function( grid ) {

			var grid = new Grid( grid );

			this.grids.push( grid );

			return grid;
		},
	};

	/**
	 * Grid Settings Model.
	 *
	 * Store the grid settings and parameters.
	 *
	 * @since    3.0
	 */
	Grids.model.Settings = Backbone.Model.extend({

		defaults : {
			type              : '',
			mode              : 'grid',
			theme             : 'default',
			preset            : '',
			columns           : 5,
			rows              : 4,
			column_width      : 160,
			row_height        : 240,
			list_columns      : 3,
			list_column_width : 240,
			list_rows         : 8,
			enable_ajax       : 1,
			enable_pagination : 1,
			settings_control  : 1,
			custom_letter     : 1,
			custom_order      : 1,
			customs_control   : 0,
			custom_mode       : 0,
			custom_content    : 0,
			custom_display    : 0
		},

		/**
		 * Initialize the Model.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    attributes
		 * @param    {object}    options
		 */
		initialize : function( attributes, options ) {

			var options = options || {},
			   booleans = [ 'enable_ajax', 'enable_pagination', 'settings_control', 'custom_letter', 'custom_order', 'customs_control', 'custom_mode', 'custom_content', 'custom_display' ],
			   integers = [ 'columns', 'rows', 'column_width', 'row_height', 'list_columns', 'list_column_width', 'list_rows' ];

			_.each( attributes, function( value, key ) {
				if ( _.isNull( value ) && _.has( this.defaults, key ) ) {
					this.set( key, this.defaults[ key ], { silent : true } );
				} else if ( ! _.isNull( value ) ) {
					if ( _.contains( booleans, key ) ) {
						this.set( key, !! value, { silent : true } );
					} else if ( _.contains( integers, key ) ) {
						this.set( key, parseInt( value ) || this.defaults[ key ], { silent : true } );
					}
				}
			}, this );

		},

	});

	/**
	 * Grid query Controller.
	 *
	 * Use the Backbone REST API Client to retrieve the grid elements from
	 * the REST API.
	 *
	 * @since    3.0
	 */
	Grids.controller.Query = Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    attributes
		 * @param    {object}    options
		 */
		initialize : function( attributes, options ) {

			this.controller = options.controller;

			this.setDefaults();
			this.setCollection();

			this.on( 'fetch:done', this.setState, this );
			this.state = new Backbone.Model({
				currentPage : parseInt( options.current_page ) || '',
				totalPages  : parseInt( options.total_page ) || ''
			});

			this.listenTo( this.controller.settings, 'change:type', this.changeType );
		},

		/**
		 * Set defaults attributes depending on grid type.
		 *
		 * Posts and Taxonomies don't support the same 'orderby' value
		 * and shouldn't be ordered the same way.
		 *
		 * @since    3.0
		 */
		setDefaults : function() {

			if ( this.isPost() ) {
				this.set({
					order   : 'desc',
					orderby : 'date',
				}, {
					silent  : true,
				});
			} else if ( this.isTaxonomy() ) {
				this.set({
					order   : 'asc',
					orderby : 'name',
				}, {
					silent  : true,
				});
			}
		},

		/**
		 * Load REST API Backbone client.
		 *
		 * @since    3.0
		 */
		setCollection : function() {

			var type = this.controller.settings.get( 'type' ),
			collections = {
				movie      : wp.api.collections.Movies,
				actor      : wp.api.collections.Actors,
				collection : wp.api.collections.Collections,
				genre      : wp.api.collections.Genres
			};

			if ( ! _.has( collections, type ) ) {
				return wpmoly.error( 'missing-api-collection', wpmolyL10n.api.missing_collection );
			}

			this.collection = new collections[ type ];

			this.mirrorEvents();
		},

		/**
		 * Set custom collection events.
		 *
		 * The query collection is directly related to the grid type so
		 * we have to use custom events to reflect the collection changes
		 * to a different collection that will be used to display the
		 * actual grid content.
		 *
		 * @since    3.0
		 */
		mirrorEvents : function() {

			this.stopListening( this.collection );

			this.listenTo( this.collection, 'request', function( collection, xhr, options ) {
				this.trigger( 'fetch:start', collection, xhr, options );
			} );

			this.listenTo( this.collection, 'reset', function( collection, xhr, options ) {
				this.trigger( 'collection:reset', collection, xhr, options );
			} );

			this.listenTo( this.collection, 'add', function( model, collection, options ) {
				this.trigger( 'collection:add', model, collection, options );
			} );

			this.listenTo( this.collection, 'remove', function( model, collection, options ) {
				this.trigger( 'collection:remove', model, collection, options );
			} );

			this.listenTo( this.collection, 'update', function( collection, options ) {
				this.trigger( 'collection:update', collection, options );
			} );
		},

		/**
		 * Update collection state: current page, total pages...
		 *
		 * @since    3.0
		 *
		 * @param    {object}    collection
		 * @param    {array}     response
		 * @param    {object}    options
		 */
		setState : function( collection, response, options ) {

			if ( ! collection.state ) {
				return false;
			}

			this.state.set( collection.state );
		},

		/**
		 * Update the grid's type of content.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model Settings Model
		 * @param    {string}    value New type
		 * @param    {object}    options Options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		changeType : function( model, value, options ) {

			this.setCollection();
			this.setDefaults();

			this.fetch();

			return this;
		},

		/**
		 * Prefetch nodes. Should be used when initializing the controller.
		 *
		 * @since    3.0
		 *
		 * @return   Promise
		 */
		preFetch : function() {

			return this.fetch();
		},

		/**
		 * Prepare query parameters.
		 *
		 * Filter the attributes and return a list of supported query
		 * parameters.
		 *
		 * @since    3.0
		 *
		 * @return   {object}
		 */
		prepare : function() {

			var options = {};
			if ( ! this.collection.options || _.isEmpty( this.attributes ) ) {
				return options;
			}

			_.each( this.attributes, function( value, key ) {
				if ( _.has( this.collection.options, key ) ) {
					var o = this.collection.options[ key ];
					if ( 'integer' === o.type ) {
						// TODO implement limits
						if ( ! value ) {
							options[ key ] = o.default;
						} else {
							options[ key ] = value;
						}
					} else if ( 'string' === o.type ) {
						if ( ! _.contains( o.enum, value ) ) {
							options[ key ] = o.default;
						} else {
							options[ key ] = value;
						}
					}
				}
			}, this );

			return options;
		},

		/**
		 * Wrapper method for collection.fetch().
		 *
		 * Original fetch method is wrapped to trigger custom events
		 * before and after fetching nodes.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 *
		 * @return   Promise
		 */
		fetch : function( options ) {

			if ( ! this.collection ) {
				this.load();
			}

			this.calculateNumber();

			var self = this,
			 options = _.extend( options || {}, { data : this.prepare() } );

			options.error = function( collection, xhr, options ) {
				self.trigger( 'fetch:failed', collection, xhr, options );
			};

			options.success = function( collection, response, options ) {
				self.trigger( 'fetch:done', collection, response, options );
			};

			options.complete = function() {
				self.trigger( 'fetch:stop' );
			};

			return this.collection.fetch( options );
		},

		/**
		 * Calculate the number of nodes to query from the number of
		 * columns and rows of the grid.
		 *
		 * TODO take into account the actual number of columns as it can
		 * changes with screen dimensions.
		 *
		 * @since    3.0
		 */
		calculateNumber : function() {

			if ( 'grid' == this.controller.settings.get( 'mode' ) ) {
				var columns = this.controller.settings.get( 'columns' ),
				       rows = this.controller.settings.get( 'rows' );
			} else if ( 'list' == this.controller.settings.get( 'mode' ) ) {
				var columns = this.controller.settings.get( 'list_columns' ),
				       rows = this.controller.settings.get( 'list_rows' );
			} else {
				return false;
			}

			if ( this.isPost() ) {
				var attr = 'per_page';
			} else if ( this.isTaxonomy() ) {
				var attr = 'number';
			} else {
				return false;
			}

			this.set( attr, Math.round( columns * rows ), { silent : true } );
		},

		/**
		 * Are we dealing with taxonomies?
		 *
		 * @since    3.0
		 *
		 * @return   {boolean}
		 */
		isTaxonomy : function() {

			return _.contains( [ 'actor', 'collection', 'genre' ], this.controller.settings.get( 'type' ) );
		},

		/**
		 * Are we dealing with posts?
		 *
		 * @since    3.0
		 *
		 * @return   {boolean}
		 */
		isPost : function() {

			return _.contains( [ 'movie' ], this.controller.settings.get( 'type' ) );
		}

	});

	/**
	 * Main Grid Controller.
	 *
	 * @since    3.0
	 */
	Grids.controller.Grid = Backbone.Model.extend({

		defaults : {
			submenu : '',
		},

		/**
		 * Initialize the Model.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    attributes
		 * @param    {object}    options
		 */
		initialize : function( attributes, options ) {

			this.settings = new Grids.model.Settings( options.settings || {} );

			this.setQuery( options || {} );

			// Preload nodes
			if ( options.prefetch ) {
				this.query.preFetch();
			}
		},

		/**
		 * Set the Query controller.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 */
		setQuery : function( options ) {

			var options = _.defaults( options || {}, {
				query_args : {},
				query_data : {}
			} );

			this.query = new Grids.controller.Query(
				options.query_args,
				_.extend( options.query_data, {
					type       : this.settings.get( 'type' ),
					controller : this,
				} )
			);

			// Browsing
			this.listenTo( this.query, 'change', this._updateQuery );
		},

		/**
		 * Alternative to yet-to-be-implemented Ajax browsing: update
		 * URL and reload the page.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model
		 * @param    {object}    options
		 */
		_updateQuery : function( model, options ) {

			this.settingsOpened = false;
			this.customsOpened  = false;

			if ( this.isDynamic() ) {
				return this.query.fetch( model.attributes );
			}

			var query = _.defaults( this._parseSearchQuery(), {
				id : this.get( 'post_id' )
			} );

			_.each( model.changed, function( value, key ) {
				query[ key ] = value;
			} );

			var url = window.location.origin + window.location.pathname;

			window.location.href = url + this._buildSearchQuery( query );
		},

		/**
		 * Parse URL to extract settings.
		 *
		 * Grid settings can be passed through URL to keep history and
		 * handle Ajax browsing deactivation.
		 *
		 * @since    3.0
		 *
		 * @return   {object}
		 */
		_parseSearchQuery : function() {

			var search = wpmoly.utils.getURLParameter( 'grid' );
			if ( ! search ) {
				return {};
			}

			var query = {},
			    regexp = new RegExp( '^([A-Za-z]+):([A-Za-z0-9]+)$' );
			_.each( search.split( ',' ), function( param ) {
				var rparam = regexp.exec( param );
				if ( ! _.isNull( rparam ) ) {
					query[ rparam[1] ] = rparam[2];
				}
			} );

			return query;
		},

		/**
		 * Build a new URL parameter to contain the grid settings.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    query
		 *
		 * @return   {string}
		 */
		_buildSearchQuery : function( query ) {

			var _query = [];
			_.each( query, function( value, param ) {
				_query.push( param + ':' + value );
			} );

			return '?grid=' + _query.join( ',' );
		},

		/**
		 * Is settings edition enabled?
		 *
		 * @since    3.0
		 *
		 * @return   {boolean}
		 */
		canEdit : function() {

			return true === this.settings.get( 'settings_control' );
		},

		/**
		 * Is customization enabled?
		 *
		 * @since    3.0
		 *
		 * @return   {boolean}
		 */
		canCustomize : function() {

			return true === this.settings.get( 'customs_control' );
		},

		/**
		 * Is pagination enabled?
		 *
		 * @since    3.0
		 *
		 * @return   {boolean}
		 */
		canBrowse : function() {

			return true === this.settings.get( 'enable_pagination' );
		},

		/**
		 * Is Ajax browsing enabled?
		 *
		 * @since    3.0
		 *
		 * @return   {boolean}
		 */
		isDynamic : function() {

			return true === this.settings.get( 'enable_ajax' );
		},

		/**
		 * Open the settings menu.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		openSettings : function() {

			this.set( { submenu : 'settings' } );

			return this;
		},

		/**
		 * Open the customization menu.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		openCustoms : function() {

			this.set( { submenu : 'customs' } );

			return this;
		},

		/**
		 * Close grid menu.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		closeMenu : function() {

			this.set( { submenu : false } );

			return this;
		},

		/**
		 * Update settings.
		 *
		 * @since    3.0
		 *
		 * @param    object    settings
		 * @param    object    options
		 *
		 * @return   {object}
		 */
		setSettings : function( settings, options ) {

			return this.settings.set( settings, options );
		},

		/**
		 * Retrieve setting value.
		 *
		 * @since    3.0
		 *
		 * @param    string    property
		 *
		 * @return   {object}
		 */
		getSetting : function( property ) {

			return this.settings.get( property );
		},

		/**
		 * Check if a specific page number matches the current page number.
		 *
		 * @since    3.0
		 *
		 * @param    int    page Page number.
		 *
		 * @return   {boolean}
		 */
		isCurrentPage : function( page ) {

			var page = parseInt( page );

			return page === this.getCurrentPage();
		},

		/**
		 * Check if a specific page number is reachable.
		 *
		 * @since    3.0
		 *
		 * @param    int    page Page number.
		 *
		 * @return   {boolean}
		 */
		isBrowsable : function( page ) {

			var page = parseInt( page );

			return 1 <= page && page <= this.getTotalPages() && ! this.isCurrentPage( page );
		},

		/**
		 * Jump to the specific page number after making sure that number is
		 * reachable.
		 *
		 * @since    3.0
		 *
		 * @param    {int}    page Page number.
		 *
		 * @return   {int}
		 */
		setCurrentPage : function( page ) {

			var page = parseInt( page );
			if ( ! this.isBrowsable( page ) ) {
				return 0;
			}

			this.query.set( 'page', page );

			return page;
		},

		/**
		 * Retrieve the current page number.
		 *
		 * @since    3.0
		 *
		 * @return   {int}
		 */
		getCurrentPage : function() {

			return parseInt( this.query.state.get( 'currentPage' ) ) || 1;
		},

		/**
		 * Retrieve the total number of pages.
		 *
		 * @since    3.0
		 *
		 * @return   {int}
		 */
		getTotalPages : function() {

			return parseInt( this.query.state.get( 'totalPages' ) ) || 1;
		},

		/**
		 * Jump to the previous page, if any.
		 *
		 * @since    3.0
		 *
		 * @return   {int}
		 */
		previousPage : function() {

			return this.setCurrentPage( this.getCurrentPage() - 1 );
		},

		/**
		 * Jump to the next page, if any.
		 *
		 * @since    3.0
		 *
		 * @return   {int}
		 */
		nextPage : function() {

			return this.setCurrentPage( this.getCurrentPage() + 1 );
		}
	});

	/**
	 * Grid Menu View.
	 *
	 * @since    3.0
	 */
	Grids.view.Menu = wp.Backbone.View.extend({

		className : 'grid-menu-inner',

		template : wp.template( 'wpmoly-grid-menu' ),

		events : {
			'click [data-action="open-settings"]'  : 'openSettings',
			'click [data-action="open-customs"]'   : 'openCustoms',
			'click [data-action="close-settings"]' : 'closeMenu',
			'click [data-action="close-customs"]'  : 'closeMenu'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 */
		initialize : function( options ) {

			this.controller = options.controller || {};

			this.on( 'prepare', this.maybeHide, this );

			this.listenTo( this.controller, 'change:submenu', this.render );
		},

		/**
		 * Render the View.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		maybeHide : function() {

			var settings = this.controller.settings,
			     preview = this.controller.preview;

			if ( ! preview && ! settings.get( 'settings_control' ) && ! settings.get( 'customs_control' ) ) {
				return this.$el.hide();
			}

			return this;
		},

		/**
		 * Open the settings menu.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		openSettings : function() {

			this.controller.openSettings();

			return this;
		},

		/**
		 * Open the customization menu.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		openCustoms : function() {

			return this.controller.openCustoms();

			return this;
		},

		/**
		 * Close grid menu.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		closeMenu : function() {

			return this.controller.closeMenu();

			return this;
		},

		/**
		 * Prepare template data.
		 *
		 * @since    3.0
		 *
		 * @return   {object}    options
		 */
		prepare : function() {

			var options = {
				show_settings : this.controller.settings.get( 'settings_control' ),
				show_customs  : this.controller.settings.get( 'customs_control' ),
				submenu       : this.controller.get( 'submenu' ),
			};

			return options;
		},

	});

	/**
	 * Grid Settings View.
	 *
	 * @since    3.0
	 */
	Grids.view.Settings = wp.Backbone.View.extend({

		className : 'grid-settings-inner',

		template : wp.template( 'wpmoly-grid-settings' ),

		events : {
			'click [data-action="apply"]' : 'apply'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 */
		initialize : function( options ) {

			this.controller = options.controller || {};

			this.listenTo( this.controller, 'change:submenu', this.toggle );
		},

		/**
		 * Apply changed settings.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		apply : function() {

			var changes = {},
			     inputs = this.$( 'input:checked' ),
			      query = this.controller.query;

			// Loop through fields to detect changes from current state.
			_.each( inputs, function( input ) {
				var param = this.$( input ).data( 'setting-type' ),
				    value = this.$( input ).val();

				if ( query.has( param ) && ( value != query.get( param ) || value ) ) {
					changes[ param ] = value;
				}
			}, this );

			// If order changed, go back to page 1.
			if ( changes.order || changes.orderby ) {
				changes.page = 1;
			}

			query.set( changes );

			this.controller.closeMenu();

			return this;
		},

		/**
		 * Show/Hide the settings menu.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model
		 * @param    {string}    value
		 * @param    {object}    options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		toggle : function( model, value, options ) {

			this.$el.toggleClass( 'active', ( 'settings' === value ) );

			return this;
		},

		/**
		 * Prepare template data.
		 *
		 * @since    3.0
		 *
		 * @return   {object}    options
		 */
		prepare : function() {

			var options = {
				grid_id  : _.uniqueId( 'wpmoly-grid-' + this.controller.get( 'post_id' ) ),
				settings : this.controller.settings,
				query    : this.controller.query
			};

			return options;
		}

	});

	/**
	 * Grid Customs View.
	 *
	 * @since    3.0
	 */
	Grids.view.Customs = Grids.view.Settings.extend({

		className : 'grid-customs-inner',

		template : wp.template( 'wpmoly-grid-customs' ),

		events : {
			'change [data-setting-type="list-columns"]' : 'columnizeList',
			'click [data-action="apply"]'               : 'apply'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 */
		initialize : function( options ) {

			this.controller = options.controller || {};

			this.listenTo( this.controller, 'change:submenu', this.toggle );
		},

		/**
		 * Show/Hide the customs menu.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model
		 * @param    {string}    value
		 * @param    {object}    options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		toggle : function( model, value, options ) {

			this.$el.toggleClass( 'active', ( 'customs' === value ) );

			return this;
		},

		/**
		 * Change List grid columns number.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    event JS 'change' event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		columnizeList : function( event ) {

			var $target = this.$( event.currentTarget ),
			    value = $target.val();

			this.controller.settings.set({ list_columns: value });

			return this;
		}
	});

	/**
	 * Grid Pagination View.
	 *
	 * @since    3.0
	 */
	Grids.view.Pagination = wp.Backbone.View.extend({

		className : 'grid-menu-inner',

		template : wp.template( 'wpmoly-grid-pagination' ),

		events : {
			'change [data-action="grid-paginate"]' : 'paginate',
			'click [data-action="grid-navigate"]'  : 'navigate'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 */
		initialize : function( options ) {

			this.controller = options.controller || {};

			this.listenTo( this.controller.query.state, 'change:currentPage', this.render );
			this.listenTo( this.controller.query.state, 'change:totalPages',  this.render );
		},

		/**
		 * Jump to a precise page.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    event JS 'change' Event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		paginate : function( event ) {

			var $target = this.$( event.currentTarget ),
			    value = $target.val();

			if ( ! this.controller.isBrowsable( value ) ) {
				return false;
			}

			this.controller.setCurrentPage( value );

			return this;
		},

		/**
		 * Navigate through the Grid's pages.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    event JS 'click' Event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		navigate : function( event ) {

			var $target = this.$( event.currentTarget ),
			    value = $target.attr( 'data-value' );

			if ( 'prev' === value ) {
				this.controller.previousPage();
			} else if ( 'next' === value ) {
				this.controller.nextPage();
			}

			return this;
		},

		/**
		 * Prepare template data.
		 *
		 * @since    3.0
		 *
		 * @return   {object}    options
		 */
		prepare : function() {

			var options = {
				current : this.controller.getCurrentPage(),
				total   : this.controller.getTotalPages()
			};

			return options;
		}

	});

	/**
	 * Grid Node View.
	 *
	 * @since    3.0
	 */
	Grids.view.Node = wp.Backbone.View.extend({

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 */
		initialize : function( options ) {

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
		 * @return   Returns itself to allow chaining.
		 */
		setTemplate : function() {

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
		setClassName : function() {

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
		 * Prepare template data.
		 *
		 * @since    3.0
		 *
		 * @return   {object}    options
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
	Grids.view.ListNode = Grids.view.Node.extend({

		tagName : 'li'

	});

	/**
	 * Single archive-mode grid item view.
	 *
	 * @since    3.0
	 *
	 * @param    {object}    [options]             View options.
	 * @param    {object}    options.model         View related Backbone.Model object.
	 * @param    {object}    options.controller    Grid controller.
	*/
	Grids.view.ArchiveNode = Grids.view.Node.extend({

		

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
	Grids.view.Nodes = wp.Backbone.View.extend({

		className : 'grid-content-inner clearfix',

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 */
		initialize : function( options ) {

			this.controller = options.controller || {};

			this.query      = this.controller.query;
			this.settings   = this.controller.settings;

			this.$window  = wpmoly.$( window );
			this.resizeEvent = 'resize.grid-' + this.controller.uniqid;

			this.rendered = false;

			this.nodes = {};

			this.bindEvents();
		},

		/**
		 * Bind events.
		 *
		 * @since    3.0
		 */
		bindEvents : function() {

			_.bindAll( this, 'adjust' );

			this.on( 'ready', this.adjust );

			// Adjust subviews dimensions on resize
			this.$window.off( this.resizeEvent ).on( this.resizeEvent, _.debounce( this.adjust, 50 ) );

			// Add views for new models
			this.listenTo( this.query, 'collection:add',    this.addNode );
			this.listenTo( this.query, 'collection:remove', this.removeNode );

			// Set grid as loading when reset
			this.listenTo( this.query, 'fetch:start', this.loading );
			this.listenTo( this.query, 'fetch:done',  this.loaded );

			// Set grid as loaded when fetch is done
			this.listenTo( this.query, 'fetch:done', _.debounce( this.adjust, 50 ) );

			// Notify query errors
			this.listenTo( this.query, 'fetch:failed', this.notifyError );
			this.listenTo( this.query, 'fetch:failed', this.loaded );

			// Switch themes
			this.listenTo( this.settings, 'change:theme', this.adjust );
		},

		/**
		 * Notify API request errors.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    collection
		 * @param    {object}    xhr
		 * @param    {object}    options
		 *
		 * @return    Returns itself to allow chaining.
		 */
		notifyError : function( collection, xhr, options ) {

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
		 * @param    {object}    model
		 * @param    {object}    collection
		 *
		 * @return   Returns itself to allow chaining.
		 */
		addNode : function( model, collection ) {

			var node = model.get( 'id' ),
			nodeType = Grids.view.Node;

			if ( 'list' === this.controller.settings.get( 'mode' ) ) {
				nodeType = Grids.view.ListNode;
			} else if ( 'list' === this.controller.settings.get( 'mode' ) ) {
				nodeType = Grids.view.ArchiveNode;
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
		 * @param    {object}    model
		 * @param    {object}    collection
		 * @param    {object}    options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		removeNode : function( model, collection, options ) {

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
		 * @return   Returns itself to allow chaining.
		 */
		loading : function() {

			if ( this.views.parent ) {
				wpmoly.$( 'body,html' ).animate({
					scrollTop : Math.round( this.views.parent.$el.offset().top - 48 ),
				}, 250 );
			}

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
		 * @return   Returns itself to allow chaining.
		 */
		loaded : function() {

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
		adjust : function() {

			return this;
		},

		/**
		 * Render the View.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		render : function() {

			wp.Backbone.View.prototype.render.apply( this, arguments );

			this.$el.addClass( this.controller.settings.get( 'mode' ) );

			if ( ! this.query.collection.length ) {
				// Replace $el with pre-generated content
				this.$el.html( wpmoly.$( this.options.content || '' ).html() );
			} else {
				this.query.collection.each( this.addNode, this );
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
		preEmpty : function() {

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
	Grids.view.NodesGrid = Grids.view.Nodes.extend({

		/**
		 * Adjust content nodes to fit the grid.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		adjust : function() {

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
	Grids.view.NodesList = Grids.view.Nodes.extend({

		tagName : 'ul',

		/**
		 * Adjust content nodes to fit the grid.
		 *
		 * TODO handle this by UL columns rather than width
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		adjust : function() {

			var columns = this.controller.settings.get( 'list_columns' ),
			idealWidth = this.controller.settings.get( 'column_width' ),
			innerWidth = this.$el.width();

			if ( ( Math.floor( innerWidth / columns ) - 8 ) < idealWidth ) {
				--columns;
			}

			this.columnWidth  = Math.floor( innerWidth / columns ) - 8;

			this.$el.addClass( 'nodes-' + columns + '-columns-list' );

			return this;
		},

		/**
		 * Render the View.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		render : function() {

			Grids.view.Nodes.prototype.render.apply( this, arguments );

			this.$el.addClass( 'nodes-list' );

			return this;
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
	Grids.view.NodesArchives = Grids.view.Nodes.extend({

		
	});

	/**
	 * Grid main View.
	 *
	 * @since    3.0
	 */
	Grids.view.Grid = wp.Backbone.View.extend({

		template : wp.template( 'wpmoly-grid' ),

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 */
		initialize : function( options ) {

			this.controller = options.controller || {};

			this.setRegions();
			this.render();

			// Change Theme.
			this.listenTo( this.controller.settings, 'change:mode',  this.setNodesView );
			this.listenTo( this.controller.settings, 'change:theme', this.changeTheme );

			// Prepare $el.
			this.on( 'prepare', this.setClassName );
		},

		/**
		 * Update the grid classes on theme change.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model
		 * @param    {object}    options
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
		 * @param    {object}    options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setMenuView : function( options ) {

			var options = options || {};
			if ( this.menu && ! options.silent ) {
				this.menu.remove();
			}

			if ( this.controller.canEdit() || this.controller.canCustomize() ) {
				this.menu = new Grids.view.Menu( { controller : this.controller } );
				this.views.set( '.grid-menu.settings-menu', this.menu );
			}

			return this;
		},

		/**
		 * Set settings subview.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setSettingsView : function( options ) {

			var options = options || {};
			if ( this.settings && ! options.silent ) {
				this.settings.remove();
			}

			if ( this.controller.canEdit() ) {
				this.settings = new Grids.view.Settings( { controller : this.controller } );
				this.views.set( '.grid-settings', this.settings );
			}

			return this;
		},

		/**
		 * Set customization subview.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setCustomsView : function( options ) {

			var options = options || {};
			if ( this.customs && ! options.silent ) {
				this.customs.remove();
			}

			if ( this.controller.canCustomize() ) {
				this.customs = new Grids.view.Customs( { controller : this.controller } );
				this.views.set( '.grid-customs', this.customs );
			}

			return this;
		},

		/**
		 * Set pagination subview.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setPaginationView : function( options ) {

			var options = options || {};
			if ( this.pagination && ! options.silent ) {
				this.pagination.remove();
			}

			if ( this.controller.canBrowse() ) {
				this.pagination = new Grids.view.Pagination( { controller : this.controller } );
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
		 * @param    {object}    options
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setNodesView : function( options ) {

			var options = options || {};
			if ( this.content && ! options.silent ) {
				this.content.remove();
			}

			var mode = this.controller.settings.get( 'mode' ),
			options = { controller : this.controller };

			// Use server-generated grid content first
			_.extend( options, { content : this.options.content } );

			if ( 'grid' === mode ) {
				this.content = new Grids.view.NodesGrid( options );
			} else if ( 'list' === mode ) {
				this.content = new Grids.view.NodesList( options );
			} else if ( 'archives' === mode ) {
				this.content = new Grids.view.NodesArchives( options );
			} else {
				this.content = new Grids.view.Nodes( options );
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

	});

	/**
	 * Run Forrest, run!
	 *
	 * Load the REST API Backbone client before loading all Grids.
	 *
	 * @see wp.api.loadPromise.done()
	 *
	 * @since    3.0
	 */
	Grids.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( _.map(
			document.querySelectorAll( '[data-grid]' ),
			Grids.add,
			Grids
		) );
	};

} )( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.grids );
