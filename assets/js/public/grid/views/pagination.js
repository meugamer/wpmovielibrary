
/**
 * Movie Grid Menu Pagination SubView
 * 
 * This View renders the Movie Grid Menu Pagination subview.
 * 
 * @since    2.1.5
 */
grid.view.PaginationMenu = wp.Backbone.View.extend({

	className: 'wpmoly-grid-menu',

	template: wp.template( 'wpmoly-grid-pagination' ),

	events: {
		'click a':                              'preventDefault',

		'click a[data-action="prev"]':          'prev',
		'click a[data-action="next"]':          'next',
		'change input[data-action="browse"]':   'browse',
		'keypress input[data-action="browse"]': 'browse',

		'click .grid-pagination-settings':      'stopPropagation'

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

		this.library = this.options.library;
		this.frame   = this.options.frame;

		this.$body = $( 'body' );

		this.library.props.on( 'change:paged', this.render, this );
		this.library.props.on( 'change:posts_per_page', this.render, this );

		this.frame.pages.on( 'change', this.render, this );
		this.frame.props.on( 'change:scroll', this.render, this );
	},

	/**
	 * Go to the previous results page.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Return itself to allow chaining
	 */
	prev: function() {

		this.library._prev();

		return this;
	},

	/**
	 * Go to the next results page.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Return itself to allow chaining
	 */
	next: function() {

		this.library._next();

		return this;
	},

	/**
	 * Go to a specific results page.
	 * 
	 * Handle multiple JS events: Click, Change and Keypress. Clicks concern 
	 * the pagination's 'previous' and 'next' links; Change handle modifications
	 * of the page input whilst Keypress handle an 'Enter' hit on the page
	 * input.
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Return itself to allow chaining
	 */
	browse: function( event ) {

		var $elem = this.$( event.currentTarget ),
		    value;

		if ( 'click' == event.type ) {
			if ( 'next' == value ) {
				this.library._next();
			} else if ( 'prev' == value ) {
				this.library._prev();
			} else {
				return this;
			}
		} else if ( 'change' == event.type || ( 'keypress' == event.type && 13 === ( event.charCode || event.keyCode ) ) ) {
			value = $elem.val() || 1 ;
			this.library._page( value );
		} else {
			return this;
		}

		return this;
	},

	/**
	 * Render the Menu
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		if ( false !== this.frame._scroll ) {
			this.$el.hide();
			return this;
		} else {
			this.$el.show();
		}

		var total = this.frame.pages.get( 'total' ),
		  options = {
			current: this.library.props.get( 'paged' ),
			total:   total,
			prev:    this.library.props.get( 'paged' ) - 1,
			next:    this.library.props.get( 'paged' ) + 1
		};

		this.$el.html( this.template( options ) );

		return this;
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
});
