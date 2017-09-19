wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	archives = wpmoly.archives = {

		runned : false,

		run : function() {

			var $archives = $( '#wpmoly-archives-page-type' );
			if ( $archives.length ) {

				wpmoly.archives = new wpmoly.view.ArchivePages({
					el : $archives,
				});

				wpmoly.archives.runned = true;
			}
		}
	};
})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.archives );
