
wpmoly = window.wpmoly || {};

wpmoly.view.GridPreview = wpmoly.view.Grid.extend({

	events: {
		'click a' : 'preventDefault'
	},

	/**
	 * Prevent from trigger default events actions.
	 *
	 * @since    3.0
	 *
	 * @param    object    event JS 'click' event.
	 */
	preventDefault : function( event ) {

		event.preventDefault();
	},

});
