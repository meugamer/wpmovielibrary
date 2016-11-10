<?php
/**
 * Define the Taxonomies Widget class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/widgets
 */

namespace wpmoly\Widgets;

/**
 * Taxonomies Widget class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/widgets
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Taxonomies extends Widget {

	/**
	 * Set default properties.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function make() {

		$this->id_base = '';
		$this->name = __( 'WPMovieLibrary Taxonomies', 'wpmovielibrary' );
		$this->description = __( 'Display a list of terms from a specific taxonomy: collections, genres or actors.', 'wpmovielibrary' );
	}

	/**
	 * Build Widget content.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function build(  ) {

		
	}

	/**
	 * Build Widget form content.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function build_form() {

		if ( empty( $this->get_attr( 'title' ) ) ) {
			$this->set_attr( 'title', __( 'Statistics', 'wpmovielibrary' ) );
		}

		if ( empty( $this->get_attr( 'description' ) ) ) {
			$this->set_attr( 'description', '' );
		}
	}
}
