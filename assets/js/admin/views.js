
wpmoly.View = wp.Backbone.View;

wpmoly.view.Frame = wp.media.view.Frame.extend({

	initialize: function() {

		_.defaults( this.options, {
			mode: [ 'select' ],
			slug:   'media'
		});

		this._createRegions();
		this._createStates();
		this._createModes();
	},

	_createRegions: function() {

		// Clone the regions array.
		this.regions = this.regions ? this.regions.slice() : [];

		var slug = this.options.slug;

		// Initialize regions.
		_.each( this.regions, function( region ) {
			this[ region ] = new wp.media.controller.Region({
				view:     this,
				id:       region,
				selector: '.' + slug + '-frame-' + region
			});
		}, this );
	},
});

// Make the `Frame` a `StateMachine`.
_.extend( wpmoly.view.Frame.prototype, wp.media.controller.StateMachine.prototype );
