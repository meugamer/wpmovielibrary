<?php
/**
 * Define the Rewrite class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * Handle the plugin's URL rewriting.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Rewrite {

	/**
	 * Default rewrite tags.
	 * 
	 * @var    array
	 */
	public $tags;

	/**
	 * Singleton.
	 *
	 * @var    Rewrite
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	private function __construct() {

		$permalinks = get_option( 'wpmoly_permalinks' );
		if ( empty( $permalinks ) ) {
			$permalinks = array();
		}

		$this->permalinks = $permalinks;

		$tags = array(
			'%imdb_id%'          => '(tt[0-9]+)',
			'%tmdb_id%'          => '([0-9]+)',
			'%year%'             => '([0-9]{4})',
			'%monthnum%'         => '([0-9]{1,2})',
			'%day%'              => '([0-9]{1,2})',
			'%release_year%'     => '([0-9]{4})',
			'%release_monthnum%' => '([0-9]{1,2})',
			'%release_day%'      => '([0-9]{1,2})'
		);
		$this->tags = $tags;
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
	 * Register custom rewrite tags.
	 * 
	 * Add a set of new movie-related rewrite tags.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function add_rewrite_tags() {

		global $wp_rewrite;

		foreach ( $this->tags as $tag => $regex ) {
			$wp_rewrite->add_rewrite_tag( $tag, $regex, '' );
		}
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

		foreach ( $this->tags as $tag => $regex ) {
			$query_vars[] = 'wpmoly_movie_' . str_replace( '%', '', $tag );
		}

		return $query_vars;
	}

	/**
	 * Replace custom rewrite tags in post links.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink
	 * @param    object     $post WP_Post instance
	 * @param    boolean    $leavename
	 * @param    boolean    $sample
	 * 
	 * @return   string
	 */
	public function replace_post_link_tags( $permalink, $post, $leavename, $sample ) {

		if ( $sample || 'movie' != $post->post_type ) {
			return $permalink;
		}

		return $this->replace_tags( $permalink, $post );
	}

	/**
	 * Add custom rewrite rules for movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function add_movie_rewrite_rules() {

		global $wp_rewrite;

		$rules = array();

		$dates = array(
			array(
				'rule' => "([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})",
				'vars' => array( 'year', 'monthnum', 'day' )
			),
			array(
				'rule' => "([0-9]{4})/([0-9]{1,2})",
				'vars' => array( 'year', 'monthnum' )
			),
			array(
				'rule' => "([0-9]{4})",
				'vars' => array( 'year' )
			)
		);

		foreach ( $dates as $date ) {

			$query = 'index.php?post_type=movie';
			$rule  = trim( $this->permalinks['movies'], '/' ) . '/' . $date['rule'];

			$i = 1;
			foreach ( $date['vars'] as $var ) {
				$query .= '&' . $var . '=' . $wp_rewrite->preg_index( $i );
				$i++;
			}

			$rules[ $rule . "/?$" ]                               = $query;
			$rules[ $rule . "/embed/?$" ]                         = $query . "&embed=true";
			$rules[ $rule . "/trackback/?$" ]                     = $query . "&tb=1";
			$rules[ $rule . "/feed/(feed|rdf|rss|rss2|atom)/?$" ] = $query . "&feed=" . $wp_rewrite->preg_index( $i );
			$rules[ $rule . "/(feed|rdf|rss|rss2|atom)/?$" ]      = $query . "&feed=" . $wp_rewrite->preg_index( $i );
			$rules[ $rule . "/page/([0-9]{1,})/?$" ]              = $query . "&paged=" . $wp_rewrite->preg_index( $i );
			$rules[ $rule . "/comment-page-([0-9]{1,})/?$" ]      = $query . "&cpage=" . $wp_rewrite->preg_index( $i );
			$rules[ $rule . "(?:/([0-9]+))?/?$" ]                 = $query . "&page=" . $wp_rewrite->preg_index( $i );
		}

		return $rules;
	}

	/**
	 * Generate specific rewrite rules for movies and taxonomies.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $rules Existing rewrite rules
	 * 
	 * @return   array
	 */
	public function rewrite_rules( $rules ) {

		$rewrites = $this->add_movie_rewrite_rules();
		$rules = array_merge( $rewrites, $rules );

		//print_r( $this->permalinks );
		//print_r( $rules ); die();

		return $rules;
	}

	/**
	 * Replace custom rewrite tags in permalinks.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $permalink
	 * @param    WP_Post    $post
	 * 
	 * @return   string
	 */
	private function replace_tags( $permalink, $post ) {

		$search  = array();
		$replace = array();

		foreach ( array_keys( $this->tags ) as $tag ) {
			if ( false !== strpos( $permalink, $tag ) ) {
				$search[]  = $tag;
				$replace[] = $this->get_replacement( $tag, $post );
			} else {
				unset( $search[ $tag ] );
			}
		}

		$search[]  = '%postname%';
		$replace[] = $post->post_name;

		$permalink = str_replace( $search, $replace, $permalink );

		return trailingslashit( $permalink );
	}

	/**
	 * Get replacement value for custom rewrite tags in permalinks.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $tag
	 * @param    WP_Post    $post
	 * 
	 * @return   string
	 */
	private function get_replacement( $tag, $post ) {

		$value = '';

		switch ( $tag ) {
			case '%imdb_id%':
			case '%tmdb_id%':
				$value = get_movie_meta( $post->ID, str_replace( '%', '', $tag ), $single = true );
				break;
			case '%release_year%':
				$value = get_movie_meta( $post->ID, 'release_date', $single = true );
				$value = date( 'Y', strtotime( $value ) );
				break;
			case '%release_monthnum%':
				$value = get_movie_meta( $post->ID, 'release_date', $single = true );
				$value = date( 'm', strtotime( $value ) );
				break;
			case '%year%':
				$value = date( 'Y', strtotime( $post->post_date ) );
				break;
			case '%monthnum%':
				$value = date( 'm', strtotime( $post->post_date ) );
				break;
			default:
				break;
		}

		return $value;
	}
}
