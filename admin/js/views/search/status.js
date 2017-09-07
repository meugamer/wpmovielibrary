wpmoly = window.wpmoly || {};

var Search = wpmoly.view.Search || {};

Search.Status = wp.Backbone.View.extend({

	className : 'wpmoly-search-status-container',

	template : wp.template( 'wpmoly-search-status' ),

	events : {
		'click .wpmoly-status-icon.rotate' : 'stopRotating',
		'click .wpmoly-status-icon'        : 'toggleHistory',
		'mouseenter .wpmoly-status-text'   : 'toggleHover',
		'mouseleave .wpmoly-status-text'   : 'toggleHover',
	},

	/**
	 * Initialize the View.
	 *
	 * @since    3.0
	 */
	initialize : function( options ) {

		var options = options || {};

		this.controller = options.controller;
		this.collection = options.controller.status;

		this.listenTo( this.collection, 'add', this.render );

		wpmoly.on( 'status:start', this.startAnimation, this );
		wpmoly.on( 'status:stop',  this.stopAnimation,  this );

		wpmoly.on( 'history:toggle', function() {
			this.$el.toggleClass( 'history-opened' );
		}, this );
	},

	/**
	 * Render the View.
	 *
	 * @since    3.0
	 */
	render : function() {

		var model = {};
		if ( this.collection.length ) {
			model = this.collection.first().toJSON();
		}

		this.$el.html( this.template( model ) );

		return this;
	},

	/**
	 * Start spinning the status icon.
	 *
	 * @since    3.0
	 */
	startAnimation : function( options ) {

		this.toggleEffect( true, options || {} );
	},

	/**
	 * Stop spinning the status icon.
	 *
	 * @since    3.0
	 */
	stopAnimation : function( options ) {

		this.toggleEffect( false, options || {} );
	},

	/**
	 * Spin the status icon.
	 *
	 * @since    3.0
	 *
	 * @param    boolean    toggle
	 */
	toggleEffect : function( toggle, options ) {

		var options = options || {};

		this.$( '.wpmoly-status-icon' ).toggleClass( options.effect || 'rotate', toggle );
	},

	/**
	 * Toggle status history.
	 *
	 * @since    3.0
	 */
	toggleHistory : function() {

		wpmoly.trigger( 'history:toggle' );
	},

	/**
	 * Toggle status text hover.
	 *
	 * Use JS events instead of pure CSS to limit hovering to
	 * real text overflow.
	 *
	 * @since    3.0
	 */
	toggleHover : function() {

		var $text = this.$( '.wpmoly-status-text' )
			text_el = $text[0];

		if ( $text.hasClass( 'expend' ) ) {
			$text.removeClass( 'expend' );
		} else if ( text_el.scrollWidth > $text.innerWidth() ) {
			$text.addClass( 'expend' );
		}
	},

});
