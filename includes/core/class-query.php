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
	 * Filter 'actor' query var value. Replace value by matching term
	 * name, if any.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_actor_query_var( $query_var ) {

		$term = get_term_by( 'slug', $query_var, 'actor' );
		if ( ! empty( $term->name ) ) {
			return $term->name;
		}

		return $this->filter_name_query_var( $query_var, 'actor' );
	}

	/**
	 * Filter 'adult' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_adult_query_var( $query_var ) {

		$query_var = _is_bool( $query_var ) ? 'true' : 'false';

		return $query_var;
	}

	/**
	 * Filter 'author' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_author_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'author' );
	}

	/**
	 * Filter 'certification' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_certification_query_var( $query_var ) {

		$query_var = strtoupper( $query_var );

		return $query_var;
	}

	/**
	 * Filter 'company' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_company_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'company' );

		return $query_var;
	}

	/**
	 * Filter 'composer' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_composer_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'composer' );
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
	 * Filter 'director' query var value. Replace value by matching term
	 * name, if any.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_director_query_var( $query_var ) {

		$term = get_term_by( 'slug', $query_var, 'collection' );
		if ( ! $term ) {
			return $query_var;
		}

		if ( ! empty( $term->name ) ) {
			$query_var = $term->name;
		}

		return $query_var;
	}

	/**
	 * Filter 'format' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_format_query_var( $query_var ) {

		$query_var = strtolower( $query_var );

		return $query_var;
	}

	/**
	 * Filter 'genre' query var value. Replace value by matching term
	 * name, if any.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_genre_query_var( $query_var ) {

		$term = get_term_by( 'slug', $query_var, 'genre' );
		if ( ! empty( $term->name ) ) {
			return $term->name;
		}

		return $this->filter_name_query_var( $query_var, 'genre' );
	}

	/**
	 * Filter 'languages' query var value. Convert a sanitized language code,
	 * standard name or localized name to its clean native name.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_languages_query_var( $query_var ) {

		$query_var = get_language( $query_var );
		$query_var = $query_var->native_name;

		return $query_var;
	}

	/**
	 * Filter 'language' query var value. Convert a sanitized language
	 * standard name, native name or localized name to its ISO code.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_language_query_var( $query_var ) {

		$query_var = get_language( $query_var );
		$query_var = $query_var->code;

		return $query_var;
	}

	/**
	 * Filter 'media' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_media_query_var( $query_var ) {

		$query_var = strtolower( $query_var );

		return $query_var;
	}

	/**
	 * Filter 'revenue' and 'budget' query var values.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_money_query_var( $query_var ) {

		$interval = explode( '-', (string) $query_var );

		sort( $interval );

		// Custom interval.
		if ( ! empty( $interval[0] ) && ! empty( $interval[1] ) ) {
			$interval = array_map( 'intval', $interval );
		// Default interval.
		} elseif ( empty( $interval[0] ) && ! empty( $interval[1] ) ) {

			$min = $max = 0;

			// Less than 100 thousands.
			if ( $interval[1] < 100000 ) {
				$min = 0;
				$max = 100000;
			}
			// 100 thousands to half million.
			elseif ( $interval[1] < 500000 ) {
				$min = floor( $interval[1] / 100000 ) * 100000;
				$max = $min + 100000;
			}
			// Half million to 1 million.
			elseif ( $interval[1] < 1000000 ) {
				$min = floor( $interval[1] / 500000 ) * 500000;
				$max = $min + 500000;
			}
			// 1 millions to 10 millions.
			elseif ( $interval[1] < 10000000 ) {
				$min = floor( $interval[1] / 1000000 ) * 1000000;
				$max = $min + 1000000;
			}
			// 10 millions to 100 millions.
			elseif ( $interval[1] < 100000000 ) {
				$min = floor( $interval[1] / 10000000 ) * 10000000;
				$max = $min + 10000000;
			}
			// 100 millions 1 billion.
			elseif ( $interval[1] < 1000000000 ) {
				$min = floor( $interval[1] / 100000000 ) * 100000000;
				$max = $min + 100000000;
			}
			// More than 1 billion.
			elseif ( $interval[1] >= 1000000000 ) {
				$interval = 1000000000;
			}

			if ( $min && $max ) {
				$interval = array( $min, $max );
			} else {
				$interval = $interval[1];
			}

		// No interval.
		} else {
			$interval = array_shift( $interval );
		}

		return $interval;
	}

	/**
	 * Filter 'photography' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_photography_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'photography' );
	}

	/**
	 * Filter 'producer' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_producer_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'producer' );
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

		if ( ! preg_match( '/^[\d](\.([\d])?)?|[\d](\.([\d])?)?-[\d](\.([\d])?)?$/', $query_var ) ) {
			return $query_var;
		}

		$interval = explode( '-', $query_var );

		if ( 2 === count( $interval ) ) {
			$interval = array_map( function( $n ) {
				return number_format( max( 0.0, min( $n, 5.0 ) ), 1 );
			}, $interval );
			sort( $interval );
		} elseif ( 1 === count( $interval ) ) {
			$interval = array_shift( $interval );
		} else {
			$interval = $query_var;
		}

		return $interval;
	}

	/**
	 * Filter 'rating' query type.
	 * 
	 * Movies can be filtered by ratings interval but require values to be
	 * casted as DECIMAL instead of NUMERIC.
	 * 
	 * @see \wpmoly\Rest\API::add_meta_interval_query_param()
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Query type.
	 * 
	 * @return   string
	 */
	public function filter_rating_query_type( $type ) {

		if ( 'DECIMAL' !== $type ) {
			$type = 'DECIMAL';
		}

		return $type;
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
	public function filter_release_query_var( $query_var ) {

		$start = $end = '';

		// Year interval.
		if ( preg_match( '/^([0-9]{4})-([0-9]{4})$/', $query_var, $match ) ) {
			$start = "{$match[1]}-1-1";
			$end   = "{$match[2]}-12-31";
		}
		// Month interval.
		elseif ( preg_match( '/^([0-9]{4}-[0-9]{1,2})-([0-9]{4}-[0-9]{1,2})$/', $query_var, $match ) ) {
			$start = "{$match[1]}-1}";
			$end   = "{$match[2]}-31}";
		}
		// Day interval.
		elseif ( preg_match( '/^([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})-([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})$/', $query_var, $match ) ) {
			$start = $match[1];
			$end   = $match[2];
		}
		// Day.
		elseif ( preg_match( '/^([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})$/', $query_var, $match ) ) {
			$start = $match[1];
		}
		// Month.
		elseif ( preg_match( '/^([0-9]{4}-[0-9]{2})$/', $query_var, $match ) ) {
			$start = $match[1];
		}
		// Year.
		elseif ( preg_match( '/^([0-9]{4})$/', $query_var, $match ) ) {
			$start = $match[1];
		}

		if ( ! empty( $start ) && ! empty( $end ) ) {
			$interval = array( $start, $end );
		} else {
			$interval = $start;
		}

		return $interval;
	}

	/**
	 * Filter 'runtime' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   array
	 */
	public function filter_runtime_query_var( $query_var ) {

		$interval = explode( '-', (string) $query_var );

		sort( $interval );

		// Custom interval.
		if ( ! empty( $interval[0] ) && ! empty( $interval[1] ) ) {
			$interval = array_map( 'intval', $interval );
		// Default interval.
		} elseif ( empty( $interval[0] ) && ! empty( $interval[1] ) ) {

			$min = (int) floor( $interval[1] / 10 ) * 10;
			$max = (int) ceil( $interval[1] / 10 ) * 10;

			$interval = array( $min, $max );
		// No interval.
		} else {
			$interval = array_shift( $interval );
		}

		return $interval;
	}

	/**
	 * Filter 'status' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_status_query_var( $query_var ) {

		$query_var = strtolower( $query_var );

		return $query_var;
	}

	/**
	 * Filter 'subtitles' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_subtitles_query_var( $query_var ) {

		$query_var = get_language( $query_var );
		$query_var = $query_var->code;

		return $query_var;
	}

	/**
	 * Filter 'writer' query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_writer_query_var( $query_var ) {

		return $this->filter_name_query_var( $query_var, 'writer' );
	}

	/**
	 * Filter people-based query var value.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $query_var Query var value.
	 * 
	 * @return   string
	 */
	public function filter_name_query_var( $query_var, $type ) {

		if ( ! is_string( $type ) ) {
			return $query_var;
		}

		$cache = $this->get_cached_names( $type );
		if ( ! empty( $cache[ $query_var ] ) ) {
			return $cache[ $query_var ];
		}

		return $query_var;
	}

	/**
	 * Retrieve a cached list of people and company names.
	 * 
	 * Meta value passed through URL are sanitized and can not be accurately
	 * matched with original value due to ambiguities around hyphens and
	 * accenttuated letters. Matching table are stored as options to retrieve
	 * original value from slug.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Meta type.
	 * 
	 * @return   array
	 */
	private function get_cached_names( $type ) {

		global $wpdb;

		$cached_names = array();
		$supported = array(
			'actor'       => 'cast',
			'author'      => 'author',
			'company'     => 'production_companies',
			'composer'    => 'composer',
			'director'    => 'director',
			'photography' => 'photography',
			'producer'    => 'producer',
			'writer'      => 'writer',
		);

		if ( ! isset( $supported[ $type ] ) ) {
			return $cached_names;
		}

		/** This filter is documented in includes/helpers/utils.php */
		$meta_key = apply_filters( 'wpmoly/filter/movie/meta/key', $supported[ $type ] );

		/**
		 * Filter cache option name.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $option_name Option name.
		 */
		$option_name = apply_filters( 'wpmoly/filter/people/cache/option/name', "_wpmoly_{$type}_cache_table" );

		/**
		 * Filter cache option autoload.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $option_autoload Option autoload.
		 */
		$option_autoload = apply_filters( 'wpmoly/filter/people/cache/option/autoload', 'no' );

		$cached_names = get_option( $option_name, $cached_names );
		if ( ! empty( $cached_names ) ) {
			return $cached_names;
		}

		$people = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s", $meta_key ) );
		foreach ( $people as $list ) {
			$list = explode( ',', $list->meta_value );
			foreach ( $list as $item ) {
				$item = trim( $item );
				$slug = sanitize_title_with_dashes( $item );
				$cached_names[ $slug ] = $item;
			}
		}

		update_option( $option_name, $cached_names, $option_autoload );

		return $cached_names;
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
