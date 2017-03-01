
wpmoly = window.wpmoly || {};

wpmoly.collection = wpmoly.collection || {};

wpmoly.collection.Movies = wp.api.collections.Movies.extend({

	parse: function( response, options ) {

		_.each( response, function( model, i ) {

			var meta = model.meta;

			model.meta = new Backbone.Model();

			_.each( meta, function( value, key ) {

				if ( ! value.rendered ) {
					var value = {
						rendered: value
					};
				}

				model.meta.set( key, value );
			} );
		} );

		return response;
	}
});