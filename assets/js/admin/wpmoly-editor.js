
( function( $, _, Backbone, wp, wpmoly ) {

	var editor = wpmoly.editor = function() {

		// Trick of treats
		redux.field_objects.select.init();

		// Extract metadata from view
		var data = {},
		  fields = document.querySelectorAll( '.meta-data-field' );
		_.each( fields, function( field ) {
			var name = field.name.replace( /meta\[(.*)\]/g, '$1' );
			data[ name ] = field.value;
		});

		// Init models
		editor.models.status = new wpmoly.editor.Model.Status();
		editor.models.movie = new wpmoly.editor.Model.Movie( data );
		editor.models.preview = new wpmoly.editor.Model.Preview();
		editor.models.search = new wpmoly.editor.Model.Search({
			post_id:  parseInt( document.querySelector( '#post_ID' ).value ),
			lang:     document.querySelector( '#wpmoly-search-lang' ).value,
			adult:    Boolean( parseInt( document.querySelector( '#wpmoly-search-adult' ).value ) ),
			paginate: Boolean( parseInt( document.querySelector( '#wpmoly-search-paginate' ).value ) )
		});
		editor.models.results = new wpmoly.editor.Model.Results();

		editor.models.movie.settings = {
			actorlimit: parseInt( document.querySelector( '#wpmoly-actor-limit' ).value ),
			setfeatured: parseInt( document.querySelector( '#wpmoly-poster-featured' ).value ),
			importimages: parseInt( document.querySelector( '#wpmoly-auto-import-images' ).value ),
			autocomplete: {
				collection: parseInt( document.querySelector( '#wpmoly-autocomplete-collection' ).value ),
				genre: parseInt( document.querySelector( '#wpmoly-autocomplete-genre' ).value ),
				actor: parseInt( document.querySelector( '#wpmoly-autocomplete-actor' ).value )
			}
		};

		// Init views
		editor.views.movie = new wpmoly.editor.View.Movie( { model: editor.models.movie } );
		editor.views.preview = new wpmoly.editor.View.Preview( { model: editor.models.movie } );
		editor.views.search = new wpmoly.editor.View.Search( {model: editor.models.search, target: editor.models.movie } );
		editor.views.settings = new wpmoly.editor.View.Settings( { model: editor.models.search } );
		editor.views.results = new wpmoly.editor.View.Results( { collection: editor.models.results } );
		editor.views.status = new wpmoly.editor.View.Status( { model: editor.models.status } );

		document.querySelector( '#title' ).addEventListener( 'input', function( event ) {
			editor.models.search.set( { s: event.target.value } );
		});

		window.addEventListener( 'resize', function() {
			editor.views.search.render();
			editor.views.results.resize();
		});
	};

	_.extend( editor, { models: {}, views: {}, Model: {}, View: {} } );

}( jQuery, _, Backbone, wp, wpmoly ) );
