
/**
 * WPMOLY Backbone Grid Settings Model
 * 
 * Contain Grids Settings data and properties
 * 
 * @since    2.1.5
 */
grid.model.Settings = Backbone.Model.extend({

	orderby: [ 'title', 'date', 'release_date', 'rating' ],

	order:   [ 'asc', 'desc', 'random' ],

	defaults: {
		// Grid Content
		orderby:          'title',
		order:            'asc',
		paged:            1,

		// Grid Filtering
		include_incoming: true,
		include_unrated:  true,

		// Grid Display
		show_title:       true,
		show_genres:      false,
		show_rating:      true,
		show_runtime:     true
	},

	update: function() {

		this.props.set({
			orderby: this.get( 'orderby' ),
			order:   this.get( 'order' ),
		});
	},

	reset: function() {

		this.props.set({
			orderby: this.defaults.orderby,
			order:   this.defaults.order,
		});
	}
});
