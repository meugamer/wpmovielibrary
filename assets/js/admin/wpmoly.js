
if ( undefined == window.redux ) window.redux = {};
if ( undefined == window.redux.field_objects ) window.redux.field_objects = {};
if ( undefined == window.redux.field_objects.select ) window.redux.field_objects.select = {};

$ = $ || jQuery;

wpmoly_l10n = window.wpmoly_l10n || {};

wpmoly = {

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
	},

	/**
	 * Find current action's nonce value.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    Action name
	 * 
	 * @return   boolean|string    Nonce value if available, false else.
	 */
	get_nonce: function( action ) {

		var nonce_name = '#_wpmolynonce_' + action.replace( /\-/g, '_' ),
		         nonce = null;

		if ( undefined != $( nonce_name ) )
			nonce = $( nonce_name ).val();

		return nonce;
	}
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
	}

} );