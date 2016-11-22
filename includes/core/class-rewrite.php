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

		$rules = $this->add_movie_archives_rewrite_rules( $rules );
		$rules = $this->add_taxonomy_archives_rewrite_rules( $rules );

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
	 * Define a list of variants for movies archive to match meta/detail
	 * permalinks.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $rules Existing rewrite rules.
	 * 
	 * @return   void
	 */
	private function add_movie_archives_rewrite_rules( $rules = array() ) {

		global $wp_rewrite;

		$new_rules = array();

		/**
		 * Filter default movie archives rewrite variants.
		 * 
		 * Each variant must define a rule and a matching array of vars.
		 * Defaults variants support meta/detail name translation, used
		 * to set the grid preset to 'custom'.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $variants Default variants.
		 */
		$variants = apply_filters( 'wpmoly/filter/movie_archives/rewrite/variants', array(
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
			),
			array(
				'rule' => "(adult|" . _x( 'adult', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_adult' )
			),
			array(
				'rule' => "(author|" . _x( 'author', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_author' )
			),
			array(
				'rule' => "(certification|" . _x( 'certification', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_certification' )
			),
			array(
				'rule' => "(composer|" . _x( 'composer', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_composer' )
			),
			array(
				'rule' => "(homepage|" . _x( 'homepage', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_homepage' )
			),
			array(
				'rule' => "(imdb_id|" . _x( 'imdb_id', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_imdb_id' )
			),
			array(
				'rule' => "(local-release-date|" . _x( 'local-release-date', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_local_release_date' )
			),
			array(
				'rule' => "(photography|" . _x( 'photography', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_photography' )
			),
			array(
				'rule' => "(producer|" . _x( 'producer', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_producer' )
			),
			array(
				'rule' => "(production-companies|" . _x( 'company', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_production_companies' )
			),
			array(
				'rule' => "(production-countries|" . _x( 'country', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_production_countries' )
			),
			array(
				'rule' => "(release-date|" . _x( 'release-date', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_release_date' )
			),
			array(
				'rule' => "(spoken-languages|" . _x( 'spoken-languages', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_spoken_languages' )
			),
			array(
				'rule' => "(tmdb_id|" . _x( 'tmdb_id', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_tmdb_id' )
			),
			array(
				'rule' => "(writer|" . _x( 'writer', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_writer' )
			),
			array(
				'rule' => "(format|" . _x( 'format', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_format' )
			),
			array(
				'rule' => "(language|" . _x( 'language', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_language' )
			),
			array(
				'rule' => "(media|" . _x( 'media', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_media' )
			),
			array(
				'rule' => "(rating|" . _x( 'rating', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_rating' )
			),
			array(
				'rule' => "(status|" . _x( 'status', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_status' )
			),
			array(
				'rule' => "(subtitles|" . _x( 'subtitles', 'permalink', 'wpmovielibrary' ) . ")/([^/]+)",
				'vars' => array( 'grid_preset', 'wpmoly_movie_subtitles' )
			)
		) );

		// Default: no archive page set
		if ( ! has_movie_archives_page() ) {

			$query = 'index.php?post_type=movie';
			$rule  = trim( $this->permalinks['movies'], '/' );
			$index = 1;

		// Existing archive page
		} else {

			$archive_page = get_movie_archives_page_id();

			$index = 2;
			$query = sprintf( 'index.php?page_id=%d', $archive_page );

			$rule1 = trim( str_replace( home_url(), '', get_permalink( $archive_page ) ), '/' );
			$rule2 = trim( $this->permalinks['movies'], '/' );
			$rule  = "($rule2|$rule1)";
		}

		// Loop through allowed variants
		foreach ( $variants as $variant ) {

			$_query = $query;
			$i = $index;

			// Use all vars to increment counter, but don't actually
			// put empty vars in the regex.
			foreach ( $variant['vars'] as $var ) {
				if ( ! empty( $var ) ) {
					$_query = $_query . '&' . $var . '=' . $wp_rewrite->preg_index( $i );
				}
				$i++;
			}

			$_rule = $rule .'/' . $variant['rule'];

			$new_rules[ $_rule . "/?$" ]                               = $_query;
			$new_rules[ $_rule . "/embed/?$" ]                         = $_query . "&embed=true";
			$new_rules[ $_rule . "/trackback/?$" ]                     = $_query . "&tb=1";
			$new_rules[ $_rule . "/feed/(feed|rdf|rss|rss2|atom)/?$" ] = $_query . "&feed=" . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . "/(feed|rdf|rss|rss2|atom)/?$" ]      = $_query . "&feed=" . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . "/page/([0-9]{1,})/?$" ]              = $_query . "&paged=" . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . "/comment-page-([0-9]{1,})/?$" ]      = $_query . "&cpage=" . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . "(?:/([0-9]+))?/?$" ]                 = $_query . "&page=" . $wp_rewrite->preg_index( $i + 1 );
		}

		return array_merge( $new_rules, $rules );
	}

	/**
	 * Add custom rewrite rules for movies.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $rules Existing rewrite rules.
	 * 
	 * @return   void
	 */
	private function add_taxonomy_archives_rewrite_rules( $rules = array() ) {

		global $wp_rewrite;

		$new_rules = array();

		$taxonomies = array( 'actor', 'collection', 'genre' );
		foreach ( $taxonomies as $taxonomy ) {

			if ( ! has_archives_page( $taxonomy ) ) {
				continue;
			}

			$archive_page = get_archives_page_id( $taxonomy );

			$index = 2;
			$query = sprintf( 'index.php?page_id=%d', $archive_page );

			$rule1 = $taxonomy;
			$rule2 = trim( get_taxonomy_archive_link( $taxonomy, 'relative' ), '/' );
			$rule  = "($rule2|$rule1)";

			$new_rules[ $rule . '/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $query . '&' . $taxonomy . '=' . $wp_rewrite->preg_index( $index ) . '&feed=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' ]      = $query . '&' . $taxonomy . '=' . $wp_rewrite->preg_index( $index ) . '&feed=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/page/([0-9]{1,})/?$' ]              = $query . '&' . $taxonomy . '=' . $wp_rewrite->preg_index( $index ) . '&paged=' . $wp_rewrite->preg_index( $index + 1 );
			$new_rules[ $rule . '/([^/]+)/embed/?$' ]                         = $query . '&' . $taxonomy . '=' . $wp_rewrite->preg_index( $index ) . '&embed=true';
			$new_rules[ $rule . '/([^/]+)/?$' ]                               = $query . '&' . $taxonomy . '=' . $wp_rewrite->preg_index( $index );
		}

		return array_merge( $new_rules, $rules );
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
