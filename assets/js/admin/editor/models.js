
var editor = wpmoly.editor,
      l10n = wpmoly.l10n;

/**
 * Basic data model to manipulate metadata and details.
 * 
 * This Model overrides the sync method to save individuals details/meta in 
 * quick-edit mode. In regular movie edit (or search) mode editor.model.Movie 
 * will use its own sync method to save all data in a single shot.
 * 
 * @since    2.2
 */
editor.model.Data = Backbone.Model.extend({

	url: ajaxurl,

	// Post ID
	id: '',

	// Data type, generally details or metadata
	type: '',

	/**
	 * Shortcut for saving metadata/details.
	 * 
	 * @since    2.2
	 * 
	 * @param    string    attribute key
	 * @param    string    attribute value
	 * 
	 * @return   xhr
	 */
	save: function( attribute, value ) {

		this.set( attribute, value );
		return this.sync( 'save', this, {} );
	},

	/**
	 * Override Backbone.Model.sync method to quick/auto-save metadata/details
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

		if ( 'save' == method ) {
	
			options = options || {};
			_.extend( options, {
				context: this,
				data: {
					action:  'wpmoly_save_' + this.type,
					method:  'update',
					nonce:   wpmoly.get_nonce( 'save-movie-meta' ),
					data:    model.changed,
					post_id: this.id,
					type:    this.type
				}
			});

			return wp.ajax.send( options );

		} else {
			return Backbone.sync.apply( this, arguments );
		}
	},
});

/**
 * WPMOLY Backbone Search Settings Model
 * 
 * Model for Search Settings manipulation.
 * 
 * @since    2.2
 */
editor.model.Settings = Backbone.Model.extend({

	defaults: {
		post_id:  '',
		s:        '',
		lang:     '',
		adult:    false,
		year:     '',
		pyear:    '',
		page:     1,
		paginate: true
	}
});

/**
 * Basic data model to store post data.
 * 
 * @since    2.2
 */
editor.model.Post = Backbone.Model.extend({

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
editor.model.Meta = editor.model.Data.extend({

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
	},

	settings: {
		actorlimit:   0,
		autocomplete: {
			collection: 1,
			genre:      1,
			actor:      1
		}
	}
});

/**
 * Movie Details Model
 * 
 * @since    2.2
 */
editor.model.Details = editor.model.Data.extend({

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
 * Movie Preview Formatted Data
 * 
 * @since    2.2
 */
editor.model.Formatted = editor.model.Data.extend({

	type: 'formatted',

	defaults: {
		status: '',
		media: '',
		rating: '',
		language: '',
		subtitles: '',
		format: '',

		title: '',
		tagline: '',
		overview: '',
		release_date: '',
		runtime: '',
		genres: '',
		director: '',
		producer: '',
		cast: '',
		certification: '',
		imdb_id: '',

		poster: '',
		backdrop: ''
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
editor.model.Movie = Backbone.Model.extend({

	id: '',

	defaults: {
		post: {},
		meta: {},
		details: {},
		formatted: {},
		nonces: {}
	},

	/**
	 * Save the movie. Our job is done!
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	save: function() {

		var params = {
			emulateJSON: true,
			data: { 
				action: 'wpmoly_save_meta',
				nonce:   wpmoly.get_nonce( 'save-movie-meta' ),
				post_id: this.post_id,
				data:    this.parse( this.toJSON() )
			},
			success: function() {
				//editor.models.status.trigger( 'status:say', l10n_movies.saved );
			}
		};

		return Backbone.sync( 'create', this, params );
	},

	/**
	 * Simple parser to prepare attributes: we don't want to feed
	 * subarrays to the server.
	 * 
	 * @since    2.2
	 * 
	 * @param    object    data Movie metadata
	 * 
	 * @return   mixed
	 */
	parse: function( data ) {

		var data = _.pick( data, _.keys( this.defaults ) );
		_.map( data, function( meta, key ) {
			if ( _.isArray( meta ) )
				data[ key ] = meta.toString();
		} );

		return data;
	},

	/**
	 * Set the movie's taxonomies if any (and needed).
	 * 
	 * This should be use only when editing a single movie. It won't work
	 * anywhere else anyway.
	 * 
	 * @since    2.2
	 * 
	 * @param    object    Movie taxonomies
	 * 
	 * @return   void
	 */
	setTaxonomies: function( taxonomies ) {

		if ( ! wpmoly.isEditMovie && ! wpmoly.isNewMovie ) {
			return;
		}

		var settings = this.settings,
		autocomplete = settings.autocomplete;

		if ( autocomplete.actor && undefined != taxonomies.actors ) {
			var limit = settings.actorlimit || 0,
			actors = limit ? taxonomies.actors.splice( 0, limit ) : taxonomies.actors;

			_.each( actors, function( actor, index ) {
				$( '#tagsdiv-actor .tagchecklist' ).append( '<span><a id="actor-check-num-' + index + '" class="ntdelbutton">X</a>&nbsp;' + actor + '</span>' );
				tagBox.flushTags( $( '#actor.tagsdiv' ), $( '<span>' + actor + '</span>' ) );
			});
		}

		if ( autocomplete.genre && undefined != taxonomies.genres ) {
			_.each( taxonomies.genres, function( genre, index ) {
				$( '#tagsdiv-genre .tagchecklist' ).append( '<span><a id="genre-check-num-' + index + '" class="ntdelbutton">X</a>&nbsp;' + genre + '</span>' );
				tagBox.flushTags( $( '#genre.tagsdiv' ), $( '<span>' + genre + '</span>' ) );
			});
		}

		if ( autocomplete.collection && undefined != taxonomies.collections ) {
			_.each( taxonomies.collections, function( collection, index ) {
				$( '#newcollection' ).delay( 1000 ).queue( function( next ) {
					$( this ).prop( 'value', collection );
					$( '#collection-add-submit' ).click();
					next();
				});
			});
		}
	}
});

/**
 * WPMOLY Backbone Movie Model
 * 
 * Model for movies list collection.
 * 
 * @since    2.2
 */
editor.model.Movies = Backbone.Collection.extend({

	url: ajaxurl,

	model: editor.model.Movie,

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
					nonce: wpmoly.get_nonce( 'fetch-movies' ),
					data: _.map( this.models, function( model ) {
						return model.id;
					}, this )
				},
				complete: function() {},
				success: function( response ) {
					_.each( response, function( data, id ) {
						var  id = parseInt( id ),
						    model = this.get( id ),
						    post = new editor.model.Post,
						    meta = _.extend( new editor.model.Meta, { id: id } ),
						details = _.extend( new editor.model.Details, { id: id } );

						model.set( {
							post:    post.set(    _.pick( data.post,    _.keys( post.defaults ) ) ),
							meta:    meta.set(    _.pick( data.meta,    _.keys( meta.defaults ) ) ),
							details: details.set( _.pick( data.details, _.keys( details.defaults ) ) ),
							nonces:  data.nonces
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

} );

/**
 * WPMOLY Backbone Result Model
 * 
 * Model for movie search results collection items.
 * 
 * @since    2.2
 */
editor.model.Result = Backbone.Model.extend({

	defaults: {
		id: '',
		poster: '',
		title: '',
		original_title: '',
		year: '',
		release_date: '',
		adult: ''
	}

});

/**
 * WPMOLY Backbone Results Model
 * 
 * Model for movie search results collection.
 * 
 * @since    2.2
 */
editor.model.Results = Backbone.Collection.extend({

	model: editor.model.Result,

	pages: '',
	results: '',
});

/**
 * WPMOLY Backbone Search Model
 * 
 * Model for the metabox movie search form. This bascillay handle
 * search data for movies: lang, type, query and a bunch of handy
 * options.
 * 
 * @since    2.2
 */
editor.model.Search = Backbone.Model.extend({

	defaults: {
		settings: new editor.model.Settings,
		results:  new editor.model.Results,
		status:   new editor.model.Status,
		movie:    new editor.model.Movie
	},

	/**
	 * Initialize Model.
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	initialize: function( options ) {

		this.settings = this.get( 'settings' );
		this.results  = this.get( 'results' );
		this.status   = this.get( 'status' );
		this.movie    = this.get( 'movie' );
	},

	/**
	 * Overload Backbone sync method to fetch our own data and save
	 * them to the server.
	 * 
	 * @since    2.2
	 * 
	 * @param    string    method Are we searching or is it a regular sync?
	 * @param    object    model Current model
	 * @param    object    options Query options
	 * 
	 * @return   mixed
	 */
	sync: function( method, model, options ) {

		// Not search means regular Backbone sync, not our concern
		if ( 'search' == method ) {

			options = options || {};
			options.context = this;
			options.data = _.extend( options.data || {}, {
				action: 'wpmoly_search_movie',
				nonce:  wpmoly.get_nonce( 'search-movies' ),
				query:  this.settings.toJSON()
			});

			// Let know we've started queryring
			options.beforeSend = function() {

				this.trigger( 'search:start', this );

				this.status.loading();
				if ( 'id' == this.settings.get( 'type' ) ) {
					this.status.say( l10n.movies.loading );
				} else {
					this.status.say( l10n.movies.searching );
				}
			};

			// Let know we're done queryring
			options.complete = function() {

				this.trigger( 'search:end', this );
				this.status.loaded();
				this.status.reset();
			};

			// Handle errors
			options.error = function( response ) {

				var error = response;
				if ( _.isArray( error ) )
					error = _.first( error );

				this.status.set({
					error:   true,
					code:    error.code,
					message: error.message
				});
			};

			// Let's go!
			options.success = function( response ) {

				// Response has meta, that's a single movie
				if ( undefined !== response.meta ) {

					// Set movie Metadata
					this.movie.setMeta( response.meta );

					// Set movie Taxonomies
					if ( undefined !== response.taxonomies )
						this.movie.setTaxonomies( response.taxonomies );

					// Triggers
					this.trigger( 'search:done', this, response );

					return true;
				}

				// Looks like we have multiple results
				this.results.pages   = response.total_pages;
				this.results.results = response.total_results;
				var results = [];

				// If not, means multiple movies, show a choice
				response.results.map( function( result ) {
					return new editor.model.Result( result );
				} );

				this.results.reset( [], { silent: true } );
				this.results.add( response.results );
				console.log( this.results );

				this.status.say( l10n_movies.multiple_results.replace( '%d', results.length ) );
			};

			return wp.ajax.send( options );
		}
		// Fallback to Backbone sync
		else {
			return Backbone.Model.prototype.sync.apply( this, options );
		}
	}
});