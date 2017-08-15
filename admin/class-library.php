<?php
/**
 * 
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Admin;

use wpmoly\Templates\Admin as Template;

/**
 * 
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Library {

	/**
	 * Single instance.
	 *
	 * @var    Library
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Singleton
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Build the Backstage Library view.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function build() {

		//$library = new Template( 'library.php' );
		//$library->render();
	}

}
