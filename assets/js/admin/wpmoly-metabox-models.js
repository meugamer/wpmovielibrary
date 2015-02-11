
window.wpmoly = window.wpmoly || {};

(function( $ ) {

	var metabox = wpmoly.metabox = function() {

		metabox.models.metabox = new metabox.Model.Metabox();
		metabox.views.metabox = new metabox.View.Metabox( { collection: metabox.models.metabox } );

		var panels = document.getElementsByClassName( 'tab' ),
		    states = [];

		_.each( panels, function( panel ) {
			states.push( new metabox.Model.State({
				id: panel.id.replace( /wpmoly\-meta\-(.*)/g, '$1' ),
				title: panel.textContent.trim(),
				label: '',
				labeltitle: ''
			}) );
		} );

		metabox.models.metabox.add( states );

	};

	_.extend( metabox, { models: {}, views: {}, Model: {}, View: {} } );

	_.extend( metabox.Model, {

		State: Backbone.Model.extend({

			defaults: {
				id: '',
				title: '',
				label: '',
				labeltitle: ''
			},

			_menu: '',

			_panel: ''
		})
	} );

	_.extend( metabox.Model, {

		Metabox: Backbone.Collection.extend({

			model: metabox.Model.State,

			initialize: function() {

				this._state  = '';
				this.states = this.models;

				
			},

			state: function( state ) {

				return _.findWhere( this.states, { id: state } );
			},

			setState: function( state ) {

				var state = this.state( state );
				if ( ! _.isEmpty( state ) )
					this._state = state.id;

				this.trigger( 'change:state', state, this );

				return this;
			}
		})
	} );

})(jQuery);
