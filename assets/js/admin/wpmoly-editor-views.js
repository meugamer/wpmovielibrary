
(function( $ ) {

	var editor = wpmoly.editor;

	editor.view = {};

	/**
	 * WPMOLY Backbone Search View
	 * 
	 * View for metabox movie search form
	 * 
	 * @since    2.2
	 */
	editor.view.Search = Backbone.View.extend({

		el: '#wpmoly-movie-meta-search',

		model: editor.search,

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

			editor.movie.on( 'sync:start', this.spin, this );
			editor.movie.on( 'sync:end', this.unspin, this );
			editor.movie.on( 'sync:done', this.reset, this );
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
			editor.movie.sync( 'search', this.model, {} );
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
			console.log( 'update', event.currentTarget );
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

			editor.results.reset();
		}
	});

	/**
	 * WPMOLY Backbone Movie Model
	 * 
	 * View for metabox movie metadata fields
	 * 
	 * @since    2.2
	 */
	editor.view.Movie = Backbone.View.extend({

		el: '#wpmoly-movie-meta',

		model: editor.movie,

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
	editor.view.Results = Backbone.View.extend({

		el: '#wpmoly-meta-search-results',

		collection: editor.results,

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

			editor.search.set( 'type', 'id' );
			editor.search.set( 'query', id );

			editor.movie.sync( 'search', this.model, {} );
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
	 * WPMOLY Backbone Panel View
	 * 
	 * View for movie metabox panels.
	 * 
	 * @since    2.2
	 */
	editor.view.Panel = Backbone.View.extend({

		el: '#wpmoly-metabox',

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function () {

			this.template = _.template( $( '#wpmoly-metabox' ).html() );
			this.render();

			if ( window.innerWidth < 1180 )
				this.resize();
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

		events: {
			"click #wpmoly-meta-menu a": "navigate"
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
		 * Resize Metabox Panel
		 *
		 * @since 2.0
		 */
		resize: function() {
			$( this.el ).toggleClass( 'small' );
		}
	});

	new editor.view.Panel();

	new editor.view.Movie();
	new editor.view.Search();
	new editor.view.Results();

})(jQuery);
