
/**
 * Custom attachment-like Movie View.
 * 
 * @since    2.1.5
 */
grid.view.Movie = media.View.extend({

	tagName:   'li',

	className: 'attachment movie',

	template:  media.template( 'wpmoly-movie' ),

	initialize: function() {

		this.grid = this.options.grid || {};
	},

	/**
	 * Render the Menu
	 * 
	 * @since    2.1.5
	 * 
	 * @return   Returns itself to allow chaining.
	 */
	render: function() {

		var rating = parseFloat( this.model.get( 'details' ).rating ),
		      star = 'empty',
		  settings = this.controller.controller;

		if ( '' != rating ) {
			if ( 3.5 < rating ) {
				star = 'filled';
			} else if ( 2 < rating ) {
				star = 'half';
			}
		}

		this.$el.html(
			this.template( _.extend( this.model.toJSON(), {
				size: {
					height: this.grid.thumbnail_height || '',
					width:  this.grid.thumbnail_width  || ''
				},
				details: _.extend( this.model.get( 'details' ), { star: star } ),
				display: {
					title:   settings.get( 'show_title' ),
					genres:  settings.get( 'show_genres' ),
					rating:  settings.get( 'show_rating' ),
					runtime: settings.get( 'show_runtime' )
				}
			} ) )
		);

		return this;
	}

});
