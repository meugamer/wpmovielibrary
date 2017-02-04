<?php
/**
 * Define the Query class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Query {

	/**
	 * Custom query vars.
	 * 
	 * @var    array
	 */
	public $vars;

	/**
	 * Singleton.
	 *
	 * @var    Query
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	private function __construct() {

		$vars = array(
			'grid',
			'grid_preset',
			'wpmoly_movie_adult',
			'wpmoly_movie_author',
			'wpmoly_movie_certification',
			'wpmoly_movie_composer',
			'wpmoly_movie_homepage',
			'wpmoly_movie_imdb_id',
			'wpmoly_movie_local_release_date',
			'wpmoly_movie_photography',
			'wpmoly_movie_producer',
			'wpmoly_movie_production_companies',
			'wpmoly_movie_production_countries',
			'wpmoly_movie_release_date',
			'wpmoly_movie_spoken_languages',
			'wpmoly_movie_tmdb_id',
			'wpmoly_movie_writer',
			'wpmoly_movie_format',
			'wpmoly_movie_language',
			'wpmoly_movie_media',
			'wpmoly_movie_rating',
			'wpmoly_movie_status',
			'wpmoly_movie_subtitles'
		);

		$tags = Rewrite::get_instance()->tags;
		foreach ( array_keys( $tags ) as $tag ) {
			$vars[] = 'wpmoly_' . str_replace( '%', '', $tag );
		}

		$this->vars = $vars;
	}

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Singleton
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register query vars for custom rewrite tags.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $query_vars
	 * 
	 * @return   void
	 */
	public function add_query_vars( $query_vars ) {

		$query_vars = array_merge( $query_vars, $this->vars );

		return $query_vars;
	}

	/**
	 * "Unsanitize" a query var, ie. turn a sanitized text to its equivalent
	 * before sanitization. The name can be confusing but this actually just
	 * replace any '-' by an empty space and add some capital letters. For
	 * instance "a-sample-text" will be converted to "A sample text".
	 * 
	 * This is used by movie queries to match URL query vars to the real movie
	 * metadata.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Sanitized value to "unsanitize".
	 * 
	 * @return   string
	 */
	public function unsanitize_query_var( $query_var ) {

		$query_var = str_replace( array( '-', '_' ), ' ', $query_var );
		$query_var = ucwords( $query_var );

		return $query_var;
	}

	/**
	 * Convert a sanitized rating to a float-formatted value.
	 * 
	 * This is used by movie queries to match URL query vars to the real movie
	 * metadata.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Sanitized value to "unsanitize".
	 * 
	 * @return   string
	 */
	public function filter_rating_query_var( $query_var ) {

		$query_var = str_replace( '-', '.', $query_var );
		$query_var = number_format( $query_var, 1 );

		return $query_var;
	}

	/**
	 * Convert a sanitized date to a standard YYYY-MM-DD date format.
	 * 
	 * This is used by movie queries to match URL query vars to the real movie
	 * metadata.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Sanitized value to "unsanitize".
	 * 
	 * @return   string
	 */
	public function filter_date_query_var( $query_var ) {

		$query_var = date( 'Y-m-d', strtotime( $query_var ) );

		return $query_var;
	}

	/**
	 * Convert a sanitized language code, standard name, native name or
	 * localized name to its clean native name.
	 * 
	 * This is used by movie queries to match URL query vars to the real movie
	 * metadata.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Sanitized value to "unsanitize".
	 * 
	 * @return   string
	 */
	public function filter_language_query_var( $query_var ) {

		$query_var = get_language( $query_var );
		$query_var = $query_var->native_name;

		return $query_var;
	}

	
	/**
	 * Convert a sanitized country code, standard name or localized name to
	 * its clean standard name.
	 * 
	 * This is used by movie queries to match URL query vars to the real movie
	 * metadata.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Sanitized value to "unsanitize".
	 * 
	 * @return   string
	 */
	public function filter_country_query_var( $query_var ) {

		$query_var = get_country( $query_var );
		$query_var = $query_var->standard_name;

		return $query_var;
	}

	/**
	 * Filter collection by letter.
	 * 
	 * Add a new WHERE clause to the current query to limit selection to the
	 * movies with a title starting with a specific letter.
	 * 
	 * @since    3.0
	 * 
	 * @param    string      $where
	 * @param    WP_Query    $query
	 * 
	 * @return   string
	 */
	public function filter_by_letter( $where, $query ) {

		global $wpdb;

		$letter = $query->get( 'letter' );
		if ( ! empty( $letter ) ) {
			$where .= " AND {$wpdb->posts}.post_title LIKE '" . $wpdb->esc_like( strtoupper( $letter ) ) . "%'";
		}

		return $where;
	}
}
