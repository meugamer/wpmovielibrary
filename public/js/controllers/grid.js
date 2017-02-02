
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

			this.listenTo( this.query, 'change:paged', this.browse );
		},

		/**
		 * Alternative to yet-to-be-implemented Ajax browsing: update
		 * URL and reload the page.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    model
		 * @param    object    value
		 * @param    object    options
		 * 
		 * @return   void
		 */
		browse: function( model, value, options ) {

			var url = window.location.origin + window.location.pathname,
			 search = '';

			if ( '' == window.location.search ) {
				search  = '?grid=id:' + this.get( 'post_id' );
				if ( 1 < model.get( 'paged' ) ) {
					search = search + ',paged:' + model.get( 'paged' );
				}
			} else {
				search = window.location.search.replace( 'paged:' + model.previous( 'paged' ), 'paged:' + model.get( 'paged' ) );
			}

			window.location.href = url + search;
		},

		/**
		 * Jump to the previous page, if any.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		prev: function() {

			var current = this.query.get( 'paged' ) || 1,
			      total = this.query.total_page,
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

			var current = this.query.get( 'paged' ) || 1,
			      total = this.query.total_page,
			       next = Math.min( current + 1, total );

			if ( current != next ) {
				this.query.set({ paged : next });
			}
		}
	})
} );
