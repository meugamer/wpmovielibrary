<?php
/**
 * Define the AdminBar class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary
 */

namespace wpmoly;

/**
 * Handle Admin Bar custom menu and stuff.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary
 * @author     Charlie Merland <charlie@caercam.org>
 */
class AdminBar {

	/**
	 * Singleton.
	 *
	 * @var    Terms
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
	 * Add a submenu to the 'Edit Post' menu to edit the grid related to an
	 * archive page.
	 * 
	 * @since    3.0
	 * 
	 * @param    WP_Admin_Bar    $wp_admin_bar
	 * 
	 * @return   void
	 */
	public function edit_grid_menu( $wp_admin_bar ) {

		$post_id = get_the_ID();
		if ( ! $post_id || ! is_archive_page( $post_id ) ) {
			return false;
		}

		// Missing edit menu
		if ( ! $wp_admin_bar->get_node( 'edit' ) ) {
			return false;
		}

		// Retrieve related grid
		$grid_id = get_post_meta( $post_id, '_wpmoly_grid_id', $single = true );
		if ( empty( $grid_id ) ) {
			return false;
		}

		// Add a new node
		$wp_admin_bar->add_node( array(
			'id'     => 'edit-grid',
			'title'  => __( 'Edit Grid', 'wpmovielibrary' ),
			'parent' => 'edit',
			'href'   => get_edit_post_link( $grid_id )
		) );
	}

}
