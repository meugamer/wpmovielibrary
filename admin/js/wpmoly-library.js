wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	library = wpmoly.library = {

		runned : false,

		run : function() {

			var $library = $( '#wpmoly-library' );

			library.controller = new wpmoly.controller.Library;

			if ( $library.length ) {

				wpmoly.library = new wpmoly.view.Library.Library({
					el         : $library,
					controller : library.controller
				});

				wpmoly.library.runned = true;
			}
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.library );
