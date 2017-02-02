
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

_.extend( Grid, {

	Pagination: wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-grid-pagination' ),

		events: {
			'change [data-action="grid-paginate"]' : 'paginate',
			'click [data-action="grid-navigate"]'  : 'navigate'
		},

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

			this.listenTo( this.controller.settings, 'change:current_page', this.render );
			this.listenTo( this.controller.settings, 'change:total_page', this.render );
		},

		/**
		 * Jump to a precise page.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'change' Event.
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		paginate: function( event ) {

			var $target = this.$( event.currentTarget ),
			      value = $target.val();

			this.controller.query.set({ paged : parseInt( value ) });

			return this;
		},

		/**
		 * Navigate through the Grid's pages.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event.
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		navigate: function( event ) {

			var $target = this.$( event.currentTarget ),
			      value = $target.attr( 'data-value' );

			if ( 'prev' === value ) {
				this.controller.prev();
			} else if ( 'next' === value ) {
				this.controller.next();
			}

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

			this.$el.html( this.template( {
				total_page   : this.controller.query.total_page,
				current_page : this.controller.query.current_page
			} ) );

			return this;
		}

	})

} );
