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
abstract class Query {

	/**
	 * Nodes.
	 * 
	 * @var    array
	 */
	protected $items;

	/**
	 * Query parameters.
	 * 
	 * @var    array
	 */
	protected $args;

	/**
	 * Internal Query.
	 * 
	 * @var    WP_Query
	 */
	public $query;

	/**
	 * Perform the query.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Query parameters
	 * 
	 * @return   array
	 */
	abstract public function query( $args = array() );

	/**
	 * Parse arguments to ensure ordering are not lost when altering the
	 * grid settings.
	 * 
	 * If order/orderby parameters are left empty, fallback to default
	 * values.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args
	 * @param    array    $defaults
	 * 
	 * @return   array
	 */
	protected function parse_args( $args, $defaults = array() ) {

		$parsed_args = wp_parse_args( $args, $defaults );

		if ( empty( $args['order'] ) && ! empty( $defaults['order'] ) ) {
			$parsed_args['order'] = $defaults['order'];
		}

		if ( empty( $args['orderby'] ) && ! empty( $defaults['orderby'] ) ) {
			$parsed_args['orderby'] = $defaults['orderby'];
		}

		return $parsed_args;
	}

	/**
	 * Retrieve query parameters.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_args() {

		return $this->args;
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
