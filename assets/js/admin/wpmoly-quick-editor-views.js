
window.wpmoly = window.wpmoly || {};

(function( $, _, Backbone, wp, wpmoly ) {

	var editor = wpmoly.editor;

	editor.controller = {

		/**
		 * wp.media.controller.EditAttachmentMetadata
		 *
		 * A state for editing an attachment's metadata.
		 *
		 * @constructor
		 * @augments wp.media.controller.State
		 * @augments Backbone.Model
		 */
		EditMovieMetadata: wp.media.controller.State.extend({
			defaults: {
				id:      'edit-movie',
				title:   'Edit Movie Metadata',
				content: 'edit-metadata',
				menu:    false,
				toolbar: false,
				router:  false
			}
		})
	};

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

		openMetaModal: function( event ) {

			event.preventDefault();

			var id = parseInt( event.currentTarget.dataset.id ),
			 model = editor.models.movies.get( id );

			if ( undefined == model )
				return;

			editor.frame = new editor.View.EditMovies( {
				frame:  'select',
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

		attributes: function() {
			return {
				'tabIndex':     0,
				'role':         'checkbox',
				'aria-label':   this.model.get( 'title' ),
				'aria-checked': false,
				'data-id':      this.model.get( 'id' )
			};
		},

		events: {
			'click .js--select-attachment':   'toggleSelectionHandler',
			'change [data-setting]':          'updateSetting',
			'change [data-setting] input':    'updateSetting',
			'change [data-setting] select':   'updateSetting',
			'change [data-setting] textarea': 'updateSetting',
			'click .close':                   'removeFromLibrary',
			'click a':                        'preventDefault',
			'keydown .close':                 'removeFromLibrary',
		},

		initialize: function() {
			var selection = this.options.selection,
				options = _.defaults( this.options, {
					rerenderOnModelChange: true
				} );

			if ( options.rerenderOnModelChange ) {
				this.model.on( 'change', this.render, this );
			}
			this.model.on( 'change:title', this._syncTitle, this );
			this.model.on( 'change:caption', this._syncCaption, this );
			this.model.on( 'change:artist', this._syncArtist, this );
			this.model.on( 'change:album', this._syncAlbum, this );

			this.listenTo( this.controller, 'attachment:compat:waiting attachment:compat:ready', this.updateSave );
		},
		/**
		 * @returns {wp.media.view.Attachment} Returns itself to allow chaining
		 */
		dispose: function() {
			var selection = this.options.selection;

			// Make sure all settings are saved before removing the view.
			this.updateAll();

			if ( selection ) {
				selection.off( null, null, this );
			}
			/**
			 * call 'dispose' directly on the parent class
			 */
			wp.media.View.prototype.dispose.apply( this, arguments );
			return this;
		},
		/**
		 * @returns {wp.media.view.Attachment} Returns itself to allow chaining
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

			if ( this.controller.state().get('allowLocalEdits') ) {
				options.allowLocalEdits = true;
			}

			if ( options.uploading && ! options.percent ) {
				options.percent = 0;
			}

			this.views.detach();
			this.$el.html( this.template( options ) );

			// Update the save status.
			this.updateSave();

			this.views.render();

			return this;
		},

		/**
		 * @param {Backbone.Model} model
		 * @param {Backbone.Collection} collection
		 */
		details: function( model, collection ) {
			var selection = this.options.selection,
				details;

			if ( selection !== collection ) {
				return;
			}

			details = selection.single();
			this.$el.toggleClass( 'details', details === this.model );
		},
		/**
		 * @param {Object} event
		 */
		preventDefault: function( event ) {
			event.preventDefault();
		},

		/**
		 * @param {Object} event
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
		save: function() {
			var view = this,
				save = this._save = this._save || { status: 'ready' },
				request = this.model.save.apply( this.model, arguments ),
				requests = save.requests ? $.when( request, save.requests ) : request;

			// If we're waiting to remove 'Saved.', stop.
			if ( save.savedTimer ) {
				clearTimeout( save.savedTimer );
			}

			this.updateSave('waiting');
			save.requests = requests;
			requests.always( function() {
				// If we've performed another request since this one, bail.
				if ( save.requests !== requests ) {
					return;
				}

				view.updateSave( requests.state() === 'resolved' ? 'complete' : 'error' );
				save.savedTimer = setTimeout( function() {
					view.updateSave('ready');
					delete save.savedTimer;
				}, 2000 );
			});
		},
		/**
		 * @param {string} status
		 * @returns {wp.media.view.Attachment} Returns itself to allow chaining
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

		updateAll: function() {
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
		},
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

			template:   wp.media.template( 'movie-metadata-quickedit' ),

			attributes: function() {
				return {
					'tabIndex':     0,
					'data-id':      this.model.get( 'id' )
				};
			},

			events: {
				'change [data-setting]':          'updateSetting',
				'change [data-setting] input':    'updateSetting',
				'change [data-setting] select':   'updateSetting',
				'change [data-setting] textarea': 'updateSetting',
				'click .delete-attachment':       'deleteAttachment',
				'click .trash-attachment':        'trashAttachment',
				'click .untrash-attachment':      'untrashAttachment',
				'click .edit-attachment':         'editAttachment',
				'click .refresh-attachment':      'refreshAttachment',
				'keydown':                        'toggleSelectionHandler'
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
				this.on( 'ready', this.resizePosters, this );

				editor.View.Movie.prototype.initialize.apply( this, arguments );

				return this;
			},

			render: function() {

				editor.View.Movie.prototype.render.apply( this, arguments );

				this.setSelects();
				this.resizePosters();
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
					details = this.model.get( 'details' );

					if ( undefined !== details[ id ] && '' != details[ id ] ) {
						this.$( select ).val( details[ id ] );
					}

					this.$( select ).select2();
				}, this );
			},

			/**
			 * Resize the Metadata Modal Posters to show a nice-looking
			 * grid.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			resizePosters: function() {

				var $posters = this.$( '.additional-poster' ),
				     $poster = this.$( '.poster' ),
				  $container = this.$( '.posters' ),
				           w = $container.width(),
				           h = $container.height(),
				      margin = Math.ceil( w * 0.01 );

				// Resize main poster (featured image)
				var width = Math.ceil( ( w * 0.499 ) - margin ),
				   height = Math.ceil( width * 1.5 );

				// Avoid resize a to 0
				if ( ! width || ! height )
					return;

				$poster.css({
					height: height,
					width: width
				});

				if ( ! $posters.length )
					return;

				// Resize small posters
				var width = Math.ceil( ( width - margin ) * 0.5 ),
				   height = Math.ceil( width * 1.5 );
				$posters.css({
					height: height,
					width: width
				});

				// Adjust margins
				var testee = $poster.width();
				_.each( $posters, function( poster, index ) {
					var marginLeft = margin + 1,
					   marginRight = 0;
					if ( testee >= poster.offsetLeft ) {
						marginRight = marginLeft - 1;
						 marginLeft = 0;
					}
					poster.style.marginLeft   = marginLeft + 'px';
					poster.style.marginRight  = marginRight + 'px';
					poster.style.marginBottom = margin + 1 + 'px';
				} );
			},

			/**
			* @param {Object} event
			*/
			deleteAttachment: function( event ) {
				event.preventDefault();

				if ( confirm( l10n.warnDelete ) ) {
					this.model.destroy();
					// Keep focus inside media modal
					// after image is deleted
					this.controller.modal.focusManager.focus();
				}
			},
			/**
			* @param {Object} event
			*/
			trashAttachment: function( event ) {
				var library = this.controller.library;
				event.preventDefault();

				if ( wp.media.view.settings.mediaTrash &&
					'edit-metadata' === this.controller.content.mode() ) {

					this.model.set( 'status', 'trash' );
					this.model.save().done( function() {
						library._requery( true );
					} );
				}  else {
					this.model.destroy();
				}
			},
			/**
			* @param {Object} event
			*/
			untrashAttachment: function( event ) {
				var library = this.controller.library;
				event.preventDefault();

				this.model.set( 'status', 'inherit' );
				this.model.save().done( function() {
					library._requery( true );
				} );
			},
			/**
			* @param {Object} event
			*/
			editAttachment: function( event ) {
				var editState = this.controller.states.get( 'edit-image' );
				if ( window.imageEdit && editState ) {
					event.preventDefault();

					editState.set( 'image', this.model );
					this.controller.setState( 'edit-image' );
				} else {
					this.$el.addClass('needs-refresh');
				}
			},
			/**
			* @param {Object} event
			*/
			refreshAttachment: function( event ) {
				this.$el.removeClass('needs-refresh');
				event.preventDefault();
				this.model.fetch();
			},
			/**
			* When reverse tabbing(shift+tab) out of the right details panel, deliver
			* the focus to the item in the list that was being edited.
			*
			* @param {Object} event
			*/
			toggleSelectionHandler: function( event ) {
				if ( 'keydown' === event.type && 9 === event.keyCode && event.shiftKey && event.target === this.$( ':tabbable' ).get( 0 ) ) {
					this.controller.trigger( 'attachment:details:shift-tab', event );
					return false;
				}

				if ( 37 === event.keyCode || 38 === event.keyCode || 39 === event.keyCode || 40 === event.keyCode ) {
					this.controller.trigger( 'attachment:keydown:arrow', event );
					return;
				}
			}
		})

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
		EditMovies: wp.media.view.MediaFrame.extend({

			className: 'edit-attachment-frame edit-movie-frame',

			template: wp.media.template( 'edit-attachment-frame' ),

			regions:   [ 'title', 'content' ],

			events: {
				'click .left':  'previousMediaItem',
				'click .right': 'nextMediaItem',
			},

			initialize: function() {

				wp.media.view.Frame.prototype.initialize.apply( this, arguments );

				_.defaults( this.options, {
					modal: true,
					state: 'edit-movie'
				});

				this.controller = this.options.controller;
				//this.gridRouter = this.controller.gridRouter;
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

			bindHandlers: function() {
				// Bind default title creation.
				this.on( 'title:create:default', this.createTitle, this );

				// Close the modal if the attachment is deleted.
				//this.listenTo( this.model, 'change:status destroy', this.close, this );

				this.on( 'content:create:edit-metadata', this.editMetadataMode, this );
				/*this.on( 'content:create:edit-image', this.editImageMode, this );
				this.on( 'content:render:edit-image', this.editImageModeRender, this );*/
				this.on( 'close', this.detach );
			},

			createModal: function() {

				var self = this;

				// Initialize modal container view.
				if ( this.options.modal ) {

					this.modal = new wp.media.view.Modal({
						controller: this,
						title:      'Title'
					});

					this.modal.on( 'open', function () {
						$( 'body' ).on( 'keydown.media-modal', _.bind( self.keyEvent, self ) );
						this.toggleNav();
					}, this );

					// Completely destroy the modal DOM element when closing it.
					this.modal.on( 'close', function() {

						self.modal.remove();
						$( 'body' ).off( 'keydown.media-modal' ); /* remove the keydown event */
						self.resetRoute();
					} );

					// Set this frame as the modal's content.
					this.modal.content( this );
					this.modal.open();
				}
			},

			/**
			* Add the default states to the frame.
			*/
			createStates: function() {

				this.states.add( [
					new editor.controller.EditMovieMetadata( { model: this.model } )
				] );
			},

			/**
			* Content region rendering callback for the `edit-metadata` mode.
			*
			* @param {Object} contentRegion Basic object with a `view` property, which
			*                               should be set with the proper region view.
			*/
			editMetadataMode: function( contentRegion ) {

				contentRegion.view = new editor.View.TwoColumn({
					controller: this,
					model:      this.model
				});

				/**
				* Attach a subview to display fields added via the
				* `attachment_fields_to_edit` filter.
				*/
				/*contentRegion.view.views.set( '.attachment-compat', new wp.media.view.AttachmentCompat({
					controller: this,
					model:      this.model
				}) );*/

				// Update browser url when navigating media details
				if ( this.model ) {
					//this.gridRouter.navigate( this.gridRouter.baseUrl( '?item=' + this.model.id ) );
				}
			},

			/**
			* Render the EditImage view into the frame's content region.
			*
			* @param {Object} contentRegion Basic object with a `view` property, which
			*                               should be set with the proper region view.
			*/
			/*editImageMode: function( contentRegion ) {

				var editImageController = new wp.media.controller.EditImage( {
					model: this.model,
					frame: this
				} );
				// Noop some methods.
				editImageController._toolbar = function() {};
				editImageController._router = function() {};
				editImageController._menu = function() {};

				contentRegion.view = new wp.media.view.EditImage.Details( {
					model: this.model,
					frame: this,
					controller: editImageController
				} );
			},

			editImageModeRender: function( view ) {
				view.on( 'ready', view.loadEditor );
			},*/

			toggleNav: function() {
				this.$('.left').toggleClass( 'disabled', ! this.hasPrevious() );
				this.$('.right').toggleClass( 'disabled', ! this.hasNext() );
			},

			/**
			* Rerender the view.
			*/
			rerender: function() {
				// Only rerender the `content` region.
				if ( this.content.mode() !== 'edit-metadata' ) {
					this.content.mode( 'edit-metadata' );
				} else {
					this.content.render();
				}

				this.toggleNav();
			},

			/**
			* Click handler to switch to the previous media item.
			*/
			previousMediaItem: function() {
				if ( ! this.hasPrevious() ) {
					this.$( '.left' ).blur();
					return;
				}
				this.model = this.library.at( this.getCurrentIndex() - 1 );
				this.rerender();
				this.$( '.left' ).focus();
			},

			/**
			* Click handler to switch to the next media item.
			*/
			nextMediaItem: function() {
				if ( ! this.hasNext() ) {
					this.$( '.right' ).blur();
					return;
				}
				console.log( this.library );
				this.model = this.library.at( this.getCurrentIndex() + 1 );
				this.rerender();
				this.$( '.right' ).focus();
			},

			getCurrentIndex: function() {
				return this.library.indexOf( this.model );
			},

			hasNext: function() {
				return ( this.getCurrentIndex() + 1 ) < this.library.length;
			},

			hasPrevious: function() {
				return ( this.getCurrentIndex() - 1 ) > -1;
			},

			/**
			* Respond to the keyboard events: right arrow, left arrow, except when
			* focus is in a textarea or input field.
			*/
			keyEvent: function( event ) {

				if ( ( 'INPUT' === event.target.nodeName || 'TEXTAREA' === event.target.nodeName ) && ! ( event.target.readOnly || event.target.disabled ) ) {
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

			resetRoute: function() {
				//this.gridRouter.navigate( this.gridRouter.baseUrl( '' ) );
			}
		})

	} );

}( jQuery, _, Backbone, wp, wpmoly ));
