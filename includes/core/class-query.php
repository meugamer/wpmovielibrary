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

		$vars = apply_filters( 'wpmoly/filter/query/default/vars', array(
			'actor',
			'adult',
			'author',
			'budget',
			'certification',
			'company',
			'composer',
			'country',
			'director',
			'format',
			'genre',
			'grid',
			'imdb_id',
			'language',
			'languages',
			'local_release',
			'media',
			'photography',
			'preset',
			'producer',
			'rating',
			'release',
			'runtime',
			'status',
			'subtitles',
			'tmdb_id',
			'writer',
		) );

		$tags = Rewrite::get_instance()->tags;
		foreach ( array_keys( $tags ) as $tag ) {
			$vars[] = str_replace( '%', '', $tag );
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
	public function filter_posts_by_letter( $where, $query ) {

		global $wpdb;

		$letter = $query->get( 'letter' );
		if ( ! empty( $letter ) ) {
			$where .= " AND {$wpdb->posts}.post_title LIKE '" . $wpdb->esc_like( strtoupper( $letter ) ) . "%'";
		}

		return $where;
	}
}
