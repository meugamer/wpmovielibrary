
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new Builder instance.
	 *
	 * @since    3.0
	 *
	 * @return   {object}    Builder instance.
	 */
	var Builder = function() {

		var controller = new GridBuilder.controller.Builder({
			post_id : document.querySelector( '#post_ID' ).value,
			nonce   : document.querySelector( '#wpmoly_save_grid_setting_nonce' ).value,
		});

		var view = new GridBuilder.view.Builder({
			el         : document.querySelector( '#wpmoly-grid-builder' ),
			controller : controller
		});

		return this;
	};

	/**
	 * Create a new Grid instance for preview.
	 *
	 * @since    3.0
	 *
	 * @param    {Element}    grid Grid DOM element.
	 *
	 * @return   {object}     Grid instance.
	 */
	var Preview = function( grid ) {

		var preview = wpmoly.grids.get( parseInt( grid.getAttribute( 'data-grid' ) ) );
		if ( ! preview.length || ! _.isArray( preview ) ) {
			return false;
		}

		var grid = preview.shift();

		grid.set({
			enable_pagination : false,
			customs_control   : false,
			settings_control  : false
		});

		grid.refresh();
		grid.reload();

		return grid;
	};

	/**
	 * Grid Builder Wrapper.
	 *
	 * Store models, controllers, views, builder and preview objects.
	 *
	 * @since    3.0
	 */
	var GridBuilder = wpmoly.gridbuilder = {

		/**
		 * 
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		builder : null,

		/**
		 * 
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		preview : null,

		/**
		 * List of grid models.
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		model : {},

		/**
		 * List of grid controllers.
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		controller : {},

		/**
		 * List of grid views.
		 *
		 * @since    3.0
		 *
		 * @var      object
		 */
		view : {},

	};

	/**
	 * GridBuilder Builder Model.
	 *
	 * 
	 *
	 * @since    3.0
	 */
	GridBuilder.model.Builder = Backbone.Model.extend({

		defaults: function() {
			var defaults = {};
			_.each( _wpmolyGridBuilderData.settings, function( value, key ) {
				defaults[ key ] = '';
			}, this );
			return defaults;
		},

		/**
		 * Initialize the Model.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    attributes
		 * @param    {object}    options
		 */
		initialize: function( attributes, options ) {

			var options = options || {};
			this.controller = options.controller;

			var data = _wpmolyGridBuilderData || {};

			this.set( data.settings );
			this.types  = data.types  || '';
			this.modes  = data.modes  || '';
			this.themes = data.themes || '';

			this.on( 'change', this.update, this );
		},

		/**
		 * Save settings.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model
		 * @param    {object}    options
		 *
		 * @return   xhr
		 */
		update: function( model, options ) {

			if ( model.isEmpty() ) {
				return;
			}

			return wp.ajax.post( 'wpmoly_autosave_grid_setting', {
				data        : model.toJSON(),
				post_id     : this.controller.get( 'post_id' ),
				_ajax_nonce : this.controller.get( 'nonce' )
			} );
		},

		/**
		 * Restore default attributes.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		reset: function() {

			this.set( this.defaults(), { silent: true } );

			return this;
		}
	});

	/**
	 * GridBuilder Builder Controller.
	 *
	 * Apply changes to the Grid model and update preview.
	 *
	 * @since    3.0
	 */
	GridBuilder.controller.Builder = Backbone.Model.extend({

		/**
		 * Initialize the Model.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    attributes
		 * @param    {object}    options
		 */
		initialize: function( attributes, options ) {

			this.builder = new GridBuilder.model.Builder( {}, { controller: this } );

			this.listenTo( this.builder, 'change:type change:mode change:theme', this.updatePreview );
		},

		/**
		 * Update the grid preview when some settings change.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model
		 * @param    {object}    options
		 */
		updatePreview: function( model, options ) {

			if ( ! GridBuilder.preview ) {
				return;
			}

			GridBuilder.preview.set( model.changed );
		},

		/**
		 * Set grid type.
		 *
		 * @since    3.0
		 *
		 * @param    {string}    Grid type.
		 *
		 * @return   {object}
		 */
		setType: function( type ) {

			this.builder.reset();

			return this.builder.set({
				theme : 'default',
				mode  : 'grid',
				type  : type
			});
		},

		/**
		 * Set grid mode.
		 *
		 * @since    3.0
		 *
		 * @param    {string}    Grid mode.
		 *
		 * @return   {object}
		 */
		setMode: function( mode ) {

			return this.builder.set({
				theme : 'default',
				mode  : mode
			});
		},

		/**
		 * Set grid theme.
		 *
		 * @since    3.0
		 *
		 * @param    {string}    Grid theme.
		 *
		 * @return   {object}
		 */
		setTheme: function( theme ) {

			return this.builder.set({
				theme : theme
			});
		}
	});

	/**
	 * GridBuilder Builder View.
	 *
	 * Wrapper for ButterBean meta boxes. Detect changes in the meta boxes
	 * to update the grid settings accordingly and show/hide the meta boxes
	 * depending on grid type.
	 *
	 * @since    3.0
	 */
	GridBuilder.view.Builder = wp.Backbone.View.extend({

		events: {
			'click #wpmoly-grid-builder-preview a' : 'preventDefault',
			'change .butterbean-control input'     : 'onChange',
			'change .butterbean-control select'    : 'onChange',
			'change .butterbean-control textarea'  : 'onChange',
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.model = this.controller.builder;

			this.setRegions();
			this.bindEvents();
		},

		/**
		 * Set Regions (subviews).
		 * 
		 * @since    3.0
		 * 
		 * @return   Returns itself to allow chaining
		 */
		setRegions: function() {

			this.parameters = new GridBuilder.view.Parameters({ controller: this.controller });

			this.views.set( '#wpmoly-grid-builder-parameters-metabox', this.parameters );

			this.togglePostbox( this.model, this.model.get( 'type' ) );

			return this;
		},

		/**
		 * Bind events.
		 *
		 * @since    3.0
		 */
		bindEvents: function() {

			this.listenTo( this.model, 'change:type',  this.togglePostbox );
			this.listenTo( this.model, 'change:mode',  this.togglePostbox );
			this.listenTo( this.model, 'change:theme', this.togglePostbox );
		},

		/**
		 * Show/Hide ButterBean Metaboxes depending on grid type.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model Model
		 * @param    {mixed}     value Changed value
		 * @param    {object}    options Options
		 */
		togglePostbox: function( model, value, options ) {

			var $postbox = this.$( '#butterbean-ui-' + value + '-grid-settings' ),
			    $postboxes = this.$( '.butterbean-ui.postbox' );
			if ( ! $postbox.length ) {
				return;
			}

			$postboxes.removeClass( 'active' );
			$postbox.addClass( 'active' );
		},

		/**
		 * Handle setting change events.
		 * 
		 * @since    3.0
		 * 
		 * @param    {object}    JS 'change' Event
		 */
		onChange : function( e ) {

			var value,
			    $control = this.$( e.target ).parents( '.butterbean-control' );

			switch ( e.target.type ) {
				case 'text':
				case 'textarea':
				case 'radio':
					value = e.target.value;
					break;
				case 'checkbox':
					value = e.target.checked ? '1' : '0';
					break;
				case 'checkboxes':
					var value = [],
					    $elems = this.$( e.target ).find( 'input:checked' );
					_.each( $elems, function( elem ) {
						value.push( elem.value );
					} );
					break;
				case 'select-one':
				case 'select-multiple':
					var value = [],
					    $elems = this.$( e.target ).find( 'option:selected' );
					_.each( $elems, function( elem ) {
						value.push( elem.value );
					} );
					break;
				default:
					break;
			}

			if ( _.isEmpty( value ) ) {
				return;
			}

			var name = $control.prop( 'id' ).replace( 'butterbean-control-_wpmoly_grid_', '' );

			this.model.set( name, value );
		},

		/**
		 * Disable click on preview links.
		 *
		 * @since    3.0
		 */
		preventDefault : function( event ) {

			event.preventDefault();
		},

	});

	/**
	 * GridBuilder Parameters View.
	 *
	 * Handle the grid parameters: type, mode, theme.
	 *
	 * @since    3.0
	 */
	GridBuilder.view.Parameters = wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-grid-builder-parameters-metabox' ),

		events: {
			'click [data-action="grid-type"]'  : 'setType',
			'click [data-action="grid-mode"]'  : 'setMode',
			'click [data-action="grid-theme"]' : 'setTheme'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.model = this.controller.builder;

			this.bindEvents();

			this.on( 'prepare', this.toggle );
		},

		/**
		 * Bind events.
		 *
		 * @since    3.0
		 */
		bindEvents: function() {

			this.listenTo( this.model, 'change:type',  this.render );
			this.listenTo( this.model, 'change:mode',  this.render );
			this.listenTo( this.model, 'change:theme', this.render );

			wpmoly.$( '[data-action="customize-grid"]' ).on( 'click', this.toggle );
		},

		/**
		 * Toggle the Metabox.
		 *
		 * If a 'click' event is passed, trigger the default WP Metabox
		 * toggle process.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    event JS 'click' event
		 */
		toggle: function( event ) {

			if ( event.originalEvent ) {
				wpmoly.$( '#wpmoly-grid-parameters-metabox .handlediv' ).trigger( 'click' );
			}

			var closed = wpmoly.$( '#wpmoly-grid-parameters-metabox' ).hasClass( 'closed' );

			wpmoly.$( '#customize-grid' ).toggleClass( 'active', ! closed );
		},

		/**
		 * Set grid type.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    JS 'click' Event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setType: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setType( value );

			return this;
		},

		/**
		 * Set grid mode.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    JS 'click' Event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setMode: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setMode( value );

			return this;
		},

		/**
		 * Set grid theme.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    JS 'click' Event.
		 *
		 * @return   Returns itself to allow chaining.
		 */
		setTheme: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setTheme( value );

			return this;
		},

		/**
		 * Prepare the View rendering options.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    JS 'click' Event.
		 *
		 * @return   {object}
		 */
		prepare : function() {

			var options = _.extend( this.model.toJSON(), {
				types  : this.model.types,
				modes  : this.model.modes,
				themes : this.model.themes
			} );

			return options;
		},

	});

	/**
	 * Create controllers.
	 *
	 * This should be called after the REST API Backbone client has
	 * been loaded.
	 *
	 * @see wp.api.loadPromise.done()
	 *
	 * @since    3.0
	 *
	 * @return   {object}
	 */
	GridBuilder.load = function() {

		GridBuilder.builder = new Builder();

		GridBuilder.preview = new Preview( document.querySelector( '[data-grid]' ) );

		return this;
	};

	/**
	 * Run Forrest, run!
	 *
	 * Load the REST API Backbone client before loading all
	 * controllers.
	 *
	 * @see wp.api.loadPromise.done()
	 *
	 * @since    3.0
	 */
	GridBuilder.run = function() {

		if ( ! wp.api ) {
			return wpmoly.error( 'missing-api', wpmolyL10n.api.missing );
		}

		wp.api.loadPromise.done( GridBuilder.load );
	};

})( jQuery, _, Backbone );

wpmoly.runners.push( wpmoly.gridbuilder );
