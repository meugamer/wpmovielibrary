
wpmoly = {};

( function( $, _, Backbone, wp ) {

	_.extend( wpmoly, {

		controller: {},

		model: {},

		view: {},

		grid: {},

		headbox: {},

		widgets: {}
	},
	{
		run: function() {

			$( '.hide-if-js' ).hide();
			$( '.hide-if-no-js' ).removeClass( 'hide-if-no-js' );

			this.grid.run();
			this.widgets.run();
		}
	} );

	_.extend( wpmoly.grid, {

		model: {},

		view: {},

		frames: []
	},
	{
		run: function() {

			var grids = document.querySelectorAll( 'div.wpmoly.movies.grid' );
			_.map( grids, function( grid ) {
				grid.id = _.uniqueId( grid.id + '-' );
				var frame = new this.view.GridFrame({ el: '#' + grid.id });
				return this.frames.push( frame );
			}, this );
		}
	} );

	_.extend( wpmoly.widgets,{

		run: function() {

			$( 'select.wpmoly.list' ).change( function() {
				if ( this.options[ this.selectedIndex ].value.length > 0 ) {
					location.href = this.options[ this.selectedIndex ].value;
				}
			} );
		}
	} );

}( jQuery, _, Backbone, wp ) );

jQuery( document ).ready( function() {
	wpmoly.run();
} );

/*( function( $ ) {

		wpmoly.headbox = wpmoly_headbox = {};

			wpmoly.headbox.toggle = function( item, post_id ) {

				var $tab = $( '#movie-headbox-' + item + '-' + post_id ),
				$parent = $( '#movie-headbox-' + post_id ),
				$tabs = $parent.find( '.wpmoly.headbox.movie.content > .content' ),
				$link = $( '#movie-headbox-' + item + '-link-' + post_id );

				if ( undefined != $tab ) {
					$tabs.hide();
					$tab.show();
					$parent.find( 'a.active' ).removeClass( 'active' );
					$link.addClass( 'active' );
				}
			};

		wpmoly.grid_resize = function() {

			var $movies = $( '#wpmoly-movie-grid.grid .movie' ),
			   $posters = $( '#wpmoly-movie-grid.grid .poster' ),
			 max_height = 0,
			  max_width = 0;

			$.each( $posters, function() {
				var $img = $( this ),
				   width = $img.width(),
				  height = $img.height();

				if ( height > max_height )
					max_height = height;
				if ( width > max_width )
					max_width = width;

			});

			if ( $( '#wpmoly-movie-grid.grid' ).hasClass( 'spaced' ) ) {
				var _poster = {
					height: Math.round( max_width * 1.33 ),
					width: max_width
				},
				     _movie = {
					width: max_width
				};
			} else {
				var _poster = {
					height: Math.round( max_width * 1.33 ),
					width: max_width
				},
				     _movie = _poster;
			}

			$posters.css( _poster );
			$movies.css( _movie );
		};

		wpmoly.init();
	
}( jQuery ) );*/