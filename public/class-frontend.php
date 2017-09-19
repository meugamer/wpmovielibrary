<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly;

use wpmoly\core\Assets;

/**
 * The public-facing functionality of the plugin.
 *
 * Register and enqueue public scripts, styles and templates.
 *
 * @package    WPMovieLibrary
 * 
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Frontend extends Assets {

	/**
	 * Display the movie Headbox along with movie content.
	 *
	 * If we're in search or archive templates, show the default, minimal
	 * Headbox; if we're in single template, show the default full Headbox.
	 *
	 * @since    3.0
	 *
	 * @param    string    $content Post content.
	 *
	 * @return   string
	 */
	public function the_headbox( $content ) {

		if ( 'movie' != get_post_type() ) {
			return $content;
		}

		$movie = get_movie( get_the_ID() );
		$headbox = get_headbox( $movie );

		if ( is_single() ) {
			$headbox->set_theme( 'extended' );
		} elseif ( is_archive() || is_search() ) {
			$headbox->set_theme( 'default' );
		}

		$template = get_movie_headbox_template( $headbox );

		return $template->render() . $content;
	}

}
