
var importer = wpmoly.importer,
      editor = wpmoly.editor;

importer.model.Draftee = Backbone.Model.extend({

	defaults: {
		title:  ''
	}

});

importer.model.Draftees = Backbone.Collection.extend({

	model: importer.model.Draftee,

	/**
	 * Initialize the Collection
	 * 
	 * @since    2.2
	 * 
	 * @return   void
	 */
	initialize: function() {

		//this.fetch();
	},

	/**
	 * Overrides Backbone.Collection.sync
	 * 
	 * @since    2.2
	 * 
	 * @param    string    method
	 * @param    object    this
	 * @param    object    options
	 * 
	 * @return   Promise
	 */
	sync: function( method, collection, options ) {

		if ( 'read' == method ) {

			options = options || {};
			options.context = this;
			options.data    = options.data || {};
			options.data    = _.extend( options.data, {
				action: 'wpmoly_fetch_draftees',
				nonce:  ''
			});
			//console.log( options );

			return wp.ajax.send( options );

		} else if ( 'save' == method ) {

			options = {};
			options.context = this;
			options.data    = options.data || {};

			options.data = _.extend( options.data, {
				action: 'wpmoly_save_draftees',
				nonce:  '',
				data:   this.toJSON()
			});

			if ( _.isEmpty( options.data.data ) ) {
				options.data.action = 'wpmoly_empty_draftees';
			}

			return wp.ajax.send( options );

		} else {
			return Backbone.sync.apply( this, arguments );
		}
	},

	/**
	 * Save current collection
	 * 
	 * @since    2.2
	 * 
	 * @return   Return itself to allow chaining
	 */
	save: function() {

		return this.sync( 'save', this, {} );
	}
});

/*importer.model.Drafts = Backbone.Collection.extend({

	model: editor.Model.Movie,

	initialize: function() {

		
	},

	add: function( model, options ) {

		model.status = 'draft';

		Backbone.Collection.prototype.add.apply( this, arguments );
	},
});

importer.model.Queued = Backbone.Collection.extend({

	model: editor.Model.Movie,

	initialize: function() {

		
	},

	add: function( model, options ) {

		model.status = 'queued';

		Backbone.Collection.prototype.add.apply( this, arguments );
	},
});*/

/**
 * Controller for draftees list.
 * 
 * Handle the connection between the draftees form and list and the 
 * draftees collection.
 * 
 * @since    2.2
 */
importer.controller.Draftees = Backbone.Model.extend({

	initialize: function() {

		this.collection = new importer.model.Draftees;
	}
});
