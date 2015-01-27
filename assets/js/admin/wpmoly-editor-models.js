
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	editor = wpmoly.editor = function() {

		// Trick of treats
		redux.field_objects.select.init();

		$( '.add-new-h2' ).on( 'click', function() {
			document.location.href = this.href;
		});

		// Extract metadata from view
		var data = {}
		$.each( $( '.meta-data-field' ), function() {
			var name = this.name.replace(/meta\[(.*)\]/g, '$1');
			data[ name ] = this.value;
		});

		// Init models
		editor.models.status = new wpmoly.editor.Model.Status();
		editor.models.panel = new wpmoly.editor.Model.Panel();
		editor.models.movie = new wpmoly.editor.Model.Movie( data );
		editor.models.preview = new wpmoly.editor.Model.Preview();
		editor.models.search = new wpmoly.editor.Model.Search();
		editor.models.results = new wpmoly.editor.Model.Results();
	};

	_.extend( editor, { models: {}, views: {}, Model: {}, View: {} } );

	/**
	 * WPMOLY Backbone Status Model
	 * 
	 * Basic Model for the metabox movie search form status.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.Model.Status = Backbone.Model.extend({

		defaults: {
			active: true,
			loading: false,
			message: 'ready!'
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
		 * @return   void
		 */
		say: function( message ) {
			this.set( { message: message } );
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
	wpmoly.editor.Model.Search = Backbone.Model.extend({

		defaults: {
			lang: $( '#wpmoly-search-lang' ).val(),
			type: $( '#wpmoly-search-type' ).val(),
			query: '',
			options: {
				actor_limit: $( '#wpmoly-actor-limit' ).val(),
				poster_featured: $( '#wpmoly-poster-featured' ).val()
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
	wpmoly.editor.Model.Movie = Backbone.Model.extend({

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

		/**
		 * Initialize Model. Set the AJAX url and current Post ID.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.url = ajaxurl;
			this.post_id = $( '#post_ID' ).val();
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
					editor.models.status.trigger( 'status:say', wpmoly_lang.search_movie );
				else
					editor.models.status.trigger( 'status:say', wpmoly_lang.search_movie_title );

				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action: 'wpmoly_search_movie',
					nonce: wpmoly.get_nonce( 'search-movies' ),
					lang: editor.models.search.get( 'lang' ),
					data: editor.models.search.get( 'query' ),
					type: editor.models.search.get( 'type' )
				});

				// Let know we're done queryring
				options.complete = function() {
					editor.models.movie.trigger( 'sync:end', this );
					editor.models.status.trigger( 'loading:end' );
				};

				// Let's go!
				options.success = function( response ) {

					// Response has meta, that's a single movie
					if ( undefined != response.meta ) {

						this.set_meta( response );
						editor.models.status.trigger( 'status:say', wpmoly_lang.done );

						return true;
					}

					// If not, means multiple movies, show a choice
					_.each( response, function( result ) {
						var result = new editor.Model.Result( result );
						editor.models.results.add( result );
					} );
					editor.models.status.trigger( 'status:say', wpmoly_lang.multiple_results );
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
		 * @param    object    data Movie metadata
		 * 
		 * @return   void
		 */
		set_meta: function( data ) {

			var meta = _.extend( this.defaults, data.meta );
			this.set( meta );

			editor.models.movie.trigger( 'sync:done', this, data );
			editor.models.status.trigger( 'status:say', wpmoly_lang.metadata_saved );
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
		 * Make sure the attributes are correct. Additional attributes
		 * are not welcomed here.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 * 
		 * @return   mixed
		 */
		validate: function( attributes, options ) {

			_.each( attributes, function( value, attr ) {
				if ( undefined == this.defaults[ attr ] ) {
					this.unset( attr, attributes );
				}
			}, this );
		}
	});

	/**
	 * WPMOLY Backbone Result Model
	 * 
	 * Model for movie search results collection items.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.Model.Result = Backbone.Model.extend({

		defaults: {
			id: '',
			poster: '',
			title: '',
			original_title: '',
			year: '',
			release_date: '',
			adult: ''
		},

		initialize: function() {}

	});

	/**
	 * WPMOLY Backbone Results Model
	 * 
	 * Model for movie search results collection.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.Model.Results = Backbone.Collection.extend({

		model: editor.Model.Result,

		initialize: function() {}
	});

	/**
	 * WPMOLY Backbone Preview Model
	 * 
	 * Model for the Metabox Preview Panel.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.Model.Preview = Backbone.Model.extend({

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
		 * Make sure the attributes are correct. Additional attributes
		 * are not welcomed here.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    attributes
		 * @param    object    options
		 * 
		 * @return   mixed
		 */
		validate: function( attributes, options ) {

			_.each( attributes, function( value, attr ) {
				if ( undefined == this.defaults[ attr ] ) {
					this.unset( attr, attributes );
				}
			}, this );
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
	});

	/**
	 * WPMOLY Backbone Panel Model
	 * 
	 * Model for the Metabox Panels.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.Model.Panel = Backbone.Model.extend({});

	/**
	 * Override Movie and Preview Models set() method.
	 * 
	 * We want attributes validation to be run each time attributes are set
	 * to avoid unknown attributes.
	 */
	_.each( [ wpmoly.editor.Model.Movie, wpmoly.editor.Model.Preview ], function( model ) {
		model.prototype.set = function( attributes, options ) {
			options = options || {};
			options.validate = true;
			return Backbone.Model.prototype.set.call( this, attributes, options );
		};
	}, this );

	// To infinity... And beyond!
	wpmoly.editor();

})(jQuery);
