
window.wpmoly = window.wpmoly || {};

(function( $, _, Backbone, wp, wpmoly ) {

	var  editor = wpmoly.editor
	l10n_movies = wpmoly.l10n.movies,
	  l10n_misc = wpmoly.l10n.misc;

	/**
	 * WPMOLY Backbone Status Model
	 * 
	 * Model for Search Settings manipulation.
	 * 
	 * @since    2.2
	 */
	editor.Model.Status = Backbone.Model.extend({

		defaults: {
			error:   false,
			loading: false,
			message: l10n_misc.api_connected
		},

		/**
		 * Initialize Model.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.on( 'loading:start', this.loading, this );
			this.on( 'loading:end',   this.loaded, this );
			this.on( 'status:say',    this.say, this );
		},

		/**
		 * Turn on loading mode.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		loading: function() {
			this.set( { loading: true } );
		},

		/**
		 * Turn off loading mode.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		loaded: function() {
			this.set( { loading: false } );
		},

		/**
		 * Update status message.
		 * 
		 * @since    2.2
		 * 
		 * @param    string     New status message
		 * @param    boolean    Error status
		 * 
		 * @return   void
		 */
		say: function( message, error ) {

			var options = { error: false, message: message };
			if ( true === error )
				options.error = true;

			this.set( options );
		},

		/**
		 * Reset Model to default
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		reset: function() {

			this.clear().set( this.defaults );
		}
	});

	/**
	 * WPMOLY Backbone Search Settings Model
	 * 
	 * Model for Search Settings manipulation.
	 * 
	 * @since    2.2
	 */
	editor.Model.Settings = Backbone.Model.extend({

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
	 * WPMOLY Backbone Search Model
	 * 
	 * Model for the metabox movie search form. This bascillay handle
	 * search data for movies: lang, type, query and a bunch of handy
	 * options.
	 * 
	 * @since    2.2
	 */
	editor.Model.Search = Backbone.Model.extend({

		defaults: {
			settings: {},
			results:  {},
			status:   {},
			movie:    {}
		},

		initialize: function( options ) {

			this.settings = options.settings;
			this.results  = options.results;
			this.status   = options.status;
			this.movie    = options.movie;
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

					this.trigger( 'sync:start',   this );
					this.trigger( 'search:start', this );

					this.status.loading();
					if ( 'id' == this.settings.get( 'type' ) ) {
						this.status.say( l10n_movies.loading );
					} else {
						this.status.say( l10n_movies.searching );
					}
				};

				// Let know we're done queryring
				options.complete = function() {
					this.trigger( 'sync:end', this );
					this.status.loaded();
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
						this.trigger( 'sync:done', this, response );

						return true;
					}

					// Looks like we have multiple results
					this.results.pages   = response.total_pages;
					this.results.results = response.total_results;
					var results = [];

					// If not, means multiple movies, show a choice
					_.each( response.results, function( result ) {
						results.push( new editor.Model.Result( result ) );
					}, this );
					

					this.results.reset( [], { silent: true } );
					this.results.add( results );

					this.status.say( l10n_movies.multiple_results.replace( '%d', results.length ) );
				};








				/*editor.models.status.trigger( 'loading:start' );
				if ( 'id' == editor.models.search.get( 'type' ) )
					editor.models.status.trigger( 'status:say', l10n_movies.loading );
				else
					editor.models.status.trigger( 'status:say', l10n_movies.searching );

				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action: 'wpmoly_search_movie',
					nonce: wpmoly.get_nonce( 'search-movies' ),
					query: editor.models.search.toJSON()
				});

				// Let know we're done queryring
				options.complete = function() {
					this.trigger( 'sync:end', this );
					editor.models.status.trigger( 'loading:end' );
				};

				// Handle errors
				options.error = function( response ) {

					var error = response;
					if ( _.isArray( error ) )
						error = _.first( error );

					editor.models.status.set({
						error: true,
						code: error.code,
						message: error.message
					});
				};

				// Let's go!
				options.success = function( response ) {

					// Response has meta, that's a single movie
					if ( undefined !== response.meta ) {

						// Set metadata
						this.setMeta( response.meta );

						// Set Taxonomies
						if ( undefined !== response.taxonomies )
							this.setTaxonomies( response.taxonomies );

						// Triggers
						this.trigger( 'sync:done', this, response );
						//editor.models.status.trigger( 'status:say', l10n_misc.done );

						return true;
					}

					editor.models.results.pages = response.total_pages;
					editor.models.results.results = response.total_results;
					var results = [];

					// If not, means multiple movies, show a choice
					_.each( response.results, function( result ) {

						var result = new editor.Model.Result( result );
						results.push( result );
					}, this );

					editor.models.results.reset( [], { silent: true } );
					editor.models.results.add( results );

					editor.models.status.trigger( 'status:say', l10n_movies.multiple_results.replace( '%d', results.length ) );
				};*/

				return wp.ajax.send( options );
			}
			// Fallback to Backbone sync
			else {
				return Backbone.Model.prototype.sync.apply( this, options );
			}
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

		// The Holy Grail: movie metadata.
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
			setfeatured:  1,
			importimages: 0,
			autocomplete: {
				collection: 1,
				genre:      1,
				actor:      1
			}
		},

		/**
		 * Initialize Model. Set the AJAX url and current Post ID.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.url = ajaxurl;

			this.post_id = parseInt( wpmoly.getValue( '#post_ID', 0 ) );

			
		},

		/**
		 * Update the Model's attributes with the fetched movie's metadata
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Movie metadata
		 * 
		 * @return   void
		 */
		setMeta: function( meta ) {

			var meta = _.extend( this.defaults, meta );
			this.set( meta );

			this.movie.save();
		},

		/**
		 * Set the movie's taxonomies if any (an needed)
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Movie taxonomies
		 * 
		 * @return   void
		 */
		setTaxonomies: function( taxonomies ) {

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
		}
	});

	/**
	 * WPMOLY Backbone Result Model
	 * 
	 * Model for movie search results collection items.
	 * 
	 * @since    2.2
	 */
	editor.Model.Result = Backbone.Model.extend({

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
	editor.Model.Results = Backbone.Collection.extend({

		model: editor.Model.Result,

		pages: '',
		results: '',

		reset: function( models, options ) {

			//editor.models.status.reset();

			return Backbone.Collection.prototype.reset.apply( this, arguments );
		}
	});

	/**
	 * WPMOLY Backbone Preview Model
	 * 
	 * Model for the Metabox Preview Panel.
	 * 
	 * @since    2.2
	 */
	editor.Model.Preview = Backbone.Model.extend({

		defaults: {
			poster: '',
			rating: '',
			title: '',
			original_title: '',
			runtime: '',
			genres: '',
			release_date: '',
			overview: '',
			director: '',
			cast: ''
		},

		/**
		 * Initialize Model.
		 * 
		 * Bind the Model update on the Movie Model sync:done event to
		 * update the preview when the Movie attributes are changed.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			//this.controller = options.controller;

			//this.controller.on( 'sync:done', this.update, this );
		},

		/**
		 * Update Model to match the Movie Model changes
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Movie Model
		 * 
		 * @return   mixed
		 */
		update: function( model, data ) {

			var meta = {};
			_.each( this.defaults, function( value, attr ) {
				meta[ attr ] = data.meta[ attr ];
			}, this );
			meta.poster = data.poster;

			this.set( meta );

			this.notify();
		},

		/**
		 * Update Metabox Menu to show a notification label
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Movie Model
		 * 
		 * @return   mixed
		 */
		notify: function( model ) {

			wpmoly.metabox.models.metabox.state( 'preview' ).set({
				label: '<span class="wpmolicon icon-certification"></span>',
				labeltitle: 'Preview updated'
			});
		}
	});

}( jQuery, _, Backbone, wp, wpmoly ) );
