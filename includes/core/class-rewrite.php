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
	 * Create admin notice transient.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function set_notice() {

		return set_transient( '_wpmoly_reset_permalinks_notice', 1 );
	}

	/**
	 * Remove admin notice transient.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function delete_notice() {

		$notice = get_transient( '_wpmoly_reset_permalinks_notice' );
		if ( 1 !== $notice ) {
			delete_transient( '_wpmoly_reset_permalinks_notice' );
		}
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
			$wp_rewrite->add_rewrite_tag( $tag, $regex, 'wpmoly_movie_' . str_replace( '%', '', $tag ) . '=' );
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
	 * WordPress automatically appends the post's name at the end of the
	 * permalink, meaning we have to check for the presence of a %postname%
	 * or %movie% tag in the permalink that could be present if we're dealing
	 * with custom permalink structures and remove it. This may result in
	 * duplicate slashes that we need to clean while we're at it.
	 * 
	 * This markers should be stripped automatically when saving permalink
	 * structures, but we're still better off checking to avoid malformed
	 * URLs.
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
	public function replace_movie_link_tags( $permalink, $post, $leavename, $sample ) {

		if ( $sample || 'movie' != $post->post_type ) {
			return $permalink;
		}

		// Check for duplicate post name and clean duplicate slashes
		if ( false !== stripos( $permalink, '%postname%' ) && false !== stripos( $permalink, $post->post_name ) ) {
			$permalink = str_replace( array( '%movie%', '%postname%' ), '', $permalink );
			$permalink = preg_replace( '/([^:])(\/{2,})/', '$1/', $permalink );
		}

		return $this->replace_tags( $permalink, $post );
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

		$rules = $this->fix_movie_rewrite_rules( $rules );

		$new_rules = $this->generate_movie_archives_rewrite_rules();
		$rules = array_merge( $new_rules, $rules );

		//printr( $rules )->toString(); die();

		return $rules;
	}

	/**
	 * Fix movie rewrite rules if needed.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $rules
	 * 
	 * @return   array
	 */
	private function fix_movie_rewrite_rules( $rules ) {

		global $wp_rewrite;

		$slug   = _x( 'movie', 'slug', 'wpmovielibrary' );
		$struct = $this->permalinks['movie'];

		if ( false !== strpos( $struct, $slug ) ) {
			return $rules;
		}

		$struct = str_replace( $wp_rewrite->rewritecode, $wp_rewrite->rewritereplace, $struct );
		$struct = trim( $struct, '/' );

		return $rules;
	}

	/**
	 * Add custom rewrite rules for movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function generate_movie_archives_rewrite_rules() {

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

		// TODO replace this block with a filter?
		$archive_pages = get_option( '_wpmoly_archive_pages' );
		if ( ! $archive_pages ) {
			$archive_pages = array();
		}

		$archive_page = array_search( 'movies', $archive_pages );
		if ( is_null( get_post( $archive_page ) ) ) {
			$query = 'index.php?post_type=movie';
			$rule  = trim( $this->permalinks['movies'], '/' );
		} else {
			$query = sprintf( 'index.php?page_id=%d', $archive_page );
			$rule1 = trim( str_replace( home_url(), '', get_permalink( $archive_page ) ), '/' );
			$rule2 = trim( $this->permalinks['movies'], '/' );

			$rule = "($rule2|$rule1)";
		}

		$index = 2;
		
		$rules[ $rule . "/?$" ]                               = $query;
		$rules[ $rule . "/embed/?$" ]                         = $query . "&embed=true";
		$rules[ $rule . "/trackback/?$" ]                     = $query . "&tb=1";
		$rules[ $rule . "/feed/(feed|rdf|rss|rss2|atom)/?$" ] = $query . "&feed=" . $wp_rewrite->preg_index( $index );
		$rules[ $rule . "/(feed|rdf|rss|rss2|atom)/?$" ]      = $query . "&feed=" . $wp_rewrite->preg_index( $index );
		$rules[ $rule . "/page/([0-9]{1,})/?$" ]              = $query . "&paged=" . $wp_rewrite->preg_index( $index );
		$rules[ $rule . "/comment-page-([0-9]{1,})/?$" ]      = $query . "&cpage=" . $wp_rewrite->preg_index( $index );
		$rules[ $rule . "(?:/([0-9]+))?/?$" ]                 = $query . "&page=" . $wp_rewrite->preg_index( $index );

		foreach ( $dates as $date ) {

			$_query = $query;
			foreach ( $date['vars'] as $var ) {
				$_query = $_query . '&' . $var . '=' . $wp_rewrite->preg_index( $index );
				$index++;
			}

			$rule .= '/' . $date['rule'];

			$rules[ $rule . "/?$" ]                               = $_query;
			$rules[ $rule . "/embed/?$" ]                         = $_query . "&embed=true";
			$rules[ $rule . "/trackback/?$" ]                     = $_query . "&tb=1";
			$rules[ $rule . "/feed/(feed|rdf|rss|rss2|atom)/?$" ] = $_query . "&feed=" . $wp_rewrite->preg_index( $index );
			$rules[ $rule . "/(feed|rdf|rss|rss2|atom)/?$" ]      = $_query . "&feed=" . $wp_rewrite->preg_index( $index );
			$rules[ $rule . "/page/([0-9]{1,})/?$" ]              = $_query . "&paged=" . $wp_rewrite->preg_index( $index );
			$rules[ $rule . "/comment-page-([0-9]{1,})/?$" ]      = $_query . "&cpage=" . $wp_rewrite->preg_index( $index );
			$rules[ $rule . "(?:/([0-9]+))?/?$" ]                 = $_query . "&page=" . $wp_rewrite->preg_index( $index );
		}

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
