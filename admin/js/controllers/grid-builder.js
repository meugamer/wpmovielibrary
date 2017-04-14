
wpmoly = window.wpmoly || {};

wpmoly.controller.GridBuilder = Backbone.Model.extend({

	/**
	 * Initialize the Model.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	initialize: function( attributes, options ) {

		this.preview = options.preview;

		this.builder = new wpmoly.model.GridBuilder( {}, { controller: this } );

		this.listenTo( this.builder, 'change:type change:mode change:theme', this.updatePreview );
	},

	/**
	 * Ready the controller.
	 *
	 * @since    3.0
	 *
	 * @return   Returns itself to allow chaining.
	 */
	ready: function() {

		return this.trigger( 'ready' );
	},

	/**
	 * Update the grid preview when some settings change.
	 * 
	 * @since    3.0
	 * 
	 * @param    {object}    model
	 * @param    {object}    options
	 * 
	 * @return   void
	 */
	updatePreview: function( model, options ) {

		this.preview.setSettings( model.changed );
	},

	/**
	 * Set grid type.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    Grid type.
	 * 
	 * @return   void
	 */
	setType: function( type ) {

		this.builder.reset();

		return this.builder.set({
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
	 * @param    string    Grid mode.
	 * 
	 * @return   void
	 */
	setMode: function( mode ) {

		return this.builder.set({
			theme : 'default',
			mode  : mode
		});
	},

	/**
	 * Set grid theme.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    Grid theme.
	 * 
	 * @return   void
	 */
	setTheme: function( theme ) {

		return this.builder.set({
			theme : theme
		});
	}
});