<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\admin;

use wpmoly\core\Assets;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPMovieLibrary
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Backstage extends Assets {

	/**
	 * Single instance.
	 *
	 * @var    Backstage
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 *
	 * @since    3.0
	 *
	 * @return   \wpmoly\admin\Backstage
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register scripts.
	 *
	 * @since    3.0
	 */
	protected function register_scripts() {

		// Vendor
		$this->register_script( 'sprintf',           'public/assets/js/sprintf.min.js', array( 'jquery', 'underscore' ), '1.0.3' );
		$this->register_script( 'underscore-string', 'public/assets/js/underscore.string.min.js', array( 'jquery', 'underscore' ), '3.3.4' );

		// Base
		$this->register_script( 'core',  'public/assets/js/wpmoly.js', array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) );
		$this->register_script( 'utils', 'public/assets/js/wpmoly-utils.js' );

		// Libraries
		$this->register_script( 'select2',                 'admin/assets/js/select2.min.js' );
		$this->register_script( 'jquery-actual',           'admin/assets/js/jquery.actual.min.js' );

		// Models
		$this->register_script( 'settings-model',          'admin/assets/js/models/settings.js' );
		$this->register_script( 'status-model',            'admin/assets/js/models/status.js' );
		$this->register_script( 'results-model',           'admin/assets/js/models/results.js' );
		$this->register_script( 'search-model',            'admin/assets/js/models/search.js' );
		$this->register_script( 'meta-model',              'admin/assets/js/models/meta.js' );
		$this->register_script( 'modal-model',             'admin/assets/js/models/modal/modal.js' );
		$this->register_script( 'image-model',             'admin/assets/js/models/image.js' );
		$this->register_script( 'images-model',            'admin/assets/js/models/images.js' );

		// Controllers
		$this->register_script( 'library-controller',      'admin/assets/js/controllers/library.js' );
		$this->register_script( 'search-controller',       'admin/assets/js/controllers/search.js' );
		$this->register_script( 'editor-controller',       'admin/assets/js/controllers/editor.js' );
		$this->register_script( 'modal-controller',        'admin/assets/js/controllers/modal.js' );

		// Views
		$this->register_script( 'frame-view',                     'public/assets/js/views/frame.js' );
		$this->register_script( 'confirm-view',                   'public/assets/js/views/confirm.js' );
		$this->register_script( 'permalinks-view',                'admin/assets/js/views/permalinks.js' );
		$this->register_script( 'archive-pages-view',             'admin/assets/js/views/archive-pages.js' );
		$this->register_script( 'metabox-view',                   'admin/assets/js/views/metabox.js' );
		$this->register_script( 'library-view',                   'admin/assets/js/views/library/library.js' );
		$this->register_script( 'library-menu-view',              'admin/assets/js/views/library/menu.js' );
		$this->register_script( 'library-content-latest-view',    'admin/assets/js/views/library/content-latest.js' );
		$this->register_script( 'library-content-favorites-view', 'admin/assets/js/views/library/content-favorites.js' );
		$this->register_script( 'library-content-import-view',    'admin/assets/js/views/library/content-import.js' );
		$this->register_script( 'search-view',                    'admin/assets/js/views/search/search.js' );
		$this->register_script( 'search-history-view',            'admin/assets/js/views/search/history.js' );
		$this->register_script( 'search-settings-view',           'admin/assets/js/views/search/settings.js' );
		$this->register_script( 'search-status-view',             'admin/assets/js/views/search/status.js' );
		$this->register_script( 'search-results-view',            'admin/assets/js/views/search/results.js' );
		$this->register_script( 'editor-image-view',              'admin/assets/js/views/editor/image.js' );
		$this->register_script( 'editor-images-view',             'admin/assets/js/views/editor/images.js' );
		$this->register_script( 'editor-meta-view',               'admin/assets/js/views/editor/meta.js' );
		$this->register_script( 'editor-details-view',            'admin/assets/js/views/editor/details.js' );
		$this->register_script( 'editor-tagbox-view',             'admin/assets/js/views/editor/tagbox.js' );
		$this->register_script( 'editor-view',                    'admin/assets/js/views/editor/editor.js' );
		$this->register_script( 'modal-view',                     'admin/assets/js/views/modal/modal.js' );
		$this->register_script( 'modal-images-view',              'admin/assets/js/views/modal/images.js' );
		$this->register_script( 'modal-browser-view',             'admin/assets/js/views/modal/browser.js' );
		$this->register_script( 'modal-post-view',                'admin/assets/js/views/modal/post.js' );

		// Runners
		$this->register_script( 'library',       'admin/assets/js/wpmoly-library.js' );
		$this->register_script( 'api',           'admin/assets/js/wpmoly-api.js' );
		$this->register_script( 'metabox',       'admin/assets/js/wpmoly-metabox.js' );
		$this->register_script( 'permalinks',    'admin/assets/js/wpmoly-permalinks.js' );
		$this->register_script( 'archive-pages', 'admin/assets/js/wpmoly-archive-pages.js' );
		$this->register_script( 'editor',        'admin/assets/js/wpmoly-editor.js' );
		$this->register_script( 'grid-builder',  'admin/assets/js/wpmoly-grid-builder.js', array( 'butterbean' ) );
		$this->register_script( 'grids',         'public/assets/js/wpmoly-grids.js' );
		$this->register_script( 'search',        'admin/assets/js/wpmoly-search.js' );
		$this->register_script( 'tester',        'admin/assets/js/wpmoly-tester.js' );
	}

	/**
	 * Register stylesheets.
	 *
	 * @since    3.0
	 */
	protected function register_styles() {

		$this->register_style( 'admin',         'admin/assets/css/wpmoly.css' );
		$this->register_style( 'library',       'admin/assets/css/wpmoly-library.css' );
		$this->register_style( 'metabox',       'admin/assets/css/wpmoly-metabox.css' );
		$this->register_style( 'permalinks',    'admin/assets/css/wpmoly-permalink-settings.css' );
		$this->register_style( 'term-editor',   'admin/assets/css/wpmoly-term-editor.css' );
		$this->register_style( 'archive-pages', 'admin/assets/css/wpmoly-archive-pages.css' );
		$this->register_style( 'grid-builder',  'admin/assets/css/wpmoly-grid-builder.css' );

		$this->register_style( 'font',          'public/assets/fonts/wpmovielibrary/style.css' );
		$this->register_style( 'common',        'public/assets/css/common.css' );
		$this->register_style( 'grids',         'public/assets/css/wpmoly-grids.css' );
		$this->register_style( 'select2',       'admin/assets/css/select2.min.css' );
	}

	/**
	 * Register templates.
	 *
	 * @since    3.0
	 */
	protected function register_templates() {

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
	 * Enqueue the JavaScript for the admin area.
	 *
	 * @since    3.0
	 */
	public function enqueue_scripts() {

		global $hook_suffix;

		$this->register_scripts();

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
	 * Enqueue the stylesheets for the admin area.
	 *
	 * @since    3.0
	 */
	public function enqueue_styles() {

		global $hook_suffix;

		$this->register_styles();

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
	 * Print the JavaScript templates for the admin area.
	 *
	 * @since    3.0
	 */
	public function enqueue_templates() {

		global $hook_suffix;

		$this->register_templates();

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
	 * Plugged on the 'admin_init' action hook.
	 *
	 * This is a workaround for adding images from URL to the Media Uploader.
	 *
	 * Filter the $_FILES array before it reaches the 'upload-attachment'
	 * Ajax callback to fix the filename. PlUpload send data with filename
	 * containing 'blob', causing errors as WordPress is --and shouldn't--
	 * using that value to check files names and extensions.
	 *
	 * @since    3.0
	 */
	public function admin_init() {

		if ( ! defined( 'DOING_AJAX' ) || true !== DOING_AJAX ) {
			return false;
		}

		if ( ! empty( $_FILES['async-upload']['name'] ) && 'blob' == $_FILES['async-upload']['name'] ) {
			if ( ! empty( $_REQUEST['name'] ) && ( ! empty( $_REQUEST['_wpmoly_nonce'] ) && wp_verify_nonce( $_REQUEST['_wpmoly_nonce'], 'wpmoly-blob-filename' ) ) ) {
				$_FILES['async-upload']['name'] = $_REQUEST['name'];
			}
		}
	}

	/**
	 * Plugged on the 'admin_menu' action hook.
	 *
	 * Register the backstage library page.
	 *
	 * @since    3.0
	 */
	public function admin_menu() {

		$library = Library::get_instance();

		$menu_page = add_menu_page(
			$page_title = __( 'Movie Library' , 'wpmovielibrary' ),
			$menu_title = __( 'Movie Library' , 'wpmovielibrary' ),
			$capability = 'read',
			$menu_slug  = 'wpmovielibrary',
			$function   = array( $library, 'build' ),
			$icon_url   = 'dashicons-wpmoly',
			$position   = 2
		);
	}

	/**
	 * Plugged on the 'admin_menu' action hook.
	 *
	 * Add taxonomies menu entries to the custom admin menu.
	 *
	 * @since    3.0
	 */
	public function admin_submenu() {

		add_submenu_page( 'wpmovielibrary', __( 'Actors', 'wpmovielibrary' ), __( 'Actors', 'wpmovielibrary' ), 'manage_options', 'edit-tags.php?taxonomy=actor&post_type=movie' );
		add_submenu_page( 'wpmovielibrary', __( 'Collections', 'wpmovielibrary' ), __( 'Collections', 'wpmovielibrary' ), 'manage_options', 'edit-tags.php?taxonomy=collection&post_type=movie' );
		add_submenu_page( 'wpmovielibrary', __( 'Genres', 'wpmovielibrary' ), __( 'Genres', 'wpmovielibrary' ), 'manage_options', 'edit-tags.php?taxonomy=genre&post_type=movie' );
	}

	/**
	 * Add a custom nonce the default settings for PlUpload.
	 *
	 * @since    3.0
	 *
	 * @param    array    $params
	 *
	 * @return   array
	 */
	public function plupload_default_params( $params ) {

		global $pagenow;

		if ( ( empty( $pagenow ) || 'post.php' != $pagenow ) || 'movie' != get_post_type() ) {
			return $params;
		}

		$params['_wpmoly_nonce'] = wp_create_nonce( 'wpmoly-blob-filename' );

		return $params;
	}
}
