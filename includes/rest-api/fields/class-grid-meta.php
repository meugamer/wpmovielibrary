<?php
/**
 * REST API: WP_REST_Grid_Meta_Fields class
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\rest\fields;

use WP_REST_Post_Meta_Fields;

/**
 * Core class used to manage meta values for grids via the REST API.
 *
 * @see        WP_REST_Post_Meta_Fields
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * 
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Grid_Meta extends WP_REST_Post_Meta_Fields {

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
			$key = unprefix_grid_meta_key( $key, false );
			$meta[ $key ] = $value;
		}

		return $meta;
	}

	/**
	 * Retrieves all the registered meta fields.
	 *
	 * Override WP_REST_Post_Meta_Fields::get_registered_fields() to remove
	 * unrelated meta keys.
	 *
	 * @since    3.0
	 *
	 * @return   array    Registered fields.
	 */
	protected function get_registered_fields() {

		$prefix = prefix_grid_meta_key( '' );
		$registered = parent::get_registered_fields();
		foreach ( $registered as $name => $field ) {
			if ( false === strpos( $name, $prefix ) ) {
				unset( $registered[ $name ] );
			}
		}

		return $registered;
	}

	/**
	 * Prepares a meta value for a response.
	 *
	 * Replace empty grid meta with default values.
	 *
	 * @since    3.0
	 *
	 * @param    mixed              $value   Meta value to prepare.
	 * @param    WP_REST_Request    $request Current request object.
	 * @param    array              $args    Options for the field.
	 *
	 * @return   mixed Prepared value.
	 */
	protected function prepare_value_for_response( $value, $request, $args ) {

		if ( '' === $value && ! empty( $args['schema']['default'] ) ) {
			$value = $args['schema']['default'];
		}

		if ( ! empty( $args['prepare_callback'] ) ) {
			$value = call_user_func( $args['prepare_callback'], $value, $request, $args );
		}

		return $value;
	}
}
