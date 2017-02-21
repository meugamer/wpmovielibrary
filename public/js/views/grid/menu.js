
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

Grid.Menu = wp.Backbone.View.extend({

	className: 'grid-menu-inner',

	template: wp.template( 'wpmoly-grid-menu' ),

	events: {
		'click [data-action="grid-settings"]' : 'toggleSettings',
		'click [data-action="grid-customs"]'  : 'toggleCustoms'
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
	toggleSettings: function() {

		this.controller.trigger( 'grid:menu:toggle', 'settings' );

		return this;
	},

	/**
	 * Show/Hide the grid menu.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	toggleCustoms: function() {

		this.controller.trigger( 'grid:menu:toggle', 'customs' );

		return this;
	}

});
