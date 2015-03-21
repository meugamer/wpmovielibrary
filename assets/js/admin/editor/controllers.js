
/**
 * wp.media.controller.State extension to change frame class upon
 * state changes.
 * 
 * @since    2.2
 */
editor.controller.State = wpmoly.controller.State.extend({

	/**
	 * Initialize the State
	 * 
	 * @since    2.2
	 * 
	 * @return   Return itself to allow chaining
	 */
	initialize: function() {

		// Use Underscore's debounce to let the DOM load properly
		this.on( 'activate',   _.debounce( this.activateState,   25 ), this );
		this.on( 'deactivate', _.debounce( this.deactivateState, 25 ), this );

		return this;
	},

	/**
	 * State activation: update frame class
	 * 
	 * @since    2.2
	 * 
	 * @return   Return itself to allow chaining
	 */
	activateState: function() {

		this.frame.$el.parents( '.media-modal' ).addClass( this.id + '-modal' );

		return this;
	},

	/**
	 * State deactivation: update frame class
	 * 
	 * @since    2.2
	 * 
	 * @return   Return itself to allow chaining
	 */
	deactivateState: function() {

		this.frame.$el.parents( '.media-modal' ).removeClass( this.id + '-modal' );

		return this;
	}
});

/**
 * wpmoly.editor.controller.EditMovie
 * 
 * A state for editing a movie's metadata.
 * 
 * @since    2.2
 */
editor.controller.EditMovie = editor.controller.State.extend({

	defaults: {
		id:      'edit-movie',
		title:   'Edit Movie',
		content: 'edit-metadata',
		menu:    false,
		toolbar: false,
		router:  false
	}
});

/**
 * wpmoly.editor.controller.PreviewMovie
 *
 * A state for previewing a movie.
 *
 * @since    2.2
 */
editor.controller.PreviewMovie = editor.controller.State.extend({

	defaults: {
		id:      'preview-movie',
		title:   'Preview Movie',
		content: 'preview-movie',
		menu:    false,
		toolbar: false,
		router:  false
	}
});
