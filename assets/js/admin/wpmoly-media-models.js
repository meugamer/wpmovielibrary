
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var media = wpmoly.media = function() {

		// Init models
		media.models.backdrops = new media.Model.Backdrops();
	};

	_.extend( media, { models: {}, views: {}, Model: {}, View: {} } );

	/**
	 * WPMOLY Backbone Backdrops Model
	 * 
	 * @since    2.2
	 */
	media.Model.Backdrops = wp.media.model.Attachments.extend({

		/*initialize: function( models, options ) {

			//this.on( 'add', this.update, this );

			return wp.media.model.Attachments.prototype.initialize.call( this, models, options );
		},

		update: function( model ) {

			
		},*/

		sync: function() {

			return wp.Backbone.sync( 'create', this );
		}
	});

	wpmoly.media = media;
	wpmoly.media();

})(jQuery);
