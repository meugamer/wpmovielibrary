<?php
/**
 * WPMovieLibrary Legacy functions.
 * 
 * Deal with old WordPress/WPMovieLibrary versions.
 * 
 * @since     1.3
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! defined( 'ABSPATH' ) )
	exit;


/**
 * Simple function to check WordPress version. This is mainly
 * used for styling as WP3.8 introduced a brand new dashboard
 * look n feel.
 *
 * @since    1.0
 *
 * @return   boolean    Older/newer than WordPress 3.8?
 */
function wpmoly_modern_wp() {
	return version_compare( get_bloginfo( 'version' ), '3.8', '>=' );
}
