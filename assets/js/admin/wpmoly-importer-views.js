
( function( $, _, Backbone, wp, wpmoly ) {

	var grid = wpmoly.grid,
	  editor = wpmoly.editor,
	importer = wpmoly.importer;

	/**
	 * Controller for draftees list.
	 * 
	 * Handle the connection between the draftees form and list and the 
	 * draftees collection.
	 * 
	 * @since    2.2
	 */
	importer.controller.Draftees = Backbone.Model.extend({

		initialize: function() {

			this.collection = new importer.Model.Draftees;
		}
	});

	/**
	 * Draftee View. Simple LI element added to the list to give some visual
	 * control over the list.
	 * 
	 * @since    2.2
	 */
	importer.View.Draftee = Backbone.View.extend({

		tagName: 'li',

		events: {
			'click .remove-draftee': 'removeDraftee'
		},

		/**
		 * Create a new LI element for the model.
		 * 
		 * @since    2.2
		 * 
		 * @return   Return itself to allow chaining
		 */
		render: function() {

			this.$el.html( '<a class="remove-draftee" href="#"><span class="wpmolicon icon-no-alt"></span></a><span class="draftee-label">' + this.model.get( 'title' ) + '</span>' );

			return this;
		},

		/**
		 * Destroy the model when user clicks the remove link.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS 'Click' Event
		 * 
		 * @return   void
		 */
		removeDraftee: function( event ) {

			event.preventDefault();

			this.model.destroy();
		},
	});

	/**
	 * Draftees View.
	 * 
	 * Handle the form to submit a list of titles using the draftees controller
	 * to manipulate a collection of draftees. User type in its list, which
	 * is split by comma and rendered in an UL element to offer the possibility
	 * to remove movies easily.
	 * 
	 * @since    2.2
	 */
	importer.View.Draftees = Backbone.View.extend({

		el: '#importer-search-list-form',

		events: {
			'keypress #importer-search-list': 'update'
		},

		_views: [],

		/**
		 * Initialize the View.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function() {

			this.controller = new importer.controller.Draftees;

			this.controller.collection.on( 'add', this.createSubView , this );
			this.controller.collection.on( 'remove', this.removeSubView , this );
		},

		/**
		 * Handle each key pressed on the list's textarea.
		 * 
		 * If key if a comma, or user hit enter, create a new model and 
		 * view for the every title not already stored. If user hit escape
		 * or backspace, try and remove the last title along with model 
		 * related model and view.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS 'Keypress' Event
		 * 
		 * @return   void
		 */
		update: function( event ) {

			var $draftees = this.$( event.currentTarget ),
			     draftees = $draftees.val(),
			    _draftees = draftees.split( ',' ),
			     lastChar = draftees.charAt( draftees.length - 1 ),
			    lastChars = draftees.substr( -2 );

			var key = event.charCode || event.keyCode;

			// Hit backspace of escape
			if ( 8 === key || 27 === key ) {

				if ( ( ',' === lastChar || ', ' === lastChars ) && ! _.isUndefined( this.lastDraftee ) ) {
					this.lastDraftee.destroy();
				}

			// Hit enter or comma
			} else if ( 13 === key || 44 === key ) {

				_.each( _draftees, function( draftee ) {
					var draftee = draftee.trim();
					if ( _.isUndefined( this.controller.collection.findWhere( { title: draftee } ) ) && '' != draftee ) {
						this.lastDraftee = new importer.Model.Draftee( { title: draftee } );
						this.controller.collection.add( this.lastDraftee );
					}
				}, this );

				if ( 13 === key ) {
					if ( ',' !== lastChar && ', ' !== lastChars ) {
						$draftees.val( draftees + ', ' );
					}
					event.preventDefault();
				}
			}

		},

		/**
		 * Create a subview for each new draftee add to the collection.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    importer.Model.Draftee instance
		 * @param    object    options
		 * 
		 * @return   Return the newly created view
		 */
		createSubView: function( model, options ) {

			var view = new importer.View.Draftee({
				model: model
			});

			this.$( '#importer-search-list-draftees' ).append( view.render().$el );

			return this._views[ model.cid ] = view;
		},

		/**
		 * Remove a subview when the related model is destroyed.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    importer.Model.Draftee instance
		 * @param    object    options
		 * 
		 * @return   void
		 */
		removeSubView: function( model, options ) {

			var view = this._views[ model.cid ];
			if ( ! _.isUndefined( view ) ) {
				view.remove();
				this.removeFromList( model.get( 'title' ) );
			}
		},

		/**
		 * Remove a title from the list's textarea.
		 * 
		 * @since    2.2
		 * 
		 * @param    string    movie title
		 * 
		 * @return   void
		 */
		removeFromList: function( title ) {

			var $list = this.$( '#importer-search-list' ),
			     list = $list.val();

			var re = new RegExp( '(' + title + ', |' + title + ',|' + title + ')', 'g' );
			$list.val( list.replace( re, '' ) );
		}
	});

	/**
	 * 
	 * 
	 * @since    2.2
	 */
	importer.View.Search = Backbone.View.extend({

		
	});

	/**
	 * 
	 * 
	 * @since    2.2
	 */
	importer.View.Settings = Backbone.View.extend({

		
	});

	/**
	 * 
	 * 
	 * @since    2.2
	 */
	importer.View.Results = Backbone.View.extend({

		
	});

})( jQuery, _, Backbone, wp, wpmoly );