<?php
/**
 * Define the Headbox Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 */

namespace wpmoly\Shortcodes;

use wpmoly\Templates\Front as Template;

/**
 * General Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Headbox extends Shortcode {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 * 
	 * @var    string
	 */
	public static $name = 'movie';

	/**
	 * Shortcode attributes sanitizers
	 * 
	 * @var    array
	 */
	protected $validates = array(
		'id' => array(
			'default' => '',
			'values'  => null,
			'filter'  => 'intval'
		),
		'title' => array(
			'default' => '',
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'type' => array(
			'default' => 'movie',
			'values'  => null,
			'filter'  => 'esc_attr'
		),
		'theme' => array(
			'default' => 'default',
			'values'  => null,
			'filter'  => 'esc_attr'
		)
	);

	/**
	 * Shortcode aliases
	 * 
	 * @var    array
	 */
	protected static $aliases = array(
		'movie_headbox'
	);

	/**
	 * Build the Shortcode.
	 * 
	 * Prepare Shortcode parameters.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function make() {

		$this->get_movie();
		if ( ! $this->movie ) {
			return false;
		}

		$this->headbox = get_headbox( $this->movie );
		$this->headbox->set( $this->attributes );

		// Set Template
		$this->template = get_movie_headbox_template( $this->movie );
	}

	/**
	 * Run the Shortcode.
	 * 
	 * Perform all needed Shortcode stuff.
	 * 
	 * @since    3.0
	 * 
	 * @return   Shortcode
	 */
	public function run() {

		if ( $this->movie->is_empty() ) {
			$this->template = wpmoly_get_template( 'notice.php' );
			$this->template->set_data( array(
				'type'    => 'info',
				'icon'    => 'wpmolicon icon-info',
				'message' => sprintf( __( 'It seems this movie does not have any metadata available yet; %s?', 'wpmovielibrary' ), sprintf( '<a href="%s">%s</a>', get_edit_post_link(), __( 'care to add some', 'wpmovielibrary' ) ) ),
				'note'    => __( 'This notice is private; only you and other administrators can see it.', 'wpmovielibrary' )
			) );
			return $this;
		}

		$this->template->set_data( array(
			'headbox' => $this->headbox,
			'movie'   => $this->movie
		) );

		return $this;
	}

	/**
	 * Retriveve the Headbox Movie.
	 * 
	 * Try to find the movie by its title is such an attribute was passed
	 * to the Shortcode.
	 * 
	 * @since    3.0
	 * 
	 * @return   WP_Post|boolean
	 */
	private function get_movie() {

		if ( empty( $this->attributes['title'] ) ) {
			return $this->movie = get_movie( $this->attributes['id'] );
		}

		$post = get_page_by_title( $this->attributes['title'], OBJECT, 'movie' );
		if ( is_null( $post ) ) {
			return false;
		}

		return $this->movie = get_movie( $post->ID );
	}

	/**
	 * Initialize the Shortcode.
	 * 
	 * Run things before doing anything.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function init() {}
}
