
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var metabox = wpmoly.metabox = function() {

		metabox.models.metabox = new metabox.Model.Metabox();
		metabox.views.metabox = new metabox.View.Metabox( { el: '#wpmoly-meta', collection: metabox.models.metabox } );

		var panels = document.getElementsByClassName( 'tab' ),
		    states = [];

		_.each( panels, function( panel ) {
			states.push( new metabox.Model.State({
				id: panel.id.replace( /wpmoly\-meta\-(.*)/g, '$1' ),
				title: panel.textContent.trim(),
				icon: panel.firstChild.firstChild.className,
				label: '',
				labeltitle: ''
			}) );
		} );

		metabox.models.metabox.add( states );

	};

	_.extend( metabox, { models: {}, views: {}, Model: {}, View: {} } );

	_.extend( metabox.Model, {

		/**
		 * WPMOLY Backbone State Model
		 * 
		 * Used to control a set of Menu and Panel Views.
		 * 
		 * @since    2.2
		 */
		State: Backbone.Model.extend({

			defaults: {
				id: '',
				title: '',
				icon: '',
				label: '',
				labeltitle: ''
			},

			_menu: '',

			_panel: ''
		})
	} );

	_.extend( metabox.Model, {

		/**
		 * WPMOLY Backbone Metabox Model
		 * 
		 * Used to control a collection of States.
		 * 
		 * @since    2.2
		 */
		Metabox: Backbone.Collection.extend({

			model: metabox.Model.State,

			/**
			 * Initialize Model.
			 * 
			 * @since    2.2
			 * 
			 * @return   void
			 */
			initialize: function() {

				this._state = '';
				this._previousState = '';
				this._queuedLabels = [];
				this.states = this.models;
			},

			/**
			 * Return a specific State object.
			 * 
			 * @since    2.2
			 * 
			 * @param    string    State ID
			 * 
			 * @return   void
			 */
			state: function( state ) {

				return _.findWhere( this.states, { id: state } );
			},

			/**
			 * Set a specific State object as the Metabox's current
			 * state.
			 * 
			 * @since    2.2
			 * 
			 * @param    string    State ID
			 * 
			 * @return   void
			 */
			setState: function( state ) {

				var state = this.state( state );
				if ( _.isEmpty( state ) )
					return this;

				this._previousState = this._state;
				this._state = state.id;

				_.each( this._queuedLabels, function( model ) {
					model.set( { label: '', labeltitle: '' } );
				} );

				this.trigger( 'change:state', state, this );

				return this;
			},

			/**
			 * Add states to the list of enqueued label for removal.
			 * 
			 * This is used to hide notification labels once the
			 * user leaves the related state.
			 * 
			 * @since    2.2
			 * 
			 * @param    string    State ID
			 * 
			 * @return   void
			 */
			enqueueLabel: function( models ) {

				this._queuedLabels.push( models );
			}
		})
	} );

})(jQuery);
