<?php
/**
 * Define the Nodes Request class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/requests
 */

namespace wpmoly\Requests;

use WP_Query;
use WP_Error;

/**
 * Find Nodes in various ways.
 * 
 * 
 * 
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/requests
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Request {

	/**
	 * Nodes.
	 * 
	 * @var    array
	 */
	protected $items;

	/**
	 * Request parameters.
	 * 
	 * @var    array
	 */
	protected $args;

	/**
	 * Request presets.
	 * 
	 * @var    array
	 */
	protected $presets;

	/**
	 * Request default preset.
	 * 
	 * @var    string
	 */
	protected $default_preset;

	/**
	 * Internal Query.
	 * 
	 * @var    WP_Query
	 */
	protected $query;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize the request.
	 * 
	 * Should be used to set the preset list.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function init();

	/**
	 * Support direct call to presets.
	 * 
	 * Makes it possible to use every preset as if it was a method using a
	 * transparent call to Request::apply()
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $method
	 * @param    array      $arguments
	 * 
	 * @return   mixed
	 */
	public function __call( $method, $arguments ) {

		// Catch default presets
		if ( 'default_preset' === $method ) {
			$method = $this->default_preset;
		}

		if ( $this->supports( $method ) ) {

			// Parameters should be $arguments[0]
			$arguments = (array) $arguments;
			$arguments = array_shift( $arguments );

			return $this->apply( $method, $arguments );
		}

		return false;
	}

	/**
	 * Check if the request supports a specific preset.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $preset Preset slug
	 * 
	 * @return   boolean
	 */
	public function supports( $preset ) {

		return isset( $this->presets[ $preset ] );
	}

	/**
	 * Apply a preset.
	 * 
	 * This actually run the query with the filtered preset parameters.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $preset Preset slug
	 * @param    array     $parameters Preset parameters
	 * 
	 * @return   mixed
	 */
	public function apply( $preset, $parameters ) {

		$parameters = $this->get_preset_parameters( $preset, $parameters );

		return $this->query( $parameters );
	}

	/**
	 * Get the parameters for a specific preset.
	 * 
	 * Filter the default parameters and parsed parameters.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $preset Preset slug
	 * @param    array     $parameters Preset parameters
	 * 
	 * @return   array
	 */
	public function get_preset_parameters( $preset, $parameters ) {

		if ( ! $this->supports( $preset ) ) {
			return array();
		}

		/**
		 * Filter the default preset parameters.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $presets
		 */
		$defaults = apply_filters( "wpmoly/filter/query/{$preset}/args/defaults", $this->presets[ $preset ] );

		/**
		 * 
		 * 
		 * @since    3.0
		 * 
		 * @param    array    
		 */
		$parameters = apply_filters( "wpmoly/filter/query/{$preset}/args", $this->parse_args( $parameters, $defaults ) );

		return $parameters;
	}

	/**
	 * Get the Query instance.
	 * 
	 * Default return value should be WP_Query or WP_Term_Query, but depends
	 * on the actual Request instance.
	 * 
	 * @since    3.0
	 * 
	 * @return   mixed
	 */
	public function get_query() {

		if ( is_null( $this->query ) ) {
			return new WP_Error( 'query_is_null', __( 'Query couln’t run', 'wpmovielibrary' ) );
		}

		return $this->query;
	}

	/**
	 * Get query items.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_items() {

		return $this->items;
	}

	/**
	 * Is the Query failed, return the error code.
	 * 
	 * @since    3.0
	 * 
	 * @return   mixed
	 */
	public function get_error() {

		if ( is_wp_error( $this->get_query() ) ) {
			return $this->get_query();
		}

		return false;
	}

	/**
	 * Check for Query failure.
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function has_error() {

		if ( is_wp_error( $this->get_query() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Perform the request.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Request parameters
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
	 * Retrieve request parameters for REST API.
	 * 
	 * Convert current request parameters to match the REST API supported
	 * parameters.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_rest_args() {

		$args = array();

		/*
		 * This array defines mappings between public API query parameters whose
		 * values are accepted as-passed, and their internal WP_Query parameter
		 * name equivalents (some are the same). Only values which are also
		 * present in $this->args will be set.
		 */
		$parameter_mappings = array(
			'offset'     => 'offset',
			'order'      => 'order',
			'orderby'    => 'orderby',
			'paged'      => 'page',
			's'          => 'search',
			'hide_empty' => 'hide_empty',
			'number'     => 'per_page'
		);

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $args.
		 */
		foreach ( $parameter_mappings as $wp_param => $api_param ) {
			if ( isset( $this->args[ $wp_param ] ) ) {
				$args[ $api_param ] = $this->args[ $wp_param ];
			}
		}

		if ( isset( $this->args['posts_per_page'] ) ) {
			$args['per_page'] = $this->args['posts_per_page'];
		}

		if ( isset( $this->args['number'] ) ) {
			$args['number'] = $this->args['number'];
		}

		if ( isset( $this->args['meta_query'] ) ) {
			foreach ( $this->args['meta_query'] as $meta ) {
				if ( is_array( $meta ) && ! empty( $meta['key'] ) && ! empty( $meta['value'] ) && false !== strpos( $meta['key'], '_wpmoly_movie_' ) ) {
					$key = str_replace( '_wpmoly_movie_', '', $meta['key'] );
					$args[ $key ] = $meta['value'];
				}
			}
		}

		return $args;
	}

	/**
	 * Retrieve request parameters.
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
