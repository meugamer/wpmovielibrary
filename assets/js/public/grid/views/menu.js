
/**
 * Movie Grid Menu View
 * 
 * This View renders the Movie Grid Menu.
 * 
 * @since    2.1.5
 */
grid.view.Menu = wp.Backbone.View.extend({

	className: 'wpmoly-grid-menu',

	template: wp.template( 'wpmoly-grid-menu' ),

	events: {
		'click a':                                'preventDefault',

		'click a[data-action="openmenu"]':        'toggle_menu',

		'click a[data-action="scroll"]':          'setScroll',

		'click a[data-action="orderby"]':         'orderby',
		'click a[data-action="order"]':           'order',
		'click a[data-action="filter"]':          'filter',
		//'click a[data-action="view"]':            'view',

		'click a[data-action="apply-settings"]':  'apply',
		'click a[data-action="cancel-settings"]': 'cancel',
		'click a[data-action="reload-settings"]': 'reload',

		'click .wpmoly-grid-settings-container':  'stopPropagation',
		'click .grid-menu-settings':              'stopPropagation',
	},

	icons: {
		yes: 'icon-yes-alt',
		no:  'icon-no-alt-2'
	},

	/**
	 * Initialize the View
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    Attributes
	 * 
	 * @return   void
	 */
	initialize: function( options ) {

		_.defaults( this.options, {
			refreshSensitivity: hasTouch ? 300 : 200
		} );

		this._mode   = '';

		this.frame    = this.options.frame;
		this.model    = this.options.model;
		this.library  = this.options.library;

		this.controller = this.frame.controller;
		console.log( this.controller );

		this.$window = $( window );
		this.$body   = $( document.body );
		this.$waitee = $( 'body.waitee' );

		// Throttle the scroll handler and bind this.
		//this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

		//this.$window.on( 'scroll', this.scroll );
	},

	/**
	 * Handle infinite scroll into the current View
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	/*scroll: function( event ) {

		var   $el = this.$el.parent( '.grid-frame-menu' ),
		   $frame = this.frame.$el,
		scrollTop = this.$window.scrollTop();

		if ( ! $frame.hasClass( 'menu-fixed' ) && $frame.offset().top <= ( scrollTop + 48 ) ) {
			$frame.addClass( 'menu-fixed' );
			$el.css({ width: $frame.width() });
		} else if ( $frame.hasClass( 'menu-fixed' ) && $frame.offset().top > ( scrollTop + 48 ) ) {
			$frame.removeClass( 'menu-fixed' );
			$el.css({ width: '' });
		}
	},*/

	/**
	 * Open or close the submenu related to the menu link clicked
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	toggle_menu: function( event ) {

		var $elem = this.$( event.currentTarget ),
		 $submenu = this.$( '.wpmoly-grid-settings-container' ),
		     mode = $elem.attr( 'data-value' );

		if ( ! this.$el.hasClass( 'open' ) ) {
			$elem.addClass( 'active' );
			this.open( mode );
		} else {
			if ( 'settings' == mode && this.$el.hasClass( 'mode-content' ) ) {
				this.$el.removeClass( 'mode-content' ).addClass( 'mode-settings' );
			} else if ( 'content' == mode && this.$el.hasClass( 'mode-settings' ) ) {
				this.$el.removeClass( 'mode-settings' ).addClass( 'mode-content' );
			} else {
				$elem.removeClass( 'active' );
				this.close();
			}
		}

		event.stopPropagation();
	},

	/**
	 * Open the submenu and set its mode
	 * 
	 * @since    2.1.5
	 *
	 * @param    string    Submenu mode, 'content' of 'settings'
	 * 
	 * @return   void
	 */
	open: function( mode ) {

		this.mode( mode );

		if ( this.$body.hasClass( 'waitee' ) ) {
			return;
		}

		// Close the submenu when clicking elsewhere
		var self = this;
		this.$body.addClass( 'waitee' ).one( 'click', function() {
			self.close();
		});
	},

	mode: function( mode ) {

		this._mode = mode;
		this.render();
	},

	/**
	 * Close the submenu
	 * 
	 * @since    2.1.5
	 * 
	 * @return   void
	 */
	close: function() {

		this.$el.removeClass( 'mode-content mode-settings open' );
		this.$( '.grid-menu-action.active' ).removeClass( 'active' );
		this.$waitee.unbind( 'click' );
		this.$waitee.removeClass( 'waitee' );
	},

	/**
	 * Update grid settings
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	/*updateSettings: function() {

		var scroll = Boolean( parseInt( this.$( 'input[data-value="scroll"]' ).val() ) || 0 ),
		   perpage = parseInt( this.$( 'input[data-action="perpage"]' ).val() ) || 0,
		     props = {},
		  defaults = {
			perpage: this.library.props.get( 'posts_per_page' ),
			scroll:  this.frame._scroll
		   };

		if ( scroll !== defaults.scroll ) {
			this.frame.props.set({ scroll: scroll });
		}

		if ( perpage && perpage !== defaults.perpage ) {
			this.library.props.set({ posts_per_page: perpage });
		}

		this.$el.removeClass( 'settings' );
	},*/

	/**
	 * Change the scroll settings from the settings menu
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	/*setScroll: function( event ) {

		var $elem = this.$( event.currentTarget ),
		   $input = this.$( 'input[data-value="scroll"]' ),
		   scroll = parseInt( $elem.attr( 'data-value' ) );

		if ( 1 === scroll ) {
			$input.val( 1 );
		} else {
			$input.val( 0 );
		}

		this.$( 'a[data-action="scroll"].selected' ).removeClass( 'selected' );
		$elem.addClass( 'selected' );
	},*/

	/**
	 * Handle ordering change menu (orderby)
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	orderby: function( event ) {

		var $elem = this.$( event.currentTarget ),
		    value = $elem.attr( 'data-value' );

		if ( ! _.contains( this.controller.orderby, value ) ) {
			return;
		}

		this.controller.set( { orderby: value, paged: 1 } );
		this.render();
	},

	/**
	 * Handle ordering change menu (order)
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	order: function( event ) {

		var $elem = this.$( event.currentTarget ),
		    value = $elem.attr( 'data-value' );

		if ( ! _.contains( this.controller.order, value ) ) {
			return;
		}

		this.controller.set( { order: value.toUpperCase(), paged: 1 } );
		this.render();
	},

	/**
	 * Handle filtering change menu
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	/*filter: function( event ) {

		var $elem = this.$( event.currentTarget ),
		    value = $elem.attr( 'data-value' ),
		   filter = ! this.settings.include[ value ];

		this.settings.include[ value ] = filter;

		this.library.props.set( { filter: this.settings.include }, { silent: true } );
		this.render();
	},*/

	/**
	 * Handle viewing change menu
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	/*view: function( event ) {

		var $elem = this.$( event.currentTarget );
		console.log( 'view!' );
	},*/

	/**
	 * Render the Menu
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		var options = {
			mode:    this._mode,
			scroll:  this.frame._scroll,
			view:    this.frame.mode(),
			orderby: this.controller.get( 'orderby' ),
			order:   this.controller.get( 'order' ),
			include: false,//this.settings.include,
			display: false//this.settings.display
		};
		this.$el.html( this.template( options ) );

		if ( ! _.isEmpty( this._mode ) ) {
			this.$el.addClass( 'open mode-' + options.mode );
		}

		this.views.render();

		return this;
	},

	/**
	 * Apply the settings.
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @since    2.1.5
	 */
	apply: function( event ) {

		this.controller.update();
		this.close();
	},

	/**
	 * Cancel the settings.
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @since    2.1.5
	 */
	cancel: function( event ) {

		this.close();
	},

	/**
	 * Reload the grid with default settings.
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @since    2.1.5
	 */
	reload: function() {

		this.controller.reset();
		this.close();
	},

	/**
	 * Prevent click events default effect
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @since    2.1.5
	 */
	switchView: function( event ) {

		var mode = event.currentTarget.dataset.mode;
		this.frame.mode( mode );
	},

	/**
	 * Prevent click events default effect
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @since    2.1.5
	 */
	preventDefault: function( event ) {

		event.preventDefault();
	},

	/**
	 * Stop Click Event Propagation
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @since    2.1.5
	 */
	stopPropagation: function( event ) {

		event.stopPropagation();
	}

});
