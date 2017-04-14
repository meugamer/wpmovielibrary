
wpmoly = window.wpmoly || {};

wpmoly.controller.Grid = Backbone.Model.extend({

	/**
	 * Initialize the Model.
	 * 
	 * @since    3.0
	 * 
	 * @param    {object}    attributes
	 * @param    {object}    [options]
	 * @param    {object}    options.settings
	 * @param    {object}    options.query_args
	 * @param    {object}    options.query_data
	 * 
	 * @return   void
	 */
	initialize: function( attributes, options ) {

		this.options = options || {};

		this.settings = new wpmoly.model.Settings( options.settings || {} );

		// Toggle Menu.
		// TODO think of something cleaner.
		this.settingsOpened = false;
		this.customsOpened  = false;
		this.on( 'grid:settings:toggle', this.toggleSettings );
		this.on( 'grid:customs:toggle',  this.toggleCustoms );
	},

	/**
	 * Ready the controller.
	 *
	 * @since    3.0
	 *
	 * @return   Returns itself to allow chaining.
	 */
	ready: function() {

		var options = _.defaults( this.options, {
			query_args : {},
			query_data : {}
		} );

		this.query = new wpmoly.controller.Query(
			options.query_args,
			_.extend( options.query_data, {
				type     : this.settings.get( 'type' ),
				settings : this.settings
			} )
		);

		// Browsing
		this.listenTo( this.query, 'change', this._updateQuery );

		// Preload nodes
		if ( this.options.prefetch ) {
			this.query.prefetch();
		}

		return this.trigger( 'ready' );
	},

	/**
	 * Show/Hide the grid Settings menu.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	toggleSettings: function() {

		if ( this.settingsOpened ) {
			this.settingsOpened = false;
			this.trigger( 'grid:settings:close' );
		} else {
			this.settingsOpened = true;
			this.trigger( 'grid:settings:open' );
		}

		if ( this.customsOpened ) {
			this.customsOpened = false;
			this.trigger( 'grid:customs:close' );
		}
	},

	/**
	 * Show/Hide the grid Settings/Customs menu.
	 * 
	 * @since    3.0
	 * 
	 * @param    {string}    menu
	 * 
	 * @return   void
	 */
	toggleCustoms: function( menu ) {

		if ( this.customsOpened ) {
			this.customsOpened = false;
			this.trigger( 'grid:customs:close' );
		} else {
			this.customsOpened = true;
			this.trigger( 'grid:customs:open' );
		}

		if ( this.settingsOpened ) {
			this.settingsOpened = false;
			this.trigger( 'grid:settings:close' );
		}
	},

	/**
	 * Alternative to yet-to-be-implemented Ajax browsing: update
	 * URL and reload the page.
	 * 
	 * @since    3.0
	 * 
	 * @param    {object}    model
	 * @param    {object}    options
	 * 
	 * @return   void
	 */
	_updateQuery: function( model, options ) {

		this.settingsOpened = false;
		this.customsOpened  = false;

		if ( this.isDynamic() ) {
			return this.query.query( model.attributes );
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
	_parseSearchQuery: function() {

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
	 * @param    object    query
	 * 
	 * @return   string
	 */
	_buildSearchQuery: function( query ) {

		var _query = [];
		_.each( query, function( value, param ) {
			_query.push( param + ':' + value );
		} );

		return '?grid=' + _query.join( ',' );
	},

	/**
	 * Retrieve Grid Type.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	getType : function() {

		return this.settings.get( 'type' );
	},

	/**
	 * Retrieve Grid Mode.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	getMode : function() {

		return this.settings.get( 'mode' );
	},

	/**
	 * Retrieve Grid Theme.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	getTheme : function() {

		return this.settings.get( 'theme' );
	},

	/**
	 * Is settings edition enabled?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	canEdit: function() {

		return true === this.settings.get( 'settings_control' );
	},

	/**
	 * Is customization enabled?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	canCustomize: function() {

		return true === this.settings.get( 'customs_control' );
	},

	/**
	 * Is pagination enabled?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	canBrowse: function() {

		return true === this.settings.get( 'enable_pagination' );
	},

	/**
	 * Is Ajax browsing enabled?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	isDynamic: function() {

		return true === this.settings.get( 'enable_ajax' );
	},

	/**
	 * Update settings.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    settings
	 * 
	 * @return   void
	 */
	setSettings: function( settings ) {

		return this.settings.set( settings );
	},

	/**
	 * Check if a specific page number matches the current page number.
	 * 
	 * @since    3.0
	 * 
	 * @param    int    page Page number.
	 * 
	 * @return   boolean
	 */
	isCurrentPage: function( page ) {

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
	 * @return   boolean
	 */
	isBrowsable: function( page ) {

		var page = parseInt( page );

		return 1 <= page && page <= this.getTotalPages() && ! this.isCurrentPage( page );
	},

	/**
	 * Jump to the specific page number after making sure that number is
	 * reachable.
	 * 
	 * @since    3.0
	 * 
	 * @param    int    page Page number.
	 * 
	 * @return   int
	 */
	setCurrentPage: function( page ) {

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
	 * @return   int
	 */
	getCurrentPage: function() {

		return parseInt( this.query.state.get( 'currentPage' ) ) || 1;
	},

	/**
	 * Retrieve the total number of pages.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	getTotalPages: function() {

		return parseInt( this.query.state.get( 'totalPages' ) ) || 1;
	},

	/**
	 * Jump to the previous page, if any.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	previousPage: function() {

		return this.setCurrentPage( this.getCurrentPage() - 1 );
	},

	/**
	 * Jump to the next page, if any.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	nextPage: function() {

		return this.setCurrentPage( this.getCurrentPage() + 1 );
	}
});
