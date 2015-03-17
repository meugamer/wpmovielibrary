
( function( $, _, Backbone, wp, wpmoly ) {

	var media = wpmoly.media = function() {

		var backdrops = $( '#wpmoly-imported-backdrops-json' ).val(),
		      posters = $( '#wpmoly-imported-posters-json' ).val(),
		    backdrops = $.parseJSON( backdrops ),
		      posters = $.parseJSON( posters );

		_.map( backdrops, function( backdrop ) { return new media.Model.Backdrop( backdrop ); } );
		_.map( posters, function( poster ) { return new media.Model.Poster( poster ); } );

		// Init models
		media.models.backdrops = new media.Model.Backdrops;
		media.models.posters = new media.Model.Posters;
		media.models.backdrops.add( backdrops, { upload: false } );
		media.models.posters.add( posters, { upload: false } );

		// Init views
		media.views.backdrops = new media.View.Backdrops( { collection: media.models.backdrops } );
		media.views.posters = new media.View.Posters( { collection: media.models.posters } );
	};

	_.extend( media, { models: {}, views: {}, Model: {}, View: {} } );

}( jQuery, _, Backbone, wp, wpmoly ) );
