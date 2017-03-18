
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

Grid.Pagination = wp.Backbone.View.extend({

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

		this.listenTo( this.controller.query.state, 'change:currentPage', this.render );
		this.listenTo( this.controller.query.state, 'change:totalPages',  this.render );
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

		var $target = this.$( event.currentTarget )
		      state = this.controller.query.state,
		      value = $target.val();

		if ( value < 1 || value == state.get( 'currentPage' ) || value > state.get( 'totalPages' ) ) {
			return false;
		}

		this.controller.query.set({ page : parseInt( value ) });

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
			this.controller.prev();
		} else if ( 'next' === value ) {
			this.controller.next();
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
			preview : this.controller.preview,
			state   : this.controller.query.state
		} ) );

		return this;
	}

});
