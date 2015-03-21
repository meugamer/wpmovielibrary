
var editor = wpmoly.editor,
      l10n = wpmoly.l10n;

/**
 * WPMOLY Backbone Status Model
 * 
 * Model for Search Settings manipulation.
 * 
 * @since    2.2
 */
editor.model.Status = Backbone.Model.extend({

	defaults: {
		error:   false,
		loading: false,
		message: l10n.misc.api_connected
	},

	/**
	 * Initialize Model.
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	initialize: function() {

		this.on( 'loading:start', this.loading, this );
		this.on( 'loading:end',   this.loaded, this );
		this.on( 'status:say',    this.say, this );
	},

	/**
	 * Turn on loading mode.
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	loading: function() {
		this.set( { loading: true } );
	},

	/**
	 * Turn off loading mode.
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	loaded: function() {
		this.set( { loading: false } );
	},

	/**
	 * Update status message.
	 * 
	 * @since    2.2
	 * 
	 * @param    string     New status message
	 * @param    boolean    Error status
	 * 
	 * @return   void
	 */
	say: function( message, error ) {

		var options = { error: false, message: message };
		if ( true === error )
			options.error = true;

		this.set( options );
	},

	/**
	 * Reset Model to default
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	reset: function() {

		this.clear().set( this.defaults );
	}
});
