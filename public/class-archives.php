<?php
/**
 * Define the Archive Pages class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 */

namespace wpmoly;

/**
 * 
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Archives {

	/**
	 * Single instance.
	 *
	 * @var    Frontend
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Singleton
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Filter post content to add grid to archive pages.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $content Post content.
	 * 
	 * @return   string
	 */
	public function archive_page_content( $content ) {

		$post_id = get_the_ID();
		if ( is_admin() || ! is_archive_page( $post_id ) ) {
			return $content;
		}

		$grid_id = get_post_meta( $post_id, '_wpmoly_grid_id', $single = true );
		$grid = $this->get_grid( (int) $grid_id );

		$position = get_post_meta( $post_id, '_wpmoly_grid_position', $single = true );
		if ( 'top' === $position ) {
			$content = $grid . $content;
		} elseif ( 'bottom' === $position ) {
			$content = $content . $grid;
		}

		return $content;
	}

	/**
	 * Load archive pages grids.
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $content Post content.
	 * 
	 * @return   string
	 */
	private function get_grid( $grid_id ) {

		$grid = get_grid( $grid_id );
		if ( empty( $grid->post ) ) {
			return null;
		}

		$template = get_grid_template( $grid );

		return $template->render( $require = 'always', $echo = false );
	}

}
