
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

_.extend( Grid, {

	Settings: wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-grid-settings' ),

		events: {},

		/**
		 * Initialize the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
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

			return this;
		}

	})

} );
