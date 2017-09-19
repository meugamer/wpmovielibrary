<?php
/**
 * Define the Template classes.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\templates;

use WP_Error;

/**
 * General Template class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * 
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Template {

	/**
	 * Template path.
	 *
	 * @since    3.0
	 *
	 * @var      string
	 */
	protected $path;

	/**
	 * Template data.
	 *
	 * @since    3.0
	 *
	 * @var      array
	 */
	protected $data = array();

	/**
	 * Template content.
	 *
	 * @since    3.0
	 *
	 * @var      string
	 */
	protected $template = '';

	/**
	 * Set the Template data.
	 *
	 * If $name is an array or an object, use it as a set of data; if not,
	 * use $name and $value as key and value.
	 *
	 * @since    3.0
	 *
	 * @param    mixed    $name Data name.
	 * @param    mixed    $value Data value.
	 *
	 * @return   Template
	 */
	public function set_data( $name, $value = '' ) {

		if ( is_object( $name ) ) {
			$name = get_object_vars( $name );
		}

		if ( is_array( $name ) ) {
			foreach ( $name as $key => $data ) {
				$this->set_data( $key, $data );
			}
		} else {
			$this->data[ (string) $name ] = $value;
		}

		return $this;
	}

	/**
	 * Prepare the Template.
	 *
	 * @since    3.0
	 *
	 * @param    string     $require 'once' to use require_once(), 'always' to use require()
	 *
	 * @return   string
	 */
	abstract protected function prepare( $require = 'once' );

	/**
	 * Render the Template.
	 *
	 * @since    3.0
	 *
	 * @param    string     $require Use 'once' to use require_once(), 'always' to use require()
	 * @param    boolean    $echo Use true to display, false to return
	 *
	 * @return   null
	 */
	public function render( $require = 'once', $echo = true ) {

		$this->prepare( $require );

		if ( true !== $echo ) {
			return $this->template;
		}

		echo $this->template;
	}

}
