<?php
/**
 * Define the content registrator.
 *
 * Register required Custom Post Types, Custom Taxonomies.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * Register the 'movie' Custom Post Type along with the 'import' post statuses.
 *
 * Also register 'collection', 'actor' and 'genre' Custom Taxonomies.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Registrar {

	/**
	 * Singleton.
	 *
	 * @var    Registrar
	 */
	private static $instance = null;

	/**
	 * Default Custom Post Types.
	 *
	 * @var    array
	 */
	private $post_types = array();

	/**
	 * Default Custom Post Statuses.
	 *
	 * @var    array
	 */
	private $post_statuses = array();

	/**
	 * Default Custom Post Meta.
	 *
	 * @var    array
	 */
	private $post_meta = array();

	/**
	 * Default Custom Taxonomies.
	 *
	 * @var    array
	 */
	private $taxonomies = array();

	/**
	 * Default Custom Taxonomies Term Meta.
	 *
	 * @var    array
	 */
	private $term_meta = array();

	/**
	 * Load permalinks settings.
	 *
	 * @since    3.0
	 */
	public function __construct() {

		$this->permalinks = get_option( 'wpmoly_permalinks', array() );
		$this->pages = get_option( '_wpmoly_archive_pages', array() );
	}

	/**
	 * Singleton.
	 *
	 * @since    3.0
	 *
	 * @return   Options
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register Custom Post Types.
	 *
	 * @since    3.0
	 */
	public function register_post_types() {

		$movies = array_search( 'movie', $this->pages );
		if ( ! $movies ) {
			if ( ! empty( $this->permalinks['movies'] ) ) {
				$movie_archives = trim( $this->permalinks['movies'], '/' );
			} else {
				$movie_archives = 'movies';
			}
		} else {
			$movie_archives = str_replace( home_url(), '', get_permalink( $movies ) );
		}

		$post_types = array(
			array(
				'slug' => 'movie',
				'args' => array(
					'labels' => array(
						'name'               => __( 'Movies', 'wpmovielibrary' ),
						'singular_name'      => __( 'Movie', 'wpmovielibrary' ),
						'add_new'            => __( 'Add New', 'wpmovielibrary' ),
						'add_new_item'       => __( 'Add New Movie', 'wpmovielibrary' ),
						'edit_item'          => __( 'Edit Movie', 'wpmovielibrary' ),
						'new_item'           => __( 'New Movie', 'wpmovielibrary' ),
						'all_items'          => __( 'All Movies', 'wpmovielibrary' ),
						'view_item'          => __( 'View Movie', 'wpmovielibrary' ),
						'search_items'       => __( 'Search Movies', 'wpmovielibrary' ),
						'not_found'          => __( 'No movies found', 'wpmovielibrary' ),
						'not_found_in_trash' => __( 'No movies found in Trash', 'wpmovielibrary' ),
						'parent_item_colon'  => '',
						'menu_name'          => __( 'Movie Library', 'wpmovielibrary' ),
					),
					'rewrite' => array(
						'slug' => ! empty( $this->permalinks['movie'] ) ? trim( $this->permalinks['movie'], '/' ) : 'movies',
					),
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_rest'       => true,
					'rest_base'          => 'movies',
					'rest_controller_class' => '\wpmoly\Rest\Movies_Controller',
					'show_in_menu'       => 'wpmovielibrary',
					'has_archive'        => $movie_archives,
					'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
					'menu_position'      => 2,
					'menu_icon'          => 'dashicons-wpmoly',
				),
			),
			array(
				'slug' => 'grid',
				'args' => array(
					'labels' => array(
						'name'               => __( 'Grids', 'wpmovielibrary' ),
						'singular_name'      => __( 'Grid', 'wpmovielibrary' ),
						'add_new'            => __( 'Add New', 'wpmovielibrary' ),
						'add_new_item'       => __( 'Add New Grid', 'wpmovielibrary' ),
						'edit_item'          => __( 'Edit Grid', 'wpmovielibrary' ),
						'new_item'           => __( 'New Grid', 'wpmovielibrary' ),
						'all_items'          => __( 'Grids', 'wpmovielibrary' ),
						'view_item'          => __( 'View Grid', 'wpmovielibrary' ),
						'search_items'       => __( 'Search Grids', 'wpmovielibrary' ),
						'not_found'          => __( 'No grids found', 'wpmovielibrary' ),
						'not_found_in_trash' => __( 'No grids found in Trash', 'wpmovielibrary' ),
						'parent_item_colon'  => '',
						'menu_name'          => __( 'Grids', 'wpmovielibrary' ),
					),
					'rewrite'            => false,
					'public'             => false,
					'publicly_queryable' => false,
					'show_ui'            => true,
					'show_in_rest'       => true,
					'rest_controller_class' => '\wpmoly\Rest\Grids_Controller',
					'show_in_menu'       => 'wpmovielibrary',
					'has_archive'        => false,
					'supports'           => array( 'title', 'custom-fields' ),
				),
			),
		);

		/**
		 * Filter the Custom Post Types parameters prior to registration.
		 *
		 * @since    3.0
		 *
		 * @param    array    $post_types Post Types list
		 */
		$this->post_types = apply_filters( 'wpmoly/filter/post_types', $post_types );

		foreach ( $this->post_types as $post_type ) {

			/**
			 * Filter the Custom Post Type parameters prior to registration.
			 *
			 * @since    3.0
			 *
			 * @param    array    $args Post Type args
			 */
			$args = apply_filters( "wpmoly/filter/post_type/{$post_type['slug']}", $post_type['args'] );

			$args = array_merge( array(
				'labels'             => array(),
				'rewrite'            => true,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'show_in_menu'       => true,
				'has_archive'        => true,
				'menu_position'      => null,
				'menu_icon'          => null,
				'taxonomies'         => array(),
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			), $args );

			register_post_type( $post_type['slug'], $args );
		}
	}

	/**
	 * Register Custom Post Statuses.
	 *
	 * @since    3.0
	 */
	public function register_post_statuses() {

		$post_statuses = array(
			array(
				'slug' => 'import-draft',
				'args' => array(
					'label'       => _x( 'Imported Draft', 'wpmovielibrary' ),
					'label_count' => _n_noop( 'Imported Draft <span class="count">(%s)</span>', 'Imported Draft <span class="count">(%s)</span>', 'wpmovielibrary' ),
				),
			),
			array(
				'slug' => 'import-queued',
				'args' => array(
					'label'       => _x( 'Queued Movie', 'wpmovielibrary' ),
					'label_count' => _n_noop( 'Queued Movie <span class="count">(%s)</span>', 'Queued Movies <span class="count">(%s)</span>', 'wpmovielibrary' ),
				),
			),
		);

		/**
		 * Filter the Custom Post Statuses parameters prior to registration.
		 *
		 * @since    3.0
		 *
		 * @param    array    $post_statuses Post Statuses list
		 */
		$this->post_statuses = apply_filters( 'wpmoly/filter/post_statuses', $post_statuses );

		foreach ( $this->post_statuses as $post_status ) {

			/**
			 * Filter the Custom Post Status parameters prior to registration.
			 *
			 * @since    3.0
			 *
			 * @param    array    $args Post Status args
			 */
			$args = apply_filters( "wpmoly/filter/post_status/{$post_status['slug']}", $post_status['args'] );
			$args = array_merge( array(
				'label'                     => false,
				'label_count'               => false,
				'public'                    => false,
				'internal'                  => true,
				'private'                   => true,
				'publicly_queryable'        => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
			), $args );

			register_post_status( $post_status['slug'], $args );
		}
	}

	/**
	 * Register Custom Post Meta.
	 *
	 * @since    3.0
	 */
	public function register_post_meta() {

		$post_meta = array(
			'movie' => array(
				'tmdb_id' => array(
					'type'         => 'integer',
					'description'  => __( 'TheMovieDb.org movie ID', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'TMDb ID', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_tmdb_id',
					),
				),
				'title' => array(
					'type'         => 'string',
					'description'  => __( 'Title', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Title', 'wpmovielibrary' ),
					),
				),
				'original_title' => array(
					'type'         => 'string',
					'description'  => __( 'Original title for the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Original Title', 'wpmovielibrary' ),
					),
				),
				'tagline' => array(
					'type'         => 'string',
					'description'  => __( 'Short movie tagline', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Tagline', 'wpmovielibrary' ),
					),
				),
				'overview' => array(
					'type'         => 'string',
					'description'  => __( 'Short movie overview', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Overview', 'wpmovielibrary' ),
					),
				),
				'release_date' => array(
					'type'         => 'string',
					'description'  => __( 'Date the movie was initially released', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Release Date', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_release_date',
					),
				),
				'local_release_date' => array(
					'type'         => 'string',
					'description'  => __( 'Date the movie was localy released based on your settings', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Local Release Date', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_local_release_date',
					),
				),
				'runtime' => array(
					'type'         => 'string',
					'description'  => __( 'Total movie runtime', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Runtime', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_runtime',
					),
				),
				'production_companies' => array(
					'type'         => 'string',
					'description'  => __( 'List of companies who produced the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Production Companies', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_production',
					),
				),
				'production_countries' => array(
					'type'         => 'string',
					'description'  => __( 'List of countries where the movie was produced', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Production Countries', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_countries',
					),
				),
				'spoken_languages' => array(
					'type'         => 'string',
					'description'  => __( 'List of languages spoken in the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Spoken Languages', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_spoken_languages',
					),
				),
				'genres' => array(
					'type'         => 'string',
					'description'  => __( 'List of genres for the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Genres', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_genres',
					),
				),
				'director' => array(
					'type'         => 'string',
					'description'  => __( 'List of directors for the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Director', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_director',
					),
				),
				'producer' => array(
					'type'         => 'string',
					'description'  => __( 'List of producers for the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Producer', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_producer',
					),
				),
				'cast' => array(
					'type'         => 'string',
					'description'  => __( 'List of actors starring in the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Actors', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_cast',
					),
				),
				'photography' => array(
					'type'         => 'string',
					'description'  => __( 'List of directors of photography for the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Director of photography', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_photography',
					),
				),
				'composer' => array(
					'type'         => 'string',
					'description'  => __( 'List of original music composers for the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Composer', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_composer',
					),
				),
				'author' => array(
					'type'         => 'string',
					'description'  => __( 'List of authors for the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Author', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_author',
					),
				),
				'writer' => array(
					'type'         => 'string',
					'description'  => __( 'List of writers for the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Writer', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_writer',
					),
				),
				'certification' => array(
					'type'         => 'string',
					'description'  => __( 'Movie certification', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Certification', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_certification',
					),
				),
				'budget' => array(
					'type'         => 'string',
					'description'  => __( 'Movie budget', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Budget', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_budget',
					),
				),
				'revenue' => array(
					'type'         => 'string',
					'description'  => __( 'Movie revenue', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Revenue', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_revenue',
					),
				),
				'imdb_id' => array(
					'type'         => 'string',
					'description'  => __( 'Internet Movie Database movie ID', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'IMDb ID', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_imdb_id',
					),
				),
				'adult' => array(
					'type'         => 'string',
					'description'  => __( 'Separate adult-only movies from all-audience movies', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Adult-only', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_adult',
					),
				),
				'homepage' => array(
					'type'         => 'string',
					'description'  => __( 'Official movie Website', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Homepage', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_homepage',
					),
				),
				'status' => array(
					'type'         => 'string',
					'description'  => __( 'Current status of your copy of the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Status', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_status',
					),
				),
				'media' => array(
					'type'         => 'string',
					'description'  => __( 'List of medias', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Media', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_media',
					),
				),
				'rating' => array(
					'type'         => 'string',
					'description'  => __( 'Your own rating of the movie', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Rating', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_rating',
					),
				),
				'language' => array(
					'type'         => 'string',
					'description'  => __( 'List of languages', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Language', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_language',
					),
				),
				'subtitles' => array(
					'type'         => 'string',
					'description'  => __( 'List of subtitles', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Subtitles', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_subtitles',
					),
				),
				'format' => array(
					'type'         => 'string',
					'description'  => __( 'List of formats', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Format', 'wpmovielibrary' ),
						'prepare_callback' => 'get_formatted_movie_format',
					),
				),
			),
			'grid' => array(
				'type' => array(
					'type'         => 'string',
					'description'  => __( 'Grid type', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Type', 'wpmovielibrary' ),
					),
				),
				'mode' => array(
					'type'         => 'string',
					'description'  => __( 'Grid mode', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => __( 'Mode', 'wpmovielibrary' ),
					),
				),
				'theme' => array(
					'type'         => 'string',
					'description'  => esc_html__( 'Grid theme', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label' => esc_html__( 'Theme', 'wpmovielibrary' ),
					),
				),
				'preset' => array(
					'type'        => 'string',
					'show_in_rest' => array(
						'label'       => esc_html__( 'Grid preset', 'wpmovielibrary' ),
					),
					'description' => esc_html__( 'Select a preset to apply to the grid. Presets override any filters and ordering settings you might define, be sure to select "Custom" for those settings to be used.', 'wpmovielibrary' ),
					'default'     => 'custom',
				),
				'enable-pagination' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Allow visitors to browse through the grid. Is Ajax browsing is enabled, pagination will use Ajax to dynamically load new pages instead of reloading. Default is enabled.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Enable Pagination', 'wpmovielibrary' ),
					),
					'sanitize_callback' => '_is_bool',
					'default'  => 1,
				),
				'columns' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Number of columns', 'wpmovielibrary' ),
					),
					'default'  => 5,
				),
				'rows' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Number of rows', 'wpmovielibrary' ),
					),
					'default'  => 4,
				),
				'column-width' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Ideal width for posters. Grid columns will never exceed that width. Default is 160.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Movie Poster ideal width', 'wpmovielibrary' ),
					),
					'default'  => 160,
				),
				'row-height' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Ideal height for posters. Grid rows will never exceed that height. Tip: that value should be equal to ideal width times 1.5. Default is 240.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Movie Poster ideal height', 'wpmovielibrary' ),
					),
					'default'  => 240,
				),
				'list-columns' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Number of columns for the grid in list mode. Default is 3.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Number of list columns', 'wpmovielibrary' ),
					),
					'default'  => 3,
				),
				'list-column-width' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Ideal width for columns in list mode. Default is 240.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Ideal column width', 'wpmovielibrary' ),
					),
					'default'  => 240,
				),
				'list-rows' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Number of rows for the grid in list mode. Default is 8.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Number of list rows', 'wpmovielibrary' ),
					),
					'default'  => 8,
				),
				'settings-control' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Visitors will be able to change some settings to browse the grid differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Enable user settings', 'wpmovielibrary' ),
					),
					'sanitize_callback' => '_is_bool',
					'default'  => 1,
				),
				'custom-letter' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Allow visitors to filter the grid by letters. Default is enabled.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Enable letter filtering', 'wpmovielibrary' ),
					),
					'sanitize_callback' => '_is_bool',
					'default'  => 1,
				),
				'custom-order' => array(
					'type' => 'integer',
					'description' => esc_html__( 'Allow visitors to change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
					'show_in_rest' => array(
						'label'    => esc_html__( 'Enable custom ordering', 'wpmovielibrary' ),
					),
					'sanitize_callback' => '_is_bool',
					'default'  => 1,
				),
			),
		);

		/**
		 * Filter the Custom Post Meta prior to registration.
		 *
		 * @since    3.0
		 *
		 * @param    array    $post_meta Post Meta list
		 */
		$this->post_meta = apply_filters( 'wpmoly/filter/post_meta', $post_meta );

		foreach ( $this->post_meta as $post_type => $post_meta ) {

			foreach ( $post_meta as $slug => $params ) {

				/**
				 * Filter meta_key.
				 *
				 * Add a '_wpmoly_{$post_type}_' prefix to meta_keys.
				 *
				 * @since    3.0
				 *
				 * @param    string    $slug Post meta slug.
				 */
				$meta_key = apply_filters( "wpmoly/filter/{$post_type}/meta/key", $slug );

				$args = wp_parse_args( $params, array(
					'type'              => 'string',
					'description'       => '',
					'default'           => '',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => null,
				) );

				if ( is_array( $args['show_in_rest'] ) ) {

					$schema = array();
					if ( isset( $args['show_in_rest']['schema'] ) ) {
						$schema = $args['show_in_rest']['schema'];
					} else {
						$schema = array(
							'type'        => isset( $args['type'] ) ? $args['type'] : '',
							'description' => isset( $args['description'] ) ? $args['description'] : '',
							'default'     => isset( $args['default'] ) ? $args['default'] : '',
						);
					}

					$schema = wp_parse_args( $schema, array(
						'type'        => '',
						'description' => '',
						'default'     => '',
					) );

					$args['show_in_rest']['schema'] = $schema;
				}

				register_meta( 'post', $meta_key, $args );
			} // End foreach().
		} // End foreach().
	}

	/**
	 * Register Custom Taxonomies.
	 *
	 * @since    3.0
	 */
	public function register_taxonomies() {

		$taxonomies = array(
			array(
				'slug'  => 'collection',
				'posts' => array( 'movie' ),
				'args'  => array(
					'labels' => array(
						'name'                       => __( 'Collections', 'wpmovielibrary' ),
						'add_new_item'               => __( 'New Collection', 'wpmovielibrary' ),
						'search_items'               => __( 'Search Collections', 'wpmovielibrary' ),
						'popular_items'              => __( 'Popular Collections', 'wpmovielibrary' ),
						'all_items'                  => __( 'All Collections', 'wpmovielibrary' ),
						'parent_item'                => __( 'Parent Collection', 'wpmovielibrary' ),
						'parent_item_colon'          => __( 'Parent Collection:', 'wpmovielibrary' ),
						'edit_item'                  => __( 'Edit Collection', 'wpmovielibrary' ),
						'view_item'                  => __( 'View Collection', 'wpmovielibrary' ),
						'update_item'                => __( 'Update Collection', 'wpmovielibrary' ),
						'add_new_item'               => __( 'Add New Collection', 'wpmovielibrary' ),
						'new_item_name'              => __( 'New Collection Name', 'wpmovielibrary' ),
						'separate_items_with_commas' => __( 'Separate collections with commas', 'wpmovielibrary' ),
						'add_or_remove_items'        => __( 'Add or remove collections', 'wpmovielibrary' ),
						'choose_from_most_used'      => __( 'Choose from the most used collections', 'wpmovielibrary' ),
						'not_found'                  => __( 'No collections found.', 'wpmovielibrary' ),
						'no_terms'                   => __( 'No collections', 'wpmovielibrary' ),
						'items_list_navigation'      => __( 'Collections list navigation', 'wpmovielibrary' ),
						'items_list'                 => __( 'Collections list', 'wpmovielibrary' ),
					),
				),
				'archive' => 'collections',
			),
			array(
				'slug'  => 'genre',
				'posts' => array( 'movie' ),
				'args'  => array(
					'labels' => array(
						'name'                       => __( 'Genres', 'wpmovielibrary' ),
						'add_new_item'               => __( 'New Genre', 'wpmovielibrary' ),
						'search_items'               => __( 'Search Genres', 'wpmovielibrary' ),
						'popular_items'              => __( 'Popular Genres', 'wpmovielibrary' ),
						'all_items'                  => __( 'All Genres', 'wpmovielibrary' ),
						'parent_item'                => __( 'Parent Genre', 'wpmovielibrary' ),
						'parent_item_colon'          => __( 'Parent Genre:', 'wpmovielibrary' ),
						'edit_item'                  => __( 'Edit Genre', 'wpmovielibrary' ),
						'view_item'                  => __( 'View Genre', 'wpmovielibrary' ),
						'update_item'                => __( 'Update Genre', 'wpmovielibrary' ),
						'add_new_item'               => __( 'Add New Genre', 'wpmovielibrary' ),
						'new_item_name'              => __( 'New Genre Name', 'wpmovielibrary' ),
						'separate_items_with_commas' => __( 'Separate genres with commas', 'wpmovielibrary' ),
						'add_or_remove_items'        => __( 'Add or remove genres', 'wpmovielibrary' ),
						'choose_from_most_used'      => __( 'Choose from the most used genres', 'wpmovielibrary' ),
						'not_found'                  => __( 'No genres found.', 'wpmovielibrary' ),
						'no_terms'                   => __( 'No genres', 'wpmovielibrary' ),
						'items_list_navigation'      => __( 'Genres list navigation', 'wpmovielibrary' ),
						'items_list'                 => __( 'Genres list', 'wpmovielibrary' ),
					),
				),
				'archive' => 'genres',
			),
			array(
				'slug'  => 'actor',
				'posts' => array( 'movie' ),
				'args'  => array(
					'labels' => array(
						'name'                       => __( 'Actors', 'wpmovielibrary' ),
						'add_new_item'               => __( 'New Actor', 'wpmovielibrary' ),
						'search_items'               => __( 'Search Actors', 'wpmovielibrary' ),
						'popular_items'              => __( 'Popular Actors', 'wpmovielibrary' ),
						'all_items'                  => __( 'All Actors', 'wpmovielibrary' ),
						'parent_item'                => __( 'Parent Actor', 'wpmovielibrary' ),
						'parent_item_colon'          => __( 'Parent Actor:', 'wpmovielibrary' ),
						'edit_item'                  => __( 'Edit Actor', 'wpmovielibrary' ),
						'view_item'                  => __( 'View Actor', 'wpmovielibrary' ),
						'update_item'                => __( 'Update Actor', 'wpmovielibrary' ),
						'add_new_item'               => __( 'Add New Actor', 'wpmovielibrary' ),
						'new_item_name'              => __( 'New Actor Name', 'wpmovielibrary' ),
						'separate_items_with_commas' => __( 'Separate actors with commas', 'wpmovielibrary' ),
						'add_or_remove_items'        => __( 'Add or remove actors', 'wpmovielibrary' ),
						'choose_from_most_used'      => __( 'Choose from the most used actors', 'wpmovielibrary' ),
						'not_found'                  => __( 'No actors found.', 'wpmovielibrary' ),
						'no_terms'                   => __( 'No actors', 'wpmovielibrary' ),
						'items_list_navigation'      => __( 'Actors list navigation', 'wpmovielibrary' ),
						'items_list'                 => __( 'Actors list', 'wpmovielibrary' ),
					),
				),
				'archive' => 'actors',
			),
		);

		/**
		 * Filter the custom taxonomies parameters prior to registration.
		 *
		 * @since    3.0
		 *
		 * @param    array    $taxonomies Taxonomies list
		 */
		$this->taxonomies = apply_filters( 'wpmoly/filter/taxonomies', $taxonomies );

		foreach ( $this->taxonomies as $taxonomy ) {

			if ( wpmoly_o( "{$taxonomy['slug']}-posts" ) ) {
				$taxonomy['args']['posts'][] = 'post';
			}

			$slug = $taxonomy['slug'];
			if ( ! empty( $this->permalinks[ $slug ] ) ) {
				$slug = trim( $this->permalinks[ $slug ], '/' );
			}

			/**
			 * Filter the custom taxonomy parameters prior to registration.
			 *
			 * @since    3.0
			 *
			 * @param    array    $taxonomy Taxonomy parameters
			 */
			$args = apply_filters( "wpmoly/filter/taxonomy/{$taxonomy['slug']}", $taxonomy['args'] );

			$args = array_merge( array(
				'show_ui'               => true,
				'show_tagcloud'         => true,
				'show_admin_column'     => true,
				'hierarchical'          => false,
				'query_var'             => true,
				'sort'                  => true,
				'show_in_rest'          => true,
				'rest_base'             => ! empty( $taxonomy['archive'] ) ? $taxonomy['archive'] : $slug,
				'rest_controller_class' => 'WP_REST_Terms_Controller',
				'rewrite'               => array(
					'slug' => $slug,
				),
			), $args );

			register_taxonomy( $taxonomy['slug'], $taxonomy['posts'], $args );
		} // End foreach().
	}

	/**
	 * Register Custom Term Meta.
	 *
	 * @since    3.0
	 */
	public function register_term_meta() {

		$term_meta = array();

		/**
		 * Filter the Custom Term Meta prior to registration.
		 *
		 * @since    3.0
		 *
		 * @param    array    $term_meta Term Meta list
		 */
		$this->term_meta = apply_filters( 'wpmoly/filter/term_meta', $term_meta );

		foreach ( $this->term_meta as $taxonomy => $term_meta ) {

			foreach ( $term_meta as $slug => $params ) {

				/**
				 * Filter meta_key.
				 *
				 * Add a '_wpmoly_{$taxonomy}_' prefix to meta_keys.
				 *
				 * @since    3.0
				 *
				 * @param    string    $slug Post meta slug.
				 */
				$meta_key = apply_filters( "wpmoly/filter/{$taxonomy}/meta/key", $slug );

				$args = wp_parse_args( $params, array(
					'type'              => 'string',
					'description'       => '',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => null,
				) );

				register_meta( 'term', $meta_key, $args );
			}
		}
	}
}
