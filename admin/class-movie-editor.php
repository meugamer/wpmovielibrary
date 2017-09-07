<?php
/**
 * Define the Movie Editor class.
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
 * Provide a tool to manage movies.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Movie_Editor extends Metabox {

	/**
	 * Current Post ID.
	 *
	 * @var    int
	 */
	private $post_id;

	/**
	 * Class constructor.
	 *
	 * @since    3.0
	 */
	public function __construct() {

		// Grap current term ID from URL
		if ( isset( $_REQUEST['post'] ) ) {
			$this->post_id = (int) $_REQUEST['post'];
		}
	}

	/**
	 * Define meta managers.
	 *
	 * @since    3.0
	 */
	protected function add_managers() {

		$this->add_manager( 'movie-metadata', array(
				'label'     => esc_html__( 'Metadata', 'wpmovielibrary' ),
				'post_type' => 'movie',
				'context'   => 'normal',
				'priority'  => 'high',
				'sections'  => array(
					'movie-meta' => array(
						'label'    => esc_html__( 'Metadata', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-meta',
						'settings' => array(
							'movie-meta-meh' => array(
								'type'     => 'text',
								'section'  => 'movie-meta',
								'label'    => esc_html__( 'Meh', 'wpmovielibrary' ),
								'description' => esc_html__( 'Meh meh meh.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
									'size'  => '2',
								),
								'default'  => 5,
							),
						),
					),
					'movie-details' => array(
						'label'    => esc_html__( 'Details', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-details',
						'settings' => array(
							'movie-details-meh' => array(
								'type'     => 'text',
								'section'  => 'movie-details',
								'label'    => esc_html__( 'Meh', 'wpmovielibrary' ),
								'description' => esc_html__( 'Meh meh meh.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
									'size'  => '2',
								),
								'default'  => 5,
							),
						),
					),
					'movie-cast' => array(
						'label'    => esc_html__( 'Cast', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-actor-alt',
						'settings' => array(
							'movie-cast-meh' => array(
								'type'     => 'text',
								'section'  => 'movie-cast',
								'label'    => esc_html__( 'Meh', 'wpmovielibrary' ),
								'description' => esc_html__( 'Meh meh meh.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
									'size'  => '2',
								),
								'default'  => 5,
							),
						),
					),
					'movie-crew' => array(
						'label'    => esc_html__( 'Crew', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-director',
						'settings' => array(
							'movie-crew-meh' => array(
								'type'     => 'text',
								'section'  => 'movie-crew',
								'label'    => esc_html__( 'Meh', 'wpmovielibrary' ),
								'description' => esc_html__( 'Meh meh meh.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
									'size'  => '2',
								),
								'default'  => 5,
							),
						),
					),
					'movie-backdrops' => array(
						'label'    => esc_html__( 'Backdrops', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-images-alt',
						'settings' => array(
							'movie-backdrops-meh' => array(
								'type'     => 'text',
								'section'  => 'movie-backdrops',
								'label'    => esc_html__( 'Meh', 'wpmovielibrary' ),
								'description' => esc_html__( 'Meh meh meh.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
									'size'  => '2',
								),
								'default'  => 5,
							),
						),
					),
					'movie-posters' => array(
						'label'    => esc_html__( 'Posters', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-poster',
						'settings' => array(
							'movie-posters-meh' => array(
								'type'     => 'text',
								'section'  => 'movie-posters',
								'label'    => esc_html__( 'Meh', 'wpmovielibrary' ),
								'description' => esc_html__( 'Meh meh meh.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
									'size'  => '2',
								),
								'default'  => 5,
							),
						),
					),
				),
			),
		);

	}

	/**
	 * Load frameworks if needed.
	 *
	 * @since    3.0
	 */
	public function load_meta_frameworks() {

		// Bail if not our post type.
		if ( 'movie' !== get_current_screen()->post_type ) {
			return;
		}

		parent::load_meta_frameworks();
	}

	/**
	 * Define metaboxes.
	 *
	 * @since    3.0
	 */
	protected function add_metaboxes() {}

}
