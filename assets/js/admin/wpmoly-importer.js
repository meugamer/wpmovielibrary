
( function( $, _, Backbone, wp, wpmoly ) {

	var importer = wpmoly.importer = function() {

		//importer.frame = new importer.View.ImporterFrame;

		/*importer.models.drafts = new importer.Model.Drafts;
		importer.models.queued = new importer.Model.Queued;

		//importer.models.search = new importer.model.Search;

		importer.views.draftees = new importer.View.Draftees;

		var draftees = wpmoly.getValue( '#importer-search-list', '' )
		    draftees = draftees.split( ',' )
		   _draftees = [];

		_.each( draftees, function( draftee, i ) {
			draftee = draftee.trim();
			if ( ! _.isEmpty( draftee ) ) {
				_draftees.push( new importer.Model.Draftee( { title: draftee } ) );
			}
		} );

		importer.views.draftees.controller.collection.add( _draftees );*/
	};

}( jQuery, _, Backbone, wp, wpmoly ) );
