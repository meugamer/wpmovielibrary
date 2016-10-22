<?php
/**
 * The file that defines the plugin template functions.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */


function get_grid_template( $grid ) {

	return new \wpmoly\Templates\Grid( $grid );
}
