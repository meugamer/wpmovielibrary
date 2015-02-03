
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var media = wpmoly.media = function() {

		var backdrops = $( '#wpmoly-imported-backdrops-json' ).val(),
		      posters = $( '#wpmoly-imported-posters-json' ).val(),
		    backdrops = $.parseJSON( backdrops ),
		      posters = $.parseJSON( posters );

		// Init models
		media.models.backdrops = new media.Model.Backdrops;
		media.models.posters = new media.Model.Posters;
		media.models.backdrops.add( backdrops );
		media.models.posters.add( posters );

		// Init views
		media.views.backdrops = new media.View.Backdrops( { collection: media.models.backdrops } );
		media.views.posters = new media.View.Posters( { collection: media.models.posters } );
	};

	_.extend( media, { models: {}, views: {}, Model: {}, View: {} } );

	_.extend( media.Model, {

		/**
		 * WPMOLY Backbone basic Attachment Model
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		Attachment: wp.media.model.Attachment.extend({

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

				options = options || {};
				options.url = options.url || this.url;

				if ( 'upload' == method ) {

					this.trigger( 'uploading:start' );
					editor.models.status.trigger( 'loading:start' );
					editor.models.status.trigger( 'status:say', wpmoly.l10n[ this._type ].uploading );
					
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
						success: function( response ) {
							this.trigger( 'uploading:end', response );
							editor.models.status.trigger( 'status:say', wpmoly.l10n[ this._type ].uploaded );
						}
					});

					wp.ajax.send( options );
				}
				// Fallback to Backbone sync
				else {
					return  wp.media.model.Attachment.prototype.sync.apply( this, arguments );
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

		})
	});

	_.extend( media.Model, {

		/**
		 * WPMOLY Backbone Backdrop Model
		 * 
		 * @since    2.2
		 */
		Backdrop: media.Model.Attachment.extend( { _type: 'backdrops' } ),

		/**
		 * WPMOLY Backbone Poster Model
		 * 
		 * @since    2.2
		 */
		Poster: media.Model.Attachment.extend( { _type: 'posters' } )
	});

	_.extend( media.Model, {

		/**
		 * WPMOLY Backbone Backdrops Collection Model
		 * 
		 * @since    2.2
		 */
		Backdrops: wp.media.model.Attachments.extend( { model: media.Model.Backdrop } ),

		/**
		 * WPMOLY Backbone Posters Collection Model
		 * 
		 * @since    2.2
		 */
		Posters: wp.media.model.Attachments.extend( { model: media.Model.Poster } )

	});

})(jQuery);
