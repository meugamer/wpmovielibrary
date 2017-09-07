wpmoly = window.wpmoly || {};

_.extend( wpmoly.view, {

	ArchivePages : Backbone.View.extend({

		el : '#wpmoly-archives-page-type',

		events : {
			'click #wpmoly-edit-archive-page'   : 'open',
			'click #wpmoly-cancel-archive-page' : 'close',
			'click #wpmoly-save-archive-page'   : 'update',
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   Returns itself to allow chaining.
		 */
		initialize : function() {

			this.settings = this.$( '#wpmoly-edit-archive-page-type' );
		},

		/**
		 * Open the archive type settings block.
		 *
		 * @since    3.0
		 *
		 * @param    object    JS 'click' Event
		 *
		 * @return   Returns itself to allow chaining.
		 */
		open : function( event ) {

			event.preventDefault();

			var $elem = this.$( event.currentTarget );

			if ( this.settings.is( ':hidden' ) ) {
				this.settings.slideDown( 'fast' );
				$elem.hide();
			}
		},

		/**
		 * Close the archive type settings block.
		 *
		 * @since    3.0
		 *
		 * @param    object    JS 'click' Event
		 *
		 * @return   Returns itself to allow chaining.
		 */
		close : function( event ) {

			event.preventDefault();

			var $trigger = this.$( '#wpmoly-edit-archive-page' );

			if ( ! this.settings.is( ':hidden' ) ) {
				this.settings.slideUp( 'fast' );
				$trigger.show();
			}
		},

		/**
		 * Update the archive type.
		 *
		 * @since    3.0
		 *
		 * @param    object    JS 'click' Event
		 *
		 * @return   Returns itself to allow chaining.
		 */
		update : function( event ) {

			event.preventDefault();

			var $selected = this.$( '#wpmoly-archive-page-types option:selected' ),
			     $trigger = this.$( '#wpmoly-edit-archive-page' ),
			        $text = this.$( '#wpmoly-archive-page-type' );

			if ( $selected.text() != $text.text() ) {
				$text.text( $selected.text() );
			}

			if ( ! this.settings.is( ':hidden' ) ) {
				this.settings.slideUp( 'fast' );
				$trigger.show();
			}
		},

	}),

} );
