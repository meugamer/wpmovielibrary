<?php
/**
 * Define the DefaultImage class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * Generic Singleton Node class to handle empty Backdrop and Poster instances.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Default_Image extends Image {

	/**
	 * Default Poster instance.
	 *
	 * @since    3.0
	 *
	 * @var      array
	 */
	private static $instance;

	/**
	 * Make the Image.
	 *
	 * @since    3.0
	 */
	public function make() {

		$this->attachment = false;

		$this->set_defaults();
	}

	/**
	 * Get a Default Poster instance.
	 *
	 * @since    3.0
	 *
	 * @return   Default_Poster
	 */
	final public static function get_instance( $unused = null ) {

		if ( isset( self::$instance ) ) {
			return self::$instance;
		}

		self::$instance = new static;

		return self::$instance;
	}
}
