<?php
/**
 * The file that defines the core plugin class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly;

/**
 * The core plugin class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * 
 * @author     Charlie Merland <charlie@caercam.org>
 */
final class Library {

	/**
	 * The single instance of the plugin.
	 *
	 * @since      3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Library
	 */
	private static $_instance = null;

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $version = WPMOLY_VERSION;

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

		// Run the plugin.
		add_action( 'plugins_loaded', array( &$this, 'run' ) );

		// Load translations.
		add_action( 'init', array( &$this, 'translate' ) );

		// Load required files.
		add_action( 'wpmoly/run', array( &$this, 'require_core_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_template_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_helper_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_node_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_rest_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_tmdb_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_widget_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_editor_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_shortcode_files' ) );

		// Register Custom Post Types, Taxonomies…
		add_action( 'wpmoly/core/loaded', array( &$this, 'register_post_types' ) );
		add_action( 'wpmoly/core/loaded', array( &$this, 'register_taxonomies' ) );
		add_action( 'wpmoly/core/loaded', array( &$this, 'register_post_meta' ) );
		add_action( 'wpmoly/core/loaded', array( &$this, 'register_term_meta' ) );
	}

	public function require_core_files() {

		do_action( 'wpmoly/core/load' );

		require_once WPMOLY_PATH . 'includes/core/class-assets.php';
		require_once WPMOLY_PATH . 'includes/core/class-loader.php';
		require_once WPMOLY_PATH . 'includes/core/class-l10n.php';
		require_once WPMOLY_PATH . 'includes/core/class-registrar.php';
		require_once WPMOLY_PATH . 'includes/core/class-query.php';

		do_action( 'wpmoly/core/loaded' );
	}

	public function require_template_files() {

		do_action( 'wpmoly/templates/load' );

		require_once WPMOLY_PATH . 'includes/templates/class-template.php';
		require_once WPMOLY_PATH . 'includes/templates/class-admin.php';
		require_once WPMOLY_PATH . 'includes/templates/class-front.php';
		require_once WPMOLY_PATH . 'includes/templates/class-javascript.php';
		require_once WPMOLY_PATH . 'includes/templates/class-grid.php';
		require_once WPMOLY_PATH . 'includes/templates/class-headbox.php';

		do_action( 'wpmoly/templates/loaded' );
	}

	public function require_helper_files() {

		do_action( 'wpmoly/helpers/load' );

		require_once WPMOLY_PATH . 'includes/helpers/defaults.php';
		require_once WPMOLY_PATH . 'includes/helpers/utils.php';
		require_once WPMOLY_PATH . 'includes/helpers/templates.php';
		require_once WPMOLY_PATH . 'includes/helpers/permalinks.php';
		require_once WPMOLY_PATH . 'includes/helpers/formatting.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-country.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-language.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-terms.php';

		do_action( 'wpmoly/helpers/loaded' );
	}

	public function require_node_files() {

		do_action( 'wpmoly/nodes/load' );

		require_once WPMOLY_PATH . 'includes/nodes/class-nodes.php';
		require_once WPMOLY_PATH . 'includes/nodes/class-node.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-image.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-image.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-backdrop.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-poster.php';
		require_once WPMOLY_PATH . 'includes/nodes/posts/class-movie.php';
		require_once WPMOLY_PATH . 'includes/nodes/posts/class-grid.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-taxonomy.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-actor.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-collection.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-genre.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-headbox.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-post.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-term.php';

		do_action( 'wpmoly/nodes/loaded' );
	}

	public function require_rest_files() {

		do_action( 'wpmoly/rest/load' );

		require_once WPMOLY_PATH . 'includes/rest-api/class-api.php';
		require_once WPMOLY_PATH . 'includes/rest-api/fields/class-grid-meta.php';
		require_once WPMOLY_PATH . 'includes/rest-api/fields/class-movie-meta.php';
		require_once WPMOLY_PATH . 'includes/rest-api/controllers/class-grids.php';
		require_once WPMOLY_PATH . 'includes/rest-api/controllers/class-movies.php';

		do_action( 'wpmoly/rest/loaded' );
	}

	public function require_tmdb_files() {

		do_action( 'wpmoly/api/load' );

		require_once WPMOLY_PATH . 'includes/api/class-api.php';
		require_once WPMOLY_PATH . 'includes/api/tmdb/class-tmdb.php';
		require_once WPMOLY_PATH . 'includes/api/tmdb/class-movie.php';

		do_action( 'wpmoly/api/loaded' );
	}

	public function require_widget_files() {

		do_action( 'wpmoly/widgets/load' );

		require_once WPMOLY_PATH . 'includes/widgets/class-widget.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-statistics.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-details.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-grid.php';

		do_action( 'wpmoly/widgets/loaded' );
	}

	public function require_shortcode_files() {

		do_action( 'wpmoly/shortcodes/load' );

		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-grid.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-headbox.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-images.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-metadata.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-detail.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-countries.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-languages.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-runtime.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-release-date.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-local-release-date.php';

		do_action( 'wpmoly/shortcodes/loaded' );
	}

	public function require_editor_files() {

		if ( is_admin() ) {
			return false;
		}

		do_action( 'wpmoly/editors/load' );

		require_once WPMOLY_PATH . 'admin/class-rewrite.php';
		require_once WPMOLY_PATH . 'admin/class-backstage.php';
		require_once WPMOLY_PATH . 'admin/editors/class-editor.php';
		require_once WPMOLY_PATH . 'admin/editors/class-page.php';
		require_once WPMOLY_PATH . 'admin/editors/class-grid.php';
		require_once WPMOLY_PATH . 'admin/editors/class-movie.php';
		require_once WPMOLY_PATH . 'admin/editors/class-term.php';
		require_once WPMOLY_PATH . 'admin/class-library.php';
		require_once WPMOLY_PATH . 'admin/class-permalink-settings.php';

		do_action( 'wpmoly/editors/loaded' );
	}

	public function register_post_types() {

		// Register Post Types, Taxonomies…
		$registrar = core\Registrar::get_instance();
		add_action( 'init', array( $registrar, 'register_post_types' ) );
		add_action( 'init', array( $registrar, 'register_post_statuses' ) );
		add_action( 'init', array( $registrar, 'register_post_meta' ) );
		add_action( 'init', array( $registrar, 'register_taxonomies' ) );
		add_action( 'init', array( $registrar, 'register_term_meta' ) );
	}

	public function register_taxonomies() {

		
	}

	public function register_post_meta() {

		
	}

	public function register_term_meta() {

		
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	/*private function load_dependencies() {

		// Core
		require_once WPMOLY_PATH . 'includes/core/class-assets.php';
		require_once WPMOLY_PATH . 'includes/core/class-loader.php';
		require_once WPMOLY_PATH . 'includes/core/class-l10n.php';
		require_once WPMOLY_PATH . 'includes/core/class-registrar.php';
		require_once WPMOLY_PATH . 'includes/core/class-query.php';

		// Templates
		require_once WPMOLY_PATH . 'includes/templates/class-template.php';
		require_once WPMOLY_PATH . 'includes/templates/class-admin.php';
		require_once WPMOLY_PATH . 'includes/templates/class-front.php';
		require_once WPMOLY_PATH . 'includes/templates/class-javascript.php';
		require_once WPMOLY_PATH . 'includes/templates/class-grid.php';
		require_once WPMOLY_PATH . 'includes/templates/class-headbox.php';

		// Helpers
		require_once WPMOLY_PATH . 'includes/helpers/defaults.php';
		require_once WPMOLY_PATH . 'includes/helpers/utils.php';
		require_once WPMOLY_PATH . 'includes/helpers/templates.php';
		require_once WPMOLY_PATH . 'includes/helpers/permalinks.php';
		require_once WPMOLY_PATH . 'includes/helpers/formatting.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-country.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-language.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-terms.php';

		// Nodes
		require_once WPMOLY_PATH . 'includes/nodes/class-nodes.php';
		require_once WPMOLY_PATH . 'includes/nodes/class-node.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-image.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-image.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-backdrop.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-poster.php';
		require_once WPMOLY_PATH . 'includes/nodes/posts/class-movie.php';
		require_once WPMOLY_PATH . 'includes/nodes/posts/class-grid.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-taxonomy.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-actor.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-collection.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-genre.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-headbox.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-post.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-term.php';

		// Rest API
		require_once WPMOLY_PATH . 'includes/rest-api/class-api.php';
		require_once WPMOLY_PATH . 'includes/rest-api/fields/class-grid-meta.php';
		require_once WPMOLY_PATH . 'includes/rest-api/fields/class-movie-meta.php';
		require_once WPMOLY_PATH . 'includes/rest-api/controllers/class-grids.php';
		require_once WPMOLY_PATH . 'includes/rest-api/controllers/class-movies.php';

		// TMDb API
		require_once WPMOLY_PATH . 'includes/api/class-api.php';
		require_once WPMOLY_PATH . 'includes/api/tmdb/class-tmdb.php';
		require_once WPMOLY_PATH . 'includes/api/tmdb/class-movie.php';

		// Main
		require_once WPMOLY_PATH . 'includes/class-admin-bar.php';
		require_once WPMOLY_PATH . 'public/class-frontend.php';
		require_once WPMOLY_PATH . 'public/class-archives.php';

		// Widgets
		require_once WPMOLY_PATH . 'includes/widgets/class-widget.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-statistics.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-details.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-grid.php';

		if ( is_admin() ) {
			// Admin stuff
			require_once WPMOLY_PATH . 'admin/class-rewrite.php';
			require_once WPMOLY_PATH . 'admin/class-backstage.php';
			require_once WPMOLY_PATH . 'admin/editors/class-editor.php';
			require_once WPMOLY_PATH . 'admin/editors/class-page.php';
			require_once WPMOLY_PATH . 'admin/editors/class-grid.php';
			require_once WPMOLY_PATH . 'admin/editors/class-movie.php';
			require_once WPMOLY_PATH . 'admin/editors/class-term.php';
			require_once WPMOLY_PATH . 'admin/class-library.php';
			require_once WPMOLY_PATH . 'admin/class-permalink-settings.php';
		} else {
			// Shortcodes
			require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-grid.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-headbox.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-images.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-metadata.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-detail.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-countries.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-languages.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-runtime.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-release-date.php';
			require_once WPMOLY_PATH . 'public/shortcodes/class-local-release-date.php';
		}

	}*/

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 3.0
	 *
	 * @access public
	 */
	/*public function define_admin_hooks() {

		if ( ! is_admin() ) {
			return false;
		}

		$admin = admin\Backstage::get_instance();
		add_filter( 'admin_init',                array( $admin, 'admin_init' ) );
		add_filter( 'admin_menu',                array( $admin, 'admin_menu' ), 9 );
		add_filter( 'admin_menu',                array( $admin, 'admin_submenu' ), 10 );
		add_filter( 'plupload_default_params',   array( $admin, 'plupload_default_params' ) );
		add_action( 'admin_enqueue_scripts',     array( $admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts',     array( $admin, 'enqueue_scripts' ) );
		add_action( 'admin_footer-post.php',     array( $admin, 'enqueue_templates' ) );
		add_action( 'admin_footer-post-new.php', array( $admin, 'enqueue_templates' ) );
		add_action( 'admin_footer-toplevel_page_wpmovielibrary', array( $admin, 'enqueue_templates' ) );

		// Term Editor
		$terms = new admin\editors\Term;
		add_action( 'load-term.php',               array( $terms, 'load_meta_frameworks' ) );
		add_action( 'load-edit-tags.php',          array( $terms, 'load_meta_frameworks' ) );
		add_action( 'haricot_register',            array( $terms, 'register_term_meta_managers' ), 10, 2 );
		add_filter( 'redirect_term_location',      array( $terms, 'term_redirect' ), 10, 2 );
		add_action( 'actor_pre_edit_form',         array( $terms, 'term_pre_edit_form' ), 10, 2 );
		add_action( 'collection_pre_edit_form',    array( $terms, 'term_pre_edit_form' ), 10, 2 );
		add_action( 'genre_pre_edit_form',         array( $terms, 'term_pre_edit_form' ), 10, 2 );

		// Archive Pages
		$archives = new admin\editors\Page;
		add_action( 'load-post.php',               array( $archives, 'load_meta_frameworks' ) );
		add_action( 'load-post-new.php',           array( $archives, 'load_meta_frameworks' ) );
		add_action( 'butterbean_register',         array( $archives, 'register_post_meta_managers' ), 10, 2 );
		add_action( 'post_submitbox_misc_actions', array( $archives, 'archive_pages_select' ), 10, 1 );
		add_action( 'save_post_page',              array( $archives, 'set_archive_page_type' ), 10, 3 );

		// Grid Builder
		// TODO load this on grid only
		$builder = new admin\editors\Grid;
		add_filter( 'post_updated_messages',       array( $builder, 'updated_messages' ) );
		add_action( 'add_meta_boxes',              array( $builder, 'add_meta_boxes' ), 4 );
		add_action( 'edit_form_top',               array( $builder, 'header' ) );
		add_action( 'post_submitbox_start',        array( $builder, 'submitbox' ) );
		add_action( 'dbx_post_sidebar',            array( $builder, 'footer' ) );
		add_action( 'load-post.php',               array( $builder, 'load_meta_frameworks' ) );
		add_action( 'load-post-new.php',           array( $builder, 'load_meta_frameworks' ) );
		add_action( 'butterbean_register',         array( $builder, 'register_post_meta_managers' ), 10, 2 );
		add_action( 'save_post_grid',              array( $builder, 'publish' ), 9, 2 );
		add_action( 'save_post_grid',              array( $builder, 'save' ), 9, 3 );

		// Permalink Settings
		$permalinks = admin\Permalink_Settings::get_instance();
		add_action( 'load-options-permalink.php', array( $permalinks, 'register' ) );
		add_action( 'admin_init',                 array( $permalinks, 'update' ) );

		$rewrite = admin\Rewrite::get_instance();
		add_filter( 'rewrite_rules_array',                array( $rewrite, 'rewrite_rules' ) );
		add_action( 'admin_notices',                      array( $rewrite, 'register_notice' ) );
		add_action( 'generate_rewrite_rules',             array( $rewrite, 'delete_notice' ) );
		add_action( 'wpmoly/action/update/archive_pages', array( $rewrite, 'set_notice' ) );
	}*/

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since 3.0
	 *
	 * @access public
	 */
	/*public function define_public_hooks() {

		$adminbar = Admin_Bar::get_instance();
		add_action( 'admin_bar_menu', array( $adminbar, 'edit_grid_menu' ), 95, 1 );

		$public = Frontend::get_instance();
		$public->set_default_filters();

		add_action( 'wp_enqueue_scripts',      array( $public, 'enqueue_styles' ), 95 );
		add_action( 'wp_enqueue_scripts',      array( $public, 'enqueue_scripts' ), 95 );
		add_action( 'wp_print_footer_scripts', array( $public, 'enqueue_templates' ) );
		add_action( 'init',                    array( $public, 'register_shortcodes' ) );
		add_action( 'widgets_init',            array( $public, 'register_widgets' ) );
		add_filter( 'the_content',             array( $public, 'the_headbox' ) );

		$archives = Archives::get_instance();
		add_filter( 'the_content',        array( $archives, 'archive_page_content' ), 10, 1 );
		add_filter( 'single_post_title',  array( $archives, 'archive_page_title' ), 10, 2 );
		add_filter( 'the_title',          array( $archives, 'archive_page_post_title' ), 10, 2 );

		// Register Post Types, Taxonomies…
		$registrar = core\Registrar::get_instance();
		add_action( 'init', array( $registrar, 'register_post_types' ) );
		add_action( 'init', array( $registrar, 'register_post_statuses' ) );
		add_action( 'init', array( $registrar, 'register_post_meta' ) );
		add_action( 'init', array( $registrar, 'register_taxonomies' ) );
		add_action( 'init', array( $registrar, 'register_term_meta' ) );

		$rest_api = rest\API::get_instance();
		add_action( 'rest_api_init',                     array( $rest_api, 'register_fields' ) );
		add_filter( 'rest_movie_query',                  array( $rest_api, 'add_post_query_params' ), 10, 2 );
		add_filter( 'rest_actor_query',                  array( $rest_api, 'add_term_query_params' ), 10, 2 );
		add_filter( 'rest_collection_query',             array( $rest_api, 'add_term_query_params' ), 10, 2 );
		add_filter( 'rest_genre_query',                  array( $rest_api, 'add_term_query_params' ), 10, 2 );
		add_filter( 'rest_movie_collection_params',      array( $rest_api, 'register_collection_params' ), 10, 2 );
		add_filter( 'rest_actor_collection_params',      array( $rest_api, 'register_collection_params' ), 10, 2 );
		add_filter( 'rest_collection_collection_params', array( $rest_api, 'register_collection_params' ), 10, 2 );
		add_filter( 'rest_genre_collection_params',      array( $rest_api, 'register_collection_params' ), 10, 2 );
		add_filter( 'rest_prepare_movie',                array( $rest_api, 'prepare_movie_for_response' ), 10, 3 );

		$query = core\Query::get_instance();
		add_filter( 'query_vars',     array( $query, 'add_query_vars' ) );
		add_filter( 'init',           array( $query, 'add_rewrite_tags' ) );
		add_filter( 'post_type_link', array( $query, 'replace_movie_link_tags' ), 10, 4 );
		add_filter( 'posts_where',    array( $query, 'filter_movies_by_letter' ), 10, 2 );
		add_filter( 'pre_get_posts',  array( $query, 'filter_movies_by_preset' ), 10, 1 );

		$terms = helpers\Terms::get_instance();
		add_filter( 'get_the_terms',                 array( $terms, 'get_the_terms' ),            10, 3 );
		add_filter( 'wp_get_object_terms',           array( $terms, 'get_ordered_object_terms' ), 10, 4 );
		add_filter( 'wpmoly/filter/post_type/movie', array( $terms, 'movie_standard_taxonomies' ) );

	}*/

	/**
	 * Load plugin translations.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function translate() {

		$plugin_path = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

		// Load main translations.
		load_plugin_textdomain( 'wpmovielibrary', false, $plugin_path );

		// Load countries and languages translations.
		load_plugin_textdomain( 'wpmovielibrary-iso', false, $plugin_path );
	}

	/**
	 * Run Forrest, run!
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function run() {

		// Run Forrest, run!
		do_action( 'wpmoly/run' );
	}

}
