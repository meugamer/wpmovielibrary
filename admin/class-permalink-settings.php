<?php
/**
 * Define the Permalink Settings class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Admin;

use wpmoly\Core\AdminTemplate as Template;

/**
 * Handle the plugin's URL rewriting settings.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class PermalinkSettings {

	/**
	 * Default permalinks slugs.
	 * 
	 * @var    array
	 */
	private $slugs;

	/**
	 * Default permalink settings.
	 * 
	 * @var    array
	 */
	private $defaults;

	/**
	 * Existing permalinks settings.
	 * 
	 * @var    array
	 */
	private $permalinks;

	/**
	 * Singleton.
	 *
	 * @var    Rewrite
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	private function __construct() {

		$slugs = array(
			'movie'       => _x( 'movie', 'slug', 'wpmovielibrary' ),
			'actor'       => _x( 'actor', 'slug', 'wpmovielibrary' ),
			'collection'  => _x( 'collection', 'slug', 'wpmovielibrary' ),
			'genre'       => _x( 'genre', 'slug', 'wpmovielibrary' ),
			'movies'      => _x( 'movies', 'slug', 'wpmovielibrary' ),
			'actors'      => _x( 'actors', 'slug', 'wpmovielibrary' ),
			'collections' => _x( 'collections', 'slug', 'wpmovielibrary' ),
			'genres'      => _x( 'genres', 'slug', 'wpmovielibrary' )
		);

		/**
		 * Default permalink slugs.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $slugs
		 */
		$this->slugs = apply_filters( 'wpmoly/filter/permalinks/slugs/defaults', $slugs );

		$defaults = array(
			'movie'       => '/' . $this->slugs['movie'] . '/%postname%/',
			'actor'       => '/' . $this->slugs['actor'] . '/%actor%/',
			'collection'  => '/' . $this->slugs['collection'] . '/%collection%/',
			'genre'       => '/' . $this->slugs['genre'] . '/%genre%/',
			'movies'      => '/' . $this->slugs['movies'] . '/',
			'actors'      => '/' . $this->slugs['actors'] . '/',
			'collections' => '/' . $this->slugs['collections'] . '/',
			'genres'      => '/' . $this->slugs['genres'] . '/'
		);

		/**
		 * Default permalink structures.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $defaults
		 */
		$this->defaults = apply_filters( 'wpmoly/filter/permalinks/defaults', $defaults );

		$this->permalinks = $this->get_permalinks();

		$settings = array(
			'movie-permalinks' => array(
				'title'  => __( 'Movies', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-movie',
				'fields' => array(
					'movie' => array(
						'type' => 'radio',
						'title' => __( 'Movie Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single movie pages. <a href="https://codex.wordpress.org/Using_Permalinks">Standard tags</a> are supported along with specific movie tags.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/interstellar/'
							),
							'title_year' => array(
								'label'  => __( 'Title and Year', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%year%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/2016/interstellar/'
							),
							'title_month' => array(
								'label'  => __( 'Title and Month', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%year%/%monthnum%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/2016/08/interstellar/'
							),
							'title_release_year' => array(
								'label'  => __( 'Title and Release Year', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%release_year%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/2014/interstellar/'
							),
							'title_release_month' => array(
								'label'  => __( 'Title and Release Month', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%release_year%/%release_monthnum%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/2014/10/interstellar/'
							),
							'imdb_id' => array(
								'label'  => __( 'IMDb ID', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%imdb_id%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/tt0816692/interstellar/'
							),
							'tmdb_id' => array(
								'label'  => __( 'TMDb ID', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movie'] . '/%tmdb_id%/',
								'description' => home_url() . '/' . $this->slugs['movie'] . '/157336/interstellar/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . trim( $this->permalinks['movies'], '/' ) . '/',
								'description' => home_url() . '/' . trim( $this->permalinks['movies'], '/' ) . '/interstellar/'
							)
						),
						'default' => 'archive',
						'custom'  => false
					),
					'movies' => array(
						'type' => 'radio',
						'title' => __( 'Movie Archives Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for movies archives pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['movies'] . '/',
								'description' => home_url() . '/' . $this->slugs['movies'] . '/'
							)
						),
						'default' => 'simple',
						'custom'  => true
					)
				)
			),
			'actor-permalinks' => array(
				'title'  => __( 'Actors', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-actor',
				'fields' => array(
					'actor' => array(
						'type' => 'radio',
						'title' => __( 'Actor Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single actor pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['actor'] . '/%actor%/',
								'description' => home_url() . '/' . $this->slugs['actor'] . '/matthew-mcconaughey/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . trim( $this->permalinks['actors'], '/' ) . '/%actor%/',
								'description' => home_url() . '/' . trim( $this->permalinks['actors'], '/' ) . '/matthew-mcconaughey/'
							)
						),
						'default' => 'simple',
						'custom'  => false
					),
					'actors' => array(
						'type' => 'radio',
						'title' => __( 'Actors Archives Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for actors archives pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['actors'] . '/',
								'description' => home_url() . '/' . $this->slugs['actors'] . '/'
							)
						),
						'default' => 'simple',
						'custom'  => true
					)
				)
			),
			'genre-permalinks' => array(
				'title'  => __( 'Genres', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-tags',
				'fields' => array(
					'genre' => array(
						'type' => 'radio',
						'title' => __( 'Genre Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single genre pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['genre'] . '/%genre%/',
								'description' => home_url() . '/' . $this->slugs['genre'] . '/science-fiction/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . trim( $this->permalinks['genres'], '/' ) . '/%genre%/',
								'description' => home_url() . '/' . trim( $this->permalinks['genres'], '/' ) . '/science-fiction/'
							)
						),
						'default' => 'simple',
						'custom'  => false
					),
					'genres' => array(
						'type' => 'radio',
						'title' => __( 'Genres Archives Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for genres archives pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['genres'] . '/',
								'description' => home_url() . '/' . $this->slugs['genres'] . '/'
							)
						),
						'default' => 'simple',
						'custom'  => true
					)
				)
			),
			'collection-permalinks' => array(
				'title'  => __( 'Collections', 'wpmovielibrary' ),
				'icon'   => 'wpmolicon icon-folder',
				'fields' => array(
					'collection' => array(
						'type' => 'radio',
						'title' => __( 'Collection Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for single collection pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['collection'] . '/%collection%/',
								'description' => home_url() . '/' . $this->slugs['collection'] . '/christopher-nolan/'
							),
							'archive' => array(
								'label'  => __( 'Archive base', 'wpmovielibrary' ),
								'value'  => '/' . trim( $this->permalinks['collections'], '/' ) . '/%collection%/',
								'description' => home_url() . '/' . trim( $this->permalinks['collections'], '/' ) . '/christopher-nolan/'
							)
						),
						'default' => 'simple',
						'custom'  => false
					),
					'collections' => array(
						'type' => 'radio',
						'title' => __( 'Collections Archives Permalinks', 'wpmovielibrary' ),
						'description' => __( 'Permalink structure for collections archives pages.', 'wpmovielibrary' ),
						'choices' => array(
							'simple' => array(
								'label'  => __( 'Simple', 'wpmovielibrary' ),
								'value'  => '/' . $this->slugs['collections'] . '/',
								'description' => home_url() . '/' . $this->slugs['collections'] . '/'
							)
						),
						'default' => 'simple',
						'custom'  => true
					)
				)
			)
		);

		if ( ! has_actor_archives_page() ) {
			$settings['actor-permalinks']['fields']['actors']['disabled'] = true;
			$settings['actor-permalinks']['fields']['actors']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for actors yet, which makes this setting meaningless. Define an archive page by <a href="%s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

		if ( ! has_genre_archives_page() ) {
			$settings['genre-permalinks']['fields']['genres']['disabled'] = true;
			$settings['genre-permalinks']['fields']['genres']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for genres yet, which makes this setting meaningless. Define an archive page by <a href="%s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

		if ( ! has_collection_archives_page() ) {
			$settings['collection-permalinks']['fields']['collections']['disabled'] = true;
			$settings['collection-permalinks']['fields']['collections']['description'] .= '<p><em>' . sprintf( __( 'You don’t have any archive page set for collections yet, which makes this setting meaningless. Define an archive page by <a href="%s">creating a standard WordPress page</a> and set it as archive in the relevant Metabox. <a href="%s">Learn more about archive pages</a>.', 'wpmovielibrary' ), esc_url( admin_url( 'post-new.php?post_type=page' ) ), esc_url( '#' ) ) . '</em></p>';
		}

		/**
		 * Filter default permalinks settings.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $settings
		 */
		$this->settings = apply_filters( 'wpmoly/filter/permalinks/settings', $settings );
	}

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
	 * Add a new block to the Permalink settings option page.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register() {

		add_settings_section( 'wpmoly-permalink', __( 'Movie Library Permalinks', 'wpmovielibrary' ), array( $this, 'register_sections' ), 'permalink' );
	}

	/**
	 * Display a custom metabox-ish permalink settings block.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_sections() {

		$enabled = ! empty( get_option( 'rewrite_rules' ) );

		$metabox = new Template( 'permalink-settings.php' );
		$metabox->set_data( array(
			'settings'   => $this->settings,
			'permalinks' => $this->permalinks,
			'enabled'    => $enabled
		) );

		$metabox->render();
	}

	/**
	 * Save custom permalinks.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function update() {

		global $pagenow;

		if ( 'options-permalink.php' !== $pagenow ) {
			return false;
		}

		if ( empty( $_POST['wpmoly_permalinks'] ) ) {
			return false;
		}

		$permalinks = array();
		$new_permalinks = $_POST['wpmoly_permalinks'];

		foreach ( $this->defaults as $name => $permalink ) {
			if ( ! empty( $new_permalinks[ $name ] ) ) {
				if ( 'custom' == $new_permalinks[ $name ] && ! empty( $new_permalinks["custom_{$name}"] ) ) {
					$permalink = $new_permalinks["custom_{$name}"];
				} else {
					$permalink = $new_permalinks[ $name ];
				}
			}

			$permalinks[ $name ] = trailingslashit( $permalink );
		}

		$this->permalinks = $permalinks;
		$this->set_permalinks();
	}

	/**
	 * Retrieve permalink settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_permalinks() {

		$permalinks = array();
		if ( is_null( $this->permalinks ) ) {
			$permalinks = get_option( 'wpmoly_permalinks' );
		}

		if ( empty( $permalinks ) ) {
			$permalinks = array();
		}

		return $this->permalinks = wp_parse_args( $permalinks, $this->defaults );
	}

	/**
	 * Save permalink settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function set_permalinks() {

		/**
		 * Filter the permalink settings before saving.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $permalinks
		 */
		$permalinks = apply_filters( 'wpmoly/filter/permalinks', $this->permalinks );

		update_option( 'wpmoly_permalinks', $permalinks );
	}

}
