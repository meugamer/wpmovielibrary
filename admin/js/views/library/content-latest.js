wpmoly = window.wpmoly || {};

wpmoly.view.Library.ContentLatest = wp.Backbone.View.extend({

	className : 'wpmoly library content-latest inner-menu',

	template : wp.template( 'wpmoly-library-content-latest' ),

	/**
	 * Initialize the View.
	 *
	 * @since    3.0
	 */
	initialize : function( options ) {

		var options = options || {};

		this.controller = options.controller || {};

	},

});
