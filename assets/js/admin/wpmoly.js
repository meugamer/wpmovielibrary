
if ( undefined == window.wpmoly ) window.wpmoly = {};
if ( undefined == window.redux ) window.redux = {};
if ( undefined == window.redux.field_objects ) window.redux.field_objects = {};
if ( undefined == window.redux.field_objects.select ) window.redux.field_objects.select = {};

$ = $ || jQuery;

wpmoly = {};

_.extend( wpmoly, {
	l10n: wpmoly_l10n || {}
});

jQuery( document ).ready( function() {

	if ( 'movie' == pagenow && 'post-php' == adminpage ) {
		wpmoly.metabox();
		wpmoly.editor();
		wpmoly.media();
	}

	if ( 'edit-movie' == pagenow && 'edit-php' == adminpage ) {
		wpmoly.editor();
	}

} );