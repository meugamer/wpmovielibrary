
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var media = wpmoly.media = function() {

		// Init models
		media.models.backdrops = new media.Model.Backdrops();
	};

	_.extend( media, { models: {}, views: {}, Model: {}, View: {} } );

	media.Model.Attachment = wp.media.model.Attachment.extend({

		url: ajaxurl,

		post_id: $( '#post_ID' ).val(),

		/**
		 * Initialize Model. Set the AJAX url and current Post ID.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function( models, options ) {

			//this.url = ajaxurl;

			return wp.media.model.Attachment.prototype.initialize.call( this, models, options );
		},

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
						data: _.extend( this.toJSON(), { post_id: this.post_id } )
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

		upload: function() {

			return this.sync( 'upload', this, {} );
		}
	});

	/**
	 * WPMOLY Backbone Backdrops Model
	 * 
	 * @since    2.2
	 */
	media.Model.Backdrops = wp.media.model.Attachments.extend({

		model: media.Model.Attachment,

		initialize: function( models, options ) {

			this.url = ajaxurl;
			this.post_id = $( '#post_ID' ).val();

			//this.on( 'add', this.update, this );

			return wp.media.model.Attachments.prototype.initialize.call( this, models, options );
		},

		update: function( model ) {

			//console.log( model );
			this.sync();
		},

		sync: function( method, model, options ) {

			console.log(  method, model, options );

			return Backbone.sync( 'create', this );
		}
	});

	wpmoly.media = media;
	wpmoly.media();

})(jQuery);
