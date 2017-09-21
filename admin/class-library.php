<?php
/**
 * Define the Library class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\admin;

use wpmoly\templates\Admin as Template;

/**
 * Provide a custom dashboard for the plugin.
 *
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Library {

	/**
	 * Single instance.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var Library
	 */
	private static $instance = null;

	/**
	 * Library.
	 *
	 * @since 3.0.0
	 *
	 * @final
	 * @static
	 * @access public
	 *
	 * @return Library
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Build the Backstage Library view.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function build() {

?>
	<div class="wrap">
		<span class="dashicons dashicons-smiley"></span>
		<p><?php _e( 'Still on Alpha2, nothing here yet!', 'wpmovielibrary' ); ?></p>
	</div>
<?php
		//$library = new Template( 'library.php' );
		//$library->render();
	}

}
