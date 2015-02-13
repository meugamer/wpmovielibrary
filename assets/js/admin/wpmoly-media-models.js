
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var media = wpmoly.media = function() {

		var backdrops = $( '#wpmoly-imported-backdrops-json' ).val(),
		      posters = $( '#wpmoly-imported-posters-json' ).val(),
		    backdrops = $.parseJSON( backdrops ),
		      posters = $.parseJSON( posters );

		_.map( backdrops, function( backdrop ) { return new media.Model.Backdrop( backdrop ); } );
		_.map( posters, function( poster ) { return new media.Model.Poster( poster ); } );

		// Init models
		media.models.backdrops = new media.Model.Backdrops;
		media.models.posters = new media.Model.Posters;
		media.models.backdrops.add( backdrops, { upload: false } );
		media.models.posters.add( posters, { upload: false } );

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

			tmdb_id: $( '#meta_data_tmdb_id' ).val(),

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

				if ( 'upload' == method || 'attach' == method ) {

					var action = 'wpmoly_' + method + '_image';
					_.extend( options, {
						context: this,
						data: _.extend( options.data || {}, {
							action: action,
							nonce: wpmoly.get_nonce( 'upload-movie-image' ),
							data: _.extend( this.toJSON(), {
								post_id: this.post_id,
								tmdb_id: this.tmdb_id
							} )
						}),
						beforeSend: function() {
							model.trigger( 'uploading:start' );
						},
						complete: function() {},
						success: function( response ) {
							model.trigger( 'uploading:end', response );
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
			 * Alias for sync( 'attach' )
			 * 
			 * @since    2.2
			 * 
			 * @return   object   this
			 */
			attach: function() {

				return this.sync( 'attach', this, {} );
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

		}),

		/**
		 * WPMOLY Backbone Attachments Collection Model
		 * 
		 * Used to handle the sequenced upload of Attachments.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		Attachments: wp.media.model.Attachments.extend({

			_queue: [],

			_uploading: false,

			/**
			 * Initialize Model.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function() {

				this.on( 'dequeue', this.dequeue, this );
			},

			/**
			 * Add Models to the Collection.
			 * 
			 * This overrides the Backbone Collection add() method to
			 * create a queue of Models to upload. If not specifically
			 * disable using options.upload = false, every single
			 * Attachment added to the collection will be uploaded.
			 * 
			 * @since    2.2
			 * 
			 * @param    array     Array of Attachment Models
			 * @param    object    Options
			 * 
			 * @return   this
			 */
			add: function( models, options ) {

				var options = options || {};
				if ( undefined === options.upload )
					options.upload = true;

				wp.media.model.Attachments.prototype.add.apply( this, arguments );

				if ( true === options.upload )
					_.each( models, this.enqueue, this );
			
				return this;
			},

			/**
			 * Add an Attachment Model to the queue.
			 * 
			 * Attachments should be instances of wpmoly.media.Model.Attachment
			 * and will be converted if not.
			 * 
			 * @since    2.2
			 * 
			 * @param    object    Attachment Model
			 * 
			 * @return   this
			 */
			enqueue: function( model ) {

				// Make sure we're queueing a media.Model.Attachment object
				var attachment = new media.Model.Attachment( model.attributes );

				// Set the Attachment type (backdrop, poster...)
				attachment.set( { type: this._type } );

				// Add to queue
				this._queue.push( attachment );

				return this;
			},

			/**
			 * Remove an Attachment from the queue and upload it.
			 * 
			 * This is the tricky part, so pay attention. We need a
			 * media.Model.Attachment object to handle the upload,
			 * but the corresponding View in the Metabox is related
			 * a WP Attachment model, so we have to relay the events
			 * triggered by our custom Attachment to the 'real' model
			 * to interact with the View.
			 * 
			 * Interaction is done by relaying 'uploading;{start|end|done}'
			 * to the model.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			dequeue: function() {

				if ( false === this._uploading ) {

					// Let it be known we've started the queue
					this.trigger( 'dequeue:start' );
					this._uploading = true;

					wpmoly.editor.models.status.trigger( 'loading:start' );
					wpmoly.editor.models.status.trigger( 'status:say', wpmoly.l10n.media[ this._type ].uploading );
				}

				// If we reached the end of the queue, don't go further
				if ( ! this._queue.length ) {

					// Let it be known we're done here
					this.trigger( 'dequeue:done' );
					this._uploading = false;

					wpmoly.editor.models.status.trigger( 'loading:end' );
					wpmoly.editor.models.status.trigger( 'status:say', wpmoly.l10n.media[ this._type ].uploaded );

					return this;
				}

				// Get current Attachment and related model
				var attachment = this._queue.pop(),
				         model = this.model.get( attachment.id );

				// Detect Attachment upload start
				attachment.once( 'uploading:start', function() {
					model.trigger( 'uploading:start' );
				}, this );

				// Detect Attachment upload end
				attachment.once( 'uploading:end', function( response ) {

					// Update the 'real' model
					model.set( { id: response } );
					model.fetch();
					model.trigger( 'uploading:done', model );

					// Next!
					this.trigger( 'dequeue' );

				}, this );

				// Already uploaded? Attach to the movie
				if ( undefined !== attachment.get( 'uploadedToLink' ) && _.isNumber( attachment.get( 'id' ) ) )
					return attachment.attach();

				// Launch the upload
				return attachment.upload();
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
		Backdrops: media.Model.Attachments.extend( { model: media.Model.Backdrop, _type: 'backdrops' } ),

		/**
		 * WPMOLY Backbone Posters Collection Model
		 * 
		 * @since    2.2
		 */
		Posters: media.Model.Attachments.extend( { model: media.Model.Poster, _type: 'posters' } )

	});

})(jQuery);
