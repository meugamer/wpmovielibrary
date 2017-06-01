
wpmoly = window.wpmoly || {};

wpmoly.view.GridPagination = wp.Backbone.View.extend({

	className: 'grid-menu-inner',

	template: wp.template( 'wpmoly-grid-pagination' ),

	events: {
		'change [data-action="grid-paginate"]' : 'paginate',
		'click [data-action="grid-navigate"]'  : 'navigate'
	},

	/**
	 * Initialize the View.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    options
	 * 
	 * @return   void
	 */
	initialize: function( options ) {

		this.controller = options.controller || {};

		this.listenToOnce( this.controller, 'ready', this.ready );

		this.listenTo( this.controller.query.state, 'change:currentPage', this.render );
		this.listenTo( this.controller.query.state, 'change:totalPages',  this.render );
	},

	ready : function() {

	},

	/**
	 * Jump to a precise page.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    JS 'change' Event.
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	paginate: function( event ) {

		var $target = this.$( event.currentTarget ),
		      value = $target.val();

		if ( ! this.controller.isBrowsable( value ) ) {
			return false;
		}

		this.controller.setCurrentPage( value );

		return this;
	},

	/**
	 * Navigate through the Grid's pages.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    JS 'click' Event.
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	navigate: function( event ) {

		var $target = this.$( event.currentTarget ),
		      value = $target.attr( 'data-value' );

		if ( 'prev' === value ) {
			this.controller.previousPage();
		} else if ( 'next' === value ) {
			this.controller.nextPage();
		}

		return this;
	},

	/**
	 * Render the View.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		this.$el.html( this.template( {
			current : this.controller.getCurrentPage(),
			total   : this.controller.getTotalPages()
		} ) );

		return this;
	}

});
