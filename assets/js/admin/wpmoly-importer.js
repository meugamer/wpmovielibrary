
( function( $, _, Backbone, wp, wpmoly ) {

	var importer = wpmoly.importer = function() {

		//importer.models.draftees = new importer.Model.Draftee;

		importer.models.drafts = new importer.Model.Drafts;
		importer.models.queued = new importer.Model.Queued;

		//importer.models.search = new importer.model.Search;

		importer.views.draftees = new importer.View.Draftees;
		
	};

}( jQuery, _, Backbone, wp, wpmoly ) );
