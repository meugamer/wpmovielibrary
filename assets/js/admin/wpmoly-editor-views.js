
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var editor = wpmoly.editor;

	_.extend( editor.View, {

		/**
		 * WPMOLY Backbone Status View
		 * 
		 * View for metabox movie search form
		 * 
		 * @since    2.2
		 */
		Status: Backbone.View.extend({

			el: '#wpmoly-search-status',

			events: {
				'mouseenter .wpmoly-status-text' : 'extend',
				'mouseleave .wpmoly-status-text' : 'collapse',
				'click' : 'reset'
			},

			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function() {

				this.template = _.template( $( '#wpmoly-search-status-template' ).html() );
				this.render();

				_.bindAll( this, 'render' );

				this.model.on( 'change', this.render, this );

			},

			/**
			 * Render the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			render: function() {

				this.$el.html( this.template( { status: this.model.toJSON() } ) );
				return this;
			},

			/**
			 *Set Status bar full width to display long messages
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			extend: function( event ) {

				if ( event.currentTarget.scrollWidth > event.currentTarget.clientWidth )
					this.$el.addClass( 'active' );
			},

			/**
			 * Reset Status bar to default width
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			collapse: function( event ) {

				this.$el.removeClass( 'active' );
			},

			/**
			 * Reset Status attributes
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			reset: function() {

				this.model.reset();
			}

		}),

		/**
		 * WPMOLY Backbone Settings View
		 * 
		 * View for metabox movie search settings toolbar
		 * 
		 * @since    2.2
		 */
		Settings: Backbone.View.extend({

			el: '#wpmoly-meta-search-settings',

			events: {
				"click a" : "stopPropagation",
				"click #wpmoly-search-adult" : "set",
				"click #wpmoly-search-paginate" : "set",
				"change #wpmoly-search-year" : "set",
				"change #wpmoly-search-pyear" : "set",
			},

			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function() {

				this.template = _.template( $( '#wpmoly-search-settings-template' ).html() );
				this.render();

				this.model.on( 'change', this.render, this );
			},

			/**
			 * Render the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			render: function() {

				this.$el.html( this.template( { settings: this.model.toJSON() } ) );

				return this;
			},

			/**
			 * Update Search Model attributes
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			set: function( event ) {

				var elem = event.currentTarget,
				    type = elem.id.replace( 'wpmoly-search-', '' ),
				    data = { page: 1 };

				switch ( type ) {
					case 'adult':
					case 'paginate':
						event.preventDefault();
						data[ type ] = ! this.model.get( type );
						this.model.set( data );
						break;
					case 'year':
					case 'pyear':
						data[ type ] = elem.value;
						this.model.set( data );
						break;
					default:
						break;
				}
			},

			/**
			 * Toogle View's element
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			toggle: function() {

				this.$el.slideToggle( 250 );
			},

			/**
			 * Toogle View's element
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS 'Click' Event
			 * 
			 * @return   void
			 */
			stopPropagation: function( event ) {

				event.stopPropagation();
			}
		}),

		/**
		 * WPMOLY Backbone Search View
		 * 
		 * View for metabox movie search form
		 * 
		 * @since    2.2
		 */
		Search: Backbone.View.extend({

			el: '#wpmoly-search-box',

			locked: false,

			events: {
				"click #wpmoly-search": "search",
				"click #wpmoly-update": "update",
				"click #wpmoly-empty": "empty",

				"click #wpmoly-lang": "openLangSelect",
				"keydown #wpmoly-lang": "closeLangSelect",
				"click .wpmoly-lang-selector": "setSearchLang",
				"click #wpmoly-search-settings": "toggleSettings",

				"keydown #wpmoly-search-query": "submit",
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
			initialize: function( options ) {

				// Allow model and target override to use search with
				// alternative content
				_.extend( this, _.pick( options, 'model', 'target' ) );

				this.template = _.template( this.$el.html() );
				this.render();

				this.model.on( 'change:s', this.updateQuery, this );
				this.model.on( 'change:lang', this.toggleLangSelect, this );

				this.target.on( 'sync:start', this.lock, this );
				this.target.on( 'sync:done', this.reset, this );
			},

			/**
			 * Lock the View to avoid sending useless multiple queries
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			lock: function() {

				this.locked = true;
			},

			/**
			 * Unlock the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			unlock: function() {

				this.locked = false;
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
				this.resize();

				return this;
			},

			/**
			 * Resize the query input to fit smoothly
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			resize: function() {

				var container = this.$el.outerWidth(),
				       status = document.getElementById( 'wpmoly-search-status' ).scrollWidth,
				        tools = document.getElementById( 'wpmoly-search-tools' ).scrollWidth,
				       search = document.getElementById( 'wpmoly-search' ).scrollWidth,
				         lang = document.getElementById( 'wpmoly-lang' ).scrollWidth,
				         form = document.getElementById( 'wpmoly-search-form' ),
				        query = document.getElementById( 'wpmoly-search-query' );

				var formwidth = ( container - ( status + tools + 106 ) );
				   inputwidth = ( formwidth - ( lang + search + 16 ) );

				form.style.width  = formwidth  + 'px';
				query.style.width = inputwidth + 'px';
			},

			/**
			 * Toggle Search Language Selectlist
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Click Event
			 */
			openLangSelect: function( event ) {

				if ( undefined !== event.preventDefault )
					event.preventDefault();

				var $select = this.$el.find( '#wpmoly-lang-select' );

				if ( 'none' !== $select.css( 'display' ) )
					return this.closeLangSelect();

				var $lang = this.$el.find( '#wpmoly-lang' ),
				$selected = this.$el.find( '#wpmoly-lang-select a.selected' );

				$lang.attr( 'data-lang', this.model.get( 'lang' ) );
				$select.slideDown( 250 );
				$selected.removeClass( 'selected' );
				
				var $selected = this.$el.find( '#wpmoly-lang-select a[data-lang="' + this.model.get( 'lang' ) + '"]' ),
				        $list = this.$el.find( '#wpmoly-lang-select ul' ),
				      $parent = $selected.parent( 'li' )[0];
				
				$selected.addClass( 'selected' );
				$list.scrollTop( $parent.offsetTop - 42 );

				return this;
			},

			/**
			 * Close Search Language Selectlist
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Click Event
			 */
			closeLangSelect: function( event ) {

				if ( undefined !== event && 27 !== event.keyCode )
					return false;

				this.$el.find( '#wpmoly-lang-select' ).slideUp( 200 );

				return this;
			},

			/**
			 * Set Search language
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Click Event
			 */
			setSearchLang: function( event ) {

				event.preventDefault();

				var $target = $( event.currentTarget ),
				   language = $target.attr( 'data-lang' );
				
				this.model.set( { lang: language } );
			},

			/**
			 * Toggle Search Settings toolbar
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Click Event
			 */
			toggleSettings: function( event ) {

				event.preventDefault();
				event.stopPropagation();

				$( event.currentTarget.parentElement ).toggleClass( 'active' );

				editor.views.settings.toggle();
				$( "body" ).addClass( 'waitee' ).on( 'click', function() {
					editor.views.settings.toggle();
					$( "body.waitee" ).removeClass( 'waitee' ).off( 'click' );
				});
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

				if ( 'query' == type )
					type = 's';

				data[ type ] = value;

				this.model.set( data );
			},

			submit: function( event ) {

				if ( 13 !== event.keyCode )
					return event;

				this.search();
				event.preventDefault();
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

				if ( undefined !== event )
					event.preventDefault();

				if ( false !== this.locked )
					return;

				var query = this.$el.find( '#wpmoly-search-query' ).val(),
				     lang = this.$el.find( '#wpmoly-search-lang' ).val();
				if ( query != this.model.get( 's' ) )
					this.model.set( { s: query, type: 'title', lang: lang } );

				this.target.sync( 'search', this.model, {} );
			},

			/**
			 * Update the meta by running a new search using the
			 * existing TMDb ID
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Event
			 * 
			 * @return   void
			 */
			update: function( event ) {

				event.preventDefault();

				if ( false !== this.locked )
					return;

				var tmdb_id = this.target.get( 'tmdb_id' ),
				    imdb_id = this.target.get( 'imdb_id' ),
					id = tmdb_id || imdb_id;

				if ( undefined != id && '' != id ) {
					this.model.set( { s: id, type: 'id' }, { silent: true } );
					this.target.sync( 'search', this.model, {} );
				}
			},

			/**
			 * Update the view's query value when model changes
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Event
			 * 
			 * @return   void
			 */
			updateQuery: function( model ) {

				this.$el.find( '#wpmoly-search-query' ).val( model.changed.s )
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

				if ( false !== this.locked )
					return;

				if ( true !== confirm( wpmoly.l10n.movies.confirm_empty ) )
					return false;

				var attributes = _.clone( editor.models.movie.defaults );
				_.each( attributes, function( attr, i ) { attributes[ i ] = null; } );

				this.reset();
				editor.models.movie.clear();
				editor.models.movie.set( attributes );
				editor.models.movie.save();
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
				this.unlock();
			}
		}),

		/**
		 * WPMOLY Backbone Movie View
		 * 
		 * View for metabox movie metadata fields
		 * 
		 * @since    2.2
		 */
		Movie: Backbone.View.extend({

			el: '#wpmoly-movie-meta',

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

				this.template = _.template( this.$el.html() );
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
		}),

		/**
		 * WPMOLY Backbone Results View
		 * 
		 * View for movie search results collection.
		 * 
		 * @since    2.2
		 */
		Results: Backbone.View.extend({

			el: '#wpmoly-meta-search-results',

			events : {
				'click .wpmoly-select-movie a' : 'get',
				'click #wpmoly-empty-select-results' : '_reset',
				'click #wpmoly-meta-search-results-prev' : 'prev',
				'click #wpmoly-meta-search-results-next' : 'next',
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

				this.collection.on( 'change', this.render, this );
				this.collection.on( 'add', this.render, this );
				this.collection.on( 'reset', this.reset, this );
				this.listenTo( editor.models.movie, 'sync:done', this.close );
			},

			/**
			 * Render the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			render: function() {

				var results = this.template({
					results : this.collection.toJSON(),
					paginated: editor.models.search.get( 'paginate' ),
					page: editor.models.search.get( 'page' ),
					total: this.collection.pages
				});

				this.$el.slideDown( 400 );
				this.$el.html( results );

				if ( true === editor.models.search.get( 'paginate' ) )
					this.$el.addClass( 'paginated' );

				this.resize();

				return this;
			},

			/**
			 * Adapt movie results posters' size to show properly in
			 * the view.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			resize: function() {

				var container = document.getElementById( 'wpmoly-meta-search-results-container' );
				if ( _.isNull( container ) )
					return false;

				var fwidth = container.clientWidth,
				     width = 120;

				if ( this.$el.hasClass( 'paginated' ) )
					fwidth -= 80;
				else
					fwidth -= 80;

				if ( fwidth >= 800 )
					width = Math.round( fwidth / 5 );
				else if ( fwidth >= 500 )
					width = Math.round( fwidth / 4 );

				this.$el.find( '.wpmoly-select-movie' ).width( width );
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

				editor.models.search.set( { type: 'id' } );
				editor.models.search.set( { s: id }, { silent: true } );

				editor.models.movie.sync( 'search', this.model, {} );
			},

			/**
			 * Previous result page
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Click Event
			 * 
			 * @return   void
			 */
			prev: function( event ) {

				this.search( -1, event );
			},

			/**
			 * Next result page
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Click Event
			 * 
			 * @return   void
			 */
			next: function( event ) {

				this.search( 1, event );
			},

			/**
			 * Trigger the search after setting the new page value
			 * 
			 * @since    2.2
			 * 
			 * @param    int       Direction, next (1) ou prev (-1)
			 * @param    object    JS Click Event
			 * 
			 * @return   void
			 */
			search: function( i, event ) {

				var page = editor.models.search.get( 'page' );
				    page = page + i;

				if ( page <= 0 )
					page = 1;

				editor.models.search.set( { page: page } );
				this.$el.find( '#wpmoly-meta-search-results-loading' ).show();

				editor.views.search.unlock();
				editor.views.search.search( event );

				this.listenToOnce( editor.views.search, 'add', this.render );
			},

			/**
			 * Reset the results Collection
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Event
			 * 
			 * @return   void
			 */
			_reset: function( event ) {

				event.preventDefault();

				this.collection.reset();
				this.close();

				editor.views.search.unlock();
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
			},

			/**
			 * Close the results view
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			close: function() {

				this.$el.slideUp( 250 );
			}

		}),

		/**
		 * WPMOLY Backbone Preview View
		 * 
		 * View for movie metabox preview panel.
		 * 
		 * @since    2.2
		 */
		Preview: Backbone.View.extend({

			el: '#wpmoly-meta-preview-panel',

			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function () {
				
				_.bindAll( this, 'render' );

				this.template = _.template( this.$el.html() );
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

				var meta = model.changed;
				$( '#wpmoly-movie-preview-poster img' ).attr( 'src', meta.poster );
				delete meta.poster;

				_.each( meta, function( value, key ) {
					if ( _.isArray( value ) )
						value = value.join( ', ' );
					$( '#wpmoly-movie-preview-' + key ).text( value );
				} );

				$( '#wpmoly-movie-preview' ).removeClass( 'empty' );
				$( '#wpmoly-movie-preview-message' ).remove();
			},
		})

	});

})(jQuery);
