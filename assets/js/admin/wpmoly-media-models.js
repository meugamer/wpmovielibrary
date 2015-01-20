
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var media = wpmoly.media = function() {

		var backdrops = $( '#wpmoly-imported-backdrops-json' ).text();
		    backdrops = $.parseJSON( backdrops );

		// Init models
		media.models.backdrops = new media.Model.Attachments();
		media.models.backdrops.add( backdrops );
	};

	_.extend( media, { models: {}, views: {}, Model: {}, View: {} } );

	/**
	 * WPMOLY Backbone basic Attachment Model
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	media.Model.Attachment = wp.media.model.Attachment.extend({

		url: ajaxurl,

		post_id: $( '#post_ID' ).val(),

		/**
		 * Overload Backbone sync method
		 * 
		 * @since    2.2
		 * 
		 * @param    string    method Are we searching or is it a regular sync?
		 * @param    object    model Current model
		 * @param    object    options Query options
		 * 
		 * @return   mixed
		 */
		sync: function( method, model, options ) {

			if ( 'upload' == method ) {

				this.trigger( 'uploading:start' );
				editor.models.status.trigger( 'loading:start' );
				editor.models.status.trigger( 'status:say', wpmoly_lang.import_images_wait );
				
				options = options || {};
				_.extend( options, {
					context: this,
					data: _.extend( options.data || {}, {
						action: 'wpmoly_upload_image',
						nonce: wpmoly.get_nonce( 'upload-movie-image' ),
						data: _.extend( this.toJSON(), {
							post_id: this.post_id,
							tmdb_id: wpmoly.editor.models.movie.get( 'tmdb_id' )
						} )
					}),
					complete: function() {
						editor.models.status.trigger( 'loading:end' );
					},
					success: function() {
						this.trigger( 'uploading:end' );
						editor.models.status.trigger( 'status:say', wpmoly_lang.images_uploaded );
					}
				});

				wp.ajax.send( options );
			}
			// Fallback to Backbone sync
			else {
				return Backbone.Model.prototype.sync.apply( this, options );
			}
		},

		/**
		 * Alias for sync( 'upload' )
		 * 
		 * @since    2.2
		 * 
		 * @return   object   this
		 */
		upload: function() {

			return this.sync( 'upload', this, {} );
		}
	});

	/**
	 * WPMOLY Backbone basic Attachments Model
	 * 
	 * @since    2.2
	 */
	media.Model.Attachments = wp.media.model.Attachments.extend({

		model: media.Model.Attachment,
	});

	wpmoly.media = media;
	wpmoly.media();

})(jQuery);
