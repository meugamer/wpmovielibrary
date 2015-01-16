
window.wpmoly = window.wpmoly || {};
wpmoly.media = wpmoly.media || {};

(function( $ ) {

	var media = wpmoly.media;

	var backdrops = function() {

		media.views.backdrops = new media.View.Backdrops( { collection: media.models.backdrops } );
	};

	media.View.Backdrops = wp.Backbone.View.extend({

		el: '#wpmoly-backdrops-preview',

		events: {
			"click #wpmoly-load-backdrops": "open"
		},

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

		update: function( model ) {
		},

		add: function( model ) {

			var backdrop = _.template( $( '#wpmoly-imported-backdrop-template' ).html(), { backdrop : model.attributes } ),
			       model = new media.Model.Attachment( _.extend( model.attributes, { type: 'backdrop', tmdb_id: 1234 } ) );

			model.upload();

			//console.log( backdrop );
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
