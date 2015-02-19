
window.wpmoly = window.wpmoly || {};

(function( $, _, Backbone, wp, wpmoly ) {

	var editor = wpmoly.editor;

	_.extend( editor.controller, {

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
	} );

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
		TwoColumn: wp.media.view.Attachment.extend({

			tagName:   'div',

			className: 'attachment-details',

			template:   wp.media.template( 'attachment-details-two-column' ),

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

			initialize: function() {
				this.options = _.defaults( this.options, {
					rerenderOnModelChange: false
				});

				//this.on( 'ready', this.initialFocus );
				// Call 'initialize' directly on the parent class.
				wp.media.view.Attachment.prototype.initialize.apply( this, arguments );
			},

			/*initialFocus: function() {
				if ( ! isTouchDevice ) {
					this.$( ':input' ).eq( 0 ).focus();
				}
			},*/
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

			className: 'edit-attachment-frame',

			template: wp.media.template( 'edit-attachment-frame' ),

			regions:   [ 'title', 'content' ],

			events: {
				'click .left':  'previousMediaItem',
				'click .right': 'nextMediaItem'
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
					} );

					// Completely destroy the modal DOM element when closing it.
					this.modal.on( 'close', function() {

						//self.modal.remove();
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
				contentRegion.view.views.set( '.attachment-compat', new wp.media.view.AttachmentCompat({
					controller: this,
					model:      this.model
				}) );

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
			/*rerender: function() {
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
			/*previousMediaItem: function() {
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
			/*nextMediaItem: function() {
				if ( ! this.hasNext() ) {
					this.$( '.right' ).blur();
					return;
				}
				this.model = this.library.at( this.getCurrentIndex() + 1 );
				this.rerender();
				this.$( '.right' ).focus();
			},*/

			getCurrentIndex: function() {
				return 2;
				//return this.library.indexOf( this.model );
			},

			hasNext: function() {
				return ( this.getCurrentIndex() + 1 ) < 6;//this.library.length;
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
