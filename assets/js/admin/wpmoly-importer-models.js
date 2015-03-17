
( function( $, _, Backbone, wp, wpmoly ) {

	var importer = wpmoly.importer,
	      editor = wpmoly.editor;

	_.extend( importer, { controller: {}, models: {}, views: {}, Model: {}, View: {} } );

	importer.Model.Draftee = Backbone.Model.extend({

		defaults: {
			title:  '',
			status: ''
		}
	});

	importer.Model.Draftees = Backbone.Collection.extend({

		model: importer.Model.Draftee
	});

	importer.Model.Drafts = Backbone.Collection.extend({

		model: editor.Model.Movie,

		initialize: function() {

			
		},

		add: function( model, options ) {

			model.status = 'draft';

			Backbone.Collection.prototype.add.apply( this, arguments );
		},
	});

	importer.Model.Queued = Backbone.Collection.extend({

		model: editor.Model.Movie,

		initialize: function() {

			
		},

		add: function( model, options ) {

			model.status = 'queued';

			Backbone.Collection.prototype.add.apply( this, arguments );
		},
	});

}( jQuery, _, Backbone, wp, wpmoly ) );
