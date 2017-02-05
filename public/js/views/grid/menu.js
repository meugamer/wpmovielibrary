
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

_.extend( Grid, {

	Menu: wp.Backbone.View.extend({

		className: 'grid-menu-inner',

		template: wp.template( 'wpmoly-grid-menu' ),

		events: {
			'click [data-action="grid-menu"]' : 'toggleMenu'
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
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			var settings = this.controller.settings;

			if ( ! settings.get( 'settings_control' ) && ! settings.get( 'customs_control' ) ) {
				return this.$el.hide();
			}

			this.$el.html( this.template( {
				show_settings : settings.get( 'settings_control' ),
				show_customs  : settings.get( 'customs_control' )
			}) );

			return this;
		},

		/**
		 * Show/Hide the grid menu.
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		toggleMenu: function() {

			this.controller.trigger( 'grid:menu:toggle' );

			return this;
		}

	})

} );
