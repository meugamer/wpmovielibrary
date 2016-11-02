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


/**
 * Get a Movie Headbox template.
 * 
 * @since    3.0
 * 
 * @param    int    $movie Movie ID, object or array
 * 
 * @return   \wpmoly\Templates\Headbox
 */
function get_movie_headbox_template( $movie ) {

	$headbox = get_headbox( $movie );

	return new \wpmoly\Templates\Headbox( $headbox );
}

/**
 * Get a Actor Headbox template.
 * 
 * @since    3.0
 * 
 * @param    mixed    $actor Actor ID, object or array
 * 
 * @return   \wpmoly\Templates\Headbox
 */
function get_actor_headbox_template( $actor ) {

	$headbox = get_headbox( $actor );

	return new \wpmoly\Templates\Headbox( $headbox );
}

/**
 * Get a Collection Headbox template.
 * 
 * @since    3.0
 * 
 * @param    mixed    $collection Collection ID, object or array
 * 
 * @return   \wpmoly\Templates\Headbox
 */
function get_collection_headbox_template( $collection ) {

	$headbox = get_headbox( $collection );

	return new \wpmoly\Templates\Headbox( $headbox );
}

/**
 * Get a Genre Headbox template.
 * 
 * @since    3.0
 * 
 * @param    mixed    $genre Genre ID, object or array
 * 
 * @return   \wpmoly\Templates\Headbox
 */
function get_genre_headbox_template( $genre ) {

	$headbox = get_headbox( $genre );

	return new \wpmoly\Templates\Headbox( $headbox );
}

/**
 * Get a Grid template.
 * 
 * @since    3.0
 * 
 * @param    mixed    $grid Grid
 * 
 * @return   \wpmoly\Templates\Grid
 */
function get_grid_template( $grid ) {

	return new \wpmoly\Templates\Grid( $grid );
}
