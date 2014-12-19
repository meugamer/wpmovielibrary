
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	editor = wpmoly.editor || {};
	editor.views = editor.views || {};

	editor.views.init = function() {

		editor.views.panel = new wpmoly.editor.View.Panel();
		editor.views.movie = new wpmoly.editor.View.Movie();
		editor.views.preview = new wpmoly.editor.View.Preview();
		editor.views.search = new wpmoly.editor.View.Search();
		editor.views.results = new wpmoly.editor.View.Results();
	};

	/**
	 * WPMOLY Backbone Search View
	 * 
	 * View for metabox movie search form
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.View.Search = Backbone.View.extend({

		el: '#wpmoly-movie-meta-search',

		model: editor.models.search,

		events: {
			"click #wpmoly-search": "search",
			"click #wpmoly-update": "update",
			"click #wpmoly-empty": "empty",
			"change #wpmoly-search-query": "set",
			"change #wpmoly-search-lang": "set",
			"change #wpmoly-search-type": "set"
		},

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.unspin();

			this.template = _.template( $( '#wpmoly-movie-meta-search' ).html() );
			this.render();

			editor.models.movie.on( 'sync:start', this.spin, this );
			editor.models.movie.on( 'sync:end', this.unspin, this );
			editor.models.movie.on( 'sync:done', this.reset, this );
		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		render: function() {

			this.$el.html( this.template() );
			return this;
		},

		/**
		 * Update the Model's search query value when changed.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Event
		 * 
		 * @return   void
		 */
		set: function( event ) {

			var data = [],
			    type = event.currentTarget.id.replace( 'wpmoly-search-', '' ),
			   value = event.currentTarget.value;
			data[ type ] = value;

			this.model.set( data );
		},

		/**
		 * Trigger the search process
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Event
		 * 
		 * @return   void
		 */
		search: function( event ) {

			event.preventDefault();

			var query = $( '#wpmoly-search-query' ).val(),
			     lang = $( '#wpmoly-search-lang' ).val();
			if ( query != this.model.get( 'query' ) )
				this.model.set( { query: query, type: 'title', lang: lang } );

			editor.models.movie.sync( 'search', this.model, {} );
		},

		/**
		 * Not implemented yet.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Event
		 * 
		 * @return   void
		 */
		update: function( event ) {

			event.preventDefault();

			var tmdb_id = editor.models.movie.get( 'tmdb_id' ),
			    imdb_id = editor.models.movie.get( 'imdb_id' ),
			         id = tmdb_id || imdb_id;

			if ( undefined != id && '' != id ) {
				this.model.set( { query: id, type: 'id' } );
				editor.models.movie.sync( 'search', this.model, {} );
			}
		},

		/**
		 * Not implemented yet.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Event
		 * 
		 * @return   void
		 */
		empty: function( event ) {

			event.preventDefault();
			console.log( 'empty', event.currentTarget );
		},

		/**
		 * Show spinner whenever needed
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		spin: function() {
			$( '#wpmoly-meta-search-spinner' ).show();
		},

		/**
		 * Hide spinner whenever needed
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		unspin: function() {
			$( '#wpmoly-meta-search-spinner' ).hide();
		},

		/**
		 * Reset search results
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		reset: function() {

			editor.models.results.reset();
		}
	});

	/**
	 * WPMOLY Backbone Movie Model
	 * 
	 * View for metabox movie metadata fields
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.View.Movie = Backbone.View.extend({

		el: '#wpmoly-movie-meta',

		model: editor.models.movie,

		events: {
			"change .meta-data-field": "update"
		},

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.template = _.template( $( '#wpmoly-movie-meta' ).html() );
			this.render();

			_.bindAll( this, 'render' );

			this.model.on( 'change', this.changed, this );
		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Model
		 * 
		 * @return   void
		 */
		render: function( model ) {

			this.$el.html( this.template() );
			return this;
		},

		/**
		 * Update the View to match the Model's changes
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Model
		 * 
		 * @return   void
		 */
		changed: function( model ) {

			_.each( model.changed, function( meta, key ) {
				$( '#meta_data_' + key ).val( meta );
			} );
		},

		/**
		 * Update the Model whenever an input value is changed
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Event
		 * 
		 * @return   void
		 */
		update: function( event ) {

			var meta = event.currentTarget.id.replace( 'meta_data_', '' ),
			   value = event.currentTarget.value;

			this.model.set( meta, value );
		}
	});

	/**
	 * WPMOLY Backbone Results View
	 * 
	 * View for movie search results collection.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.View.Results = Backbone.View.extend({

		el: '#wpmoly-meta-search-results',

		collection: editor.models.results,

		events : {
			'click .wpmoly-select-movie a' : 'get'
		},

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.template = _.template( $( '#wpmoly-search-results-template' ).html() );

			_.bindAll( this, 'render' );
			this.collection.bind( 'change', this.render );
			this.collection.bind( 'add', this.render );

			this.collection.on( 'reset', this.reset, this );
		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		render: function() {

			var results = this.template( { results : this.collection.toJSON() } );

			$( this.el ).show();
			$( this.el ).html( results );

			return this;
		},

		/**
		 * Fetch movie metadata based on the select result
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Event
		 * 
		 * @return   void
		 */
		get: function( event ) {

			event.preventDefault();

			var id = event.currentTarget.hash.replace( '#', '' );

			editor.models.search.set( 'type', 'id' );
			editor.models.search.set( 'query', id );

			editor.models.movie.sync( 'search', this.model, {} );
		},

		/**
		 * Reset the results view
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		reset: function() {

			this.$el.empty();
			this.$el.hide();
		},

	});

	/**
	 * WPMOLY Backbone Preview View
	 * 
	 * View for movie metabox preview panel.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.View.Preview = Backbone.View.extend({

		el: '#wpmoly-meta-preview-panel',

		model: editor.models.preview,

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function () {
			
			_.bindAll( this, 'render' );

			this.template = _.template( $( this.el ).html() );
			this.render();

			this.model.on( 'change', this.changed, this );
		},

		/**
		 * Update the View to match the Model's changes
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Model
		 * 
		 * @return   void
		 */
		changed: function( model ) {

			_.each( model.changed, function( meta, key ) {
				$( '#wpmoly-movie-preview-' + key ).text( meta );
			} );

			$( '#wpmoly-movie-preview' ).removeClass( 'empty' );
			$( '#wpmoly-movie-preview-message' ).remove();
		},
	});

	/**
	 * WPMOLY Backbone Panel View
	 * 
	 * View for movie metabox panels.
	 * 
	 * @since    2.2
	 */
	wpmoly.editor.View.Panel = Backbone.View.extend({

		el: '#wpmoly-metabox',

		meta: '#wpmoly-meta',
		menu: '#wpmoly-meta-menu',

		events: {
			"click #wpmoly-meta-menu a": "navigate",
		},

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function () {
			
			_.bindAll( this, 'render', 'fix' );

			this.template = _.template( $( '#wpmoly-metabox' ).html() );
			this.render();

			if ( window.innerWidth < 1180 )
				this.resize();

			$( window ).scroll( this.fix );
		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		render: function () {

			this.$el.html( this.template() );
			return this;
		},

		/**
		 * Jump through the panels
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Event
		 * 
		 * @return   void
		 */
		navigate: function( event ) {

			event.preventDefault();

			var $panel = $( event.currentTarget.hash ),
			      $tab = $( event.currentTarget.parentElement ),
			   $panels = $( '.panel' ),
			     $tabs = $( '.tab' );

			if ( undefined == $panel || undefined == $tab || $tab.hasClass( 'off' ) )
				return false;

			$panels.removeClass( 'active' );
			$tabs.removeClass( 'active' );
			$panel.addClass( 'active' );
			$tab.addClass( 'active' );
		},

		/**
		 * Set a fixed position for the panel tabs when scrolling long
		 * panels (essentially the meta one).
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		fix: function() {

			var $meta = $( this.meta ),
			    $menu = $( this.menu ),
			   menu_t = $menu[0].offsetTop,
			   menu_h = $menu[0].offsetHeight
			   menu_b = menu_t + menu_h,
			   meta_h = $meta[0].offsetHeight,
			   meta_t = $meta.offset().top - 36,
			   meta_b = meta_t + meta_h,
			        y = window.scrollY;

			if ( meta_t < y && meta_b > y ) {

				var t = Math.round( y - meta_t ),
				   _t = t + menu_h,
				  top = 'auto';
				if ( menu_b < meta_h && _t < meta_h ) {
					top = t;
				}
				$( this.menu ).css( { top: top } );
			}
			else if ( menu_t ) {
				$( this.menu ).css( { top: 0 } );
			}
		},

		/**
		 * Resize Metabox Panel
		 *
		 * @since 2.0
		 */
		resize: function() {
			$( this.el ).toggleClass( 'small' );
		}
	});

	wpmoly.editor.views.init();

})(jQuery);
