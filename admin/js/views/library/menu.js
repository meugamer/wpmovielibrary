wpmoly = window.wpmoly || {};

_.extend( wpmoly.view.Library, {

	Menu : wp.Backbone.View.extend({

		className : 'wpmoly library inner-menu',

		template : wp.template( 'wpmoly-library-menu' ),

		events : {
			'click [data-action="library-mode"]' : 'switchMode',
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 */
		initialize : function( options ) {

			var options = options || {};

			this.controller = options.controller || {};

			this.listenTo( this.controller, 'change:mode', this.render );

		},

		/**
		 * Switch Library Mode
		 *
		 * @since    3.0
		 *
		 * @param    {object}    JS 'click' Event
		 *
		 * @return   Returns itself to allow chaining.
		 */
		switchMode : function( event ) {

			event.preventDefault();

			var $target = this.$( event.currentTarget ),
			       mode = $target.attr( 'data-value' );

			this.controller.setMode( mode );

			return this;
		},

		/**
		 * Render the view.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		render : function() {

			var data = {
				mode : this.controller.get( 'mode' )
			};

			this.$el.html( this.template( data ) );

			return this;
		}

	})
} );
