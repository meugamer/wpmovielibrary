
window.wpmoly = window.wpmoly || {};
wpmoly.media = wpmoly.media || {};

(function( $ ) {

	var media = wpmoly.media;

	/**
	 * WPMOLY Backdrops Media Views init
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	var load = function() {

		media.views.backdrops = new media.View.Backdrops( { collection: media.models.backdrops } );
		//media.views.posters = new media.View.Posters( { collection: media.models.posters } );
	};

	/**
	 * WPMOLY Backbone basic Attachment View
	 * 
	 * Handle each imported backdrop/poster/whatever's view. This View has
	 * to be extended to work. Required properties:
	 *  - _tmpl: View's template
	 *  - _type: View's type, backdrop/poster/whatever
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	media.View.Attachment = wp.Backbone.View.extend({

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.template = _.template( $( this._tmpl ).html() );
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

			var attachment = this.template( { attachment: _.extend( this.model.toJSON() ) } );
			$( this.el ).html( attachment );

			return this;
		},

		/**
		 * Starting upload, let's spin!
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		uploading: function() {

			this.$el.addClass( 'wpmoly-' + this._type + '-loading' );
		},

		/**
		 * Done uploading, stop spinning!
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		uploaded: function() {

			this.$el.removeClass( 'wpmoly-' + this._type + '-loading' );
		}
	});

	/**
	 * WPMOLY Backbone basic Attachments View
	 * 
	 * Collection View to handle imported media views (Attachment View
	 * extends). This View has to be extended to work. Required properties:
	 *  - el: Collection View element
	 *  - _subview: Collection's models View
	 *  - _tmpl: View's template
	 *  - _type: View's type, backdrop/poster/whatever
	 *  - _library: View Library main state
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	media.View.Attachments = wp.Backbone.View.extend({

		_views: [],

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.template = _.template( $( this._tmpl ).html() );
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

			var attachments = this.template( { attachments : this.collection.toJSON() } );

			$( this.el ).show();
			$( this.el ).html( attachments );

			return this;
		},

		/**
		 * Open the Modal frame
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS Click Event
		 * 
		 * @return   void
		 */
		open: function( event ) {

			this.modal.open();
			event.preventDefault();
		},

		/**
		 * Add a new Attachment to the Attachments Collection View and
		 * upload the Attachment.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    wp.media.model.Attachment instance
		 * 
		 * @return   void
		 */
		add: function( model ) {

			var _model = new media.Model.Attachment( _.extend( model.attributes, { type: this._type } ) ),
			      view = $( '<li id="attachment-' + model.attributes.id + '" class="wpmoly-' + this._type + ' wpmoly-imported-' + this._type + '">' )
			     _view = new this._subview( { el: view, model: _model } );

			this.$el.prepend( view );
			this._views.push( _view );

			_model.upload();
			_model.on( 'uploading:end', function() {
				var __model = this._frame.state().get( 'library' ).models.find( function( m ) { return model.cid === m.cid; } );
				    //__model.destroy();
			}, this );
		},

		/**
		 * Create/return the Modal frame
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		frame: function() {

			if ( this._frame )
				return this._frame;

			this._frame = wp.media( {
				state: this._library.id,
				states: [ this._library.state ],
				button: {
					text: this._library.button
				}
			} );

			this._frame.options.button.reset = false;

			this._frame.state( this._library.id ).on( 'select', this.select, this );

			wp.Uploader.queue.on( 'add', this.upload, this );

			return this._frame;
		},

		/**
		 * Handle Attachments selection
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		select: function() {

			var selection = this._frame.state().get( 'selection' ),
			       models = selection.models;

			this.collection.add( models );
		},

		/**
		 * Handle user attachment upload
		 * 
		 * Add each Attachment uploaded by the user to the collection
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		upload: function( attachment ) {

			var attachments = attachment.collection.models,
			         models = this._frame.state().get( 'library' ).models;
			    attachments = _.filter( attachments, function( obj ) { return ! _.findWhere( models, obj ); });

			_.each( attachments, function( _attachment ) {
				this._frame.state().get( 'library' ).models.unshift( _attachment );
			}, this );
		}
	});



	/**
	 * WPMOLY Backbone Backdrop View
	 * 
	 * Extends media.View.Attachment to set template ID and Attachment type
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	media.View.Backdrop = media.View.Attachment.extend({

		_tmpl: '#wpmoly-imported-backdrop-template',
		_type: 'backdrop',
	});

	/**
	 * WPMOLY Backbone Backdrops View
	 * 
	 * Extends media.View.Attachments
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	media.View.Backdrops = media.View.Attachments.extend({

		el: '#wpmoly-backdrops-preview',

		events: {
			"click #wpmoly-load-backdrops": "open"
		},

		_subview: media.View.Backdrop,

		_tmpl: '#wpmoly-imported-backdrops-template',

		_type: 'backdrop',

		_library: {
			id: 'backdrops',
			button: wpmoly_lang.import_images,
			state: new wp.media.controller.Library({
				id:                 'backdrops',
				title:              function() {
					var title = wpmoly.editor.models.movie.get( 'title' )
					if ( '' != title && undefined != title )
						return wpmoly_lang.import_images_title.replace( '%s', title );
					return 'Images';
				},
				priority:           20,
				library:            wp.media.query( { type: 'backdrops', s: wpmoly.editor.models.movie.get( 'tmdb_id' ), post__in: [ $( '#post_ID' ).val() ] } ),
				content:            'browse',
				search:             false,
				searchable:         false,
				filterable:         false,
				multiple:           true,
				contentUserSetting: false
			})
		},

		/*_library: new wp.media.controller.Library( {
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
		} ),*/

	});

	// Let's get this party started
	load();

})(jQuery);
