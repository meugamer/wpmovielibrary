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
			'grid'
		);

		$tags = Rewrite::get_instance()->tags;
		foreach ( $tags as $tag => $regex ) {
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
