
wpmoly = window.wpmoly || {};

var Headbox = wpmoly.view.Headbox = wp.Backbone.View.extend({

	events: {
		'click [data-action="expand"]'   : 'expand',
		'click [data-action="collapse"]' : 'collapse'
	},

	/**
	 * Initialize the View.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	initialize: function( options ) {

		this.prepare();
	},

	/**
	 * Prepare the Headbox.
	 * 
	 * Determine if the Headbox is collapsable, and collapse it.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	prepare: function() {

		var $content = this.$( '.headbox-content' ),
		    collapse = $content.outerHeight() > 260;

		if ( this.$el.hasClass( 'movie-headbox theme-default' ) ) {

			if ( collapse ) {
				this.$el.addClass( 'collapse' );
			}

			if ( collapse && ! this.$el.hasClass( 'collapsed' ) ) {
				this.$el.addClass( 'collapsed' );
			}
		}
	},

	/**
	 * Show the Headbox full size.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	expand: function() {

		this.$el.removeClass( 'collapsed' );
	},

	/**
	 * Reduce the Headbox height.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	collapse: function() {

		this.$el.addClass( 'collapsed' );
	}

});
