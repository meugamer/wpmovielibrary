
_.extend( wpmoly, {

	/**
	 * Parse URL Query Search part
	 * 
	 * @since    2.2
	 * 
	 * @return   object    URL Parameters
	 */
	parseSearchQuery: function() {
		return _.chain( location.search.slice( 1 ).split( '&' ) )
			.map( function( item ) { if ( item ) return item.split( '=' ); } )
			.compact()
			.object()
			.value();
	},

	/**
	 * Update current action's nonce value.
	 * 
	 * @since    1.0
	 * 
	 * @param    string    Action name
	 * @param    string    Action nonce
	 */
	update_nonce: function( action, nonce ) {

		var nonce_name = '#_wpmolynonce_' + action.replace( /\-/g, '_' );

		if ( undefined != $( nonce_name ) && undefined != nonce )
			$( nonce_name ).val( nonce );
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
	},

	/**
	 * Get a specific selector's value.
	 * 
	 * @since    2.2
	 * 
	 * @param    string    selector query
	 * @param    string    default value
	 * 
	 * @return   mixed     selector's value or default if none
	 */
	getValue: function( selector, _default ) {

		return ( document.querySelector( selector ) || {} ).value || _default;
	},

	compare: function( a, b, ac, bc ) {

		if ( _.isEqual( a, b ) ) {
			return ac === bc ? 0 : (ac > bc ? -1 : 1);
		} else {
			return a > b ? -1 : 1;
		}
	}

} );
