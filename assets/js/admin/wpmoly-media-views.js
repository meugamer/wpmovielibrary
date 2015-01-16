
window.wpmoly = window.wpmoly || {};
wpmoly.media = wpmoly.media || {};

(function( $ ) {

	var media = wpmoly.media;

	var backdrops = function() {

		media.views.backdrops = new media.View.Backdrops( { collection: media.models.backdrops } );
	};

	media.View.Backdrop = wp.Backbone.View.extend({

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.template = _.template( $( '#wpmoly-imported-backdrop-template' ).html() );
			this.render();

			this.model.on( 'uploading:start', this.uploading, this );
			this.model.on( 'uploading:end', this.uploaded, this );
		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		render: function() {

			var backdrop = this.template( { backdrop: _.extend( this.model.toJSON() ) } );
			$( this.el ).html( backdrop );

			return this;
		},

		uploading: function() {

			this.$el.addClass( 'wpmoly-image-loading' );
		},

		uploaded: function() {

			this.$el.removeClass( 'wpmoly-image-loading' );
		}
	});

	media.View.Backdrops = wp.Backbone.View.extend({

		el: '#wpmoly-backdrops-preview',

		events: {
			"click #wpmoly-load-backdrops": "open"
		},

		backdrops: [],

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.template = _.template( $( '#wpmoly-imported-backdrops-template' ).html() );
			this.render();

			this.modal = this.frame();

			this.collection.on( 'add', this.add, this );

		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		render: function() {

			var backdrops = this.template( { backdrops : this.collection.toJSON() } );

			$( this.el ).show();
			$( this.el ).html( backdrops );

			return this;
		},

		open: function( event ) {

			this.modal.open();
			event.preventDefault();
		},

		add: function( model ) {

			var model = new media.Model.Attachment( _.extend( model.attributes, { type: 'backdrop', tmdb_id: 1234 } ) ),
			     view = $( '<li id="attachment-' + model.attributes.id + '" class="wpmoly-image wpmoly-imported-image">' )
			    _view = new media.View.Backdrop( { el: view, model: model } );

			this.$el.prepend( view );
			this.backdrops.push( _view );

			model.upload();
		},

		frame: function() {

			if ( this._frame )
				return this._frame;

			var title = wpmoly.editor.models.movie.get( 'title' ),
			  tmdb_id = wpmoly.editor.models.movie.get( 'tmdb_id' );

			if ( '' != title && undefined != title ) {
				title = wpmoly_lang.import_images_title.replace( '%s', title );
			} else {
				title = 'Images';
			}

			var states = [
				new wp.media.controller.Library( {
						id:                 'backdrops',
						title:              title,
						priority:           20,
						library:            wp.media.query( { type: 'backdrops', s: 550 } ),
						content:            'browse',
						search:             false,
						searchable:         false,
						filterable:         false,
						multiple:           true,
						contentUserSetting: false
				} ),
				/*new wp.media.controller.Library( {
						id:                 'posters',
						title:              'Posters',
						priority:           40,
						library:            wp.media.query( { type: 'posters', s: 1234 } ),
						content:            'browse',
						search:             false,
						searchable:         false,
						filterable:         false,
						multiple:           true,
						contentUserSetting: false
				} )*/
			];

			this._frame = wp.media( {
				state: 'backdrops',
				states: states,
				button: {
					text: wpmoly_lang.import_images
				}
			} );

			this._frame.options.button.reset = false;

			this._frame.state( 'backdrops' ).on( 'select', this.select, this );

			wp.Uploader.queue.on( 'add', this.uploading, this );

			return this._frame;
		},

		select: function() {

			var selection = this._frame.state().get( 'selection' ),
			       models = selection.models;

			this.collection.add( models );
			/*_.each( models, function( model ) {
				this.collection.add( model );
			}, this );*/
		},

		uploading: function( attachment ) {

			var attachments = attachment.collection.models,
			         models = this._frame.state().get( 'library' ).models;
			    attachments = _.filter( attachments, function( obj ) { return ! _.findWhere( models, obj ); });

			_.each( attachments, function( _attachment ) {
				this._frame.state().get( 'library' ).models.unshift( _attachment );
			}, this );
		}
	});

	backdrops();

})(jQuery);
