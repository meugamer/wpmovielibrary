
wpmoly = window.wpmoly || {};

(function( $, _, Backbone ) {

	/**
	 * Create a new Builder instance.
	 *
	 * @since    3.0
	 *
	 * @return   {object}    Builder instance.
	 */
	Builder = function() {

		var post_id = document.querySelector( '#post_ID' ).value;

		var builder = new wp.api.models.Grid( { id : post_id } );

		var controller = new GridBuilder.controller.Builder({
			post_id : post_id,
		}, {
			builder : builder,
		});

		builder.once( 'change', function( e ) {
			var view = new GridBuilder.view.Builder({
				el         : document.querySelector( '#wpmoly-grid-builder' ),
				controller : controller
			});
		} );

		var builder = {

			post_id : post_id,

			controller : controller,

		};

		return builder;
	};

	/**
	 * Create a new Tutorial instance.
	 *
	 * @since    3.0
	 *
	 * @return   {object}    Builder instance.
	 */
	Tutorial = function() {

		var tutorial = document.querySelector( '[data-tutorial-grid]' ),
		     grid_id = tutorial.getAttribute( 'data-tutorial-grid' );

		if ( ! tutorial ) {
			return;
		}

		var $tutorial = wpmoly.$( tutorial ),
		     $preview = wpmoly.$( '<div class="wpmoly grid" data-grid="' + grid_id + '"></div>' );

		// Add preview grid to DOM.
		$tutorial.after( $preview );

		GridBuilder.preview = new Grid( $preview[0], { context : 'edit' } );

		var tutorial = {

			remove : function() {

				$tutorial.remove();
				$preview.show();
			},

		};

		return tutorial;
	};

	/**
	 * Create a new Grid Preview instance.
	 *
	 * @since    3.0
	 *
	 * @return   {object}    Builder instance.
	 */
	Preview = function() {

		var grid = document.querySelector( '[data-preview-grid]' );
		if ( ! grid ) {
			return;
		}

		grid.setAttribute( 'data-grid', grid.getAttribute( 'data-preview-grid' ) );
		grid.removeAttribute( 'data-preview-grid' );

		return new Grid( grid, { context : 'edit' } );
	};

	/**
	 * Grid Builder Wrapper.
	 *
	 * Store models, controllers, views, builder and preview objects.
	 *
	 * @since    3.0
	 */
	GridBuilder = wpmoly.gridbuilder = {

		$el : wpmoly.$( '#wpmoly-grid-builder-preview' ),

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

	GridBuilder.model.Builder = Backbone.Model.extend({

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
		},

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

			var options = options || {};

			this.builder = options.builder;
			this.listenTo( this.builder, 'change:meta', this.updateModel );

			this.model = new GridBuilder.model.Builder;
			this.listenTo( this.model, 'change', this.updatePreview );

			this.builder.fetch( { data : { context : 'edit' } } );
		},

		/**
		 * Update the model to match settings change.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model Model instance.
		 * @param    {mixed}     value Changed value(s).
		 * @param    {object}    options Options.
		 *
		 * @return   {object}
		 */
		updateModel : function( model, value, options ) {

			return this.model.set( value );
		},

		/**
		 * Update the grid preview when some settings change.
		 *
		 * @since    3.0
		 *
		 * @param    {object}    model
		 */
		updatePreview: function( model ) {

			// Hide Tutorial, if needed.
			if ( GridBuilder.tutorial ) {
				if ( ! _.isEmpty( model.get( 'type' ) ) ) {
					GridBuilder.tutorial.remove();
				}
			}

			// Update preview.
			if ( GridBuilder.preview ) {
				GridBuilder.preview.set( model.changed );
			}
		},

		/**
		 * Set grid type.
		 *
		 * @since    3.0
		 *
		 * @param    {string}    type Grid type.
		 *
		 * @return   {object}
		 */
		setType: function( type ) {

			return this.model.set({
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
		 * @param    {string}    mode Grid mode.
		 *
		 * @return   {object}
		 */
		setMode: function( mode ) {

			return this.model.set({
				theme : 'default',
				mode  : mode
			});
		},

		/**
		 * Set grid theme.
		 *
		 * @since    3.0
		 *
		 * @param    {string}    theme Grid theme.
		 *
		 * @return   {object}
		 */
		setTheme: function( theme ) {

			return this.model.set({
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
			this.model = this.controller.model;

			this.setRegions();
			this.bindEvents();

			this.preFill();
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
		},

		/**
		 * Update ButterBean Meta Box values.
		 *
		 * Avoid leaving empty fields, which is default ButterBean behaviour.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		preFill: function() {

			var type = this.model.get( 'type' ),
			$postbox = this.$( '#butterbean-ui-' + type + '-grid-settings' );

			_.each( this.model.attributes, function( value, key ) {
				var $field = $postbox.find( '[name="butterbean_' + type + '-grid-settings_setting__wpmoly_grid_' + key + '"]' );
				if ( $field.length ) {
					var $control = $field.parents( '.butterbean-control' );
					if ( $control.hasClass( 'butterbean-control-radio-image' ) ) {
						$control.find( 'input[value="' + value + '"]' ).prop( 'checked', true );
					} else if ( $control.hasClass( 'butterbean-control-checkbox' ) ) {
						$control.find( 'input[type="checkbox"]' ).prop( 'checked', true );
					} else if ( $control.hasClass( 'butterbean-control-text' ) ) {
						$control.find( 'input[type="text"]' ).val( value );
					}
				}
			}, this );

			return this;
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

			/*if ( _.isUndefined( value ) ) {
				return false;
			}*/

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

	GridBuilder.view.Tutorial = wp.Backbone.View.extend({

		className : 'grid-tutorial',

		template : wp.template( 'wpmoly-grid-builder-tutorial' ),

	});

	/**
	 * GridBuilder Parameters View.
	 *
	 * Handle the grid parameters: type, mode, theme.
	 *
	 * @since    3.0
	 */
	GridBuilder.view.Parameters = wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-grid-builder-parameters' ),

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

			this.bindEvents();

			this.on( 'prepare', this.toggle );
		},

		/**
		 * Bind events.
		 *
		 * @since    3.0
		 */
		bindEvents: function() {

			this.listenTo( this.controller.model, 'change:type',  this.render );
			this.listenTo( this.controller.model, 'change:mode',  this.render );
			this.listenTo( this.controller.model, 'change:theme', this.render );

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

			var options = _.extend(
				_.pick( this.controller.builder.toJSON(), 'support' ) || {}, {
					meta : _.pick( this.controller.model.toJSON(), 'type', 'mode', 'theme' ) || {},
				}
			);

			return options;
		},

	});

	/**
	 * Create tutorial and preview.
	 *
	 * @since    3.0
	 */
	GridBuilder.loadPreview = function() {

		if ( document.querySelector( '[data-tutorial-grid]' ) ) {
			GridBuilder.tutorial = new Tutorial();
		}

		if ( document.querySelector( '[data-preview-grid]' ) ) {
			GridBuilder.preview = new Preview();
		}
	};

	/**
	 * Create grid builder instance.
	 *
	 * @since    3.0
	 */
	GridBuilder.loadBuilder = function() {

		GridBuilder.builder = new Builder();
	};

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

		GridBuilder.loadPreview();
		GridBuilder.loadBuilder();

		return GridBuilder;
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
