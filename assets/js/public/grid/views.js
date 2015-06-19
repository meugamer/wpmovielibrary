
var grid = wpmoly.grid,
  editor = wpmoly.editor,
importer = wpmoly.importer,
   media = wp.media,
hasTouch = ( 'ontouchend' in document );

/*_.extend( grid.view, {

	Menu: media.View.extend({

		template: media.template( 'wpmoly-grid-menu' ),

		render: function() {

			this.$el.html( 'Menu' );
		},
	}),

	Movie: media.View.extend({

		tagName:   'li',

		className: 'attachment movie',

		template:  media.template( 'wpmoly-movie' ),

		initialize: function() {

			this.grid = this.options.grid || {};
		},

		render: function() {

			var rating = parseFloat( this.model.get( 'details' ).rating ),
			    star = 'empty';

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
					details: _.extend( this.model.get( 'details' ), { star: star } )
				} ) )
			);

			return this;
		}

	})
},
{
	Content: media.View.extend({

		id: 'grid-content-grid',

		tagName:   'ul',

		className: 'attachments movies',

		template: media.template( 'wpmoly-grid-content-grid' ),

		initialize: function( options ) {

			this.library = options.library;

			// Add new views for new movies
			this.library.on( 'add', function( movie ) {
				this.views.add( this.create_subview( movie ) );
			}, this );

			// Re-render the view when library is emptied
			this.library.on( 'reset', function() {
				this.render();
			}, this );
		},

		create_subview: function( movie ) {

			return new grid.view.Movie({
				model: movie
			});
		},

		render: function() {

			this.$el.html( this.template() );
		},
	})
},
{
	Grid: media.View.extend({

		template: media.template( 'wpmoly-grid-frame' ),

		pages: new Backbone.Model,

		initialize: function( options ) {

			var options = options || {};
			_.defaults( options, {
				library: {
					orderby: 'date',
					order:   'DESC',
					paged:   1
				}
			} );

			// Render the view
			this.render();

			// Set controller
			this.library = grid.query( options.library, this );

			// Set regions
			this.set_regions();
		},

		set_regions: function() {

			this.menu    = new grid.view.Menu({
				library: this.library
			});
			this.content = new grid.view.Content({
				library: this.library
			});

			this.views.add( '.grid-frame-menu',    this.menu );
			this.views.add( '.grid-frame-content', this.content );
		}
	})
} );*/

/* Required files:
 * - ./views/pagination.js
 * - ./views/menu.js
 * - ./views/movie.js
 * - ./views/content-grid.js
 * - ./views/content-exerpt.js
 * - ./views/frame.js
 * - ./views/grid-frame.js
 */
