
( function( $, _, Backbone, wp, wpmoly ) {

	var grid = wpmoly.grid,
	   media = wp.media,
	  editor = wpmoly.editor,
	importer = wpmoly.importer;

	/**
	 * 
	 * 
	 * @since    2.2
	 */
	importer.View.Search = Backbone.View.extend({

		events: {
			'input #importer-search-query': 'update'
		},

		template: media.template( 'wpmoly-grid-content-import-single' ),

		initialize: function() {

			console.log( '!' );
		},

		update: function( event ) {

			console.log( event );
		},
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
	 * Handle the Single Search View.
	 * 
	 * 
	 * 
	 * @since    2.2
	 */
	importer.View.ContentSingle = media.View.extend({

		template: media.template( 'wpmoly-grid-content-import-single' ),

		events: {
			'click #importer-search-list-open': 'open'
		},

		/**
		 * Initialize the View.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.frame = options.frame;
		},

		/**
		 * Close the current view and open the list importer view.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS 'Click' Event
		 * 
		 * @return   void
		 */
		open: function( event ) {

			event.preventDefault();

			this.frame.content.mode( 'multiple' );
		},
	});

	/**
	 * Handle the Multiple Import View.
	 * 
	 * Handle the form to submit a list of titles using the draftees controller
	 * to manipulate a collection of draftees. User type in its list, which
	 * is split by comma and rendered in an UL element to offer the possibility
	 * to remove movies easily.
	 * 
	 * @since    2.2
	 */
	importer.View.ContentMultiple = media.View.extend({

		template: media.template( 'wpmoly-grid-content-import-multiple' ),

		events: {
			'click #importer-search-list-quit': 'switchState',
			'keypress #importer-search-list':   'update'
		},

		_views: [],

		/**
		 * Initialize the View.
		 * 
		 * @since    2.2
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			this.frame = options.frame;

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
					this.controller.collection.save();
				}

			// Hit enter or comma
			} else if ( 13 === key || 44 === key ) {

				var models = [];
				_.each( _draftees, function( draftee ) {
					var draftee = draftee.trim();
					if ( _.isUndefined( this.controller.collection.findWhere( { title: draftee } ) ) && '' != draftee ) {
						this.lastDraftee = new importer.Model.Draftee( { title: draftee } );
						models.push( this.lastDraftee );
					}
				}, this );

				this.controller.collection.add( models );
				this.controller.collection.save();

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
		 * @return   void
		 */
		createSubView: function( model, models, options ) {

			if ( models.models.length ) {

				var $draftees = this.$( '#importer-search-list-draftees' );
				_.each( models.models, function( model ) {

					if ( _.isUndefined( this._views[ model.cid ] ) ) {

						var view = new importer.View.Draftee({
							model: model
						});

						$draftees.append( view.render().$el );

						this._views[ model.cid ] = view;
					}
				}, this );
			}
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
				this.controller.collection.save();
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
		},

		/**
		 * Close the list importer and go back to the single search.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS 'Click' Event
		 * 
		 * @return   void
		 */
		close: function( event ) {

			event.preventDefault();

			this.frame.content.mode( 'single' );
		},
	});

	/**
	 * WPMOLY Admin Movie Grid View
	 * 
	 * This View renders the Admin Movie Grid.
	 * 
	 * @since    2.2
	 */
	importer.View.Frame = media.View.extend({

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function() {

			this._createRegions();
			this._createStates();
		},

		/**
		 * Create the frame's regions.
		 * 
		 * @since    2.2
		 */
		_createRegions: function() {

			// Clone the regions array.
			this.regions = this.regions ? this.regions.slice() : [];

			// Initialize regions.
			_.each( this.regions, function( region ) {
				this[ region ] = new media.controller.Region({
					view:     this,
					id:       region,
					selector: '.importer-frame-' + region
				});
			}, this );
		},
	
		/**
		 * Create the frame's states.
		 * 
		 * @since    2.2
		 */
		_createStates: function() {

			// Create the default `states` collection.
			this.states = new Backbone.Collection( null, {
				model: media.controller.State
			});

			// Ensure states have a reference to the frame.
			this.states.on( 'add', function( model ) {
				model.frame = this;
				model.trigger( 'ready' );
			}, this );

			if ( this.options.states ) {
				this.states.add( this.options.states );
			}
		},

		/**
		 * Render the View.
		 * 
		 * @since    2.2
		 */
		render: function() {

			// Activate the default state if no active state exists.
			if ( ! this.state() && this.options.state ) {
				this.setState( this.options.state );
			}

			return media.View.prototype.render.apply( this, arguments );
		}

	});

	// Make the `Frame` a `StateMachine`.
	_.extend( importer.View.Frame.prototype, media.controller.StateMachine.prototype );

	/**
	 * Importer Frame View.
	 * 
	 * @since    2.2
	 */
	importer.View.ImporterFrame = importer.View.Frame.extend({

		id: 'wpmoly-importer-frame',

		tagName: 'div',

		className: 'wpmoly-importer-frame',

		template: media.template( 'wpmoly-grid-content-import' ),

		regions: [ 'content' ],

		/**
		 * Initialize the View
		 * 
		 * @since    2.2
		 *
		 * @param    object    Attributes
		 * 
		 * @return   void
		 */
		initialize: function( options ) {

			importer.View.Frame.prototype.initialize.apply( this, arguments );

			this.options = options || {};
			_.defaults( this.options, {
				mode:  'single',
				state: 'single'
			} );

			this.createStates();
			this.bindHandlers();

			this.render();
		},

		/**
		 * Bind events
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		bindHandlers: function() {

			this.on( 'content:create:single', this.createContentSingle, this );
			this.on( 'content:create:multiple', this.createContentMultiple, this );
		},

		/**
		 * Create the default states on the frame.
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createStates: function() {

			var options = this.options;

			if ( this.options.states ) {
				return;
			}

			// Add the default states.
			this.states.add([
				// Main states.
				new importer.controller.State({
					id: 'single'
				}),
				new importer.controller.State({
					id: 'multiple'
				})
			]);

			return this;
		},

		/**
		 * Render the View.
		 * 
		 * @since    2.2
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			importer.View.Frame.prototype.render.apply( this, arguments );

			this.$el.html( this.template() );

			_.each( this.regions, function( region ) {
				this[ region ].mode( this.options.mode );
			}, this );

			return this;
		},

		/**
		 * Create the Frame content for single search view.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Region
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createContentSingle: function( region ) {

			var state = this.state();

			region.view = new importer.View.ContentSingle({
				frame: this,
				model: state
			});
		},

		/**
		 * Create the Frame content for multiple import view.
		 * 
		 * @since    2.2
		 * 
		 * @param    object    Region
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		createContentMultiple: function( region ) {

			var state = this.state();

			region.view = new importer.View.ContentMultiple({
				model: state,
				frame: this
			});
		},

	});

})( jQuery, _, Backbone, wp, wpmoly );