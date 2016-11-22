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
}
