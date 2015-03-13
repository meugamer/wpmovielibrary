
window.wpmoly = window.wpmoly || {};

(function( $, _, Backbone, wp, wpmoly ) {

	var editor = wpmoly.editor,
	controller = editor.controller = {};

	/**
	 * wp.media.controller.State extension to change frame class upon
	 * state changes.
	 * 
	 * @since    2.2
	 */
	controller.State = wp.media.controller.State.extend({

		/**
		 * Initialize the State
		 * 
		 * @since    2.2
		 * 
		 * @return   Return itself to allow chaining
		 */
		initialize: function() {

			// Use Underscore's debounce to let the DOM load properly
			this.on( 'activate', _.debounce( this.activateState, 25 ), this );
			this.on( 'deactivate', _.debounce( this.deactivateState, 25 ), this );

			return this;
		},

		/**
		 * State activation: update frame class
		 * 
		 * @since    2.2
		 * 
		 * @return   Return itself to allow chaining
		 */
		activateState: function() {

			this.frame.$el.parents( '.media-modal' ).addClass( this.id + '-modal' );

			return this;
		},

		/**
		 * State deactivation: update frame class
		 * 
		 * @since    2.2
		 * 
		 * @return   Return itself to allow chaining
		 */
		deactivateState: function() {

			this.frame.$el.parents( '.media-modal' ).removeClass( this.id + '-modal' );

			return this;
		}
	});

	/**
	 * wp.media.controller.EditAttachmentMetadata
	 *
	 * A state for editing an attachment's metadata.
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	controller.EditMovie = controller.State.extend({

		defaults: {
			id:      'edit-movie',
			title:   'Edit Movie',
			content: 'edit-metadata',
			menu:    false,
			toolbar: false,
			router:  false
		}
	});

	/**
	 * wp.media.controller.EditAttachmentMetadata
	 *
	 * A state for editing an attachment's metadata.
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	controller.PreviewMovie = controller.State.extend({

		defaults: {
			id:      'preview-movie',
			title:   'Preview Movie',
			content: 'preview-movie',
			menu:    false,
			toolbar: false,
			router:  false
		}
	});

	/**
	 * editor.View.Movies
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	editor.View.Movies = wp.media.View.extend({

		el: '#the-list',

		events: {
			'click .quick-edit-meta a': 'openMetaModal',
		},

		initialize: function() {},

		/**
		 * Open the Movie Modal
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS 'Click' event
		 * @param    int       Optional movie model ID
		 * @param    string    state to use for the frame
		 * 
		 * @return   boolean
		 */
		openMovieModal: function( event, id, state ) {

			var id, model;

			event.preventDefault();

			if ( _.isUndefined( id ) ) {
				id = parseInt( this.$( event.currentTarget ).attr( 'data-id' ) );
			}

			model = editor.models.movies.get( id );

			if ( undefined == model )
				return;

			if ( 'preview-movie' !== state ) {
				state = 'edit-movie';
			}

			editor.frame = new editor.View.MovieModal( {
				state:   state,
				library: editor.models.movies,
				model:   model
			} ).open();
		}

	});

	/**
	 * editor.View.Movie
	 * 
	 * Altered version of wp.media.view.Attachment
	 *
	 * @class
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	editor.View.Movie = wp.media.View.extend({

		tagName:   'li',

		className: 'attachment movie',

		template:  wp.media.template( 'attachment' ),

		initialize: function() {

			var options = _.defaults( this.options, {
				rerenderOnModelChange: true
			} );

			if ( options.rerenderOnModelChange ) {
				this.model.on( 'change', this.render, this );
			}
		},

		/**
		 * Render the View
		 * 
		 * @since    2.2
		 * 
		 * @return   object    Returns itself to allow chaining
		 */
		render: function() {

			var options = _.extend(
				this.options,
				_.defaults(
					this.model.toJSON(),
					this.model.defaults
				  )
			);

			options.can = {};
			if ( options.nonces ) {
				options.can.remove = !! options.nonces['delete'];
				options.can.save = !! options.nonces.update;
			}

			this.views.detach();
			this.$el.html( this.template( options ) );

			// Update the save status.
			this.updateSave();

			this.views.render();

			return this;
		},

		/**
		 * Update changed metadata
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS 'Change' event
		 * 
		 * @return   void
		 */
		updateMeta: function( event ) {

			var $meta = this.$( event.target ).closest( '.meta-data-field' ), meta, value;

			if ( ! $meta.length ) {
				return;
			}

			meta  = event.target.name.replace( /meta\[(.*)\]/g, '$1' );
			value = event.target.value;

			if ( this.model.get( 'meta' ).get( meta ) !== value ) {
				this.save( this.model.get( 'meta' ), meta, value );
			}
		},

		/**
		 * Update changed details
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS 'Change' event
		 * 
		 * @return   void
		 */
		updateDetails: function( event ) {

			var $detail = this.$( event.target ).closest( '.wpmoly-details-item input, .wpmoly-details-item select' ), detail, value;

			if ( ! $detail.length ) {
				return;
			}

			detail = event.target.name.replace( /wpmoly_details\[(.*)\]/g, '$1' );
			if ( true === event.target.multiple ) {
				value = event.target.selectedOptions;
				value = _.map( value, function( v ) { return v.value; } );
			} else {
				value = event.target.value;
			}

			if ( this.model.get( 'details' ).get( detail ) !== value ) {
				this.save( this.model.get( 'details' ), detail, value );
			}
		},

		/**
		 * Update changed metadata
		 * 
		 * @since    2.2
		 * 
		 * @param    object    JS 'Change' event
		 * 
		 * @return   void
		 */
		updateSetting: function( event ) {

			var $setting = $( event.target ).closest('[data-setting]'),
				setting, value;

			if ( ! $setting.length ) {
				return;
			}

			setting = $setting.data('setting');
			value   = event.target.value;

			if ( this.model.get( setting ) !== value ) {
				this.save( setting, value );
			}
		},

		/**
		 * Pass all the arguments to the model's save method.
		 *
		 * Records the aggregate status of all save requests and updates the
		 * view's classes accordingly.
		 */
		save: function( model, attribute, value ) {

			var view = this,
			    save = this._save = this._save || { status: 'ready' },
			 request = model.save( attribute, value ),
			requests = save.requests ? $.when( request, save.requests ) : request;

			// If we're waiting to remove 'Saved.', stop.
			if ( save.savedTimer ) {
				clearTimeout( save.savedTimer );
			}

			this.updateSave( 'waiting' );
			save.requests = requests;
			requests.always( function() {
				// If we've performed another request since this one, bail.
				if ( save.requests !== requests ) {
					return;
				}

				view.updateSave( requests.state() === 'resolved' ? 'complete' : 'error' );
				save.savedTimer = setTimeout( function() {
					view.updateSave( 'ready' );
					delete save.savedTimer;
				}, 2000 );
			});
		},

		/**
		 * Update Attachment/Movie View status
		 * 
		 * @since    2.2
		 * 
		 * @param    string    status
		 * 
		 * @return   object    Returns itself to allow chaining
		 */
		updateSave: function( status ) {

			var save = this._save = this._save || { status: 'ready' };

			if ( status && status !== save.status ) {
				this.$el.removeClass( 'save-' + save.status );
				save.status = status;
			}

			this.$el.addClass( 'save-' + save.status );
			return this;
		},

		/*updateAll: function() {

			var $settings = this.$('[data-setting]'),
				model = this.model,
				changed;

			changed = _.chain( $settings ).map( function( el ) {
				var $input = $('input, textarea, select, [value]', el ),
					setting, value;

				if ( ! $input.length ) {
					return;
				}

				setting = $(el).data('setting');
				value = $input.val();

				// Record the value if it changed.
				if ( model.get( setting ) !== value ) {
					return [ setting, value ];
				}
			}).compact().object().value();

			if ( ! _.isEmpty( changed ) ) {
				model.save( changed );
			}
		},*/
	});

	_.extend( editor.View, {

		/**
		 * A similar view to media.view.Attachment.Details
		 * for use in the Edit Attachment modal.
		 *
		 * @constructor
		 * @augments wp.media.view.Attachment.Details
		 * @augments wp.media.view.Attachment
		 * @augments wp.media.View
		 * @augments wp.Backbone.View
		 * @augments Backbone.View
		 */
		TwoColumn: editor.View.Movie.extend({

			tagName:   'div',

			className: 'attachment-details movie-metadata',

			template:   wp.media.template( 'wpmoly-edit-movie-frame-content' ),

			events: {
				'change .meta-data-field':            'updateMeta',
				'change .wpmoly-details-item input':  'updateDetails',
				'change .wpmoly-details-item select': 'updateDetails',
			},
			
			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   object    this
			 */
			initialize: function() {

				this.on( 'ready', this.setSelects, this );
				this.on( 'ready', this.resizeImages, this );
				this.on( 'ready', this.resizeTextareas, this );

				var options = _.defaults( this.options, {
					rerenderOnModelChange: true
				} );

				editor.View.Movie.prototype.initialize.apply( this, arguments );

				return this;
			},

			/**
			 * Render the View
			 * 
			 * @since    2.2
			 * 
			 * @return   boolean
			 */
			render: function() {

				editor.View.Movie.prototype.render.apply( this, arguments );

				this.setSelects();
				this.resizeImages();
				this.resizeTextareas();
			},

			/**
			 * Prepare details select elements using Select2
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			setSelects: function() {

				_.each( this.$( '.redux-container-select select' ), function( select ) {

					var  id = select.name.replace( /wpmoly_details\[(.*?)\](\[\])?/g, '$1' ),
					 detail = this.model.get( 'details' );

					if ( _.isFunction( detail.get ) ) {

						detail = detail.get( id );
						if ( _.isDefined( detail ) && '' != detail ) {
							this.$( select ).val( detail );
						}

						this.$( select ).select2();
					}
				}, this );
			},

			/**
			 * Resize the Metadata Modal Posters to show a nice-looking
			 * editor.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			resizeImages: function() {

				var $poster = this.$( '.poster' ),
				   $posters = this.$( '.posters .additional-poster' ),
				 $backdrops = this.$( '.backdrops .image' ),
				    _height = Math.floor( $poster.width() * 1.5 );

				$poster.css({
					height: _height
				});

				// Resize posters
				_.each( $posters, function( poster, index ) {

					var $this = this.$( poster ),
					   height = Math.floor( _height / 4 );

					if ( ! index ) {
						if ( _height > height * 4 )
							height = ( height * 2 ) + ( _height - ( height * 4 ) );
						else
							height *= 2;
					}

					$this.css({
						height: height
					});

					if ( $this.hasClass( 'more' ) )
						$this.find( 'a' ).css( { lineHeight: height + 'px' } );
				} );

				// Resize backdrops
				_.each( $backdrops, function( backdrop, index ) {

					var $this = this.$( backdrop ),
					   height = Math.floor( $this.width() / 1.7 );

					if ( index > 2 ) {
						height = $this.width();
					}

					$this.css({
						height: height
					});

					if ( $this.hasClass( 'more' ) )
						$this.find( 'a' ).css( { lineHeight: height + 'px' } );
				} );

				if ( 2 > $backdrops.length )
					return;

				// Fix middle backdrop
				var backdrop = $backdrops.get( 2 ),
				       first = $backdrops.get( 0 ),
				        prev = $backdrops.get( 1 ),
				        next = $backdrops.get( 3 );

				if ( undefined === backdrop )
					return;

				var height = Math.floor( ( prev.offsetHeight - first.offsetHeight ) + ( prev.offsetWidth / 3 ) );

				this.$( backdrop ).css({
					height: height,
					marginTop: 0 - ( prev.offsetHeight - this.$( backdrop ).height() )
				});
				
			},

			/**
			 * Resize Metadata text areas to fit their full height
			 * 
			 * @since    2.2
			 * 
			 * @return   boolean
			 */
			resizeTextareas: function() {

				var elems = this.$( 'textarea' );
				_.each( elems, function( elem ) {
					if ( elem.scrollHeight )
						elem.style.height = elem.scrollHeight + 'px';
				} );
			},

			
		}),

		PreviewMovie: editor.View.Movie.extend({

			tagName:   'div',

			className: 'movie-preview',

			template:   wp.media.template( 'wpmoly-preview-movie-frame-content' ),

			events: {
				
			},
			
			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   object    this
			 */
			initialize: function() {

				var options = _.defaults( this.options, {
					rerenderOnModelChange: true
				} );

				this.on( 'ready', _.debounce( this.fixModal, 50 ), this );

				// Event handlers
				_.bindAll( this, 'fixModal' );

				this.$window = $( window );
				var self = this;
				this.$window.off( 'resize.movie-preview-modal' ).on( 'resize.movie-preview-modal', _.debounce( this.fixModal, 50 ) );

				editor.View.Movie.prototype.initialize.apply( this, arguments );

				return this;
			},

			/**
			 * Modal shouldn't not have full height on smaller screen
			 * like 1280*1024.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			fixModal: function() {

				var modal = this.controller.$el.parents( '.media-modal' ),
				    width = this.$( '.movie-preview-poster' ).width(),
				   height = Math.floor( width * 1.5 ),
				      max = document.body.clientHeight - 40;

				modal.css({
					height: height,
					maxHeight: max
				});
			},

			formatDetails: function() {

				var details = this.model.get( 'details' ).toJSON();

				details.status = wpmoly.l10n.details[ details.status ] || '−';
				details.rating = wpmoly.l10n.details[ details.rating ] || '−';

				var getLanguage = function( code ) {
					return _.isUndefined( wpmoly.l10n.languages[ code.trim() ] ) ? code.trim() : wpmoly.l10n.languages[ code.trim() ].text;
				};

				var getDetail = function( detail ) {
					return _.isUndefined( wpmoly.l10n.details[ detail.trim() ] ) ? detail.trim() : wpmoly.l10n.details[ detail.trim() ];
				};

				details.media     = _.map( details.media, getDetail ).join( ', ' )       || '−';
				details.format    = _.map( details.format, getDetail ).join( ', ' )      || '−';
				details.language  = _.map( details.language, getLanguage ).join( ', ' )  || '−';
				details.subtitles = _.map( details.subtitles, getLanguage ).join( ', ' ) || '−';

				return details;
			},

			/**
			 * Render the View
			 * 
			 * @since    2.2
			 * 
			 * @return   Returns itself to allow chaining
			 */
			render: function() {

				var options = _.extend( this.model.get( 'formatted' ).toJSON(), this.formatDetails(), {
					year: this.model.get( 'meta' ).get( 'year' )
				});

				this.$el.html( this.template( options ) );

				return this;

			},
		} )

	} );

	_.extend( editor.View, {

		/**
		 * A frame for editing the details of a specific media item.
		 *
		 * Opens in a modal by default.
		 *
		 * Requires an attachment model to be passed in the options hash under `model`.
		 *
		 * @constructor
		 * @augments wp.media.view.Frame
		 * @augments wp.media.View
		 * @augments wp.Backbone.View
		 * @augments Backbone.View
		 * @mixes wp.media.controller.StateMachine
		 */
		MovieModal: wp.media.view.MediaFrame.extend({

			className: 'edit-attachment-frame edit-movie-frame',

			template: wp.media.template( 'wpmoly-edit-movie-frame' ),

			regions:   [ 'title', 'content' ],

			events: {
				'click .switch-mode': 'switchMode',
				'click .left':        'previousMediaItem',
				'click .right':       'nextMediaItem',
			},

			/**
			 * Initialize the View
			 * 
			 * @since    2.2
			 * 
			 * @return   boolean
			 */
			initialize: function() {

				wp.media.view.Frame.prototype.initialize.apply( this, arguments );

				_.defaults( this.options, {
					modal: true,
					mode:  [
						'edit-movie'
					],
					state: 'edit-movie'
				});

				this.mode = this.options.mode;

				this.controller = this.options.controller;
				//this.editorRouter = this.controller.editorRouter;
				this.library = this.options.library;

				if ( this.options.model ) {
					this.model = this.options.model;
				}

				this.bindHandlers();
				this.createStates();
				this.createModal();

				this.title.mode( 'default' );
				this.toggleNav();
			},

			/**
			 * Bind the View's event handlers
			 * 
			 * @since    2.2
			 * 
			 * @return   boolean
			 */
			bindHandlers: function() {

				this.on( 'title:create:default', this.createTitle, this );
				this.on( 'content:create:edit-metadata', this.editMetadataMode, this );
				this.on( 'content:create:preview-movie', this.PreviewMovieMode, this );
				this.on( 'close', this.detach );
			},

			/**
			 * Create the modal window
			 * 
			 * @since    2.2
			 * 
			 * @return   boolean
			 */
			createModal: function() {

				//var self = this;

				// Initialize modal container view.
				if ( this.options.modal ) {

					this.modal = new wp.media.view.Modal({
						controller: this,
						title:      'Title'
					});

					// Customize the media frame
					this.modal.on( 'ready', this.prepareModal, this );
					this.modal.on( 'open',  this.openModal,    this );
					this.modal.on( 'close', this.closeModal,   this );

					// Set this frame as the modal's content.
					this.modal.content( this );
					this.modal.open();
				}
			},

			/**
			 * Add the default states to the frame.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			createStates: function() {

				this.states.add( [
					new controller.EditMovie( { model: this.model } ),
					new controller.PreviewMovie( { model: this.model } )
				] );
			},

			/**
			 * Content region rendering callback for the `edit-metadata` mode.
			 * 
			 * @since    2.2
			 *
			 * @param    object    contentRegion Basic object with a `view` property, which should be set with the proper region view.
			 * 
			 * @return   void
			 */
			editMetadataMode: function( contentRegion ) {

				contentRegion.view = new editor.View.TwoColumn({
					controller: this,
					model:      this.model
				});

				// Update browser url when navigating media details
				if ( this.model ) {
					//this.editorRouter.navigate( this.editorRouter.baseUrl( '?item=' + this.model.id ) );
				}
			},

			/**
			 * Content region rendering callback for the `preview-movie` mode.
			 * 
			 * @since    2.2
			 *
			 * @param    object    contentRegion Basic object with a `view` property, which should be set with the proper region view.
			 * 
			 * @return   void
			 */
			PreviewMovieMode: function( contentRegion ) {

				contentRegion.view = new editor.View.PreviewMovie({
					controller: this,
					model:      this.model
				});
			},

			/**
			 * Add custom classes to the modal container to allow
			 * frame customization
			 * 
			 * @since    2.2
			 * 
			 * @return   Returns itself to allow chaining
			 */
			prepareModal: function() {

				this.modal.$( '.media-modal' ).addClass( 'movie-modal ' + this.options.mode + '-modal' );

				return this;
			},

			/**
			 * Open the Modal.
			 * 
			 * Bind event and store the scroll position.
			 * 
			 * @since    2.2
			 * 
			 * @return   Returns itself to allow chaining
			 */
			openModal: function() {

				// Bind keydown event
				$( 'body' ).on( 'keydown.media-modal', _.bind( this.keyEvent, this ) );

				// Adapt nav menu
				this.toggleNav();

				// Store the scroll position
				var $body = $( 'html,body' );
				this._scrollTop = $body.scrollTop();

				$body.scrollTop( 0 );

				return this;
			},

			/**
			 * Close the Modal.
			 * 
			 * Completely destroy the modal DOM element when closing it.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			closeModal: function() {

				// remove the keydown event
				$( 'body' ).off( 'keydown.media-modal' );

				// Scroll back to the initial position
				$( 'html,body' ).scrollTop( this._scrollTop || 0 );

				this.modal.remove();
				this.resetRoute();
			},

			/**
			 * Disable nav menu items depending on collection
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			toggleNav: function() {

				this.$('.left').toggleClass( 'disabled', ! this.hasPrevious() );
				this.$('.right').toggleClass( 'disabled', ! this.hasNext() );
			},

			/**
			 * Rerender the view.
			 * 
			 * @since    2.2
			 * 
			 * @param    boolean    force rerendering
			 * 
			 * @return   void
			 */
			rerender: function( force ) {

				if ( true === force ) {

					this.content.render();
					this.toggleNav();

					return;
				}

				// Only rerender the `content` region.
				if ( 'preview-movie' == this.content.mode() ) {
					console.log( 1 );
					this.content.mode( 'preview-movie' );
				} else if ( 'edit-metadata' !== this.content.mode() ) {
					console.log( 2 );
					this.content.mode( 'edit-metadata' );
				} else {
					console.log( 3 );
					this.content.render();
				}

				this.toggleNav();
			},

			switchMode: function( event ) {

				var state = this.$( event.currentTarget ).attr( 'data-state' );

				if ( _.isUndefined( this.states.get( state ) ) ) {
					return;
				}

				console.log( state );
				this.setState( state );
			},

			/**
			 * Switch modal to the previous model
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			previousMediaItem: function() {

				if ( ! this.hasPrevious() ) {
					this.$( '.left' ).blur();
					return;
				}
				this.model = this.library.at( this.getCurrentIndex() - 1 );
				this.rerender( force = true );
				this.$( '.left' ).focus();
			},

			/**
			 * Switch modal to the next model
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			nextMediaItem: function() {

				if ( ! this.hasNext() ) {
					this.$( '.right' ).blur();
					return;
				}

				this.model = this.library.at( this.getCurrentIndex() + 1 );
				this.rerender( force = true );
				this.$( '.right' ).focus();
			},

			/**
			 * Get current model's index in collection.
			 * 
			 * @since    2.2
			 * 
			 * @return   boolean
			 */
			getCurrentIndex: function() {

				return this.library.indexOf( this.model );
			},

			/**
			 * Make sure there is a model in collection after the 
			 * current one.
			 * 
			 * @since    2.2
			 * 
			 * @return   boolean
			 */
			hasNext: function() {

				return ( this.getCurrentIndex() + 1 ) < this.library.length;
			},

			/**
			 * Make sure there is a model in collection before the 
			 * current one.
			 * 
			 * @since    2.2
			 * 
			 * @return   boolean
			 */
			hasPrevious: function() {

				return ( this.getCurrentIndex() - 1 ) > -1;
			},

			/**
			 * Respond to the keyboard events: right arrow, left arrow, except when
			 * focus is in a textarea or input field.
			 * 
			 * @since    2.2
			 * 
			 * @param    object    JS 'Keydown' event
			 * 
			 * @return   void
			 */
			keyEvent: function( event ) {

				if ( ( 'INPUT' === event.target.nodeName ||
				    'TEXTAREA' === event.target.nodeName ) &&
				 ! ( event.target.readOnly || event.target.disabled ) ) {
					return;
				}

				// The right arrow key
				if ( 39 === event.keyCode ) {
					this.nextMediaItem();
				}

				// The left arrow key
				if ( 37 === event.keyCode ) {
					this.previousMediaItem();
				}
			},

			/**
			 * Reset Router
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			resetRoute: function() {
				//this.editorRouter.navigate( this.editorRouter.baseUrl( '' ) );
			}
		})

	} );

}( jQuery, _, Backbone, wp, wpmoly ));
