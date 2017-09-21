<?php
/**
 * The file that defines the dashboard class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly;

use wpmoly\admin\editors;

/**
 * The dashboard class.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Dashboard {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_filter( 'admin_init',                array( &$this, 'admin_init' ) );
		add_filter( 'admin_menu',                array( &$this, 'admin_menu' ), 9 );
		add_filter( 'admin_menu',                array( &$this, 'admin_submenu' ), 10 );
		add_filter( 'plupload_default_params',   array( &$this, 'plupload_default_params' ) );

	}

	/**
	 * Register the plugin's assets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_assets() {

		$assets = core\Assets::get_instance();
		add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_admin_styles' ), 95 );
		add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_admin_scripts' ), 95 );
		add_action( 'admin_footer',          array( $assets, 'enqueue_admin_templates' ), 95 );
	}

	/**
	 * Register the Archive Page Editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_archive_editor() {

		$archives = new editors\Page;
		add_action( 'load-post.php',               array( $archives, 'load_meta_frameworks' ) );
		add_action( 'load-post-new.php',           array( $archives, 'load_meta_frameworks' ) );
		add_action( 'butterbean_register',         array( $archives, 'register_post_meta_managers' ), 10, 2 );
		add_action( 'post_submitbox_misc_actions', array( $archives, 'archive_pages_select' ), 10, 1 );
		add_action( 'save_post_page',              array( $archives, 'set_archive_page_type' ), 10, 3 );
	}

	/**
	 * Register the Grid Editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_grid_editor() {

		// Grid Builder
		// TODO load this on grid only
		$builder = new editors\Grid;
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
	}

	/**
	 * Register the Movie Editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_movie_editor() {}

	/**
	 * Register the Term Editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_term_editor() {

		// Term Editor
		$terms = new editors\Term;
		add_action( 'load-term.php',            array( $terms, 'load_meta_frameworks' ) );
		add_action( 'load-edit-tags.php',       array( $terms, 'load_meta_frameworks' ) );
		add_action( 'haricot_register',         array( $terms, 'register_term_meta_managers' ), 10, 2 );
		add_filter( 'redirect_term_location',   array( $terms, 'term_redirect' ), 10, 2 );
		add_action( 'actor_pre_edit_form',      array( $terms, 'term_pre_edit_form' ), 10, 2 );
		add_action( 'collection_pre_edit_form', array( $terms, 'term_pre_edit_form' ), 10, 2 );
		add_action( 'genre_pre_edit_form',      array( $terms, 'term_pre_edit_form' ), 10, 2 );
	}

	/**
	 * Register the Permalinks Editor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_permalinks_editor() {

		// Permalink Settings
		$permalinks = new editors\Permalinks;
		add_action( 'load-options-permalink.php',         array( $permalinks, 'register' ) );
		add_action( 'admin_init',                         array( $permalinks, 'update' ) );

		add_filter( 'rewrite_rules_array', array( $permalinks, 'fix_movie_rewrite_rules' ) );
		add_filter( 'rewrite_rules_array', array( $permalinks, 'add_movie_archives_rewrite_rules' ) );
		add_filter( 'rewrite_rules_array', array( $permalinks, 'add_taxonomy_archives_rewrite_rules' ) );

		add_action( 'admin_notices',                      array( $permalinks, 'register_notice' ) );
		add_action( 'generate_rewrite_rules',             array( $permalinks, 'delete_notice' ) );
		add_action( 'wpmoly/action/update/archive_pages', array( $permalinks, 'set_notice' ) );
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
	 * @since 3.0.0
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
	 * @since 3.0.0
	 */
	public function admin_menu() {

		$menu_page = add_menu_page( __( 'Movie Library' , 'wpmovielibrary' ), __( 'Movie Library' , 'wpmovielibrary' ), 'read', 'wpmovielibrary', '__return_false',/*array( $library, 'build' ),*/ 'dashicons-wpmoly', 2 );
	}

	/**
	 * Plugged on the 'admin_menu' action hook.
	 *
	 * Add taxonomies menu entries to the custom admin menu.
	 *
	 * @since 3.0.0
	 */
	public function admin_submenu() {

		add_submenu_page( 'wpmovielibrary', __( 'Actors', 'wpmovielibrary' ), __( 'Actors', 'wpmovielibrary' ), 'manage_options', 'edit-tags.php?taxonomy=actor&post_type=movie' );
		add_submenu_page( 'wpmovielibrary', __( 'Collections', 'wpmovielibrary' ), __( 'Collections', 'wpmovielibrary' ), 'manage_options', 'edit-tags.php?taxonomy=collection&post_type=movie' );
		add_submenu_page( 'wpmovielibrary', __( 'Genres', 'wpmovielibrary' ), __( 'Genres', 'wpmovielibrary' ), 'manage_options', 'edit-tags.php?taxonomy=genre&post_type=movie' );
	}

	/**
	 * Add a custom nonce the default settings for PlUpload.
	 *
	 * @since 3.0.0
	 *
	 * @param array $params
	 *
	 * @return array
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
