
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var editor = wpmoly.editor = function() {

		// Trick of treats
		redux.field_objects.select.init();

		// Extract metadata from view
		var data = {},
		  fields = document.getElementsByClassName( 'meta-data-field' );
		_.each( fields, function( field ) {
			var name = field.name.replace( /meta\[(.*)\]/g, '$1' );
			data[ name ] = field.value;
		});

		// Init models
		editor.models.status = new wpmoly.editor.Model.Status();
		editor.models.movie = new wpmoly.editor.Model.Movie( data );
		editor.models.preview = new wpmoly.editor.Model.Preview();
		editor.models.search = new wpmoly.editor.Model.Search();
		editor.models.results = new wpmoly.editor.Model.Results();

		// Init views
		editor.views.panel = new wpmoly.editor.View.Panel();
		editor.views.movie = new wpmoly.editor.View.Movie( { model: editor.models.movie } );
		editor.views.preview = new wpmoly.editor.View.Preview( { model: editor.models.movie } );
		editor.views.search = new wpmoly.editor.View.Search( { model: editor.models.search, target: editor.models.movie } );
		editor.views.settings = new wpmoly.editor.View.Settings( { model: editor.models.search } );
		editor.views.results = new wpmoly.editor.View.Results( { collection: editor.models.results } );
		editor.views.status = new wpmoly.editor.View.Status( { model: editor.models.status } );

		document.getElementById( 'title' ).addEventListener( 'input', function( event ) {
			editor.models.search.set( { s: event.target.value } );
		});

		window.addEventListener( 'resize', function() {
			editor.views.search.render();
		});
	};

	_.extend( editor, { models: {}, views: {}, Model: {}, View: {} } );

	_.extend( editor.Model, {

		/**
		 * WPMOLY Backbone Status Model
		 * 
		 * Basic Model for the metabox movie search form status.
		 * 
		 * @since    2.2
		 */
		Status: Backbone.Model.extend({

			defaults: {
				error: false,
				loading: false,
				message: wpmoly.l10n.misc.api_connected
			},

			/**
			 * Initialize Model.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function() {

				this.on( 'loading:start', this.load, this );
				this.on( 'loading:end', this.unload, this );
				this.on( 'status:say', this.say, this );
			},

			/**
			 * Turn on loading mode.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			load: function() {
				this.set( { loading: true } );
			},

			/**
			 * Turn off loading mode.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			unload: function() {
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
		}),

		/**
		 * WPMOLY Backbone Search Model
		 * 
		 * Model for the metabox movie search form. This bascillay handle
		 * search data for movies: lang, type, query and a bunch of handy
		 * options.
		 * 
		 * @since    2.2
		 */
		Search: Backbone.Model.extend({

			defaults: {
				post_id: parseInt( document.getElementById( 'post_ID' ).value ),
				s: '',
				lang: document.getElementById( 'wpmoly-search-lang' ).value,
				adult: '',
				year: '',
				pyear: '',
				page: 1,
				paginate: false
			}
		}),

		/**
		 * WPMOLY Backbone Movie Model
		 * 
		 * Model for the metabox movie metadata fields. Holy Grail! That model
		 * is linked to a view containing all the inputs and handles the sync
		 * with the server to search for movies.
		 * 
		 * @since    2.2
		 */
		Movie: Backbone.Model.extend({

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
				actorlimit: parseInt( document.getElementById( 'wpmoly-actor-limit' ).value ),
				setfeatured: parseInt( document.getElementById( 'wpmoly-poster-featured' ).value ),
				autocomplete: {
					collection: parseInt( document.getElementById( 'wpmoly-autocomplete-collection' ).value ),
					genre: parseInt( document.getElementById( 'wpmoly-autocomplete-genre' ).value ),
					actor: parseInt( document.getElementById( 'wpmoly-autocomplete-actor' ).value )
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
				this.post_id = document.getElementById( 'post_ID' ).value;
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

				// Let know we've started queryring
				editor.models.movie.trigger( 'sync:start', this );

				// Not search means regular Backbone sync, not our concern
				if ( 'search' == method ) {

					editor.models.status.trigger( 'loading:start' );
					if ( 'id' == editor.models.search.get( 'type' ) )
						editor.models.status.trigger( 'status:say', wpmoly.l10n.movies.loading );
					else
						editor.models.status.trigger( 'status:say', wpmoly.l10n.movies.searching );

					options = options || {};
					options.context = this;
					options.data = _.extend( options.data || {}, {
						action: 'wpmoly_search_movie',
						nonce: wpmoly.get_nonce( 'search-movies' ),
						query: editor.models.search.toJSON()
					});

					// Let know we're done queryring
					options.complete = function() {
						editor.models.movie.trigger( 'sync:end', this );
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
							editor.models.movie.trigger( 'sync:done', this, response );
							editor.models.status.trigger( 'status:say', wpmoly.l10n.misc.done );

							return true;
						}

						// If not, means multiple movies, show a choice
						_.each( response, function( result ) {
							var result = new editor.Model.Result( result );
							editor.models.results.add( result );
						} );
						editor.models.status.trigger( 'status:say', wpmoly.l10n.movies.multiple_results );
					};

					return wp.ajax.send( options );
				}
				// Fallback to Backbone sync
				else {
					return Backbone.Model.prototype.sync.apply( this, options );
				}
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

				wpmoly.editor.models.movie.save();
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

				var settings = editor.models.movie.settings,
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
						nonce: wpmoly.get_nonce( 'save-movie-meta' ),
						post_id: this.post_id,
						data: this.parse( this.toJSON() )
					},
					success: function() {
						editor.models.status.trigger( 'status:say', wpmoly.l10n.movies.saved );
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
		}),

		/**
		 * WPMOLY Backbone Result Model
		 * 
		 * Model for movie search results collection items.
		 * 
		 * @since    2.2
		 */
		Result: Backbone.Model.extend({

			defaults: {
				id: '',
				poster: '',
				title: '',
				original_title: '',
				year: '',
				release_date: '',
				adult: ''
			}

		}),

		/**
		 * WPMOLY Backbone Results Model
		 * 
		 * Model for movie search results collection.
		 * 
		 * @since    2.2
		 */
		Results: Backbone.Collection.extend({

			model: editor.Model.Result,

			reset: function() {

				editor.models.status.reset();

				return Backbone.Collection.prototype.reset.apply( this, arguments );
			}
		} ),

		/**
		 * WPMOLY Backbone Preview Model
		 * 
		 * Model for the Metabox Preview Panel.
		 * 
		 * @since    2.2
		 */
		Preview: Backbone.Model.extend({

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
			initialize: function() {

				editor.models.movie.on( 'sync:done', this.update, this );
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
			}
		})

	});

})(jQuery);
