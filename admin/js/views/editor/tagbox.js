wpmoly = window.wpmoly || {};

wpmoly.view.TagBox = Backbone.View.extend({

	events : {
		'click [data-action="terms-autocomplete"]' : 'autocomplete'
	},

	/**
	 * Initialize the View.
	 *
	 * @since    3.0
	 */
	initialize : function( options ) {

		this.prepare();
	},

	/**
	 * Add a new link to the tagBox to manually trigger terms
	 * autocompletion.
	 *
	 * @since    3.0
	 */
	prepare : function() {

		var $link = this.$( 'a#link-' + this.type ),
			link = '<a href="#" data-action="terms-autocomplete">' + wpmolyL10n.autocomplete + '</a>';
			$link.after( '<span> ' + s.sprintf( wpmolyL10n.termsAutocomplete, link, wpmolyL10n[ this.type + 's' ] ) + '</span>' );
	},

	/**
	 * Trigger terms autocompletion.
	 *
	 * @since    3.0
	 *
	 * @param    object    JS 'click' event.
	 */
	autocomplete : function( event ) {

		event.preventDefault();

		var tab = {
			collection : 'director',
			genre      : 'genres',
			actor      : 'cast'
		};
		var terms = wpmoly.editor.controller.meta.get( tab[ this.type ] );

		wpmoly.trigger( 'editor:meta:terms-autocomplete', this.type, null, terms );
	}

});

wpmoly.view.CollectionsBox = wpmoly.view.TagBox.extend({

	el : '#tagsdiv-collection',

	type : 'collection',

});

wpmoly.view.GenresBox = wpmoly.view.TagBox.extend({

	el : '#tagsdiv-genre',

	type : 'genre',

});

wpmoly.view.ActorsBox = wpmoly.view.TagBox.extend({

	el : '#tagsdiv-actor',

	type : 'actor',

});
