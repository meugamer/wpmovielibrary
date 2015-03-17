
( function( $, _, Backbone, wp, wpmoly ) {

	var grid = wpmoly.grid,
	  editor = wpmoly.editor,
	importer = wpmoly.importer;

	importer.controller.Draftees = Backbone.Model.extend({

		defaults: {
			list: []
		},

		initialize: function() {

			this.collection = new importer.Model.Draftees;
		}
	});

	importer.View.Draftee = Backbone.View.extend({

		tagName: 'li',

		render: function() {

			this.$el.html( '<a class="remove-draftee" href="#"><span class="wpmolicon icon-no-alt"></span></a><span class="draftee-label">' + this.model.get( 'title' ) + '</span>' );

			return this;
		},
	});

	importer.View.Draftees = Backbone.View.extend({

		el: '#importer-search-list-form',

		events: {
			'keydown #importer-search-list': 'update'
		},

		_views: [],

		initialize: function() {

			this.controller = new importer.controller.Draftees;

			this.controller.collection.on( 'add', this.createSubView , this );
		},

		update: function( event ) {

			if ( 8 === event.keyCode || 27 === event.keyCode ) {
				
			} else if ( 13 === event.keyCode || 188 === event.keyCode ) {

				var draftees = this.$( event.currentTarget ).val();
				    draftees = draftees.split( ',' );

				_.each( draftees, function( draftee ) {
					var raftee = draftee.trim();
					if ( _.isUndefined( this.controller.collection.findWhere( { title: draftee } ) ) ) {
						draftee = new importer.Model.Draftee( { title: draftee } );
						this.controller.collection.add( draftee );
					}
				}, this );
			} else {
				return;
			}

		},

		createSubView: function( model, options ) {

			var view = new importer.View.Draftee({
				model: model
			});

			this.$( '#importer-search-list-draftees' ).append( view.render().$el );
		},

		/*render: function() {
			return this;
		}*/
	});

	importer.View.Search = Backbone.View.extend({

		
	});

	importer.View.Settings = Backbone.View.extend({

		
	});

	importer.View.Results = Backbone.View.extend({

		
	});

})( jQuery, _, Backbone, wp, wpmoly );