<?php
/**
 * WPMovieLibrary Bootstrap file.
 *
 * @link              http://wpmovielibrary.com
 * @since             3.0
 * @package           WPMovieLibrary
 *
 * @wordpress-plugin
 * Plugin Name:       WPMovieLibrary
 * Plugin URI:        https://wpmovielibrary.com
 * Description:       WordPress Movie Library is an advanced movie library managing plugin to turn your WordPress Blog into a Movie Library. 
 * Version:           3.0.0-alpha2
 * Author:            Charlie Merland
 * Author URI:        https://charliemerland.me/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wpmovielibrary
 * Domain Path:       /languages
 */

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	exit;
}

define( 'WPMOLY_VERSION', '3.0.0-alpha2' );
define( 'WPMOLY_PATH',    trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WPMOLY_URL',     trailingslashit( plugin_dir_url( __FILE__ ) ) );

require_once WPMOLY_PATH . 'class-library.php';

/**
 * Retrieve plugin instance.
 *
 * @since 3.0.0
 *
 * @return \wpmoly\Library
 */
function wpmoly() {

	return \wpmoly\Library::get_instance();
}

$GLOBALS['wpmoly'] = wpmoly();
