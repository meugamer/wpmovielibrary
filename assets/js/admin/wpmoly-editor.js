
( function( $, _, Backbone, wp, wpmoly ) {

	var editor = wpmoly.editor = function() {

		// Treats or tricks
		redux.field_objects.select.init();

		// Extract metadata from view
		var data = {},
		  fields = document.querySelectorAll( '.meta-data-field' );
		_.each( fields, function( field ) {
			var name = field.name.replace( /meta\[(.*)\]/g, '$1' );
			data[ name ] = field.value;
		});

		var lang = wpmoly.getValue( '#wpmoly-search-lang', '' ),
		 post_id = parseInt( wpmoly.getValue( '#post_ID', 0 ) ),
		   adult = Boolean( parseInt( wpmoly.getValue( '#wpmoly-search-adult', false ) ) ),
		paginate = Boolean( parseInt( wpmoly.getValue( '#wpmoly-search-paginate', true ) ) );

		// Init models
		// Create movie model
		editor.models.movie = new editor.Model.Movie;
		editor.models.movie.settings = {
			actorlimit:   parseInt( wpmoly.getValue( '#wpmoly-actor-limit',        0 ) ),
			setfeatured:  parseInt( wpmoly.getValue( '#wpmoly-poster-featured',    1 ) ),
			importimages: parseInt( wpmoly.getValue( '#wpmoly-auto-import-images', 0 ) ),
			autocomplete: {
				collection: parseInt( wpmoly.getValue( '#wpmoly-autocomplete-collection', 1 ) ),
				genre:      parseInt( wpmoly.getValue( '#wpmoly-autocomplete-genre',      1 ) ),
				actor:      parseInt( wpmoly.getValue( '#wpmoly-autocomplete-actor',      1 ) )
			}
		};

		// Not a single metadata found, consider it's empty
		if ( '0' != _.flatten( data ).join( '' ) ) {
			editor.models.movie.set( data, { silent: true } );
		}

		// Preview Metabox Tab
		editor.models.preview = new editor.Model.Preview({
			controller: editor.models.movie
		});

		// Search engine
		editor.models.search = search = new editor.Model.Search({
			settings: new editor.Model.Settings({
				post_id:  post_id,
				lang:     lang,
				adult:    adult,
				paginate: paginate
			}),
			status:   new editor.Model.Status,
			results:  new editor.Model.Results,
			movie:    editor.models.movie
		});

		// Init views
		_.extend( editor.views, {

			movie: new editor.View.Movie({
				model: editor.models.movie
			}),

			preview: new editor.View.Preview({
				model: editor.models.movie
			}),

			search: new editor.View.Search({
				model:  editor.models.search,
				target: editor.models.movie
			}),

			settings: new editor.View.Settings({
				model:      editor.models.search.get( 'settings' ),
				controller: editor.models.search
			}),

			results: new editor.View.Results({
				collection: editor.models.search.get( 'results' ),
				controller: editor.models.search
			}),

			status: new editor.View.Status({
				model:      editor.models.search.get( 'status' ),
				controller: editor.models.search
			})
		} );

		editor.models.search.view = editor.views.search;
		editor.models.search.get( 'settings' ).view = editor.views.settings;

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
