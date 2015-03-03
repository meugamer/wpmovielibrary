
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

		editor.views.movies = new editor.View.Movies();
		editor.frame = new editor.View.EditMovies( {
			frame: 'select',
			library: editor.models.movies,
			model: editor.models.movies.get( 11 )
		} );
	};

	_.extend( editor, { controller: {}, models: {}, views: {}, Model: {}, View: {} } );

	/**
	 * Basic data model to manipulate metadata and details.
	 * 
	 * @since    2.2
	 */
	editor.Model.Data = Backbone.Model.extend({

		url: ajaxurl,

		id: '',

		type: '',

		save: function( attribute, value ) {

			this.set( attribute, value );
			return this.sync( 'save', this, {} );
		},

		sync: function( method, model, options ) {

			if ( 'save' == method ) {

				_.extend( options, {
					context: this,
					data: {
						action: 'wpmoly_save_' + this.type,
						//nonce: wpmoly.get_nonce( 'fetch-movies' ),
						post_id: this.id,
						method: 'update',
						type: this.type,
						data: model.changed
					}
				});

				return wp.ajax.send( options );

			} else {
				return Backbone.sync.apply( this, arguments );
			}
		},
	});

	/**
	 * Basic data model to store post data.
	 * 
	 * @since    2.2
	 */
	editor.Model.Post = Backbone.Model.extend({

		defaults: {
			post_id: '',
			post_title: '',
			post_date: '',
			post_author: '',
			post_author_url: '',
			post_author_name: '',
			post_status: '',
			post_thumbnail: '',
			images: '',
			posters: '',
			images_total: '',
			posters_total: '',
			edit_poster: '',
			edit_posters: '',
			edit_images: ''
		},
	});

	/**
	 * Movie Meta Model
	 * 
	 * @since    2.2
	 */
	editor.Model.Meta = editor.Model.Data.extend({

		type: 'meta',

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
		}
	});

	/**
	 * Movie Details Model
	 * 
	 * @since    2.2
	 */
	editor.Model.Details = editor.Model.Data.extend({

		type: 'details',

		defaults: {
			status: '',
			media: '',
			rating: '',
			language: '',
			subtitles: '',
			format: ''
		}
	});

	/**
	 * WPMOLY Backbone Movie Model
	 * 
	 * Model for the metabox movie metadata fields. Holy Grail! That model
	 * is linked to a view containing all the inputs and handles the sync
	 * with the server to search for movies.
	 * 
	 * @since    2.2
	 */
	editor.Model.Movie = Backbone.Model.extend({

		id: '',

		defaults: {
			post: {},
			meta: {},
			details: {}
		}
	});

	_.extend( editor.Model, {

		/**
		 * WPMOLY Backbone Movie Model
		 * 
		 * Model for movies list collection.
		 * 
		 * @since    2.2
		 */
		Movies: Backbone.Collection.extend({

			url: ajaxurl,

			model: editor.Model.Movie,

			/**
			 * Overload Backbone sync method to fetch our own data and save
			 * them to the server.
			 * 
			 * @since    2.2
			 * 
			 * @param    string    method Are we reading or is it a regular sync?
			 * @param    object    model Current model
			 * @param    object    options Query options
			 * 
			 * @return   mixed
			 */
			sync: function( method, model, options ) {

				if ( 'read' == method ) {

					_.extend( options, {
						context: this,
						data: {
							action: 'wpmoly_fetch_movies',
							//nonce: wpmoly.get_nonce( 'fetch-movies' ),
							data: _.map( this.models, function( model ) {
								//if ( _.isEmpty( _.filter( model.attributes, function( attr ) { return '' != attr; } ) ) )
									return model.id;
							}, this )
						},
						complete: function() {},
						success: function( response ) {
							_.each( response, function( data, id ) {
								var  id = parseInt( id ),
								  model = this.get( id ),
								   post = new editor.Model.Post,
								   meta = _.extend( new editor.Model.Meta, { id: id } ),
								details = _.extend( new editor.Model.Details, { id: id } );

								model.set( {
									post:    post.set(    _.pick( data.post,    _.keys( post.defaults ) ) ),
									meta:    meta.set(    _.pick( data.meta,    _.keys( meta.defaults ) ) ),
									details: details.set( _.pick( data.details, _.keys( details.defaults ) ) )
								} );
							}, this );
						}
					});

					wp.ajax.send( options );

				} else if ( 'update' == method ) {

					//TODO: save data

				} else {
					return Backbone.sync.apply( this, arguments );
				}
			},

			/**
			 * Add Models to the Collection.
			 * 
			 * This simply overrides the Backbone Collection add()
			 * method to fetch complete meta when adding new movies
			 * to the collection.
			 * 
			 * @since    2.2
			 * 
			 * @param    array     Array of Attachment Models
			 * @param    object    Options
			 * 
			 * @return   this
			 */
			add: function( models, options ) {

				Backbone.Collection.prototype.add.apply( this, arguments );

				this.fetch();
			}

			
		} )
	} );

}(jQuery, _, Backbone, wp));
