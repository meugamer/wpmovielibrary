<?php
/**
 * Define the API class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\api;

use WP_Error;

/**
 * Handle the interactions with the TMDb API.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class API {

	/**
	 * Current instance.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @var Library
	 */
	public static $instance;

	/**
	 * Define the API class.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		self::$instance = $this;

		$this->movie  = new Movie;
		//$this->tv     = new TV;
		//$this->person = new Person;
	}

	/**
	 * Singleton.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return API
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

}
