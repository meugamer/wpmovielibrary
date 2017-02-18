
wpmoly = window.wpmoly || {};

_.extend( wpmoly.controller, {

	Grid: Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 * 
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			this.settings = new wpmoly.model.Settings( options.settings || {} );
			this.query    = new wpmoly.model.Query( options.query_args || {}, options.query_data || {} );

			this.load();
			this.prefetch();

			this.listenTo( this.query, 'change', this.browse );

			this.settingsOpened = false;
			this.customsOpened  = false;

			this.on( 'grid:menu:toggle', this.toggleMenu );
		},

		/**
		 * Load REST API Backbone client.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		load: function() {

			if ( ! wp.api ) {
				return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
			}

			var collections = {
				movie      : wp.api.collections.Movies,
				actor      : wp.api.collections.Actors,
				collection : wp.api.collections.Collections,
				genre      : wp.api.collections.Genres
			};

			if ( ! _.has( collections, this.settings.get( 'type' ) ) ) {
				return wpmoly.error( 'missing-api-collection', wpmolyL10n.api.missing_collection );
			}

			this.collection = new collections[ this.settings.get( 'type' ) ];
		},

		/**
		 * Load grid content.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		prefetch: function() {

			var self = this;
			this.collection.fetch({
				complete : function() {
					self.trigger( 'fetch:stop' );
				},
				success : function() {
					self.trigger( 'fetch:done' );
				},
				data : {
					per_page : this.query.get( 'number' ) || this.query.get( 'posts_per_page' ),
					orderby  : this.query.get( 'orderby' ),
					order    : this.query.get( 'order' )
				}
			});
		},

		/**
		 * Show/Hide the grid Settings/Customs menu.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    menu
		 * 
		 * @return   void
		 */
		toggleMenu: function( menu ) {

			if ( 'settings' == menu ) {

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

			} else if ( 'customs' == menu ) {

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

			} else {
				return false;
			}
		},

		/**
		 * Alternative to yet-to-be-implemented Ajax browsing: update
		 * URL and reload the page.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * @param    object    options
		 * 
		 * @return   void
		 */
		browse: function( model, options ) {

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
		 * @return   object
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

			var current = parseInt( this.query.get( 'paged' ) ) || 1,
			      total = parseInt( this.query.total_page ),
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

			var current = parseInt( this.query.get( 'paged' ) ) || 1,
			      total = parseInt( this.query.total_page ),
			       next = Math.min( current + 1, total );

			if ( current != next ) {
				this.query.set({ paged : next });
			}
		}
	})
} );
