
window.wpmoly = window.wpmoly || {};

(function( $, _, Backbone, wp ) {

	var editor = wpmoly.editor = function() {

		var movies = [];
		_.each( document.querySelectorAll( '#the-list tr' ), function( movie ) {
			var id = movie.id.replace( 'post-', '' ),
			 movie = new editor.Model.Movie;
			movies.push( _.extend( movie, { id: id } ) );
		} );

		editor.models.movies = new editor.Model.Movies();
		editor.models.movies.add( movies );

		//editor.frame = new editor.View.EditMovies( { frame: 'select' } );
	};

	_.extend( editor, { controller: {}, models: {}, views: {}, Model: {}, View: {} } );

	_.extend( editor.Model, {

		Movie: Backbone.Model.extend({

			id: '',

			defaults: {
				tmdb_id: '',
				title: '',
				original_title: '',
				tagline: '',
				overview: '',
				release_date: '',
				local_release_date: '',
				runtime: '',
				production_companies: '',
				production_countries: '',
				spoken_languages: '',
				genres: '',
				director: '',
				producer: '',
				cast: '',
				photography: '',
				composer: '',
				author: '',
				writer: '',
				certification: '',
				budget: '',
				revenue: '',
				imdb_id: '',
				adult: '',
				homepage: ''
			},
		})

	} );

	_.extend( editor.Model, {

		Movies: Backbone.Collection.extend({

			url: ajaxurl,

			model: editor.Model.Movie,

			sync: function( method, model, options ) {

				

				if ( 'read' == method ) {

					_.extend( options, {
						context: this,
						data: {
							action: 'wpmoly_fetch_movies',
							//nonce: wpmoly.get_nonce( 'fetch-movies' ),
							data: _.map( this.models, function( model ) {
								return model.id;
							}, this )
						},
						complete: function() {},
						success: function( response ) {
							_.each( response, function( data, id ) {
								var model = this.get( id );
								model.set( _.pick( data, _.keys( model.defaults ) ) );
							}, this );
						}
					});

					wp.ajax.send( options );

				} else if ( 'update' == method ) {

					
					

				} else {
					return Backbone.sync.apply( this, arguments );
				}
			},

			add: function( models, options ) {

				Backbone.Collection.prototype.add.apply( this, arguments );

				this.fetch();
			},

			
		} )
	} );

}(jQuery, _, Backbone, wp));
