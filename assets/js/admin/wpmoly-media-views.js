
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

	//_.extend( images.Modal, { View: {}, Controller: {} } );

	_.extend( images.Modal, {

		frame: function() {

			if ( this._frame )
				return this._frame;

			this._frame = wp.media();

			var states = [
				new wp.media.controller.Library( {
						id:                 'image',
						title:              'Images',
						priority:           20,
						library:            wp.media.query( { type: 'backdrops', s: 170522 } ),
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
				states: states
			} );

			this._frame.on( 'open', function( a ) {

			}, this);

			/*this._frame.state('library').collection.on( 'activate', function() {
				
			});*/

			return this._frame;
		},

		newmenu: function() {

			
		}
	} );

	wpmoly.images = images;
	images.views.init();

})(jQuery);
