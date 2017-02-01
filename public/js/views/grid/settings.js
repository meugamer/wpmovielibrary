
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

_.extend( Grid, {

	Settings: wp.Backbone.View.extend({

		className : 'grid-settings-inner',

		template : wp.template( 'wpmoly-grid-settings' ),

		events: {
			'click [data-action="apply"]' : 'apply'
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};

			this.listenTo( this.controller, 'grid:menu:toggle', this.toggle );
		},

		apply: function() {

			this.toggle();

			return this;
		},

		toggle: function() {

			this.$el.toggleClass( 'active' );

			return this;
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		render: function() {

			this.$el.html( this.template() );

			this.$( '.selectize' ).selectize();

			return this;
		}

	})

} );
