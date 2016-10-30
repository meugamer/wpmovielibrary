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
								'section'     => 'actor-appearance',
								'label'       => esc_html__( 'Actor picture' ),
								'description' => esc_html__( 'A default picture for this actor.', 'wpmovielibrary' ),
								'choices' => array(
									'neutral' => array(
										'url'   => WPMOLY_URL . 'public/img/actor-neutral-thumbnail.png',
										'label' => esc_html__( 'Neutral', 'wpmovielibrary' )
									),
									'female' => array(
										'url'   => WPMOLY_URL . 'public/img/actor-female-thumbnail.png',
										'label' => esc_html__( 'Female', 'wpmovielibrary' )
									),
									'male' => array(
										'url'   => WPMOLY_URL . 'public/img/actor-male-thumbnail.png',
										'label' => esc_html__( 'Male', 'wpmovielibrary' )
									),
								),
								'default' => 'neutral'
							)
						)
					)
				)
			) );

		} elseif ( 'collection' == $this->taxonomy ) {

			$this->add_manager( 'collection-meta', array(
				'label'    => esc_html__( 'Genre Meta', 'wpmovielibrary' ),
				'taxonomy' => 'collection',
				'sections' => array(
					'collection-identity' => array(
						'label' => esc_html__( 'Identity', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-tag',
						'settings' => array(
							'collection-person-id' => array(
								'type'        => 'posts',
								'post_type'   => 'people',
								'section'     => 'collection-identity',
								'label'       => esc_html__( 'Person ID', 'wpmovielibrary' ),
								'description' => esc_html__( 'Select a Person related to this collection. A director maybe?', 'wpmovielibrary' ),
							),
						)
					),
					'collection-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'collection-thumbnail' => array(
								'type'        => 'radio-image',
								'section'     => 'collection-appearance',
								'label'       => esc_html__( 'Collection thumbnail' ),
								'description' => esc_html__( 'A default thumbnail for this collection.', 'wpmovielibrary' ),
								'attr' => array( 'class' => array( 'visible-labels' ) ),
								'choices' => array(
									'collection-A' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-A-thumbnail.png',
										'label' => esc_html__( 'Collection A', 'wpmovielibrary' )
									),
									'collection-B' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-B-thumbnail.png',
										'label' => esc_html__( 'Collection B', 'wpmovielibrary' )
									),
									'collection-C' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-C-thumbnail.png',
										'label' => esc_html__( 'Collection C', 'wpmovielibrary' )
									),
									'collection-D' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-D-thumbnail.png',
										'label' => esc_html__( 'Collection D', 'wpmovielibrary' )
									),
									'collection-E' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-E-thumbnail.png',
										'label' => esc_html__( 'Collection E', 'wpmovielibrary' )
									),
									'collection-F' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-F-thumbnail.png',
										'label' => esc_html__( 'Collection F', 'wpmovielibrary' )
									),
									'collection-G' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-G-thumbnail.png',
										'label' => esc_html__( 'Collection G', 'wpmovielibrary' )
									),
									'collection-H' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-H-thumbnail.png',
										'label' => esc_html__( 'Collection H', 'wpmovielibrary' )
									),
									'collection-I' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-I-thumbnail.png',
										'label' => esc_html__( 'Collection I', 'wpmovielibrary' )
									),
									'collection-J' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-J-thumbnail.png',
										'label' => esc_html__( 'Collection J', 'wpmovielibrary' )
									),
									'collection-K' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-K-thumbnail.png',
										'label' => esc_html__( 'Collection K', 'wpmovielibrary' )
									),
									'collection-L' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-L-thumbnail.png',
										'label' => esc_html__( 'Collection L', 'wpmovielibrary' )
									),
									'collection-M' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-M-thumbnail.png',
										'label' => esc_html__( 'Collection M', 'wpmovielibrary' )
									),
									'collection-N' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-N-thumbnail.png',
										'label' => esc_html__( 'Collection N', 'wpmovielibrary' )
									),
									'collection-O' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-O-thumbnail.png',
										'label' => esc_html__( 'Collection O', 'wpmovielibrary' )
									),
									'collection-P' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-P-thumbnail.png',
										'label' => esc_html__( 'Collection P', 'wpmovielibrary' )
									),
									'collection-Q' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-Q-thumbnail.png',
										'label' => esc_html__( 'Collection Q', 'wpmovielibrary' )
									),
									'collection-R' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-R-thumbnail.png',
										'label' => esc_html__( 'Collection R', 'wpmovielibrary' )
									),
									'collection-S' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-S-thumbnail.png',
										'label' => esc_html__( 'Collection S', 'wpmovielibrary' )
									),
									'collection-T' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-T-thumbnail.png',
										'label' => esc_html__( 'Collection T', 'wpmovielibrary' )
									),
									'collection-U' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-U-thumbnail.png',
										'label' => esc_html__( 'Collection U', 'wpmovielibrary' )
									),
									'collection-V' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-V-thumbnail.png',
										'label' => esc_html__( 'Collection V', 'wpmovielibrary' )
									),
									'collection-W' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-W-thumbnail.png',
										'label' => esc_html__( 'Collection W', 'wpmovielibrary' )
									),
									'collection-X' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-X-thumbnail.png',
										'label' => esc_html__( 'Collection X', 'wpmovielibrary' )
									),
									'collection-Y' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-Y-thumbnail.png',
										'label' => esc_html__( 'Collection Y', 'wpmovielibrary' )
									),
									'collection-Z' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-Z-thumbnail.png',
										'label' => esc_html__( 'Collection Z', 'wpmovielibrary' )
									),
									'collection-default' => array(
										'url'   => WPMOLY_URL . 'public/img/collection-default-thumbnail.png',
										'label' => esc_html__( 'Default Collection', 'wpmovielibrary' )
									),
								),
								'default' => 'unknown'
							)
						)
					)
				)
			) );

		} elseif ( 'genre' == $this->taxonomy ) {

			$this->add_manager( 'genre-meta', array(
				'label'    => esc_html__( 'Genre Meta', 'wpmovielibrary' ),
				'taxonomy' => 'genre',
				'sections' => array(
					'genre-identity' => array(
						'label' => esc_html__( 'Identity', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-tag',
						'settings' => array(
							'genre-tmdb-id' => array(
								'type'        => 'select',
								'section'     => 'genre-identity',
								'label'       => esc_html__( 'TMDb genre', 'wpmovielibrary' ),
								'description' => esc_html__( 'Select the TMDb genre corresponding to this genre, if any.', 'wpmovielibrary' ),
								'choices' => array(
									28    => __( 'Action', 'wpmovielibrary' ),
									12    => __( 'Adventure', 'wpmovielibrary' ),
									16    => __( 'Animation', 'wpmovielibrary' ),
									35    => __( 'Comedy', 'wpmovielibrary' ),
									80    => __( 'Crime', 'wpmovielibrary' ),
									99    => __( 'Documentary', 'wpmovielibrary' ),
									18    => __( 'Drama', 'wpmovielibrary' ),
									10751 => __( 'Family', 'wpmovielibrary' ),
									14    => __( 'Fantasy', 'wpmovielibrary' ),
									10769 => __( 'Foreign', 'wpmovielibrary' ),
									36    => __( 'History', 'wpmovielibrary' ),
									27    => __( 'Horror', 'wpmovielibrary' ),
									10402 => __( 'Music', 'wpmovielibrary' ),
									9648  => __( 'Mystery', 'wpmovielibrary' ),
									10749 => __( 'Romance', 'wpmovielibrary' ),
									878   => __( 'Science-fiction', 'wpmovielibrary' ),
									53    => __( 'Thriller', 'wpmovielibrary' ),
									10770 => __( 'TV Movie', 'wpmovielibrary' ),
									10752 => __( 'War', 'wpmovielibrary' ),
									37    => __( 'Western', 'wpmovielibrary' ),
								)
							)
						)
					),
					'genre-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'genre-thumbnail' => array(
								'type'        => 'radio-image',
								'section'     => 'genre-appearance',
								'label'       => esc_html__( 'Genre thumbnail' ),
								'description' => esc_html__( 'A default thumbnail for this genre.', 'wpmovielibrary' ),
								'attr' => array( 'class' => array( 'visible-labels' ) ),
								'choices' => array(
									'horror' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-horror-thumbnail.png',
										'label' => esc_html__( 'Horror', 'wpmovielibrary' )
									),
									'crime' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-crime-thumbnail.png',
										'label' => esc_html__( 'Crime', 'wpmovielibrary' )
									),
									'romance' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-romance-thumbnail.png',
										'label' => esc_html__( 'Romance', 'wpmovielibrary' )
									),
									'music' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-music-thumbnail.png',
										'label' => esc_html__( 'Music', 'wpmovielibrary' )
									),
									'family' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-family-thumbnail.png',
										'label' => esc_html__( 'Family', 'wpmovielibrary' )
									),
									'animation' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-animation-thumbnail.png',
										'label' => esc_html__( 'Animation', 'wpmovielibrary' )
									),
									'adventure' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-adventure-thumbnail.png',
										'label' => esc_html__( 'Adventure', 'wpmovielibrary' )
									),
									'science-fiction' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-science-fiction-thumbnail.png',
										'label' => esc_html__( 'Science Fiction', 'wpmovielibrary' )
									),
									'fantasy' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-fantasy-thumbnail.png',
										'label' => esc_html__( 'Fantasy', 'wpmovielibrary' )
									),
									'foreign' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-foreign-thumbnail.png',
										'label' => esc_html__( 'Foreign', 'wpmovielibrary' )
									),
									'war' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-war-thumbnail.png',
										'label' => esc_html__( 'War', 'wpmovielibrary' )
									),
									'tv-movie' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-tv-movie-thumbnail.png',
										'label' => esc_html__( 'TV Movie', 'wpmovielibrary' )
									),
									'western' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-western-thumbnail.png',
										'label' => esc_html__( 'Western', 'wpmovielibrary' )
									),
									'action' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-action-thumbnail.png',
										'label' => esc_html__( 'Action', 'wpmovielibrary' )
									),
									'comedy' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-comedy-thumbnail.png',
										'label' => esc_html__( 'Comedy', 'wpmovielibrary' )
									),
									'drama' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-drama-thumbnail.png',
										'label' => esc_html__( 'Drama', 'wpmovielibrary' )
									),
									'history' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-history-thumbnail.png',
										'label' => esc_html__( 'History', 'wpmovielibrary' )
									),
									'documentary' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-documentary-thumbnail.png',
										'label' => esc_html__( 'Documentary', 'wpmovielibrary' )
									),
									'mystery' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-mystery-thumbnail.png',
										'label' => esc_html__( 'Mystery', 'wpmovielibrary' )
									),
									'thriller' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-thriller-thumbnail.png',
										'label' => esc_html__( 'Thriller', 'wpmovielibrary' )
									),
									'unknown' => array(
										'url'   => WPMOLY_URL . 'public/img/genre-unknown-thumbnail.png',
										'label' => esc_html__( 'Unknown', 'wpmovielibrary' )
									),
								),
								'default' => 'unknown'
							)
						)
					)
				)
			) );

		}

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
