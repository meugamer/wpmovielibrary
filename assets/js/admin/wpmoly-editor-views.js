
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
			initialize: function( options ) {

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
				    data = {};

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

			events: {
				"click #wpmoly-search": "search",
				"click #wpmoly-update": "update",
				"click #wpmoly-empty": "empty",
				"click #wpmoly-lang": "toggleLangSelect",
				"click .wpmoly-lang-selector": "setSearchLang",
				"click #wpmoly-search-settings": "toggleSettings",
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

				var template = this.$el.html();
				if ( undefined === template )
					return false;

				this.template = _.template( template );
				this.render();

				this.model.on( 'change:s', this.updateQuery, this );
				this.model.on( 'change:lang', this.toggleLangSelect, this );
				this.target.on( 'sync:done', this.reset, this );
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
				   inputwidth = ( formwidth - ( lang + search + 12 ) );

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
			toggleLangSelect: function( event ) {

				if ( undefined !== event.preventDefault )
					event.preventDefault();

				this.$el.find( '#wpmoly-lang' ).attr( 'data-lang', this.model.get( 'lang' ) );
				this.$el.find( '#wpmoly-lang-select' ).toggle();
				this.$el.find( '#wpmoly-lang-select a.selected' ).removeClass( 'selected' );
				
				var $selected = this.$el.find( '#wpmoly-lang-select a[data-lang="' + this.model.get( 'lang' ) + '"]' ),
				        $list = this.$el.find( '#wpmoly-lang-select ul' ),
				      $parent = $selected.parent( 'li' )[0];
				
				$selected.addClass( 'selected' );
				$list.scrollTop( $parent.offsetTop - 42 );
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

				$( event.currentTarget.parentElement ).toggleClass( 'active' );
				editor.views.settings.toggle();
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

				var query = this.$el.find( '#wpmoly-search-query' ).val(),
				     lang = this.$el.find( '#wpmoly-search-lang' ).val();
				if ( query != this.model.get( 's' ) )
					this.model.set( { s: query, type: 'title', lang: lang } );

				this.target.sync( 'search', this.model, {} );
			},

			/**
			 * 
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Event
			 * 
			 * @return   void
			 */
			update: function( event ) {

				event.preventDefault();

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
				console.log( 'empty', event.currentTarget );
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

				var template = $( this.el ).html();
				if ( undefined === template )
					return this;

				this.template = _.template( template );
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
				'click #wpmoly-empty-select-results' : '_reset'
			},

			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function() {

				var template = $( '#wpmoly-search-results-template' ).html();
				if ( undefined === template )
					return false;

				this.template = _.template( template );

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

				this.$el.slideDown( 400 );
				this.$el.html( results );

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

				editor.models.search.set( { type: 'id' } );
				editor.models.search.set( { s: id }, { silent: true } );

				editor.models.movie.sync( 'search', this.model, {} );
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
			},

			/**
			 * Reset the results view
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			reset: function() {

				this.$el.slideUp( 250 );
				this.$el.empty();
			},

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
		}),

		/**
		 * WPMOLY Backbone Panel View
		 * 
		 * View for movie metabox panels.
		 * 
		 * @since    2.2
		 */
		Panel: Backbone.View.extend({

			el: '#wpmoly-meta',

			meta: '#wpmoly-meta',
			menu: '#wpmoly-meta-menu',
			status: '#wpmoly-meta-status',
			panels: '#wpmoly-meta-panels',

			events: {
				"click #wpmoly-meta-menu a.navigate": "navigate",
				"click #wpmoly-meta-menu .tab.off a": "resize"
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

				var template = $( this.el ).html();
				if ( undefined === template )
					return false;

				this.template = _.template( template );
				this.render();

				if ( window.innerWidth < 1180 )
					this.resize();

				$( this.panels ).css( { minHeight: $( $( this.menu ) ).height() } );

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
				meta_t = $meta.offset().top - 32,
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
					$( this.status ).css( { top: top } );
				}
				else if ( menu_t ) {
					$( this.menu ).css( { top: 0 } );
					$( this.status ).css( { top: 0 } );
				}
			},

			/**
			 * Resize Metabox Panel
			 *
			 * @since 2.0
			 */
			resize: function( event ) {
				if ( undefined !== event )
					event.preventDefault();
				$( this.el ).toggleClass( 'small' );
			}
		})

	});

	// To infinity... And beyond!
	wpmoly.editor();

})(jQuery);
