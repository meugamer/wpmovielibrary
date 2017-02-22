
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

Grid.Settings = wp.Backbone.View.extend({

	className : 'grid-settings-inner',

	template : wp.template( 'wpmoly-grid-settings' ),

	events: {
		'click [data-action="apply"]' : 'apply'
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

		this.listenTo( this.controller, 'grid:settings:open',  this.open );
		this.listenTo( this.controller, 'grid:settings:close', this.close );
	},

	/**
	 * Apply changed settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	apply: function() {

		var changes = {},
		     inputs = this.$( 'input:checked' ),
		      query = this.controller.query;

		// Loop through fields to detect changes from current state.
		_.each( inputs, function( input ) {
			var param = this.$( input ).attr( 'data-setting-type' ),
			    value = this.$( input ).val();

			if ( query.has( param ) && ( value != query.get( param ) /*|| value*/ ) ) {
				changes[ param ] = value;
			}
		}, this );

		// If order changed, go back to page 1.
		if ( changes.order || changes.orderby ) {
			changes.page = 1;
		}

		query.set( changes );

		this.toggle();

		return this;
	},

	/**
	 * Open the customs menu.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	open: function() {

		return this.toggle( true );
	},

	/**
	 * Close the customs menu.
	 * 
	 * @since    3.0
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	close: function() {

		return this.toggle( false );
	},

	/**
	 * Show/Hide the customs menu.
	 * 
	 * @since    3.0
	 * 
	 * @param    boolean    toggle
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	toggle: function( toggle ) {

		if ( true !== toggle ) {
			toggle = false;
		}

		this.$el.toggleClass( 'active', toggle );

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
			grid_id  : _.uniqueId( 'wpmoly-grid-' + this.controller.get( 'post_id' ) ),
			settings : this.controller.settings,
			query    : this.controller.query
		} ) );

		return this;
	}

});
