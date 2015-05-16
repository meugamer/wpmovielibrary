
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
		'click a':                               'preventDefault',

		'click a[data-action="openmenu"]':       'toggleSubMenu',
		'click a[data-action="opensettings"]':   'toggleSettings',
		'click a[data-action="applysettings"]':  'updateSettings',

		'click a[data-action="scroll"]':         'setScroll',

		/*'click a[data-action="orderby"]':      'orderby',
		'click a[data-action="order"]':          'order',
		'click a[data-action="filter"]':         'filter',
		'click a[data-action="view"]':           'view',*/

		'click .wpmoly-grid-settings-container': 'stopPropagation',
		'click .grid-menu-settings':             'stopPropagation',
	},

	settings: {
		orderby: [ 'post_title', 'post_date', 'release_date', 'rating' ],
		order:   [ 'asc', 'desc', 'random' ],
		include: {
			incoming: 1,
			unrated:  1
		},
		display: {
			title:   1,
			genres:  0,
			rating:  1,
			runtime: 1,
		}
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

		this.frame   = this.options.frame;
		this.model   = this.options.model;
		this.library = this.options.library;

		this.$window = $( window );
		this.$body   = $( document.body );

		// Throttle the scroll handler and bind this.
		this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

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
	scroll: function( event ) {

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
	},

	/**
	 * Open or close the submenu related to the menu link clicked
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	toggleSubMenu: function( event ) {

		var $elem = this.$( event.currentTarget ),
		 $submenu = this.$( '.wpmoly-grid-settings-container' );

		// Close submenu if needed
		if ( this.$el.hasClass( 'mode-content' ) ) {
			this.$el.removeClass( 'mode-content mode-settings' );
			$elem.removeClass( 'active' );
			return false;
		}

		// Open current submenu
		this.$el.removeClass( 'mode-content mode-settings' ).addClass( 'mode-content' );
		$elem.addClass( 'active' );

		event.stopPropagation();

		if ( this.$body.hasClass( 'waitee' ) ) {
			return;
		}

		// Close the submenu when clicking elsewhere
		var self = this;
		this.$body.addClass( 'waitee' ).one( 'click', function() {
			$elem.removeClass( 'active' );
			self.$el.removeClass( 'mode-content' );
			self.$body.removeClass( 'waitee' );
		});

	},

	/**
	 * Open or close the setting menu
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	toggleSettings: function( event ) {

		var $elem = this.$( event.currentTarget ),
		 $submenu = this.$( '.wpmoly-grid-settings-container' );

		// Close submenu if needed
		if ( this.$el.hasClass( 'mode-settings' ) ) {
			this.$el.removeClass( 'mode-content mode-settings' );
			$elem.removeClass( 'active' );
			return false;
		}

		// Open current submenu
		this.$el.removeClass( 'mode-content mode-settings' ).addClass( 'mode-settings' );
		$elem.addClass( 'active' );

		event.stopPropagation();

		if ( this.$body.hasClass( 'waitee' ) ) {
			return;
		}

		// Close the submenu when clicking elsewhere
		var self = this;
		this.$body.addClass( 'waitee' ).one( 'click', function() {
			$elem.removeClass( 'active' );
			self.$el.removeClass( 'mode-settings' );
			self.$body.removeClass( 'waitee' );
		});
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
	updateSettings: function() {

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
	},

	/**
	 * Change the scroll settings from the settings menu
	 * 
	 * @since    2.1.5
	 *
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	setScroll: function( event ) {

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
	},

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

		if ( ! _.contains( this.defaults.orderby, value ) ) {
			return;
		}

		this.library.props.set({ orderby: value });

		this.$( 'a[data-action="orderby"].active' ).removeClass( 'active' );
		$elem.closest( 'a[data-action="orderby"]' ).addClass( 'active' );
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

		if ( ! _.contains( this.defaults.order, value ) ) {
			return;
		}

		this.library.props.set({ order: value.toUpperCase() });

		this.$( 'a[data-action="order"].active' ).removeClass( 'active' );
		$elem.closest( 'a[data-action="order"]' ).addClass( 'active' );
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
	filter: function( event ) {

		var $elem = this.$( event.currentTarget );
		console.log( 'filter!' );
	},

	/**
	 * Handle viewing change menu
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    JS 'Click' Event
	 * 
	 * @return   void
	 */
	view: function( event ) {

		var $elem = this.$( event.currentTarget );
		console.log( 'view!' );
	},

	/**
	 * Render the Menu
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		var options = {
			scroll:  this.frame._scroll,
			mode:    this.frame.mode(),
			orderby: this.library.props.get( 'orderby' ),
			order:   this.library.props.get( 'order' )
		};
		this.$el.html( this.template( options ) );

		this.views.render();

		return this;
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
