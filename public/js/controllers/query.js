
wpmoly = window.wpmoly || {};

_.extend( wpmoly.controller, {

	Query: Backbone.Model.extend({

		defaults: function() { return {
			order   : 'desc',
			orderby : 'date',
			letter  : ''
		} },

		/**
		 * Initialize the Model.
		 * 
		 * @since    3.0
		 * 
		 * @param    {object}    attributes
		 * @param    {object}    options
		 * 
		 * @return   void
		 */
		initialize: function( attributes, options ) {

			var options = options || {};

			this.type = options.type;

			this.load();

			this.on( 'fetch:done', this.setState, this );

			this.state = new Backbone.Model({
				currentPage : parseInt( options.current_page ) || '',
				totalPages  : parseInt( options.total_page ) || ''
			});
		},

		/**
		 * Load REST API Backbone client.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		load: function() {

			var collections = {
				movie      : wpmoly.collection.Movies,
				actor      : wp.api.collections.Actors,
				collection : wp.api.collections.Collections,
				genre      : wp.api.collections.Genres
			};

			if ( ! _.has( collections, this.type ) ) {
				return wpmoly.error( 'missing-api-collection', wpmolyL10n.api.missing_collection );
			}

			this.collection = new collections[ this.type ];
		},

		/**
		 * Update collection state: current page, total pages...
		 * 
		 * @since    3.0
		 * 
		 * @param    {object}    collection
		 * @param    {array}     response
		 * @param    {object}    options
		 * 
		 * @return   void
		 */
		setState: function( collection, response, options ) {

			if ( ! collection.state ) {
				return false;
			}

			this.state.set( collection.state );
		},

		/**
		 * Fetch nodes when initializing the controller.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		prefetch: function() {

			this.query({
				per_page : this.get( 'number' ) || this.get( 'posts_per_page' ),
				orderby  : this.get( 'orderby' ),
				order    : this.get( 'order' ),
				page     : this.get( 'page' )
			});
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
		 * @return   void
		 */
		query: function( options ) {

			var self = this,
			 options = { data : options };

			this.trigger( 'fetch:start' );

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

	}),

} );
