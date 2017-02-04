
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

			this.settings = new Backbone.Model( options.settings || {} );
			this.query    = new wpmoly.model.Query( options.query_args || {}, options.query_data || {} );

			this.listenTo( this.query, 'change', this.browse );
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
