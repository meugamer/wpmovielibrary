
window.wpmoly = window.wpmoly || {};

(function($){

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
		editor.panel = new editor.model.Panel();
		editor.movie = new editor.model.Movie( data );
		editor.search = new editor.model.Search();
		editor.results = new editor.model.Results();
	};

	_.extend( editor, { model: {}, view: {} } );

	/**
	 * WPMOLY Backbone Search Model
	 * 
	 * Model for the metabox movie search form. This bascillay handle
	 * search data for movies: lang, type, query and a bunch of handy
	 * options.
	 */
	Search = editor.model.Search = Backbone.Model.extend({

		defaults: {
			lang: $( '#wpmoly-search-lang' ).val(),
			type: $( '#wpmoly-search-type' ).val(),
			query: '',
			options: {
				actor_limit: $( '#wpmoly-actor-limit' ).val(),
				poster_featured: $( '#wpmoly-poster-featured' ).val()
			}
		},

		// TODO: is this of any use?
		initialize: function() {},
		changed: function( model ) {}
	});

	/**
	 * WPMOLY Backbone Movie Model
	 * 
	 * Model for the metabox movie metadata fields. Holy Grail! That model
	 * is linked to a view containing all the inputs and handles the sync
	 * with the server to search for movies.
	 */
	Movie = editor.model.Movie = Backbone.Model.extend({

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
			this.trigger( 'sync:start', this );

			// Not search means regular Backbone sync, not our concern
			if ( 'search' == method ) {

				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action: 'wpmoly_search_movie',
					nonce: wpmoly.get_nonce( 'search-movies' ),
					lang: editor.search.get( 'lang' ),
					data: editor.search.get( 'query' ),
					type: editor.search.get( 'type' )
				});

				// Let know we're done queryring
				options.complete = function() {
					this.trigger( 'sync:end', this );
				};

				// Let's go!
				options.success = function( response ) {

					// Response has meta, that's a single movie
					if ( undefined != response.meta ) {
						this.set_meta( response );
						return true;
					}

					// If not, means multiple movies, show a choice
					_.each( response, function( result ) {
						var result = new Result( result );
						editor.results.add( result );
					} );
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
			editor.movie.set( meta );

			this.trigger( 'sync:done', this );
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
	Result = editor.model.Result = Backbone.Model.extend({

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
	Results = editor.model.Results = Backbone.Collection.extend({

		model: Result,

		initialize: function() {}
	});

	/**
	 * WPMOLY Backbone Panel Model
	 * 
	 * Model for the Metabox Panels.
	 * 
	 * @since    2.2
	 */
	Panel = editor.model.Panel = Backbone.Model.extend({});

	wpmoly.editor();

})(jQuery);
