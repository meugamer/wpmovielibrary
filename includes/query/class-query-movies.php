<?php
/**
 * Define the Movies Query class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/query
 */

namespace wpmoly\Query;

use WP_Query;

/**
 * Find Movies in various ways.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/query
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movies {

	/**
	 * Nodes.
	 * 
	 * @var    array
	 */
	private $items;

	/**
	 * Query parameters.
	 * 
	 * @var    array
	 */
	private $args;

	/**
	 * Current page.
	 * 
	 * @var    int
	 */
	//protected $current = 0;

	/**
	 * Internal Query.
	 * 
	 * @var    WP_Query
	 */
	public $query;

	/**
	 * Define a default preset for this Query.
	 * 
	 * The default preset should only run an existing preset callback.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function default_preset() {

		/**
		 * Filter default preset callback.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $callback
		 */
		$callback = apply_filters( 'wpmoly/filter/query/movies/defaults/preset', 'last_added_movies' );

		return $this->$callback();
	}

	/**
	 * 'alphabetical-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 movies alphabetically.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function alphabetical_movies() {

		return $this->query( array(
			'meta_key'       => '_wpmoly_movie_title',
			'orderby'        => 'meta_value',
			'order'          => 'ASC'
		) );
	}

	/**
	 * 'unalphabetical-movies' Grid preset.
	 * 
	 * Default: retrieve the last 20 movies alphabetically.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function unalphabetical_movies() {

		return $this->query( array(
			'meta_key'       => '_wpmoly_movie_title',
			'orderby'        => 'meta_value',
			'order'          => 'DESC'
		) );
	}

	/**
	 * '-movies' Grid preset.
	 * 
	 * Default: retrieve 20 movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function current_year_movies() {

		return $this->query( array(
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
			'order'      => 'DESC'
		) );
	}

	/**
	 * '-movies' Grid preset.
	 * 
	 * Default: retrieve 20 movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function last_year_movies() {

		return $this->query( array(
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
			'order'      => 'DESC'
		) );
	}

	/**
	 * 'last-added-movies' Grid preset.
	 * 
	 * Default: retrieve the last 20 movies added to the library.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function last_added_movies() {

		return $this->query();
	}

	/**
	 * 'first-added-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 movies added to the library.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function first_added_movies() {

		return $this->query( array(
			'order' => 'ASC'
		) );
	}

	/**
	 * '-movies' Grid preset.
	 * 
	 * Default: retrieve the 20 last released movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function last_released_movies() {

		return $this->query( array(
			'meta_key'  => '_wpmoly_movie_release_date',
			'meta_type' => 'date',
			'orderby'   => 'meta_value',
			'order'     => 'DESC'
		) );
	}

	/**
	 * '-movies' Grid preset.
	 * 
	 * Default: retrieve the 20 first released movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function first_released_movies() {

		return $this->query( array(
			'meta_key'  => '_wpmoly_movie_release_date',
			'meta_type' => 'date',
			'orderby'   => 'meta_value',
			'order'     => 'ASC'
		) );
	}

	/**
	 * 'incoming-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 incoming movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function incoming_movies() {

		return $this->query( array(
			'meta_key'     => '_wpmoly_movie_release_date',
			'meta_type'    => 'date',
			'meta_value'   => sprintf( '%d-01-01', date( 'Y' ) + 1 ),
			'meta_compare' => '>=',
			'orderby'      => 'meta_value',
			'order'        => 'DESC'
		) );
	}

	/**
	 * 'most-rated-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 most movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function most_rated_movies() {

		return $this->query( array(
			'meta_key'     => '_wpmoly_movie_rating',
			//'meta_value'   => 0.0
			//'meta_compare' => '>',
			'orderby'      => 'meta_value_num',
			'order'        => 'DESC'
		) );
	}

	/**
	 * 'least-rated-movies' Grid preset.
	 * 
	 * Default: retrieve the first 20 least movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function least_rated_movies() {

		return $this->query( array(
			'meta_key'     => '_wpmoly_movie_rating',
			//'meta_value'   => 0.0
			//'meta_compare' => '>',
			'orderby'      => 'meta_value_num',
			'order'        => 'ASC'
		) );
	}

	/**
	 * Custom Grid.
	 * 
	 * Simple alias for $this->query()
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Query parameters
	 * 
	 * @return   array
	 */
	public function custom( $args = array() ) {

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
		$orderby = apply_filters( 'wpmoly/filter/query/movies/defaults/orderby', 'post_date' );

		/**
		 * Filter default query order.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $order
		 */
		$order = apply_filters( 'wpmoly/filter/query/movies/defaults/order', 'DESC' );

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
