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
use WP_Term_Query;

/**
 * Find Movies in various ways.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/query
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Actors extends Query {

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
		$callback = apply_filters( 'wpmoly/filter/query/actors/defaults/preset', 'alphabetical_actors' );

		return $this->$callback();
	}

	/**
	 * 'alphabetical-actors' Grid preset.
	 * 
	 * Default: retrieve the first 20 actors alphabetically.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function alphabetical_actors( $a ) {

		return $this->term_query( array(
			'orderby' => 'name',
			'order'   => 'ASC'
		) );
	}

	/**
	 * 'unalphabetical-actors' Grid preset.
	 * 
	 * Default: retrieve the last 20 actors alphabetically inverted.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function unalphabetical_actors() {

		return $this->term_query( array(
			'orderby' => 'name',
			'order'   => 'DESC'
		) );
	}

	/**
	 * 'alphabetical-persons' Grid preset.
	 * 
	 * Default: retrieve the first 20 persons alphabetically.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function alphabetical_persons() {

		return $this->post_query( array(
			'meta_key' => '_wpmoly_person_name',
			'orderby'  => 'meta_value',
			'order'    => 'ASC'
		) );
	}

	/**
	 * 'unalphabetical-persons' Grid preset.
	 * 
	 * Default: retrieve the last 20 persons alphabetically inverted.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function unalphabetical_persons() {

		return $this->post_query( array(
			'meta_key' => '_wpmoly_person_name',
			'orderby'  => 'meta_value',
			'order'    => 'DESC'
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

		if ( ! empty( $args['taxonomy'] ) ) {
			return $this->term_query( $args );
		} elseif ( ! empty( $args['post_type'] ) ) {
			return $this->post_query( $args );
		}

		return false;
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
	public function term_query( $args = array() ) {

		// Clean up empty rows
		$args = array_filter( $args );

		/**
		 * Filter default number of terms.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $number
		 */
		$number = apply_filters( 'wpmoly/filter/query/actors/defaults/number', 20 );
		
		/**
		 * Filter default offset.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $offset
		 */
		$offset = apply_filters( 'wpmoly/filter/query/actors/defaults/offset', 0 );
		
		/**
		 * Filter default hide_empty value.
		 * 
		 * @since    3.0
		 * 
		 * @param    boolean    $hide_empty
		 */
		$hide_empty = apply_filters( 'wpmoly/filter/query/actors/defaults/hide_empty', false );
		
		/**
		 * Filter default query orderby.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $orderby
		 */
		$orderby = apply_filters( 'wpmoly/filter/query/actors/defaults/orderby', 'name' );
		
		/**
		 * Filter default query order.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $order
		 */
		$order = apply_filters( 'wpmoly/filter/query/actors/defaults/order', 'ASC' );

		// paged is for Post Queries, use offset instead
		if ( ! empty( $args['paged'] ) ) {
			$args['offset'] = $args['paged'];
		} else {
			$args['paged'] = 1;
		}

		/**
		 * Filter default query args.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $args
		 */
		$defaults = apply_filters( 'wpmoly/filter/query/actors/defaults/query_args', array(
			'taxonomy'   => 'actor',
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
			$this->items[] = get_actor( $term );
		}

		return $this->items;
	}

	/**
	 * Perform the Post query.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Query parameters
	 * 
	 * @return   array
	 */
	public function post_query( $args = array() ) {

		/**
		 * Filter default preset post status.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $post_status
		 */
		$post_status = apply_filters( 'wpmoly/filter/query/actors/defaults/post_status', array( 'publish' ) );

		/**
		 * Filter default number of posts per page.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $posts_per_page
		 */
		$posts_per_page = apply_filters( 'wpmoly/filter/query/actors/defaults/posts_per_page', 20 );

		/**
		 * Filter default query orderby.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $orderby
		 */
		$orderby = apply_filters( 'wpmoly/filter/query/actors/defaults/orderby', 'post_date' );

		/**
		 * Filter default query order.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $order
		 */
		$order = apply_filters( 'wpmoly/filter/query/actors/defaults/order', 'DESC' );

		/**
		 * Filter default query args.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $args
		 */
		$defaults = apply_filters( 'wpmoly/filter/query/actors/defaults/query_args', array(
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

		$total = wp_count_terms( 'actor' );
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

		$page = $this->query->query_vars['paged'];
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
