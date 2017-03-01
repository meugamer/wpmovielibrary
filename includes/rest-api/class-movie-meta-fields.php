<?php
/**
 * REST API: WP_REST_Movie_Meta_Fields class
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/rest-api
 */

namespace wpmoly\Rest;

use WP_REST_Post_Meta_Fields;

/**
 * Core class used to manage meta values for movies via the REST API.
 *
 * @see        WP_REST_Post_Meta_Fields
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/rest-api
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movie_Meta_Fields extends WP_REST_Post_Meta_Fields {

	/**
	 * Prepare a meta value for a response.
	 * 
	 * Override WP_REST_Post_Meta_Fields::prepare_value_for_response() to 
	 * keep a copy of the raw value along with the rendered version.
	 * 
	 * @see WP_REST_Post_Meta_Fields::prepare_value_for_response()
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed              $value   Meta value to prepare.
	 * @param    WP_REST_Request    $request Current request object.
	 * @param    array              $args    Options for the field.
	 * 
	 * @return   mixed              Prepared value.
	 */
	protected function prepare_value_for_response( $value, $request, $args ) {

		if ( ! empty( $args['prepare_callback'] ) ) {
			$value = array(
				'rendered' => call_user_func( $args['prepare_callback'], $value, $request, $args ),
				'raw'      => $value
			);
		} else {
			$value = array( 'rendered' => $value );
		}

		return $value;
	}

	/**
	 * Retrieves the meta field value.
	 * 
	 * Override WP_REST_Post_Meta_Fields::get_value() to remove custom prefix
	 * on meta keys.
	 *
	 * @see WP_REST_Post_Meta_Fields::get_value()
	 * 
	 * @since    3.0
	 * 
	 * @param    int                $object_id Object ID to fetch meta for.
	 * @param    WP_REST_Request    $request   Full details about the request.
	 * 
	 * @return   WP_Error|object    Object containing the meta values by name, otherwise WP_Error object.
	 */
	public function get_value( $object_id, $request ) {

		$response = parent::get_value( $object_id, $request );

		$meta = array();
		foreach ( $response as $key => $value ) {
			$key = str_replace( '_wpmoly_movie_', '', $key );
			$meta[ $key ] = $value;
		}

		return $meta;
	}
}
