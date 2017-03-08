
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	headboxes = wpmoly.headboxes = {

		runned: false,

		views: [],

		run: function() {

			var headboxes = document.querySelectorAll( '.wpmoly.headbox' );
			_.each( headboxes, function( headbox ) {

				var view = new wpmoly.view.Headbox({
					el : headbox
				});

				wpmoly.headboxes.views.push( view );
			} );
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.headboxes );
