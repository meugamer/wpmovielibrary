
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

		this.settings = new wpmoly.model.Settings( options.settings || {} );
		this.uniqid = _.uniqueId( 'grid-' + this.get( 'post_id' ) + '-' );

		this.query = new wpmoly.controller.Query(
			options.query_args || {},
			_.extend( options.query_data || {}, {
				type     : this.settings.get( 'type' ),
				settings : this.settings
			} )
		);

		this.listenTo( this.query, 'change', this.browse );

		if ( options.prefetch ) {
			this.query.prefetch();
		}

		this.settingsOpened = false;
		this.customsOpened  = false;

		this.on( 'grid:settings:toggle', this.toggleSettings );
		this.on( 'grid:customs:toggle',  this.toggleCustoms );
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
	browse: function( model, options ) {

		this.settingsOpened = false;
		this.customsOpened  = false;

		if ( this.settings.get( 'enable_ajax' ) ) {

			return this.query.query( model.attributes );
		}

		var query = _.defaults( this.parseSearchQuery(), {
			id : this.get( 'post_id' )
		} );

		_.each( model.changed, function( value, key ) {
			query[ key ] = value;
		} );

		var url = window.location.origin + window.location.pathname;

		window.location.href = url + this.buildSearchQuery( query );
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
	parseSearchQuery: function() {

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
	buildSearchQuery: function( query ) {

		var _query = [];
		_.each( query, function( value, param ) {
			_query.push( param + ':' + value );
		} );

		return '?grid=' + _query.join( ',' );
	},

	/**
	 * Jump to the previous page, if any.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	prev: function() {

		var current = parseInt( this.query.get( 'paged' ) || this.query.get( 'page' ) ) || 1,
		      total = parseInt( this.query.state.get( 'totalPages' ) ),
		       prev = Math.max( 1, current - 1 );

		if ( current != prev ) {
			this.query.set({ paged : prev });
		}
	},

	/**
	 * Jump to the next page, if any.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	next: function() {

		var current = parseInt( this.query.get( 'paged' ) || this.query.get( 'page' ) ) || 1,
		      total = parseInt( this.query.state.get( 'totalPages' ) ),
		       next = Math.min( current + 1, total );

		if ( current != next ) {
			this.query.set({ paged : next });
		}
	}
});
