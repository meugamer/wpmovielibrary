<?php
/**
 * Define the Template classes.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Templates;

use WP_Error;

/**
 * Public-side Template class.
 * 
 * Public Templates are allowed more customization and interaction than Admin
 * Template, including filtering and template replacement.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Front extends Template {

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $path Template file path
	 * @param    array     $data Template data
	 * 
	 * @return   Template|WP_Error
	 */
	public function __construct( $path, $data = array(), $params = array() ) {

		$path = 'public/templates/' . (string) $path;
		if ( ! file_exists( WPMOLY_PATH . $path ) ) {
			return new WP_Error( 'missing_template_path', sprintf( __( 'Error: "%s" does not exists.', 'wpmovielibrary' ), esc_attr( WPMOLY_PATH . $path ) ) );
		}

		$this->path = $path;

		return $this;
	}

	/**
	 * Prepare the Template.
	 *
	 * Allows parent/child themes to override the markup by placing a file
	 * named basename( $default_template_path ) in their root folder, and
	 * also allows plugins or themes to override the markup by a filter.
	 * 
	 * Themes might prefer that method if they place their templates in
	 * sub-directories to avoid cluttering the root folder. In both cases,
	 * the theme/plugin will have access to the variables so they can fully
	 * customize the output.
	 *
	 * @since    3.0
	 * 
	 * @param    string     $require 'once' to use require_once(), 'always' to use require()
	 * 
	 * @return   string
	 */
	public function prepare( $require = 'once' ) {

		/**
		 * Fired before starting to prepare the template.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $path Plugin-relative file path
		 * @param    array     $data Template data
		 */
		do_action( "wpmoly/render/template/pre", $this->path, $this->data );

		$template = $this->locate_template();
		if ( is_file( $template ) ) {

			extract( $this->data );
			ob_start();

			if ( 'always' == $require ) {
				require( $template );
			} else {
				require_once( $template );
			}

			$content = ob_get_clean();

			/**
			 * Filter the template content.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $content Plugin-relative file path
			 * @param    string    $template WordPress-relative file path
			 * @param    string    $path Plugin-relative file path
			 * @param    array     $data Template data
			 */
			$this->template = apply_filters( "wpmoly/filter/template/content", $content, $template, $this->path, $this->data );
		}

		/**
		 * Fired after the template preparation.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $template Template content
		 * @param    string    $template WordPress-relative file path
		 * @param    string    $path Plugin-relative file path
		 * @param    array     $data Template data
		 */
		do_action( "wpmoly/render/template/after", $this->template, $template, $this->path, $this->data );

		return $this->template;
	}

	/**
	 * Public Templates can be overriden by themes.
	 * 
	 * A theme implementing its own WPMovieLibrary templates should have a
	 * 'wpmovielibrary' folders at its root with an organization conform to
	 * the plugin's templates file organization.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	private function locate_template(  ) {

		$template = locate_template( 'wpmovielibrary/' . $this->path, false, false );
		if ( ! $template ) {
			$template = WPMOLY_PATH . $this->path;
		}

		/**
		 * Filter the template filepath.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $template WordPress-relative file path
		 * @param    string    $path Plugin-relative file path
		 * @param    array     $data Template data
		 */
		return $template = apply_filters( "wpmoly/filter/template/path", $template, $this->path, $this->data );
	}
}
