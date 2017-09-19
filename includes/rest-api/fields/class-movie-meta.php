<?php
/**
 * REST API: WP_REST_Movie_Meta_Fields class
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\rest\fields;

use WP_REST_Post_Meta_Fields;

/**
 * Core class used to manage meta values for movies via the REST API.
 *
 * @see        WP_REST_Post_Meta_Fields
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * 
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movie_Meta extends WP_REST_Post_Meta_Fields {

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
				'raw'      => $value,
			);
		} else {
			$value = array(
				'rendered' => $value,
			);
		}

		return $value;
	}

	/**
	 * Retrieves the meta field value.
	 *
	 * Override WP_REST_Post_Meta_Fields::get_value() to remove custom prefix
	 * on meta keys and filtered unwanted meta.
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

		$fields   = $this->get_registered_fields();
		$response = array();

		foreach ( $fields as $meta_key => $args ) {

			// Unprefix meta keys.
			$name = unprefix_movie_meta_key( $args['name'] );

			// Only include requested fields.
			$requested_fields = array_filter( $request['fields'] );
			if ( ! empty( $requested_fields ) && ! in_array( $name, $requested_fields ) ) {
				continue;
			}

			$all_values = get_metadata( $this->get_meta_type(), $object_id, $meta_key, false );
			if ( $args['single'] ) {
				if ( empty( $all_values ) ) {
					$value = $args['schema']['default'];
				} else {
					$value = $all_values[0];
				}
				$value = $this->prepare_value_for_response( $value, $request, $args );
			} else {
				$value = array();
				foreach ( $all_values as $row ) {
					$value[] = $this->prepare_value_for_response( $row, $request, $args );
				}
			}

			$response[ $name ] = $value;
		}

		return $response;
	}

}
