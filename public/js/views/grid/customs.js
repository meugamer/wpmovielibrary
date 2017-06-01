
wpmoly = window.wpmoly || {};

wpmoly.view.GridCustoms = wpmoly.view.GridSettings.extend({

	className : 'grid-customs-inner',

	template : wp.template( 'wpmoly-grid-customs' ),

	events: {
		'change [data-setting-type="list-columns"]' : 'columnizeList',
		'click [data-action="apply"]'               : 'apply'
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

		this.listenTo( this.controller, 'grid:customs:open',  this.open );
		this.listenTo( this.controller, 'grid:customs:close', this.close );
	},

	/**
	 * Change List grid columns number.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    JS 'change' event.
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	columnizeList: function( event ) {

		var $target = this.$( event.currentTarget ),
		      value = $target.val();

		this.controller.settings.set({ list_columns: value });

		return this;
	}
});
