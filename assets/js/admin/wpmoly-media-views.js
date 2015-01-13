
window.wpmoly = window.wpmoly || {};
wpmoly.images = wpmoly.images || {};

(function( $ ) {

	var images = wpmoly.images;

	_.extend( images, { controllers: {}, views: {}, View: {}, Controller: {}, Modal: {} } );

	images.views.init = function() {

		images.views.images = new images.View.Images();
		images.views.modal = images.Modal.frame();
	};

	images.View.Images = Backbone.View.extend({

		el: '#wpmoly-images-preview',

		events: {
			"click #wpmoly-load-images": "modal"
		},

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.template = _.template( $( this.el ).html() );
			this.render();

		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		render: function() {

			this.$el.html( this.template() );
			return this;
		},

		modal: function( event ) {

			images.views.modal.open();
			event.preventDefault();
		},
	});

	//_.extend( images.Modal, { /*View: {}, Controller: {}, Toolbar: {}*/ } );

	_.extend( images.Modal, {

		frame: function() {

			if ( this._frame )
				return this._frame;

			var title = wpmoly.editor.models.movie.get('title'),
			  tmdb_id = wpmoly.editor.models.movie.get('tmdb_id');

			if ( '' != title && undefined != title ) {
				title = wpmoly_lang.import_images_title.replace( '%s', title );
			} else {
				title = 'Images';
			}

			

			var states = [
				new wp.media.controller.Library( {
						id:                 'image',
						title:              title,
						priority:           20,
						library:            wp.media.query( { type: 'backdrops', s: tmdb_id } ),
						//toolbar:            t,
						content:            'browse',
						search:             false,
						searchable:         false,
						filterable:         false,
						multiple:           true,
						contentUserSetting: false
				} ),
				/*new wp.media.controller.Library( {
						id:                 'poster',
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
				state: 'image',
				states: states,
			} );

			/*this._frame.state('library').collection.on( 'activate', function() {
				
			});*/

			return this._frame;
		}
	});

	wpmoly.images = images;
	images.views.init();

})(jQuery);
