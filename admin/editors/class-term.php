<?php
/**
 * Define the Term Editor class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\admin\editors;

/**
 * Provide a tool to manage custom terms.
 *
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Term extends Editor {

	/**
	 * Current Term ID.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $term_id;

	/**
	 * Current Term Taxonomy.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $taxonomy;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
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
	}

	/**
	 * Define meta managers.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function add_managers() {

		if ( 'actor' == $this->taxonomy ) {

			$this->add_manager( 'actor-meta', array(
				'label'    => esc_html__( 'Actor Meta', 'wpmovielibrary' ),
				'taxonomy' => 'actor',
				'sections' => array(
					/*'actor-identity' => array(
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
							),
						),
					),*/
					'actor-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'actor-custom-picture' => array(
								'type'        => 'image',
								'section'     => 'actor-appearance',
								'label'       => esc_html__( 'Custom Actor Picture', 'wpmovielibrary' ),
								'description' => esc_html__( 'Upload a custom picture for this actor.', 'wpmovielibrary' ),
								'size'        => 'thumbnail',
							),
							'actor-picture' => array(
								'type'        => 'radio-image',
								'section'     => 'actor-appearance',
								'label'       => esc_html__( 'Actor picture', 'wpmovielibrary' ),
								'description' => esc_html__( 'A default picture for this actor.', 'wpmovielibrary' ),
								'choices' => array(
									'neutral' => array(
										'label' => esc_html__( 'Neutral', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/actor-neutral-thumbnail.png',
									),
									'female' => array(
										'label' => esc_html__( 'Female', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/actor-female-thumbnail.png',
									),
									'male' => array(
										'label' => esc_html__( 'Male', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/actor-male-thumbnail.png',
									),
								),
								'default' => 'neutral',
							),
						),
					),
				),
			) );

		} elseif ( 'collection' == $this->taxonomy ) {

			$this->add_manager( 'collection-meta', array(
				'label'    => esc_html__( 'Genre Meta', 'wpmovielibrary' ),
				'taxonomy' => 'collection',
				'sections' => array(
					/*'collection-identity' => array(
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
						),
					),*/
					'collection-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'collection-custom-thumbnail' => array(
								'type'        => 'image',
								'section'     => 'collection-appearance',
								'label'       => esc_html__( 'Custom Collection Thumbnail', 'wpmovielibrary' ),
								'description' => esc_html__( 'Upload a custom thumbnail for this collection.', 'wpmovielibrary' ),
								'size'        => 'thumbnail',
							),
							'collection-thumbnail' => array(
								'type'        => 'radio-image',
								'section'     => 'collection-appearance',
								'label'       => esc_html__( 'Collection thumbnail' ),
								'description' => esc_html__( 'A default thumbnail for this collection.', 'wpmovielibrary' ),
								'attr' => array(
									'class' => array( 'visible-labels' ),
								),
								'choices' => array(
									'collection-A' => array(
										'label' => esc_html__( 'Collection A', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-A-thumbnail.png',
									),
									'collection-B' => array(
										'label' => esc_html__( 'Collection B', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-B-thumbnail.png',
									),
									'collection-C' => array(
										'label' => esc_html__( 'Collection C', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-C-thumbnail.png',
									),
									'collection-D' => array(
										'label' => esc_html__( 'Collection D', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-D-thumbnail.png',
									),
									'collection-E' => array(
										'label' => esc_html__( 'Collection E', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-E-thumbnail.png',
									),
									'collection-F' => array(
										'label' => esc_html__( 'Collection F', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-F-thumbnail.png',
									),
									'collection-G' => array(
										'label' => esc_html__( 'Collection G', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-G-thumbnail.png',
									),
									'collection-H' => array(
										'label' => esc_html__( 'Collection H', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-H-thumbnail.png',
									),
									'collection-I' => array(
										'label' => esc_html__( 'Collection I', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-I-thumbnail.png',
									),
									'collection-J' => array(
										'label' => esc_html__( 'Collection J', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-J-thumbnail.png',
									),
									'collection-K' => array(
										'label' => esc_html__( 'Collection K', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-K-thumbnail.png',
									),
									'collection-L' => array(
										'label' => esc_html__( 'Collection L', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-L-thumbnail.png',
									),
									'collection-M' => array(
										'label' => esc_html__( 'Collection M', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-M-thumbnail.png',
									),
									'collection-N' => array(
										'label' => esc_html__( 'Collection N', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-N-thumbnail.png',
									),
									'collection-O' => array(
										'label' => esc_html__( 'Collection O', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-O-thumbnail.png',
									),
									'collection-P' => array(
										'label' => esc_html__( 'Collection P', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-P-thumbnail.png',
									),
									'collection-Q' => array(
										'label' => esc_html__( 'Collection Q', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-Q-thumbnail.png',
									),
									'collection-R' => array(
										'label' => esc_html__( 'Collection R', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-R-thumbnail.png',
									),
									'collection-S' => array(
										'label' => esc_html__( 'Collection S', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-S-thumbnail.png',
									),
									'collection-T' => array(
										'label' => esc_html__( 'Collection T', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-T-thumbnail.png',
									),
									'collection-U' => array(
										'label' => esc_html__( 'Collection U', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-U-thumbnail.png',
									),
									'collection-V' => array(
										'label' => esc_html__( 'Collection V', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-V-thumbnail.png',
									),
									'collection-W' => array(
										'label' => esc_html__( 'Collection W', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-W-thumbnail.png',
									),
									'collection-X' => array(
										'label' => esc_html__( 'Collection X', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-X-thumbnail.png',
									),
									'collection-Y' => array(
										'label' => esc_html__( 'Collection Y', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-Y-thumbnail.png',
									),
									'collection-Z' => array(
										'label' => esc_html__( 'Collection Z', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-Z-thumbnail.png',
									),
									'collection-default' => array(
										'label' => esc_html__( 'Default Collection', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/collection-default-thumbnail.png',
									),
								),
								'default' => 'unknown',
							),
						),
					),
				),
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
								// @TODO Use TMDb to update the list.
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
								),
							),
						),
					),
					'genre-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'genre-custom-thumbnail' => array(
								'type'        => 'image',
								'section'     => 'genre-appearance',
								'label'       => esc_html__( 'Custom Genre Thumbnail', 'wpmovielibrary' ),
								'description' => esc_html__( 'Upload a custom thumbnail for this genre.', 'wpmovielibrary' ),
								'size'        => 'thumbnail',
							),
							'genre-thumbnail' => array(
								'type'        => 'radio-image',
								'section'     => 'genre-appearance',
								'label'       => esc_html__( 'Genre thumbnail', 'wpmovielibrary' ),
								'description' => esc_html__( 'A default thumbnail for this genre.', 'wpmovielibrary' ),
								'attr' => array(
									'class' => array( 'visible-labels' ),
								),
								'choices' => array(
									'horror' => array(
										'label' => esc_html__( 'Horror', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-horror-thumbnail.png',
									),
									'crime' => array(
										'label' => esc_html__( 'Crime', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-crime-thumbnail.png',
									),
									'romance' => array(
										'label' => esc_html__( 'Romance', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-romance-thumbnail.png',
									),
									'music' => array(
										'label' => esc_html__( 'Music', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-music-thumbnail.png',
									),
									'family' => array(
										'label' => esc_html__( 'Family', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-family-thumbnail.png',
									),
									'animation' => array(
										'label' => esc_html__( 'Animation', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-animation-thumbnail.png',
									),
									'adventure' => array(
										'label' => esc_html__( 'Adventure', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-adventure-thumbnail.png',
									),
									'science-fiction' => array(
										'label' => esc_html__( 'Science Fiction', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-science-fiction-thumbnail.png',
									),
									'fantasy' => array(
										'label' => esc_html__( 'Fantasy', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-fantasy-thumbnail.png',
									),
									'foreign' => array(
										'label' => esc_html__( 'Foreign', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-foreign-thumbnail.png',
									),
									'war' => array(
										'label' => esc_html__( 'War', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-war-thumbnail.png',
									),
									'tv-movie' => array(
										'label' => esc_html__( 'TV Movie', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-tv-movie-thumbnail.png',
									),
									'western' => array(
										'label' => esc_html__( 'Western', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-western-thumbnail.png',
									),
									'action' => array(
										'label' => esc_html__( 'Action', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-action-thumbnail.png',
									),
									'comedy' => array(
										'label' => esc_html__( 'Comedy', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-comedy-thumbnail.png',
									),
									'drama' => array(
										'label' => esc_html__( 'Drama', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-drama-thumbnail.png',
									),
									'history' => array(
										'label' => esc_html__( 'History', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-history-thumbnail.png',
									),
									'documentary' => array(
										'label' => esc_html__( 'Documentary', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-documentary-thumbnail.png',
									),
									'mystery' => array(
										'label' => esc_html__( 'Mystery', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-mystery-thumbnail.png',
									),
									'thriller' => array(
										'label' => esc_html__( 'Thriller', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-thriller-thumbnail.png',
									),
									'unknown' => array(
										'label' => esc_html__( 'Unknown', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'public/assets/img/genre-unknown-thumbnail.png',
									),
								),
								'default' => 'unknown',
							),
						),
					),
				),
			) );

		} // End if().
	}

	/**
	 * Load frameworks if needed.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function load_meta_frameworks() {

		$screen = get_current_screen();

		// Bail if not our post type.
		if ( 'term' !== $screen->base || ! in_array( $screen->taxonomy, array( 'actor', 'collection', 'genre' ) ) ) {
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
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $location The destination URL.
	 * @param object $taxonomy The taxonomy object.
	 *
	 * @return string
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
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param object $term     Current taxonomy term object.
	 * @param string $taxonomy Current $taxonomy slug.
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

	/**
	 * Define metaboxes.
	 *
	 * Not available in edit-term screen.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function add_metaboxes() {}

}
