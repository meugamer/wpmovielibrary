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
 * Get an Headbox template.
 * 
 * @since    3.0
 * 
 * @param    Headbox    $headbox Headbox instance.
 * 
 * @return   \wpmoly\Templates\Headbox
 */
function get_headbox_template( $headbox ) {

	if ( ! $headbox instanceof \wpmoly\Node\Headbox ) {
		$headbox = get_headbox( $headbox );
	}

	return new \wpmoly\Templates\Headbox( $headbox );
}

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

	return get_headbox_template( $movie );
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

	return get_headbox_template( $actor );
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

	return get_headbox_template( $collection );
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

	return get_headbox_template( $genre );
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
