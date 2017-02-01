<?php
/**
 * Define the Person Editor class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Admin;

use wpmoly\Core\Metabox;

/**
 * Provide a tool to manage Persons: actors, directors, crew...
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class PersonEditor extends Metabox {

	/**
	 * Current Post ID.
	 * 
	 * @var    int
	 */
	private $post_id = 0;

	/**
	 * Grid instance.
	 * 
	 * @var    Grid
	 */
	private $person;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

		$this->add_manager( 'movie-person', array(
				'label'     => esc_html__( 'Identity', 'wpmovielibrary' ),
				'post_type' => 'person',
				'context'   => 'normal',
				'priority'  => 'high',
				'sections'  => array(
					
				)
			)
		);
	}

	/**
	 * Load frameworks if needed.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function load_meta_frameworks() {

		// Bail if not our post type.
		if ( 'person' !== get_current_screen()->post_type ) {
			return;
		}

		parent::load_meta_frameworks();
	}

	/**
	 * Save the person settings.
	 * 
	 * Actually does two things. 1) make sure person type, mode and theme are
	 * saved; it should already be done through Ajax, but let's make sure it
	 * really did. 2) clean up POST data. We're using three ButterBean 
	 * managers with identical control IDs, meaning we have to remove the
	 * unwanted fields corresponding to different types than current type to
	 * avoid having values overflowed by the last manager in the list.
	 * 
	 * @since    3.0
	 * 
	 * @param    int        $post_id Post ID.
	 * @param    WP_Post    $post Post object.
	 * @param    boolean    $update Updating existing post?
	 * 
	 * @return   void
	 */
	public function save( $post_id, $post, $update ) {

	}

}
