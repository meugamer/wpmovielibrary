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
 * Get a specific template.
 * 
 * @since    3.0
 * 
 * @param    string    $template Template name.
 * 
 * @return   \wpmoly\Templates\Template
 */
function wpmoly_get_template( $template ) {

	if ( is_admin() ) {
		return new \wpmoly\Templates\Admin( $template );
	}

	return new \wpmoly\Templates\Front( $template );
}

/**
 * Get a specific JavaScript template.
 * 
 * @since    3.0
 * 
 * @param    string     $template Template name.
 * 
 * @return   \wpmoly\Templates\JavaScript
 */
function wpmoly_get_js_template( $template ) {

	$template = new \wpmoly\Templates\JavaScript( $template );

	return $template;
}

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
 * Simple alias for get_headbox_template().
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
 * Simple alias for get_headbox_template().
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
 * Simple alias for get_headbox_template().
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
 * Simple alias for get_headbox_template().
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

/**
 * Get a Widget public or admin template.
 * 
 * @since    3.0
 * 
 * @param    string    $template_id Template ID
 * 
 * @return   \wpmoly\Templates\Template
 */
function get_widget_template( $template_id ) {

	return wpmoly_get_template( 'widgets/' . (string) $template_id . '.php' );
}
