
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
		media.views.posters = new media.View.Posters( { collection: media.models.posters } );
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
	//media.View.Attachment = Backbone.View.extend({
	media.View.Attachment = wp.media.view.Attachment.extend({

		tagName: 'li',

		events: {
			"click .wpmoly-imported-attachment-menu-toggle": "toggleMenu",
			"click .wpmoly-imported-attachment-menu-delete": "deleteAttachment",
			"click .wpmoly-imported-attachment-menu-featured": "setFeatured",
		},

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

			this.$el.html( this.template( { attachment: this.model.toJSON(), type: this._type } ) );
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
		},

		/**
		 * Toggle Attachment custom menu
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		toggleMenu: function( event ) {

			event.preventDefault();
			this.$el.find( '.wpmoly-imported-attachment-menu' ).toggleClass( 'active' );
		},

		deleteAttachment: function( event ) {

			event.preventDefault();
			//if ( true === confirm( 'Delete Attachment?' ) ) {
				console.log( this );
				this.model.trigger( 'destroy', this.model, this.collection, {} );
				//this.model.destroy();
			//}
		},

		setFeatured: function( event ) {

			event.preventDefault();
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

			this.$el.find( '.wpmoly-imported-' + this._type ).remove();

			this.collection.forEach( this.renderAttachment, this );
			return this;
		},

		renderAttachment: function( model ) {

			var model = new media.Model.Attachment( model.attributes );
			var attachment = new this._subview( { model: model, collection: this.collection } );

			var el = attachment.render().el;

			this.$el.prepend( el );  
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

			/*var _model = new media.Model.Attachment( _.extend( model.attributes, { type: this._type } ) );
			var attachment = new this._subview( { model: _model, collection: this.collection } );

			this.$el.prepend( view );
			this._views.push( _view );

			_model.upload();
			_model.on( 'uploading:end', function() {
				model.trigger( 'destroy', model, model.collection, {} );
			}, this );

			/*      view = $( '<li id="attachment-' + model.attributes.id + '" class="wpmoly-' + this._type + ' wpmoly-imported-' + this._type + '">' )
			     _view = new this._subview( { el: view, model: _model } );

			this.$el.prepend( view );
			this._views.push( _view );

			_model.upload();
			_model.on( 'uploading:end', function() {
				model.trigger( 'destroy', model, model.collection, {} );
			}, this );*/
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
				states: this._library.state,
				button: {
					text: this._library.button
				}
			} );

			this._frame.options.button.reset = false;

			this._frame.on( 'open', this.hidemenu, this );

			console.log( this._frame );
			this._frame.state( this._library.id ).on( 'select', this.select, this );

			wp.Uploader.queue.on( 'add', this.upload, this );

			return this._frame;
		},

		/**
		 * Hide Modal Menu
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		hidemenu: function() {

			this._frame.$el.addClass( 'hide-menu' );
		},

		/**
		 * Handle Attachments selection
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		select: function() {

			var selection = this._frame.state( this._library.id ).get( 'selection' ),
			       models = selection.models;
			console.log( models );

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

		className: 'wpmoly-backdrop wpmoly-imported-backdrop',
		_tmpl: '#wpmoly-imported-attachment-template',
		_type: 'backdrop',
	});

	/**
	 * WPMOLY Backbone Poster View
	 * 
	 * Extends media.View.Attachment to set template ID and Attachment type
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	media.View.Poster = media.View.Attachment.extend({

		className: 'wpmoly-poster wpmoly-imported-poster',
		_tmpl: '#wpmoly-imported-attachment-template',
		_type: 'poster',
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

		el: '#wpmoly-imported-backdrops',

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
		}

	});

	/**
	 * WPMOLY Backbone Posters View
	 * 
	 * Extends media.View.Attachments
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	media.View.Posters = media.View.Attachments.extend({

		el: '#wpmoly-imported-posters',

		events: {
			"click #wpmoly-load-posters": "open"
		},

		_subview: media.View.Poster,

		_tmpl: '#wpmoly-imported-posters-template',

		_type: 'backdrop',

		_library: {
			id: 'posters',
			button: wpmoly_lang.import_posters,
			state: new wp.media.controller.Library({
				id:                 'posters',
				title:              function() {
					var title = wpmoly.editor.models.movie.get( 'title' )
					if ( '' != title && undefined != title )
						return wpmoly_lang.import_posters_title.replace( '%s', title );
					return 'Images';
				},
				priority:           20,
				library:            wp.media.query( { type: 'posters', s: wpmoly.editor.models.movie.get( 'tmdb_id' ), post__in: [ $( '#post_ID' ).val() ] } ),
				content:            'browse',
				search:             false,
				searchable:         false,
				filterable:         false,
				multiple:           true,
				contentUserSetting: false
			})
		}

	});

	// Let's get this party started
	load();

})(jQuery);
