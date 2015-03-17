
if ( undefined == window.redux ) window.redux = {};
if ( undefined == window.redux.field_objects ) window.redux.field_objects = {};
if ( undefined == window.redux.field_objects.select ) window.redux.field_objects.select = {};

$ = $ || jQuery;

wpmoly = {};
wpmoly_l10n = window.wpmoly_l10n || {};

_.extend( wpmoly, {

	l10n: wpmoly_l10n,

	parseSearchQuery: function() {
		return _.chain( location.search.slice( 1 ).split( '&' ) )
			.map( function( item ) { if ( item ) return item.split( '=' ); } )
			.compact()
			.object()
			.value();
	},

	getValue: function( selector, _default ) {

		return ( document.querySelector( selector ) || {} ).value || _default;
	}
} );

_.isDefined = function( elem ) {
	return undefined !== elem;
};

jQuery( document ).ready( function() {

	if ( 'movie' == pagenow && ( 'post-php' == adminpage || 'post-new-php' == adminpage ) ) {
		wpmoly.metabox();
		wpmoly.editor();
		wpmoly.media();
	}

	if ( 'edit-movie' == pagenow && 'edit-php' == adminpage ) {
		wpmoly.grid();
		wpmoly.editor();
		wpmoly.importer();
	}

} );