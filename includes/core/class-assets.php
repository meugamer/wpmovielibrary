<?php
/**
 * .
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\core;

/**
 * .
 *
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Assets {

	/**
	 * The single instance of the class.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Library
	 */
	private static $_instance = null;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return \wpmoly\Library
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new static;
			self::$_instance->init();
		}

		return self::$_instance;
	}

	/**
	 * Initialize core.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {

		global $wpmoly_templates;

		if ( ! has_filter( 'wpmoly/filter/assets/handle' ) ) {
			add_filter( 'wpmoly/filter/assets/handle', array( $this, 'prefix_handle' ) );
		}

		if ( ! has_filter( 'wpmoly/filter/assets/src' ) ) {
			add_filter( 'wpmoly/filter/assets/src', array( $this, 'prefix_src' ) );
		}

		if ( ! has_filter( 'wpmoly/filter/assets/version' ) ) {
			add_filter( 'wpmoly/filter/assets/version', array( $this, 'default_version' ) );
		}

		add_filter( 'wpmoly/filter/admin/style/src',   array( $this, 'prefix_admin_style_src' ) );
		add_filter( 'wpmoly/filter/admin/script/src',  array( $this, 'prefix_admin_script_src' ) );
		add_filter( 'wpmoly/filter/admin/font/src',    array( $this, 'prefix_admin_font_src' ) );
		add_filter( 'wpmoly/filter/public/style/src',  array( $this, 'prefix_public_style_src' ) );
		add_filter( 'wpmoly/filter/public/script/src', array( $this, 'prefix_public_script_src' ) );
		add_filter( 'wpmoly/filter/public/font/src',   array( $this, 'prefix_public_font_src' ) );

		if ( ! isset( $wpmoly_templates ) ) {
			$wpmoly_templates = array();
		}
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_admin_scripts() {

		if ( ! is_admin() ) {
			return false;
		}

		global $hook_suffix;

		$this->register_admin_scripts();

		if ( 'toplevel_page_wpmovielibrary' == $hook_suffix ) {

			// Vendor
			$this->enqueue_script( 'sprintf' );
			$this->enqueue_script( 'underscore-string' );

			// Base
			$this->enqueue_script( 'core' );

			// Controllers
			$this->enqueue_script( 'library-controller' );

			// Views
			$this->enqueue_script( 'library-view' );
			$this->enqueue_script( 'library-menu-view' );
			$this->enqueue_script( 'library-content-latest-view' );
			$this->enqueue_script( 'library-content-favorites-view' );
			$this->enqueue_script( 'library-content-import-view' );

			// Runners
			$this->enqueue_script( 'library' );
		}

		if ( 'options-permalink.php' == $hook_suffix ) {

			// Vendor
			$this->enqueue_script( 'sprintf' );
			$this->enqueue_script( 'underscore-string' );

			// Base
			$this->enqueue_script( 'core' );

			// Metabox
			$this->enqueue_script( 'metabox-view' );
			$this->enqueue_script( 'metabox' );

			// Permalinks
			$this->enqueue_script( 'permalinks-view' );
			$this->enqueue_script( 'permalinks' );
		}

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'movie' == get_post_type() ) {

			// Vendor
			$this->enqueue_script( 'sprintf' );
			$this->enqueue_script( 'underscore-string' );

			// Base
			$this->enqueue_script( 'core' );
			$this->enqueue_script( 'utils' );

			// Models
			$this->enqueue_script( 'settings-model' );
			$this->enqueue_script( 'status-model' );
			$this->enqueue_script( 'results-model' );
			$this->enqueue_script( 'search-model' );
			$this->enqueue_script( 'meta-model' );
			$this->enqueue_script( 'modal-model' );
			$this->enqueue_script( 'image-model' );
			$this->enqueue_script( 'admin-image-model' );
			$this->enqueue_script( 'images-model' );

			// Controllers
			$this->enqueue_script( 'search-controller' );
			$this->enqueue_script( 'editor-controller' );
			$this->enqueue_script( 'modal-controller' );

			// Views
			$this->enqueue_script( 'frame-view' );
			$this->enqueue_script( 'confirm-view' );
			$this->enqueue_script( 'metabox-view' );
			$this->enqueue_script( 'search-view' );
			$this->enqueue_script( 'search-history-view' );
			$this->enqueue_script( 'search-settings-view' );
			$this->enqueue_script( 'search-status-view' );
			$this->enqueue_script( 'search-results-view' );
			$this->enqueue_script( 'editor-image-view' );
			$this->enqueue_script( 'editor-images-view' );
			$this->enqueue_script( 'editor-meta-view' );
			$this->enqueue_script( 'editor-details-view' );
			$this->enqueue_script( 'editor-tagbox-view' );
			$this->enqueue_script( 'editor-view' );
			$this->enqueue_script( 'modal-view' );
			$this->enqueue_script( 'modal-images-view' );
			$this->enqueue_script( 'modal-browser-view' );
			$this->enqueue_script( 'modal-post-view' );

			// Runners
			$this->enqueue_script( 'api' );
			$this->enqueue_script( 'metabox' );
			$this->enqueue_script( 'editor' );
			$this->enqueue_script( 'search' );
			$this->enqueue_script( 'tester' );

			// Libraries
			$this->enqueue_script( 'select2' );
			$this->enqueue_script( 'jquery-actual' );
		} // End if().

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'grid' == get_post_type() ) {

			// Vendor
			$this->enqueue_script( 'sprintf' );
			$this->enqueue_script( 'underscore-string' );
			$this->enqueue_script( 'wp-backbone' );

			// Base
			$this->enqueue_script( 'core' );
			$this->enqueue_script( 'utils' );

			// Libraries
			$this->enqueue_script( 'select2' );

			// Runners
			$this->enqueue_script( 'grids' );
			$this->enqueue_script( 'grid-builder' );
		}

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'page' == get_post_type() ) {

			// Vendor
			$this->enqueue_script( 'sprintf' );
			$this->enqueue_script( 'underscore-string' );
			$this->enqueue_script( 'wp-backbone' );

			// Base
			$this->enqueue_script( 'core' );
			$this->enqueue_script( 'utils' );

			// Views
			$this->enqueue_script( 'archive-pages-view' );

			// Runners
			$this->enqueue_script( 'archive-pages' );
		}
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_scripts() {

		$this->register_public_scripts();

		// Vendor
		$this->enqueue_script( 'sprintf' );
		$this->enqueue_script( 'underscore-string' );

		// Base
		$this->enqueue_script( 'core' );
		$this->enqueue_script( 'utils' );

		// Runners
		$this->enqueue_script( 'grids' );
		$this->enqueue_script( 'headboxes' );
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_admin_styles() {

		if ( ! is_admin() ) {
			return false;
		}

		global $hook_suffix;

		$this->register_admin_styles();

		$this->enqueue_style( 'admin' );
		$this->enqueue_style( 'font' );
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'select2' );

		if ( 'toplevel_page_wpmovielibrary' == $hook_suffix ) {
			$this->enqueue_style( 'library' );
		}

		if ( 'options-permalink.php' == $hook_suffix ) {
			$this->enqueue_style( 'metabox' );
			$this->enqueue_style( 'permalinks' );
		}

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'grid' == get_post_type() ) {
			$this->enqueue_style( 'grid-builder' );
		}

		if ( ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) && 'page' == get_post_type() ) {
			$this->enqueue_style( 'archive-pages' );
		}

		if ( 'term.php' == $hook_suffix || 'edit-tags.php' == $hook_suffix ) {
			$this->enqueue_style( 'term-editor' );
		}
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_styles() {

		$this->register_public_styles();

		$this->enqueue_style( 'core' );
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'headboxes' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'flags' );
		$this->enqueue_style( 'font' );
	}

	/**
	 * Enqueue templates.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_admin_templates() {

		if ( ! is_admin() ) {
			return false;
		}

		global $hook_suffix;

		$this->register_admin_templates();

		if ( 'toplevel_page_wpmovielibrary' == $hook_suffix ) {
			$this->enqueue_template( 'library-menu' );
			$this->enqueue_template( 'library-content-latest' );
			$this->enqueue_template( 'library-content-favorites' );
			$this->enqueue_template( 'library-content-import' );
			$this->enqueue_template( 'library-sidebar' );
			$this->enqueue_template( 'library-footer' );
		}

		if ( 'movie' == get_post_type() ) {
			$this->enqueue_template( 'search' );
			$this->enqueue_template( 'search-form' );
			$this->enqueue_template( 'search-settings' );
			$this->enqueue_template( 'search-status' );
			$this->enqueue_template( 'search-history' );
			$this->enqueue_template( 'search-history-item' );
			$this->enqueue_template( 'search-result' );
			$this->enqueue_template( 'search-results' );
			$this->enqueue_template( 'search-results-header' );
			$this->enqueue_template( 'search-results-menu' );

			$this->enqueue_template( 'editor-image-editor' );
			$this->enqueue_template( 'editor-image-more' );
			$this->enqueue_template( 'editor-image' );

			$this->enqueue_template( 'modal-browser' );
			$this->enqueue_template( 'modal-sidebar' );
			$this->enqueue_template( 'modal-toolbar' );
			$this->enqueue_template( 'modal-image' );
			$this->enqueue_template( 'modal-selection' );

			$this->enqueue_template( 'confirm-modal' );
		}

		if ( 'grid' == get_post_type() ) {
			$this->enqueue_template( 'grid-builder-parameters' );

			$this->enqueue_template( 'grid' );
			$this->enqueue_template( 'grid-menu' );
			$this->enqueue_template( 'grid-customs' );
			$this->enqueue_template( 'grid-settings' );
			$this->enqueue_template( 'grid-pagination' );

			$this->enqueue_template( 'grid-movie-grid' );
			$this->enqueue_template( 'grid-movie-grid-variant-1' );
			$this->enqueue_template( 'grid-movie-grid-variant-2' );
			$this->enqueue_template( 'grid-movie-list' );
			$this->enqueue_template( 'grid-actor-grid' );
			$this->enqueue_template( 'grid-actor-list' );
			$this->enqueue_template( 'grid-collection-grid' );
			$this->enqueue_template( 'grid-collection-list' );
			$this->enqueue_template( 'grid-genre-grid' );
			$this->enqueue_template( 'grid-genre-list' );
		}
	}

	/**
	 * Enqueue templates.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_templates() {

		$this->register_public_templates();

		$this->enqueue_template( 'grid' );
		$this->enqueue_template( 'grid-menu' );
		$this->enqueue_template( 'grid-customs' );
		$this->enqueue_template( 'grid-settings' );
		$this->enqueue_template( 'grid-pagination' );

		$this->enqueue_template( 'grid-empty' );
		$this->enqueue_template( 'grid-error' );

		$this->enqueue_template( 'grid-movie-grid' );
		$this->enqueue_template( 'grid-movie-grid-variant-1' );
		$this->enqueue_template( 'grid-movie-grid-variant-2' );
		$this->enqueue_template( 'grid-movie-list' );
		$this->enqueue_template( 'grid-actor-grid' );
		$this->enqueue_template( 'grid-actor-list' );
		$this->enqueue_template( 'grid-collection-grid' );
		$this->enqueue_template( 'grid-collection-list' );
		$this->enqueue_template( 'grid-genre-grid' );
		$this->enqueue_template( 'grid-genre-list' );
	}

	/**
	 * Register scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_scripts() {

		if ( ! is_admin() ) {
			return false;
		}

		// Vendor
		$this->add_public_js( 'sprintf',           'sprintf.min.js', array( 'jquery', 'underscore' ), '1.0.3' );
		$this->add_public_js( 'underscore-string', 'underscore.string.min.js', array( 'jquery', 'underscore' ), '3.3.4' );

		// Base
		$this->add_public_js( 'core',  'wpmoly.js', array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) );
		$this->add_public_js( 'utils', 'wpmoly-utils.js' );

		// Libraries
		$this->add_admin_js( 'select2',       'select2.min.js' );
		$this->add_admin_js( 'jquery-actual', 'jquery.actual.min.js' );

		// Models
		$this->add_admin_js( 'settings-model', 'models/settings.js' );
		$this->add_admin_js( 'status-model',   'models/status.js' );
		$this->add_admin_js( 'results-model',  'models/results.js' );
		$this->add_admin_js( 'search-model',   'models/search.js' );
		$this->add_admin_js( 'meta-model',     'models/meta.js' );
		$this->add_admin_js( 'modal-model',    'models/modal/modal.js' );
		$this->add_admin_js( 'image-model',    'models/image.js' );
		$this->add_admin_js( 'images-model',   'models/images.js' );

		// Controllers
		$this->add_admin_js( 'library-controller', 'controllers/library.js' );
		$this->add_admin_js( 'search-controller',  'controllers/search.js' );
		$this->add_admin_js( 'editor-controller',  'controllers/editor.js' );
		$this->add_admin_js( 'modal-controller',   'controllers/modal.js' );

		// Views
		$this->add_public_js( 'frame-view',                    'public/assets/js/views/frame.js' );
		$this->add_public_js( 'confirm-view',                  'public/assets/js/views/confirm.js' );
		$this->add_admin_js( 'permalinks-view',                'views/permalinks.js' );
		$this->add_admin_js( 'archive-pages-view',             'views/archive-pages.js' );
		$this->add_admin_js( 'metabox-view',                   'views/metabox.js' );
		$this->add_admin_js( 'library-view',                   'views/library/library.js' );
		$this->add_admin_js( 'library-menu-view',              'views/library/menu.js' );
		$this->add_admin_js( 'library-content-latest-view',    'views/library/content-latest.js' );
		$this->add_admin_js( 'library-content-favorites-view', 'views/library/content-favorites.js' );
		$this->add_admin_js( 'library-content-import-view',    'views/library/content-import.js' );
		$this->add_admin_js( 'search-view',                    'views/search/search.js' );
		$this->add_admin_js( 'search-history-view',            'views/search/history.js' );
		$this->add_admin_js( 'search-settings-view',           'views/search/settings.js' );
		$this->add_admin_js( 'search-status-view',             'views/search/status.js' );
		$this->add_admin_js( 'search-results-view',            'views/search/results.js' );
		$this->add_admin_js( 'editor-image-view',              'views/editor/image.js' );
		$this->add_admin_js( 'editor-images-view',             'views/editor/images.js' );
		$this->add_admin_js( 'editor-meta-view',               'views/editor/meta.js' );
		$this->add_admin_js( 'editor-details-view',            'views/editor/details.js' );
		$this->add_admin_js( 'editor-tagbox-view',             'views/editor/tagbox.js' );
		$this->add_admin_js( 'editor-view',                    'views/editor/editor.js' );
		$this->add_admin_js( 'modal-view',                     'views/modal/modal.js' );
		$this->add_admin_js( 'modal-images-view',              'views/modal/images.js' );
		$this->add_admin_js( 'modal-browser-view',             'views/modal/browser.js' );
		$this->add_admin_js( 'modal-post-view',                'views/modal/post.js' );

		// Runners
		$this->add_admin_js( 'library',       'wpmoly-library.js' );
		$this->add_admin_js( 'api',           'wpmoly-api.js' );
		$this->add_admin_js( 'metabox',       'wpmoly-metabox.js' );
		$this->add_admin_js( 'permalinks',    'wpmoly-permalinks.js' );
		$this->add_admin_js( 'archive-pages', 'wpmoly-archive-pages.js' );
		$this->add_admin_js( 'editor',        'wpmoly-editor.js' );
		$this->add_admin_js( 'grid-builder',  'wpmoly-grid-builder.js', array( 'butterbean' ) );
		$this->add_public_js( 'grids',        'wpmoly-grids.js' );
		$this->add_admin_js( 'search',        'wpmoly-search.js' );
		$this->add_admin_js( 'tester',        'wpmoly-tester.js' );
	}

	/**
	 * Register scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_scripts() {

		// Vendor
		$this->add_public_js( 'sprintf',           'sprintf.min.js',           array( 'jquery', 'underscore' ), '1.0.3' );
		$this->add_public_js( 'underscore-string', 'underscore.string.min.js', array( 'jquery', 'underscore' ), '3.3.4' );

		// Base
		$this->add_public_js( 'core',  'wpmoly.js', array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) );
		$this->add_public_js( 'utils', 'wpmoly-utils.js' );

		// Runners
		$this->add_public_js( 'grids',     'wpmoly-grids.js',     array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) );
		$this->add_public_js( 'headboxes', 'wpmoly-headboxes.js', array( 'jquery', 'underscore', 'backbone', 'wp-backbone' ) );
	}

	/**
	 * Register stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_styles() {

		if ( ! is_admin() ) {
			return false;
		}

		$this->add_public_font( 'font',    'wpmovielibrary/style.css' );

		$this->add_admin_css( 'admin',         'wpmoly.css' );
		$this->add_admin_css( 'library',       'wpmoly-library.css' );
		$this->add_admin_css( 'metabox',       'wpmoly-metabox.css' );
		$this->add_admin_css( 'permalinks',    'wpmoly-permalink-settings.css' );
		$this->add_admin_css( 'term-editor',   'wpmoly-term-editor.css' );
		$this->add_admin_css( 'archive-pages', 'wpmoly-archive-pages.css' );
		$this->add_admin_css( 'grid-builder',  'wpmoly-grid-builder.css' );

		$this->add_admin_css( 'select2', 'select2.min.css' );
		$this->add_public_css( 'common', 'common.css' );
		$this->add_public_css( 'grids',  'wpmoly-grids.css' );
	}

	/**
	 * Register stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_styles() {

		// Plugin icon font
		$this->add_public_font( 'font',     'wpmovielibrary/style.css' );

		// Plugin-wide normalize
		$this->add_public_css( 'normalize', 'wpmoly-normalize-min.css' );

		// Main stylesheet
		$this->add_public_css( 'core',      'wpmoly.css' );

		// Common stylesheets
		$this->add_public_css( 'common',    'common.css' );
		$this->add_public_css( 'headboxes', 'wpmoly-headboxes.css' );
		$this->add_public_css( 'grids',     'wpmoly-grids.css' );
		$this->add_public_css( 'flags',     'wpmoly-flags.css' );
	}

	/**
	 * Register templates.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_templates() {

		if ( ! is_admin() ) {
			return false;
		}

		global $hook_suffix;

		if ( 'toplevel_page_wpmovielibrary' == $hook_suffix ) {
			$this->register_template( 'library-menu',              'admin/assets/js/templates/library/menu.php' );
			$this->register_template( 'library-content-latest',    'admin/assets/js/templates/library/content-latest.php' );
			$this->register_template( 'library-content-favorites', 'admin/assets/js/templates/library/content-favorites.php' );
			$this->register_template( 'library-content-import',    'admin/assets/js/templates/library/content-import.php' );
			$this->register_template( 'library-sidebar',           'admin/assets/js/templates/library/sidebar.php' );
			$this->register_template( 'library-footer',            'admin/assets/js/templates/library/footer.php' );
		}

		if ( 'movie' == get_post_type() ) {
			$this->register_template( 'search',                'admin/assets/js/templates/search/search.php' );
			$this->register_template( 'search-form',           'admin/assets/js/templates/search/search-form.php' );
			$this->register_template( 'search-settings',       'admin/assets/js/templates/search/settings.php' );
			$this->register_template( 'search-status',         'admin/assets/js/templates/search/status.php' );
			$this->register_template( 'search-history',        'admin/assets/js/templates/search/history.php' );
			$this->register_template( 'search-history-item',   'admin/assets/js/templates/search/history-item.php' );
			$this->register_template( 'search-result',         'admin/assets/js/templates/search/result.php' );
			$this->register_template( 'search-results',        'admin/assets/js/templates/search/results.php' );
			$this->register_template( 'search-results-header', 'admin/assets/js/templates/search/results-header.php' );
			$this->register_template( 'search-results-menu',   'admin/assets/js/templates/search/results-menu.php' );

			$this->register_template( 'editor-image-editor',   'admin/assets/js/templates/editor/image-editor.php' );
			$this->register_template( 'editor-image-more',     'admin/assets/js/templates/editor/image-more.php' );
			$this->register_template( 'editor-image',          'admin/assets/js/templates/editor/image.php' );

			$this->register_template( 'modal-browser',         'admin/assets/js/templates/modal/browser.php' );
			$this->register_template( 'modal-sidebar',         'admin/assets/js/templates/modal/sidebar.php' );
			$this->register_template( 'modal-toolbar',         'admin/assets/js/templates/modal/toolbar.php' );
			$this->register_template( 'modal-image',           'admin/assets/js/templates/modal/image.php' );
			$this->register_template( 'modal-selection',       'admin/assets/js/templates/modal/selection.php' );

			$this->register_template( 'confirm-modal',         'public/assets/js/templates/confirm.php' );
		}

		if ( 'grid' == get_post_type() ) {
			$this->register_template( 'grid-builder-parameters',   'admin/assets/js/templates/builder/parameters.php' );

			$this->register_template( 'grid',                      'public/assets/js/templates/grid/grid.php' );
			$this->register_template( 'grid-menu',                 'public/assets/js/templates/grid/menu.php' );
			$this->register_template( 'grid-customs',              'public/assets/js/templates/grid/customs.php' );
			$this->register_template( 'grid-settings',             'public/assets/js/templates/grid/settings.php' );
			$this->register_template( 'grid-pagination',           'public/assets/js/templates/grid/pagination.php' );

			$this->register_template( 'grid-movie-grid',           'public/assets/js/templates/grid/content/movie-grid.php' );
			$this->register_template( 'grid-movie-grid-variant-1', 'public/assets/js/templates/grid/content/movie-grid-variant-1.php' );
			$this->register_template( 'grid-movie-grid-variant-2', 'public/assets/js/templates/grid/content/movie-grid-variant-2.php' );
			$this->register_template( 'grid-movie-list',           'public/assets/js/templates/grid/content/movie-list.php' );
			$this->register_template( 'grid-actor-grid',           'public/assets/js/templates/grid/content/actor-grid.php' );
			$this->register_template( 'grid-actor-list',           'public/assets/js/templates/grid/content/actor-list.php' );
			$this->register_template( 'grid-collection-grid',      'public/assets/js/templates/grid/content/collection-grid.php' );
			$this->register_template( 'grid-collection-list',      'public/assets/js/templates/grid/content/collection-list.php' );
			$this->register_template( 'grid-genre-grid',           'public/assets/js/templates/grid/content/genre-grid.php' );
			$this->register_template( 'grid-genre-list',           'public/assets/js/templates/grid/content/genre-list.php' );
		}
	}

	/**
	 * Register templates.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_templates() {

		$this->register_template( 'grid',                      'public/assets/js/templates/grid/grid.php' );
		$this->register_template( 'grid-menu',                 'public/assets/js/templates/grid/menu.php' );
		$this->register_template( 'grid-customs',              'public/assets/js/templates/grid/customs.php' );
		$this->register_template( 'grid-settings',             'public/assets/js/templates/grid/settings.php' );
		$this->register_template( 'grid-pagination',           'public/assets/js/templates/grid/pagination.php' );

		$this->register_template( 'grid-error',                'public/assets/js/templates/grid/content/error.php' );
		$this->register_template( 'grid-empty',                'public/assets/js/templates/grid/content/empty.php' );

		$this->register_template( 'grid-movie-grid',           'public/assets/js/templates/grid/content/movie-grid.php' );
		$this->register_template( 'grid-movie-grid-variant-1', 'public/assets/js/templates/grid/content/movie-grid-variant-1.php' );
		$this->register_template( 'grid-movie-grid-variant-2', 'public/assets/js/templates/grid/content/movie-grid-variant-2.php' );
		$this->register_template( 'grid-movie-list',           'public/assets/js/templates/grid/content/movie-list.php' );
		$this->register_template( 'grid-actor-grid',           'public/assets/js/templates/grid/content/actor-grid.php' );
		$this->register_template( 'grid-actor-list',           'public/assets/js/templates/grid/content/actor-list.php' );
		$this->register_template( 'grid-collection-grid',      'public/assets/js/templates/grid/content/collection-grid.php' );
		$this->register_template( 'grid-collection-list',      'public/assets/js/templates/grid/content/collection-list.php' );
		$this->register_template( 'grid-genre-grid',           'public/assets/js/templates/grid/content/genre-grid.php' );
		$this->register_template( 'grid-genre-list',           'public/assets/js/templates/grid/content/genre-list.php' );
	}

	/**
	 * Register an admin style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 */
	private function add_admin_css( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/**
		 * Filter the admin style URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/admin/style/src', $src );

		$this->register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register a public style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 */
	private function add_public_css( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/**
		 * Filter the public style URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/public/style/src', $src );

		$this->register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register an admin script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 */
	private function add_admin_js( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the admin script URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/admin/script/src', $src );

		$this->register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register a public script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 */
	private function add_public_js( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the public script URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/public/script/src', $src );

		$this->register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register a public font.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Font handle name.
	 * @param string  $src       Font full URL.
	 * @param array   $deps      Font dependencies.
	 * @param string  $version   Font version.
	 * @param boolean $media     Media
	 */
	private function add_public_font( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/**
		 * Filter the public font URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/public/font/src', $src );

		$this->register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register single script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 *
	 * @return boolean
	 */
	private function register_script( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the Asset handle.
		 *
		 * @since 3.0.0
		 *
		 * @param string $handle Asset handle.
		 */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		/**
		 * Filter the Asset URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/assets/src', $src );

		/**
		 * Filter the Asset version.
		 *
		 * @since 3.0.0
		 *
		 * @param string $version Asset version.
		 */
		$version = apply_filters( 'wpmoly/filter/assets/version', $version );

		return wp_register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register single style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 *
	 * @return boolean
	 */
	private function register_style( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		/** This filter is defined in includes/core/class-assets.php */
		$src = apply_filters( 'wpmoly/filter/assets/src', $src );

		/** This filter is defined in includes/core/class-assets.php */
		$version = apply_filters( 'wpmoly/filter/assets/version', $version );

		return wp_register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register single template.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Template handle name.
	 * @param string $src    Template URL.
	 */
	private function register_template( $handle, $src ) {

		global $wpmoly_templates;

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		$wpmoly_templates[ $handle ] = wpmoly_get_js_template( $src );
	}

	/**
	 * Enqueue single script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Script handle name.
	 */
	private function enqueue_script( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		wp_enqueue_script( $handle );
	}

	/**
	 * Enqueue single style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Style handle name.
	 */
	private function enqueue_style( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		wp_enqueue_style( $handle );
	}

	/**
	 * Enqueue single template.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Template handle name.
	 */
	private function enqueue_template( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		global $wpmoly_templates;

		if ( ! isset( $wpmoly_templates[ $handle ] ) || ! $wpmoly_templates[ $handle ] instanceof \wpmoly\templates\Template ) {
			return false;
		}
?>
	<script type="text/html" id="tmpl-<?php echo esc_attr( $handle ); ?>"><?php $wpmoly_templates[ $handle ]->render( 'always' ); ?>
	</script>

<?php
	}

	/**
	 * Prefix the Asset handle with plugin slug.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset handle.
	 *
	 * @return string
	 */
	public function prefix_handle( $handle ) {

		return "wpmoly-{$handle}";
	}

	/**
	 * Prefix the styles URL with admin folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_admin_style_src( $src ) {

		return "admin/assets/css/{$src}";
	}

	/**
	 * Prefix the scripts URL with admin folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_admin_script_src( $src ) {

		return "admin/assets/js/{$src}";
	}

	/**
	 * Prefix the fonts URL with admin folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_admin_font_src( $src ) {

		return "admin/assets/fonts/{$src}";
	}

	/**
	 * Prefix the styles URL with public folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_public_style_src( $src ) {

		return "public/assets/css/{$src}";
	}

	/**
	 * Prefix the scripts URL with public folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_public_script_src( $src ) {

		return "public/assets/js/{$src}";
	}

	/**
	 * Prefix the fonts URL with public folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_public_font_src( $src ) {

		return "public/assets/fonts/{$src}";
	}

	/**
	 * Prefix the Asset URL with plugin URL.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_src( $src ) {

		return WPMOLY_URL . $src;
	}

	/**
	 * Set a default Asset version is needed.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset Version.
	 *
	 * @return string
	 */
	public function default_version( $version ) {

		if ( empty( $version ) ) {
			$version = WPMOLY_VERSION;
		}

		return $version;
	}

}
