<?php
/**
 * REST API:Grids_Controller class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/rest-api
 */

namespace wpmoly\Rest;

use WP_REST_Posts_Controller;

/**
 * Core class to access grids via the REST API.
 *
 * @see        WP_REST_Posts_Controller
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/rest-api
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Grids_Controller extends WP_REST_Posts_Controller {

	/**
	 * Constructor.
	 *
	 * @since    3.0
	 *
	 * @param    string    $post_type Post type.
	 */
	public function __construct( $post_type ) {

		$this->post_type = $post_type;
		$this->namespace = 'wp/v2';
		$obj = get_post_type_object( $post_type );
		$this->rest_base = ! empty( $obj->rest_base ) ? $obj->rest_base : $obj->name;

		$this->meta = new Grid_Meta_Fields( $this->post_type );
	}
}
