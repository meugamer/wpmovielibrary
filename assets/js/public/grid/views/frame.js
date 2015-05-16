
/**
 * WPMOLY Admin Movie Grid View
 * 
 * This View renders the Admin Movie Grid.
 * 
 * @since    2.1.5
 */
grid.view.Frame = media.View.extend({

	_mode: 'grid',

	/**
	 * Initialize the View
	 * 
	 * @since    2.1.5
	 * 
	 * @param    object    Attributes
	 * 
	 * @return   void
	 */
	initialize: function() {

		this._createRegions();
		this._createStates();
	},

	/**
	 * Create the frame's regions.
	 * 
	 * @since    2.1.5
	 */
	_createRegions: function() {

		// Clone the regions array.
		this.regions = this.regions ? this.regions.slice() : [];

		// Initialize regions.
		_.each( this.regions, function( region ) {
			this[ region ] = new media.controller.Region({
				view:     this,
				id:       region,
				selector: '.grid-frame-' + region
			});
		}, this );
	},

	/**
	 * Create the frame's states.
	 * 
	 * @since    2.1.5
	 */
	_createStates: function() {

		// Create the default `states` collection.
		this.states = new Backbone.Collection( null, {
			model: media.controller.State
		});

		// Ensure states have a reference to the frame.
		this.states.on( 'add', function( model ) {
			model.frame = this;
			model.trigger( 'ready' );
		}, this );

		if ( this.options.states ) {
			this.states.add( this.options.states );
		}
	},

	/**
	 * Render the View.
	 * 
	 * @since    2.1.5
	 */
	render: function() {

		// Activate the default state if no active state exists.
		if ( ! this.state() && this.options.state ) {
			this.setState( this.options.state );
		}

		return media.View.prototype.render.apply( this, arguments );
	}

});

// Make the `Frame` a `StateMachine`.
_.extend( grid.view.Frame.prototype, media.controller.StateMachine.prototype );
