<?php
/**
 * Define the Movies Request class.
 * 
 * @link       http://wpmovielibrary.com
 * @since      3.0
 * 
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/requests
 */

namespace wpmoly\Requests;

use WP_Query;

/**
 * Find Movies in various ways.
 * 
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/requests
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movies extends Request {

	/**
	 * Initialize the request.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function init() {

		/**
		 * Filter default preset callback.
		 * 
		 * The default preset should only run an existing preset callback.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $callback
		 */
		$this->default_preset = apply_filters( 'wpmoly/filter/query/movies/defaults/preset', 'last_added_movies' );

		$presets = array(

			// Retrieve the 20 first movies alphabetically
			'alphabetical_movies' => array(
				'meta_key'       => '_wpmoly_movie_title',
				'orderby'        => 'meta_value',
				'order'          => 'asc'
			),

			// Retrieve the 20 last movies alphabetically inverted
			'unalphabetical_movies' => array(
				'meta_key'       => '_wpmoly_movie_title',
				'orderby'        => 'meta_value',
				'order'          => 'desc'
			),

			// Retrieve the 20 last movies released in the current year.
			'current_year_movies' => array(
				'meta_key'   => '_wpmoly_movie_release_date',
				'meta_type'  => 'date',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => '_wpmoly_movie_release_date',
						'type'    => 'date',
						'value'   => sprintf( '%d-01-01', date( 'Y' ) ),
						'compare' => '>='
					),
					array(
						'key'     => '_wpmoly_movie_release_date',
						'type'    => 'date',
						'value'   => sprintf( '%d-12-31', date( 'Y' ) ),
						'compare' => '<='
					),
				),
				'orderby'    => 'meta_value',
				'order'      => 'desc'
			),

			// Retrieve the 20 last movies released last year.
			'last_year_movies' => array(
				'meta_key'   => '_wpmoly_movie_release_date',
				'meta_type'  => 'date',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => '_wpmoly_movie_release_date',
						'type'    => 'date',
						'value'   => sprintf( '%d-01-01', date( 'Y' ) - 1 ),
						'compare' => '>='
					),
					array(
						'key'     => '_wpmoly_movie_release_date',
						'type'    => 'date',
						'value'   => sprintf( '%d-12-31', date( 'Y' ) - 1 ),
						'compare' => '<='
					),
				),
				'orderby'    => 'meta_value',
				'order'      => 'desc'
			),

			// Retrieve the 20 last movies added to the library.
			'last_added_movies' => array(
				'orderby' => 'date',
				'order'   => 'desc'
			),

			// Retrieve the 20 first movies added to the library.
			'first_added_movies' => array(
				'orderby' => 'date',
				'order'   => 'asc'
			),

			// Retrieve the 20 last movies released.
			'last_released_movies' => array(
				'meta_key'  => '_wpmoly_movie_release_date',
				'meta_type' => 'date',
				'orderby'   => 'meta_value',
				'order'     => 'desc'
			),

			// Retrieve the 20 first movies released.
			'first_released_movies' => array(
				'meta_key'  => '_wpmoly_movie_release_date',
				'meta_type' => 'date',
				'orderby'   => 'meta_value',
				'order'     => 'asc'
			),

			// Retrieve the 20 first movies not released yet.
			'incoming_movies' => array(
				'meta_key'     => '_wpmoly_movie_release_date',
				'meta_type'    => 'date',
				'meta_value'   => sprintf( '%d-01-01', date( 'Y' ) + 1 ),
				'meta_compare' => '>=',
				'orderby'      => 'meta_value',
				'order'        => 'desc'
			),

			// Retrieve the 20 most rated movies.
			'most_rated_movies' => array(
				'meta_key'     => '_wpmoly_movie_rating',
				//'meta_value'   => 0.0
				//'meta_compare' => '>',
				'orderby'      => 'meta_value_num',
				'order'        => 'desc'
			),

			// Retrieve the 20 least rated movies.
			'least_rated_movies' => array(
				'meta_key'     => '_wpmoly_movie_rating',
				//'meta_value'   => 0.0
				//'meta_compare' => '>',
				'orderby'      => 'meta_value_num',
				'order'        => 'asc'
			)
		);

		/**
		 * Filter default presets.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $presets
		 */
		$this->presets = apply_filters( 'wpmoly/filter/query/movies/defaults/presets', $presets );
	}

	/**
	 * Custom Grid.
	 * 
	 * Look for special query vars to narrow the query using meta/details
	 * values.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Query parameters
	 * 
	 * @return   array
	 */
	public function custom( $args = array() ) {

		global $wp_query;

		$vars = array(
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

		foreach ( $vars as $var ) {
			$query_var = get_query_var( $var );
			if ( ! empty( $query_var ) ) {

				$_var = str_replace( 'wpmoly_movie_', '', $var );

				/**
				 * Filter query var value.
				 * 
				 * @since    3.0
				 * 
				 * @since    string    $query_var query var value.
				 */
				$query_var = apply_filters( "wpmoly/filter/query/movies/$_var/value", $query_var, $var );

				$args['meta_query'][] = array(
					'key'   => "_$var",
					'value' => $query_var,
					'compare' => 'LIKE'
				);
			}
		}

		if ( isset( $args['meta_query'] ) && 1 < count( $args['meta_query'] ) ) {
			$args['meta_query']['relation'] = 'AND';
		}

		return $this->query( $args );
	}

	/**
	 * Perform the query.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Query parameters
	 * 
	 * @return   array
	 */
	public function query( $args = array() ) {

		/**
		 * Filter default preset post status.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $post_status
		 */
		$post_status = apply_filters( 'wpmoly/filter/query/movies/defaults/post_status', array( 'publish' ) );

		/**
		 * Filter default number of posts per page.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $posts_per_page
		 */
		$posts_per_page = apply_filters( 'wpmoly/filter/query/movies/defaults/posts_per_page', 20 );

		/**
		 * Filter default query orderby.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $orderby
		 */
		$orderby = apply_filters( 'wpmoly/filter/query/movies/defaults/orderby', 'date' );

		/**
		 * Filter default query order.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $order
		 */
		$order = apply_filters( 'wpmoly/filter/query/movies/defaults/order', 'desc' );

		/**
		 * Filter default query args.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $args
		 */
		$defaults = apply_filters( 'wpmoly/filter/query/movies/defaults/query_args', array(
			'post_type'      => 'movie',
			'post_status'    => $post_status,
			'posts_per_page' => $posts_per_page,
			'orderby'        => $orderby,
			'order'          => $order
		) );
		$this->args = wp_parse_args( $args, $defaults );

		$this->query = new WP_Query( $this->args );
		if ( ! $this->query->have_posts() ) {
			return $this->items;
		}

		foreach ( $this->query->posts as $post ) {
			$this->items[] = get_movie( $post );
		}

		return $this->items;
	}

	/**
	 * Retrieve total number of result pages.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_total_pages() {

		return (int) $this->query->max_num_pages;
	}

	/**
	 * Retrieve current page number.
	 * 
	 * Default if 0, which we convert to 1 to avoid confusions.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_current_page() {

		$paged = $this->query->get( 'paged' );
		if ( ! $paged ) {
			$paged = 1;
		}

		return $paged;
	}

	/**
	 * Calculate previous page number.
	 * 
	 * Avoid returning a negative number if result is smaller than 0.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_previous_page() {

		$previous = $this->get_current_page() - 1;

		return max( 0, $previous );
	}

	/**
	 * Retrieve next page number.
	 * 
	 * Avoid returning a number greater than total number of pages.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_next_page() {

		$next = $this->get_current_page() + 1;

		return min( $this->get_total_pages(), $next );
	}
}
