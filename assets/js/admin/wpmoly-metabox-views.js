
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var metabox = wpmoly.metabox;

	_.extend( metabox.View, {

		/**
		 * WPMOLY Backbone Menu View
		 * 
		 * View for metabox
		 * 
		 * @since    2.2
		 */
		Menu: Backbone.View.extend({

			events: {
				"click" : "togglePanel"
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

				_.bindAll( this, 'render' );
				this.model.on( 'change:label', this.setLabel, this );
				this.model.on( 'change:labeltitle', this.setLabel, this );
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
				this.setLabel( this.model );

				return this;
			},

			/**
			 * Fill in notification label
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			setLabel: function( model ) {

				var label = model.get( 'label' ),
				    title = model.get( 'labeltitle' ),
				    $elem = this.$el.find( '.label' );

				if ( '' === label && '' === title ) {
					$elem.hide();
					return this;
				} else {
					$elem.show();
				}

				if ( '' != label )
					this.$el.find( '.label' ).html( label );

				if ( '' != title )
					this.$el.find( '.label' ).prop( 'title', title );

				return this;
			},

			/**
			 * Set current panel as state
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Click Event
			 * 
			 * @return   void
			 */
			togglePanel: function( event ) {

				event.preventDefault();

				this.model.collection.setState( this.model.id );
				this.model.collection.enqueueLabel( this.model );
			}
		}),

		/**
		 * WPMOLY Backbone Panel View
		 * 
		 * View for metabox
		 * 
		 * @since    2.2
		 */
		Panel: Backbone.View.extend({

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

				_.bindAll( this, 'render' );
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
		})

	} );

	_.extend( metabox.View, {

		MenuCollapse: Backbone.View.extend({

			tagName: 'li',

			className: 'tab-off',

			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function() {

				this.template = _.template( '<a href="#"><span class="wpmolicon icon-collapse"></span>&nbsp; <span class="text">Collapse</span></a>' );
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
		})
	} );

	_.extend( metabox.View, {

		/**
		 * WPMOLY Backbone Metabox View
		 * 
		 * View for metabox
		 * 
		 * @since    2.2
		 */
		Metabox: Backbone.View.extend({

			meta: '#wpmoly-meta',
			menu: '#wpmoly-meta-menu',
			status: '#wpmoly-meta-status',
			panels: '#wpmoly-meta-panels',

			events: {
				"click #wpmoly-meta-menu .tab-off a": "resize"
			},

			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function() {

				var template = $( this.el ).html();
				if ( undefined === template )
					return false;

				this.template = _.template( template );
				this.render();

				_.bindAll( this, 'render', 'fix' );

				this.collection.on( 'add', this.createView, this );
				this.collection.on( 'change:state', this.setState, this );

				if ( window.innerWidth < 1180 )
					this.resize();

				$( this.panels ).css( { minHeight: $( $( this.menu ) ).height() } );

				$( window ).scroll( this.fix );
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
				this.renderMenuCollapse();

				return this;
			},

			renderMenuCollapse: function() {

				var collapse = new metabox.View.MenuCollapse();
				$( this.menu ).append( collapse.render().el );
			},

			/**
			 * Create Menu and Panel Views for each State
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			createView: function( model, collection ) {

				model._menu  = new metabox.View.Menu( { el: '#wpmoly-meta-' + model.get( 'id' ), model: model } );
				model._panel = new metabox.View.Panel( { el: '#wpmoly-meta-' + model.get( 'id' ) + '-panel', model: model } );
			},

			/**
			 * Jump between panels
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS Event
			 * 
			 * @return   void
			 */
			setState: function( state ) {

				this.$el.find( '.tab.active' ).removeClass( 'active' );
				this.$el.find( '.panel.active' ).removeClass( 'active' );

				state._menu.$el.addClass( 'active' );
				state._panel.$el.addClass( 'active' );
			},

			/**
			 * Set a fixed position for the panel tabs when scrolling long
			 * panels (essentially the meta one).
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			fix: function() {

				var $meta = $( this.meta ),
				    $menu = $( this.menu ),
				menu_t = $menu[0].offsetTop,
				menu_h = $menu[0].offsetHeight
				menu_b = menu_t + menu_h,
				meta_h = $meta[0].offsetHeight,
				meta_t = $meta.offset().top - 32,
				meta_b = meta_t + meta_h,
					y = window.scrollY;

				if ( meta_t < y && meta_b > y ) {

					var t = Math.round( y - meta_t ),
					_t = t + menu_h,
					top = 'auto';
					if ( menu_b < meta_h && _t < meta_h ) {
						top = t;
					}
					$( this.menu ).css( { top: top } );
					$( this.status ).css( { top: top } );
				}
				else if ( menu_t ) {
					$( this.menu ).css( { top: 0 } );
					$( this.status ).css( { top: 0 } );
				}
			},

			/**
			 * Resize Metabox Panel
			 *
			 * @since 2.0
			 */
			resize: function( event ) {

				if ( undefined !== event )
					event.preventDefault();

				$( this.el ).toggleClass( 'small' );
			}
		})

	} );

	metabox();

})(jQuery);
