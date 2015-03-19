<?php
/**
 * WPMovieLibrary Import Class extension.
 * 
 * Import Movies
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Import' ) ) :

	class WPMOLY_Import extends WPMOLY_Module {

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_filter( 'set-screen-option', __CLASS__ . '::import_movie_list_set_option', 10, 3 );

			add_action( 'wp_ajax_wpmoly_fetch_draftees', array( $this, 'fetch_draftees_callback' ) );
			add_action( 'wp_ajax_wpmoly_save_draftees', array( $this, 'save_draftees_callback' ) );
			add_action( 'wp_ajax_wpmoly_empty_draftees', array( $this, 'empty_draftees_callback' ) );

			add_action( 'wp_ajax_wpmoly_delete_movies', __CLASS__ . '::delete_movies_callback' );
			add_action( 'wp_ajax_wpmoly_import_movies', __CLASS__ . '::import_movies_callback' );
			add_action( 'wp_ajax_wpmoly_imported_movies', __CLASS__ . '::imported_movies_callback' );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                          Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		public function fetch_draftees_callback() {

			$response = $this->get_draftees();
			foreach ( $response as $i => $r ) {
				$response[ $i ]['title'] = esc_attr( $r['title'] );
			}

			sleep(2);
			wp_send_json_success( $response );
		}

		public function save_draftees_callback() {

			$draftees = ( isset( $_POST['data'] ) && '' != $_POST['data'] ? $_POST['data'] : null );
			if ( is_null( $draftees ) )
				wp_send_json_error();

			$response = $this->save_draftees( $draftees );

			wp_send_json_success( $response );
		}

		public function empty_draftees_callback() {

			$empty = $this->empty_draftees();
			if ( ! $empty )
				wp_send_json_error();

			wp_send_json_success();
		}

		/**
		 * Delete movie
		 * 
		 * Remove imported movies draft and attachment from database
		 *
		 * @since    1.0
		 */
		public static function delete_movies_callback() {

			wpmoly_check_ajax_referer( 'delete-movies' );

			$movies = ( isset( $_GET['movies'] ) && '' != $_GET['movies'] ? $_GET['movies'] : null );

			$response = self::delete_movies( $movies );
			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( 'delete-movies' ) );
		}

		/**
		 * Callback for Imported Movies WPMOLY_Import_Table AJAX navigation.
		 * 
		 * Checks the AJAX nonce, create a new instance of WPMOLY_Import_Table
		 * and calls the AJAX handling method to echo the requested rows.
		 *
		 * @since    1.0
		 */
		public static function imported_movies_callback() {

			wpmoly_check_ajax_referer( 'imported-movies' );

			$wp_list_table = new WPMOLY_Import_Table();
			$wp_list_table->ajax_response();
		}

		/**
		 * Callback for WPMOLY_Import movie import method.
		 * 
		 * Checks the AJAX nonce and calls import_movies() to
		 * create import drafts of all movies passed through the list.
		 *
		 * @since    1.0
		 */
		public static function import_movies_callback() {

			wpmoly_check_ajax_referer( 'import-movies-list' );

			$movies = ( isset( $_POST['movies'] ) && '' != $_POST['movies'] ? esc_textarea( $_POST['movies'] ) : null );

			$response = self::import_movies( $movies );
			wpmoly_ajax_response( $response, array(), wpmoly_create_nonce( 'import-movies-list' ) );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                            Methods
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Save movie draftees.
		 * 
		 * @since    2.2
		 * 
		 * @param    array    $draftees Array of movie draftees to save
		 * 
		 * @return   boolean    True if update was successfull, false else
		 */
		public function save_draftees( $draftees ) {

			foreach ( $draftees as $i => $draftee ) {
				if ( isset( $draftee['title'] ) ) {
					$draftees[ $i ]['title'] = esc_attr( $draftee['title'] );
				}
			}

			$update = update_option( '_wpmoly_import_draftess', $draftees );

			return $update;
		}

		/**
		 * Empty movie draftees.
		 * 
		 * @since    2.2
		 * 
		 * @return   boolean    True if update was successfull, false else
		 */
		public function empty_draftees() {

			return update_option( '_wpmoly_import_draftess', array() );
		}

		/**
		 * Delete movies
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $movies Array of movies Post IDs to delete
		 * 
		 * @return   array|WP_Error    Array of delete movie IDs if successfull, WP_Error instance if anything failed
		 */
		public static function delete_movies( $movies ) {

			$errors = new WP_Error();
			$response = array();

			if ( is_null( $movies ) || ! is_array( $movies ) ) {
				$errors->add( 'invalid', __( 'Invalid movie list submitted.', 'wpmovielibrary' ) );
				return $errors;
			}

			$response = wpmoly_ajax_filter( array( __CLASS__, 'delete_movie' ), array( $movies ), $loop = true );

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $response;
		}

		/**
		 * Delete imported movie
		 * 
		 * Delete the specified movie from the list of movie set for further
		 * import. Automatically delete attached images such as featured
		 * image if any is found.
		 *
		 * @since    1.0
		 * 
		 * @param    int    $post_id Movie's post ID.
		 * 
		 * @return   int|WP_Error    Movie Post ID if deleted, WP_Error if Post or Attachment delete failed
		 */
		public static function delete_movie( $post_id ) {

			if ( false === wp_delete_post( $post_id, $force_delete = true ) )
				return new WP_Error( 'error', sprintf( __( 'An error occured trying to delete Post #%s', 'wpmovielibrary' ), $post_id ) );

			$thumb_id = get_post_thumbnail_id( $post_id );
			if ( '' != $thumb_id )
				if ( false === wp_delete_attachment( $thumb_id ) )
					return new WP_Error( 'error', sprintf( __( 'An error occured trying to delete Attachment #%s', 'wpmovielibrary' ), $thumb_id ) );

			return $post_id;
		}

		/**
		 * Process the submitted movie list
		 * 
		 * This method can be used through an AJAX callback; in this case
		 * the nonce check is already done in callback so we only check
		 * for nonce we're not doing AJAX. List is exploded by comma and
		 * fed to import_movie() to create import drafts.
		 * 
		 * If AJAX, the function echo a status message and simply dies.
		 * If no AJAX, ir returns false on failure, and a status message
		 * on success.
		 *
		 * @since    1.0
		 * 
		 * @param    array    $movies Array of movie titles to import
		 * 
		 * @return   mixed
		 */
		private static function import_movies( $movies ) {

			$errors = new WP_Error();
			$response = array();

			$movies = explode( ',', $movies );
			$movies = array_map( __CLASS__ . '::prepare_movie_import', $movies );

			if ( is_null( $movies ) || ! is_array( $movies ) ) {
				$errors->add( 'invalid', __( 'Invalid movie list submitted.', 'wpmovielibrary' ) );
				return $errors;
			}

			$response = wpmoly_ajax_filter( array( __CLASS__, 'import_movie' ), array( $movies ), $loop = true );

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $response;
		}

		/**
		 * Save a temporary movie for submitted title.
		 * 
		 * This is used to save movies submitted from a list before any
		 * alteration is made by user. Posts will be kept as 'import-draft'
		 * for 24 hours and then destroyed on the next plugin init.
		 *
		 * @since    1.0
		 * 
		 * @param    string    $title Movie title.
		 * 
		 * @return   int       Newly created post ID if everything worked, 0 if no post created.
		 */
		public static function import_movie( $movie ) {

			$post_date     = current_time('mysql');
			$post_date     = wp_checkdate( substr( $post_date, 5, 2 ), substr( $post_date, 8, 2 ), substr( $post_date, 0, 4 ), $post_date );
			$post_date_gmt = get_gmt_from_date( $post_date );
			$post_author   = get_current_user_id();
			$post_content  = '';
			$post_excerpt  = '';
			$post_title    = $movie['movietitle'];

			$page = get_page_by_title( $post_title, OBJECT, 'movie' );

			if ( ! is_null( $page ) ) {
				$message = sprintf( '%s − <span class="edit"><a href="%s">%s</a> |</span> <span class="view"><a href="%s">%s</a></span>',
					sprintf( __( 'Movie "%s" already imported.', 'wpmovielibrary' ), "<em>" . get_the_title( $page->ID ) . "</em>" ),
					get_edit_post_link( $page->ID ),
					__( 'Edit', 'wpmovielibrary' ),
					get_permalink( $page->ID ),
					__( 'View', 'wpmovielibrary' )
				);
				return new WP_Error( 'existing_movie', $message );
			}

			$posts = array(
				'ID'             => '',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_author'    => $post_author,
				'post_content'   => $post_content,
				'post_excerpt'   => $post_excerpt,
				'post_date'      => $post_date,
				'post_date_gmt'  => $post_date_gmt,
				'post_name'      => sanitize_title( $post_title ),
				'post_status'    => 'import-draft',
				'post_title'     => $post_title,
				'post_type'      => 'movie'
			);

			$import = wp_insert_post( $posts );

			return $import;
		}

		/**
		 * Set the default values for imported movies list
		 *
		 * @since    1.0
		 * 
		 * @param    string    $title    Movie title
		 * 
		 * @return   array     Default movie values
		 */
		private static function prepare_movie_import( $title ) {

			$title = str_replace( "\&#039;", "'", $title );

			$movie = array(
				'ID'         => 0,
				'poster'     => '--',
				'movietitle' => trim( $title ),
				'director'   => '--',
				'tmdb_id'    => '--'
			);

			return $movie;
		}

		/**
		 * Get previously stored movie draftees.
		 *
		 * @since    2.2
		 * 
		 * @return   array    Stored draftees if any, empty array else
		 */
		public static function get_draftees() {

			return get_option( '_wpmoly_import_draftess', array() );
		}

		/**
		 * Get previously imported movies.
		 * 
		 * Fetch all posts with 'import-draft' status and 'movie' post type
		 *
		 * @since    1.0
		 * 
		 * @return   array    Default movie values
		 */
		public static function get_imported_movies() {

			$columns = array();

			$args = array(
				'posts_per_page' => -1,
				'post_type'   => 'movie',
				'post_status' => 'import-draft'
			);

			query_posts( $args );

			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					if ( 'import-draft' == get_post_status() ) {
						$columns[ get_the_ID() ] = array(
							'ID'         => get_the_ID(),
							'poster'     => get_post_meta( get_the_ID(), '_wp_attached_file', true ),
							'movietitle' => get_the_title(),
							'director'   => get_post_meta( get_the_ID(), '_wpmoly_tmdb_director', true ),
							'tmdb_id'    => get_post_meta( get_the_ID(), '_wpmoly_tmdb_id', true )
						);
					}
				}
			}

			return $columns;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {}

	}

endif;