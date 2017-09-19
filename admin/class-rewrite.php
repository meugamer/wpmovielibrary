<?php
/**
 * Define the Rewrite class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\admin;

/**
 * Handle the plugin's URL rewriting.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * 
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

		$this->tags = \wpmoly\get_default_rewrite_tags();
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
	 * Register admin notice.
	 *
	 * @since    3.0
	 */
	public function register_notice() {

		$check = get_transient( '_wpmoly_reset_permalinks_notice' );
		if ( ! $check ) {
			return false;
		}

		/**
		 * Filter the update permalinks notice message.
		 *
		 * @since 3.0
		 *
		 * @param string $message Update permalinks notice message content.
		 */
		$message = apply_filters( 'wpmoly/filter/permalinks/notice/message', __( 'Changes have been made to the archive pages, permalinks settings need to be update. Simply reload the %s, no saving required.', 'wpmovielibrary' ) );

		/**
		 * Filter the update permalinks notice CSS class names.
		 *
		 * @since 3.0
		 *
		 * @param string $message Update permalinks notice message content.
		 */
		$class = apply_filters( 'wpmoly/filter/permalinks/notice/style', array(
			'notice',
			'notice-wpmoly',
			'notice-info',
			'is-dismissible',
		) );

		$class = implode( ' ', $class );

		echo '<div class="' . esc_attr( $class ) . '"><p>' . sprintf( esc_html( $message ), '<a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '" target="_blank">' . __( 'Permalinks page', 'wpmovielibrary' ) . '</a>' ) . '</p></div>';
	}

	/**
	 * Create admin notice transient.
	 *
	 * @since    3.0
	 *
	 * @return   boolean
	 */
	public function set_notice() {

		return set_transient( '_wpmoly_reset_permalinks_notice', 1 );
	}

	/**
	 * Remove admin notice transient.
	 *
	 * @since    3.0
	 */
	public function delete_notice() {

		$notice = get_transient( '_wpmoly_reset_permalinks_notice' );
		if ( 1 !== $notice ) {
			delete_transient( '_wpmoly_reset_permalinks_notice' );
		}
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
		$struct = isset( $this->permalinks['movie'] ) ? $this->permalinks['movie'] : '';

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
	 * @return   array
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
				'rule' => '([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})',
				'vars' => array( 'year', 'monthnum', 'day' ),
			),
			array(
				'rule' => '([0-9]{4})/([0-9]{1,2})',
				'vars' => array( 'year', 'monthnum' ),
			),
			array(
				'rule' => '([0-9]{4})',
				'vars' => array( 'year' ),
			),
			array(
				'rule' => '(adult|' . _x( 'adult', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'adult' ),
			),
			array(
				'rule' => '(author|' . _x( 'author', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'author' ),
			),
			array(
				'rule' => '(budget|' . _x( 'budget', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array( 'preset', 'budget' ),
			),
			array(
				'rule' => '(certification|' . _x( 'certification', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'certification' ),
			),
			array(
				'rule' => '(company|production-company|production-companies|' . _x( 'company', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'company' ),
			),
			array(
				'rule' => '(composer|' . _x( 'composer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'composer' ),
			),
			array(
				'rule' => '(country|production-country|production-countries|' . _x( 'country', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'country' ),
			),
			array(
				'rule' => '(director|' . _x( 'director', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'director' ),
			),
			array(
				'rule' => '(format|' . _x( 'format', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'format' ),
			),
			array(
				'rule' => '(language|' . _x( 'language', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'language' ),
			),
			array(
				'rule' => '(languages|spoken-languages|' . _x( 'spoken-languages', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'languages' ),
			),
			array(
				'rule' => '(local-release|local-release-date|' . _x( 'local-release-date', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'local_release' ),
			),
			array(
				'rule' => '(media|' . _x( 'media', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'media' ),
			),
			array(
				'rule' => '(photography|' . _x( 'photography', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'photography' ),
			),
			array(
				'rule' => '(producer|' . _x( 'producer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'producer' ),
			),
			array(
				'rule' => '(rating|' . _x( 'rating', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'rating' ),
			),
			array(
				'rule' => '(release|release-date|' . _x( 'release-date', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'release' ),
			),
			array(
				'rule' => '(revenue|' . _x( 'revenue', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array( 'preset', 'revenue' ),
			),
			array(
				'rule' => '(runtime|' . _x( 'runtime', 'permalink', 'wpmovielibrary' ) . ')/([0-9]+|[0-9]+-|[0-9]+-[0-9]+)',
				'vars' => array( 'preset', 'runtime' ),
			),
			array(
				'rule' => '(status|' . _x( 'status', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'status' ),
			),
			array(
				'rule' => '(subtitles|' . _x( 'subtitles', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'subtitles' ),
			),
			array(
				'rule' => '(writer|' . _x( 'writer', 'permalink', 'wpmovielibrary' ) . ')/([^/]+)',
				'vars' => array( 'preset', 'writer' ),
			),
		) );

		$movies = isset( $this->permalinks['movies'] ) ? $this->permalinks['movies'] : '';

		if ( ! has_movie_archives_page() ) {
			// Default: no archive page set
			$query = 'index.php?post_type=movie';
			$rule  = trim( $movies, '/' );
			$index = 1;
		} else {
			// Existing archive page
			$archive_page = get_movie_archives_page_id();

			$index = 2;
			$query = sprintf( 'index.php?page_id=%d', $archive_page );

			$rule1 = trim( str_replace( home_url(), '', get_permalink( $archive_page ) ), '/' );
			$rule2 = trim( $movies, '/' );
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

			$_rule = $rule . '/' . $variant['rule'];

			$new_rules[ $_rule . '/?$' ]                               = $_query;
			$new_rules[ $_rule . '/embed/?$' ]                         = $_query . '&embed=true';
			$new_rules[ $_rule . '/trackback/?$' ]                     = $_query . '&tb=1';
			$new_rules[ $_rule . '/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $_query . '&feed=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/(feed|rdf|rss|rss2|atom)/?$' ]      = $_query . '&feed=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/page/([0-9]{1,})/?$' ]              = $_query . '&paged=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '/comment-page-([0-9]{1,})/?$' ]      = $_query . '&cpage=' . $wp_rewrite->preg_index( $i + 1 );
			$new_rules[ $_rule . '(?:/([0-9]+))?/?$' ]                 = $_query . '&page=' . $wp_rewrite->preg_index( $i + 1 );
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
	 * @return   array
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
}