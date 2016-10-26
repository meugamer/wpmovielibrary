<?php
/**
 * Define the Term Editor class.
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
 * Provide a tool to manage custom terms.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class TermEditor extends Metabox {

	/**
	 * Current Term ID.
	 * 
	 * @var    int
	 */
	private $term_id;

	/**
	 * Current Term Taxonomy.
	 * 
	 * @var    string
	 */
	private $taxonomy;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

		// Grap current term ID from URL
		if ( isset( $_REQUEST['tag_ID'] ) ) {
			$this->term_id = (int) $_REQUEST['tag_ID'];
		}

		// Grap current term taxonomy from URL
		if ( isset( $_REQUEST['taxonomy'] ) ) {
			$this->taxonomy = $_REQUEST['taxonomy'];
		}

		if ( 'actor' == $this->taxonomy ) {
			$this->add_manager( 'actor-meta', array(
				'label'    => esc_html__( 'Actor Meta', 'wpmovielibrary' ),
				'taxonomy' => 'actor',
				'sections' => array(
					'actor-identity' => array(
						'label' => esc_html__( 'Identity', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-actor-alt',
						'settings' => array(
							'actor-person-id' => array(
								'type'        => 'posts',
								'post_type'   => 'people',
								'section'     => 'actor-identity',
								'label'       => esc_html__( 'Person ID', 'wpmovielibrary' ),
								'description' => sprintf( esc_html__( 'Select a Person corresponding to this actor. Or maybe %s?', 'wpmovielibrary' ), sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( admin_url( 'post-new.php?post_type=person' ) ), __( 'add a new one', 'wpmovielibrary' ) ) ),
							),
							'actor-bio-description' => array(
								'type'        => 'checkbox',
								'section'     => 'actor-identity',
								'label'       => esc_html__( 'Use description as biography', 'wpmovielibrary' ),
								'description' => esc_html__( 'Use the term description content as the actor’s biography?', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col widefat' ),
							)
						)
					),
					'actor-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'actor-picture' => array(
								'type'        => 'radio-image',
								'section'     => 'actor-identity',
								'label'       => esc_html__( 'Actor picture' ),
								'description' => esc_html__( 'A default picture for this actor.', 'wpmovielibrary' ),
								'choices' => array(
									'neutral' => array(
										'url'   => WPMOLY_URL . 'public/img/actor-neutral-thumbnail.jpg',
										'label' => esc_html__( 'Neutral', 'wpmovielibrary' )
									),
									'female' => array(
										'url'   => WPMOLY_URL . 'public/img/actor-female-thumbnail.jpg',
										'label' => esc_html__( 'Female', 'wpmovielibrary' )
									),
									'male' => array(
										'url'   => WPMOLY_URL . 'public/img/actor-male-thumbnail.jpg',
										'label' => esc_html__( 'Male', 'wpmovielibrary' )
									),
								),
								'default' => 'neutral'
							)
						)
					)
				)
			) );
			/*$this->add_manager( 'actor-meta', array(
				'label'    => esc_html__( 'Actor Meta', 'wpmovielibrary' ),
				'taxonomy' => 'actor',
				'sections' => array(
					'identity' => array(
						'label' => esc_html__( 'Identity', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-actor-alt',
						'settings' => array(
							'tmdb_id' => array(
								'type'     => 'text',
								'section'  => 'identity',
								'label'    => esc_html__( 'TMDb ID', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'default'  => ''
							),
							'imdb_id' => array(
								'type'     => 'text',
								'section'  => 'identity',
								'label'    => esc_html__( 'IMDb ID', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'default'  => ''
							),
							'name' => array(
								'type'     => 'text',
								'section'  => 'identity',
								'label'    => esc_html__( 'Name', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'widefat' ),
								'default'  => ''
							),
							'biography' => array(
								'type'     => 'textarea',
								'section'  => 'identity',
								'label'    => esc_html__( 'Biography', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'default'  => ''
							),
							'birthday' => array(
								'type'     => 'text',
								'section'  => 'identity',
								'label'    => esc_html__( 'Birthday', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'default'  => ''
							),
							'deathday' => array(
								'type'     => 'text',
								'section'  => 'identity',
								'label'    => esc_html__( 'Deathday', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'default'  => ''
							),
							'also_known_as' => array(
								'type'     => 'text',
								'section'  => 'identity',
								'label'    => esc_html__( 'Alias(es)', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'default'  => ''
							),
							'place_of_birth' => array(
								'type'     => 'text',
								'section'  => 'identity',
								'label'    => esc_html__( 'Place of birth', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'default'  => ''
							),
							'gender' => array(
								'type'     => 'select',
								'section'  => 'identity',
								'label'    => esc_html__( 'Gender', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'choices'  => array(
									'1' => __( 'Female', 'wpmovielibrary' ),
									'2' => __( 'Male', 'wpmovielibrary' ),
								),
								'default'  => ''
							),
							'homepage' => array(
								'type'     => 'text',
								'section'  => 'identity',
								'label'    => esc_html__( 'Homepage', 'wpmovielibrary' ),
								'description' => '',
								'attr'     => array( 'class' => 'half-col widefat' ),
								'default'  => '',
								'sanitize' => 'esc_url'
							)
						)
					),
					'movie-credits' => array(
						'label' => esc_html__( 'Movie Credits', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-movie',
						'description' => esc_html__( '.', 'wpmovielibrary' ),
						'settings' => array(
							'movie-credits' => array(
								'type'     => 'textarea',
								'section'  => 'movie-credits',
								'label'    => esc_html__( '', 'wpmovielibrary' ),
								'description' => esc_html__( '', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' )
							)
						)
					),
					'tv-credits' => array(
						'label' => esc_html__( 'TV Credits', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-movie',
						'description' => esc_html__( '.', 'wpmovielibrary' ),
						'settings' => array(
							'tv-credits' => array(
								'type'     => 'textarea',
								'section'  => 'tv-credits',
								'label'    => esc_html__( '', 'wpmovielibrary' ),
								'description' => esc_html__( '', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' )
							)
						)
					)
				)
			) );*/
		}
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
		if ( ! in_array( get_current_screen()->taxonomy, array( 'actor', 'collection', 'genre' ) ) ) {
			return;
		}

		parent::load_meta_frameworks();
	}

	/**
	 * Redirect to the term editor after update.
	 * 
	 * Default behaviour is to redirect the user to the main taxonomy page
	 * ie. 'edit-tags.php', but we want users to see their updated term meta
	 * rather than go back to the list of terms.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $location The destination URL.
	 * @param    object    $taxonomy The taxonomy object.
	 * 
	 * @return   string
	 */
	public function term_redirect( $location, $taxonomy ) {

		if ( ! in_array( $taxonomy->name, array( 'actor', 'collection', 'genre' ) ) ) {
			return $location;
		}

		$location = sprintf( 'term.php?taxonomy=%s&tag_ID=%d&post_type=movie&message=3', $taxonomy->name, $this->term_id );

		return admin_url( $location );
	}

	/**
	 * Replace HTTP referer. This is used to replace the 'Back to Taxonomy'
	 * notice link showed after terms updated. Since we filter the term 
	 * update redirect location to go to the term editor instead of the main
	 * taxonomy page we have to update the referer used by the go-back link
	 * to avoid a loop.
	 *
	 * @since    3.0
	 * 
	 * @param    object    $term     Current taxonomy term object.
	 * @param    string    $taxonomy Current $taxonomy slug.
	 * 
	 * @return   void
	 */
	public function term_pre_edit_form( $term, $taxonomy ) {

		if ( empty( $_REQUEST['message'] ) ) {
			return false;
		}

		$location = sprintf( 'edit-tags.php?taxonomy=%s&post_type=movie', $taxonomy );
		$location = admin_url( $location );

		$_REQUEST['_wp_original_http_referer'] = $location;
		$_REQUEST['_wp_http_referer'] = $location;
	}

}
