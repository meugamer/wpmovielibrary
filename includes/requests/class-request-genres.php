<?php
/**
 * Define the Genres Request class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/requests
 */

namespace wpmoly\Requests;

use WP_Query;
use WP_Term_Query;

/**
 * Find Genres in various ways.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/requests
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Genres extends Request {

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
		$this->default_preset = apply_filters( 'wpmoly/filter/query/genres/defaults/preset', 'alphabetical_genres' );

		$presets = array(

			// retrieve the 20 first 20 genres alphabetically.
			'alphabetical_genres' => array(
				'orderby' => 'name',
				'order'   => 'asc'
			),

			// Retrieve the 20 last genres alphabetically inverted.
			'unalphabetical_genres' => array(
				'orderby' => 'name',
				'order'   => 'desc'
			)
		);

		/**
		 * Filter default presets.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $presets
		 */
		$this->presets = apply_filters( 'wpmoly/filter/query/genres/defaults/presets', $presets );
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
	 * Perform the Term query.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Query parameters
	 * 
	 * @return   array
	 */
	public function query( $args = array() ) {

		// Clean up empty rows
		$args = array_filter( $args );

		/**
		 * Filter default number of terms.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $number
		 */
		$number = apply_filters( 'wpmoly/filter/query/genres/defaults/number', 20 );
		
		/**
		 * Filter default offset.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $offset
		 */
		$offset = apply_filters( 'wpmoly/filter/query/genres/defaults/offset', 0 );
		
		/**
		 * Filter default hide_empty value.
		 * 
		 * @since    3.0
		 * 
		 * @param    boolean    $hide_empty
		 */
		$hide_empty = apply_filters( 'wpmoly/filter/query/genres/defaults/hide_empty', false );
		
		/**
		 * Filter default query orderby.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $orderby
		 */
		$orderby = apply_filters( 'wpmoly/filter/query/genres/defaults/orderby', 'name' );
		
		/**
		 * Filter default query order.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $order
		 */
		$order = apply_filters( 'wpmoly/filter/query/genres/defaults/order', 'asc' );

		// page is for Post Queries, use offset instead
		if ( ! empty( $args['page'] ) ) {
			$args['offset'] = $args['page'];
		} else {
			$args['page'] = 1;
		}

		/**
		 * Filter default query args.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $args
		 */
		$defaults = apply_filters( 'wpmoly/filter/query/genres/defaults/query_args', array(
			'taxonomy'   => 'genre',
			'number'     => $number,
			'offset'     => $offset,
			'hide_empty' => $hide_empty,
			'orderby'    => $orderby,
			'order'      => $order
		) );
		$this->args = wp_parse_args( $args, $defaults );

		// Calculate offset
		$this->args['offset'] = $this->args['number'] * max( 0, $this->args['offset'] - 1 );

		$this->query = new WP_Term_Query( $this->args );
		if ( empty( $this->query->terms ) ) {
			return $this->items;
		}

		foreach ( $this->query->terms as $term ) {
			$this->items[] = get_genre( $term );
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

		$total = wp_count_terms( 'genre' );
		$limit = $this->query->query_vars['number'];

		$pages = ceil( $total / $limit );

		return (int) $pages;
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

		$page = $this->query->query_vars['page'];
		if ( ! $page ) {
			$page = 1;
		}

		return $page;
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
