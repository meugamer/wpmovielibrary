
window.wpmoly = window.wpmoly || {};
wpmoly.images = wpmoly.images || {};

(function( $ ) {

	var images = wpmoly.images;

	_.extend( images, { views: {}, View: {} } );

	images.views.init = function() {

		images.views.images = new images.View.Images();
		images.views.modal = images.View.Modal.frame();
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

		duh: function() {
			alert('duh!');
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

	images.View.Modal = {

		frame: function() {

			if ( this._frame )
				return this._frame;

			this._frame = wp.media();

			this._frame.on( 'open', this.newmenu, this );

			this._frame.state('library').collection.on( 'activate', function() {
				
			});

			return this._frame;
		},

		newmenu: function() {

			var options = this._frame.options,
			   menuitem = new wp.media.view.RouterItem( _.extend( options, { text: 'New Item!' } ) );

			this._frame.menu.view.views.set( '.media-router', menuitem, _.extend( options, { add: true } ) );
		}
	};

	wpmoly.images = images;
	images.views.init();

})(jQuery);
