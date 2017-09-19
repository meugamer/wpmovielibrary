wpmoly = window.wpmoly || {};

wpmoly.controller.Library = Backbone.Model.extend({

	defaults : {
		mode : 'latest'
	},

	allowed_modes : [ 'latest', 'favorites', 'import' ],

	/**
	 * Set Library Mode.
	 *
	 * @since    3.0
	 *
	 * @param    {string}    mode
	 *
	 * @return   Returns itself to allow chaining.
	 */
	setMode : function( mode ) {

		if ( ! _.contains( this.allowed_modes, mode ) ) {
			return false;
		}

		this.set( { mode : mode } );

		return this;
	},

});
