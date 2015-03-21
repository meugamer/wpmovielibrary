
if ( undefined == window.redux ) window.redux = {};
if ( undefined == window.redux.field_objects ) window.redux.field_objects = {};
if ( undefined == window.redux.field_objects.select ) window.redux.field_objects.select = {};

( function( $, _, Backbone ) {

	wpmoly_l10n = window.wpmoly_l10n || {};
	wpmoly = {};

	_.extend( wpmoly, {

		// Localization
		l10n: wpmoly_l10n,

		// Store our controllers
		controller: {},

		// Store our models
		model: {},

		// Store our views
		view: {},

		// Movie Editor base object
		editor: {},

		// Movie Grid base object
		grid: {},

		// Movie Importer base object
		importer: {},
	}, {

		/**
		 * Run the application: unleash the Backbone magic!
		 * 
		 * This function prepare runners to be run depending on the page
		 * we're on.
		 * 
		 * @since    2.2
		 * 
		 * @return   Return itself to allow chaining
		 */
		run: function() {

			// Store our position
			this.isEditMovie  = ( 'edit-movie' == pagenow && 'post-php'     == adminpage );
			this.isEditMovies = ( 'edit-movie' == pagenow && 'edit-php'     == adminpage );
			this.isNewMovie   = ( 'movie'      == pagenow && 'post-new-php' == adminpage );

			var runners = [];

			// Are we editing/posting a movie?
			if ( this.isEditMovie || this.isNewMovie ) {

				$( '#toplevel_page_wpmovielibrary, #toplevel_page_wpmovielibrary > a' ).addClass( 'wp-has-current-submenu wp-open-submenu' );

				runners.push( 'metabox', 'editor', 'media' );
			}
			// Are we editing movies (all movies page)?
			else if ( this.isEditMovies ) {

				runners.push( 'grid', 'editor' );
			}

			// Map the runners
			_.map( runners, function( runner ) {
				return _.isFunction( wpmoly[ runner ].run ) ? wpmoly[ runner ].run() : false;
			}, this );

			return this;
		}
	} );

}( jQuery, _, Backbone ) );

// Wait for the DOM to be ready
jQuery( document ).ready( function() {

	// Let the fun begin!
	wpmoly.run();
} );