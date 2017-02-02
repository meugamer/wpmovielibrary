
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
		 * @param    object    options
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};

			this.listenTo( this.controller, 'grid:menu:toggle', this.toggle );
		},

		/**
		 * Apply changed settings.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		apply: function() {

			this.toggle();

			return this;
		},

		/**
		 * Show/Hide the settings menu.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		toggle: function() {

			this.$el.toggleClass( 'active' );

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
				settings : this.controller.settings,
				query    : this.controller.query
			} ) );

			return this;
		}

	})

} );
