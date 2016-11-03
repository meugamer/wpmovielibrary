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
	 * Determine if we're dealing with a single item, ie. a term, or a real
	 * archive page.
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

		$type = get_archive_page_type( $post_id );
		if ( ! empty( get_query_var( $type ) ) ) {
			return $this->single_page_content( $post_id, $type, $content );
		}

		return $this->real_archive_page_content( $post_id, $content );
	}

	/**
	 * Handle single item content.
	 * 
	 * Mostly used to show custom pages for taxonomy terms.
	 * 
	 * @since    3.0
	 * 
	 * @param    int       $post_id Current Post ID
	 * @param    string    $type Archive page type.
	 * @param    string    $content Post content.
	 * 
	 * @return   string
	 */
	public function single_page_content( $post_id, $type, $content ) {

		$name = get_query_var( $type );
		$term = get_term_by( 'slug', $name, $type );
		if ( ! $term ) {
			return $content;
		}

		$headbox = get_term_headbox( $term );
		$headbox->set_theme( 'extended' );

		$template = get_headbox_template( $headbox );

		//TODO add custom grid.

		$content = $template->render() . $content;

		return $content;
	}

	/**
	 * Handle archive page content.
	 * 
	 * @since    3.0
	 * 
	 * @param    int       $post_id Current Post ID
	 * @param    string    $content Post content.
	 * 
	 * @return   string
	 */
	public function real_archive_page_content( $post_id, $content ) {

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
