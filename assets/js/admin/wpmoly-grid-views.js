
( function( $, _, Backbone, wp, wpmoly ) {

	var grid = wpmoly.grid || {}, media = wp.media;

	grid.View.Menu = media.View.extend({

		id: 'grid-menu',

		template: media.template( 'wpmoly-grid-menu' ),

		initialize: function() {

			this.mode = this.controller.get( 'mode' );
		},

		render: function() {

			this.$el.html( this.template( this.mode ) );

			return this;
		}

	});

	grid.View.Content = media.View.extend({

		id: 'grid-content',

		initialize: function() {

			
			
		},

		/*render: function() {

			return this;
		}*/

	});

	grid.View.Frame = media.View.extend({

		id: 'movie-grid-frame',

		tagName: 'div',

		className: 'movie-grid',

		template: media.template( 'wpmoly-grid-frame' ),

		initialize: function() {

			this.controller = new grid.controller.State;
			this.controller.set( { mode: this.options.mode } );
			this.controller.on( 'change:mode', this.changeMode, this );

			this.states = [
				
			];

			this.menu    = new grid.View.Menu( { frame: this, controller: this.controller } );
			this.content = new grid.View.Content( { frame: this, controller: this.controller } );

			this.preRender();
			this.render();
			this.postRender();
		},

		preRender: function() {

			$( '.wrap' ).append( '<div id="grid-list"></div>' );
			$( '.wrap > *' ).not( 'h2' ).appendTo( '#grid-list' );

			return this;
		},

		render: function() {

			this.$el.html( this.template() );

			this.$( '.grid-frame-menu' ).append( this.menu.render().$el );
			this.$( '.grid-frame-content' ).append( this.content.render().$el );

			return this;
		},

		postRender: function() {

			this.$el.appendTo( $( '.wrap' ) );

			$( '#grid-list' ).appendTo( this.$( '.grid-frame-content' ) );

			return this;
		},

		changeMode: function( mode ) {

			this.render();
			/*this.mode = mode;
			this.trigger( 'change:mode', mode, this );*/
		},

	});

	

}( jQuery, _, Backbone, wp, wpmoly ) );
