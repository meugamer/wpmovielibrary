
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
						beforeSend: function() {
							this.trigger( 'uploading:start' );
							wpmoly.editor.models.status.trigger( 'loading:start' );
							wpmoly.editor.models.status.trigger( 'status:say', wpmoly.l10n.media[ model.get( 'type' ) ].uploading );
						},
						complete: function() {
							wpmoly.editor.models.status.trigger( 'loading:end' );
						},
						success: function( response ) {
							this.trigger( 'uploading:end', response );
							wpmoly.editor.models.status.trigger( 'status:say', wpmoly.l10n.media[ model.get( 'type' ) ].uploaded );
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

		}),

		Attachments: wp.media.model.Attachments.extend({

			_queue: [],

			initialize: function() {

				this.on( 'dequeue', this.dequeue, this );
			},

			add: function( models, options ) {

				var options = options || {};
				if ( undefined === options.upload )
					options.upload = true;

				wp.media.model.Attachments.prototype.add.apply( this, arguments );

				if ( true === options.upload )
					_.each( models, this.enqueue, this );
			},

			enqueue: function( model ) {

				if ( undefined !== model._previousAttributes && true === model._previousAttributes.uploading ) {
					model.trigger( 'uploading:end', model );
					return this;
				}

				model = new media.Model.Attachment( model.attributes ),
				model.set( { type: this._type } );

				this._queue.unshift( model );

				return this
			},

			dequeue: function() {

				if ( ! this._queue.length )
					return this;

				var model = this._queue.pop();
				    model.upload();

				model.on( 'uploading:end', function( response ) {

					model.set( { id: response } );
					model.fetch();
					model.trigger( 'uploading:done', model );

					this.dequeue();
				}, this );
			}

			/*enqueue: function( model ) {

				this._queue.push( model );
			},

			dequeue: function() {

				var model = _.first( this._queue );
				    model.upload();
			},*/

			

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
