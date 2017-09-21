<?php
/**
 * Define the Post Meta Registrar class.
 *
 * Register required .
 *
 * @link https://wpmovielibrary.com
 * @since 3.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\registrars;

/**
 * Register the plugin post meta.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Post_Meta {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		$post_meta = array(
			'tmdb_id' => array(
				'type'         => 'integer',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'TheMovieDb.org movie ID', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'TMDb ID', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_tmdb_id',
				),
			),
			'title' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Title', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Title', 'wpmovielibrary' ),
				),
			),
			'original_title' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Original title for the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Original Title', 'wpmovielibrary' ),
				),
			),
			'tagline' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Short movie tagline', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Tagline', 'wpmovielibrary' ),
				),
			),
			'overview' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Short movie overview', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Overview', 'wpmovielibrary' ),
				),
			),
			'release_date' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Date the movie was initially released', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Release Date', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_release_date',
				),
			),
			'local_release_date' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Date the movie was localy released based on your settings', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Local Release Date', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_local_release_date',
				),
			),
			'runtime' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Total movie runtime', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Runtime', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_runtime',
				),
			),
			'production_companies' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of companies who produced the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Production Companies', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_production',
				),
			),
			'production_countries' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of countries where the movie was produced', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Production Countries', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_countries',
				),
			),
			'spoken_languages' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of languages spoken in the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Spoken Languages', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_spoken_languages',
				),
			),
			'genres' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of genres for the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Genres', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_genres',
				),
			),
			'director' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of directors for the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Director', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_director',
				),
			),
			'producer' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of producers for the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Producer', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_producer',
				),
			),
			'cast' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of actors starring in the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Actors', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_cast',
				),
			),
			'photography' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of directors of photography for the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Director of photography', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_photography',
				),
			),
			'composer' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of original music composers for the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Composer', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_composer',
				),
			),
			'author' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of authors for the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Author', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_author',
				),
			),
			'writer' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of writers for the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Writer', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_writer',
				),
			),
			'certification' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Movie certification', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Certification', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_certification',
				),
			),
			'budget' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Movie budget', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Budget', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_budget',
				),
			),
			'revenue' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Movie revenue', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Revenue', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_revenue',
				),
			),
			'imdb_id' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Internet Movie Database movie ID', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'IMDb ID', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_imdb_id',
				),
			),
			'adult' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Separate adult-only movies from all-audience movies', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Adult-only', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_adult',
				),
			),
			'homepage' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Official movie Website', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Homepage', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_homepage',
				),
			),
			'status' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Current status of your copy of the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Status', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_status',
				),
			),
			'media' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of medias', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Media', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_media',
				),
			),
			'rating' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'Your own rating of the movie', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Rating', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_rating',
				),
			),
			'language' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of languages', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Language', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_language',
				),
			),
			'subtitles' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of subtitles', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Subtitles', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_subtitles',
				),
			),
			'format' => array(
				'type'         => 'string',
				'post_type'    => array( 'movie' ),
				'description'  => __( 'List of formats', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Format', 'wpmovielibrary' ),
					'prepare_callback' => 'get_formatted_movie_format',
				),
			),
			'type' => array(
				'type'         => 'string',
				'post_type'    => array( 'grid' ),
				'description'  => __( 'Grid type', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Type', 'wpmovielibrary' ),
				),
			),
			'mode' => array(
				'type'         => 'string',
				'post_type'    => array( 'grid' ),
				'description'  => __( 'Grid mode', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => __( 'Mode', 'wpmovielibrary' ),
				),
			),
			'theme' => array(
				'type'         => 'string',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Grid theme', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Theme', 'wpmovielibrary' ),
				),
			),
			'preset' => array(
				'type'         => 'string',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Select a preset to apply to the grid. Presets override any filters and ordering settings you might define, be sure to select "Custom" for those settings to be used.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Grid preset', 'wpmovielibrary' ),
				),
				'default' => 'custom',
			),
			'enable_pagination' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Allow visitors to browse through the grid. Is Ajax browsing is enabled, pagination will use Ajax to dynamically load new pages instead of reloading. Default is enabled.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Enable Pagination', 'wpmovielibrary' ),
				),
				'sanitize_callback' => '_is_bool',
				'default' => 1,
			),
			'columns' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Number of columns', 'wpmovielibrary' ),
				),
				'default' => 5,
			),
			'rows' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Number of rows', 'wpmovielibrary' ),
				),
				'default' => 4,
			),
			'column_width' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Ideal width for posters. Grid columns will never exceed that width. Default is 160.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Movie Poster ideal width', 'wpmovielibrary' ),
				),
				'default' => 160,
			),
			'row_height' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Ideal height for posters. Grid rows will never exceed that height. Tip: that value should be equal to ideal width times 1.5. Default is 240.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Movie Poster ideal height', 'wpmovielibrary' ),
				),
				'default' => 240,
			),
			'list_columns' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Number of columns for the grid in list mode. Default is 3.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Number of list columns', 'wpmovielibrary' ),
				),
				'default' => 3,
			),
			'list_column_width' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Ideal width for columns in list mode. Default is 240.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Ideal column width', 'wpmovielibrary' ),
				),
				'default' => 240,
			),
			'list_rows' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Number of rows for the grid in list mode. Default is 8.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Number of list rows', 'wpmovielibrary' ),
				),
				'default' => 8,
			),
			'settings_control' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Visitors will be able to change some settings to browse the grid differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Enable user settings', 'wpmovielibrary' ),
				),
				'sanitize_callback' => '_is_bool',
				'default' => 1,
			),
			'custom_letter' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Allow visitors to filter the grid by letters. Default is enabled.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Enable letter filtering', 'wpmovielibrary' ),
				),
				'sanitize_callback' => '_is_bool',
				'default' => 1,
			),
			'custom_order' => array(
				'type'         => 'integer',
				'post_type'    => array( 'grid' ),
				'description'  => esc_html__( 'Allow visitors to change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
				'show_in_rest' => array(
					'label' => esc_html__( 'Enable custom ordering', 'wpmovielibrary' ),
				),
				'sanitize_callback' => '_is_bool',
				'default' => 1,
			),
		);

		/**
		 * Filter the Custom Post Meta prior to registration.
		 *
		 * @since 3.0.0
		 *
		 * @param array $post_meta Post Meta list.
		 */
		$this->post_meta = apply_filters( 'wpmoly/filter/post_meta', $post_meta );
	}

	/**
	 * Register Custom Post Meta.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_post_meta() {

		if ( empty( $this->post_meta ) ) {
			return false;
		}

		foreach ( $this->post_meta as $slug => $params ) {

			$args = wp_parse_args( $params, array(
				'type'              => 'string',
				'post_type'         => '',
				'description'       => '',
				'default'           => '',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => null,
			) );

			foreach ( (array) $args['post_type'] as $post_type ) {

				/**
				 * Filter meta_key.
				 *
				 * Add a '_wpmoly_{$post_type}_' prefix to meta_keys.
				 *
				 * @since 3.0.0
				 *
				 * @param string $slug Post meta slug.
				 */
				$meta_key = apply_filters( "wpmoly/filter/{$post_type}/meta/key", $slug );

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

}
