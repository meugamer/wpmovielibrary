<?php
/**
 * Define the Rest API extension class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/rest-api
 */

namespace wpmoly\Rest;

use WP_Error;

/**
 * Handle the custom WordPress Rest API endpoints.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/rest-api
 * @author     Charlie Merland <charlie@caercam.org>
 */
class API {

	/**
	 * Current instance.
	 *
	 * @since    3.0
	 *
	 * @var      Library
	 */
	public static $instance;

	/**
	 * Define the API class.
	 * 
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public function __construct() {

		self::$instance = $this;

		$supported_parameters = apply_filters( 'wpmoly/filter/query/default/parameters', array(
			'actor'         => 'cast',
			'adult'         => 'adult',
			'author'        => 'author',
			'budget'        => 'budget',
			'certification' => 'certification',
			//'collection'    => 'collection',
			'company'       => 'production_companies',
			'composer'      => 'composer',
			'country'       => 'production_countries',
			'director'      => 'director',
			'format'        => 'format',
			'genre'         => 'genres',
			'language'      => 'language',
			'languages'     => 'spoken_languages',
			'local_release' => 'local_release_date',
			'media'         => 'media',
			'photography'   => 'photography',
			'producer'      => 'producer',
			'rating'        => 'rating',
			'release'       => 'release_date',
			'revenue'       => 'revenue',
			'runtime'       => 'runtime',
			'status'        => 'status',
			'subtitles'     => 'subtitles',
			'writer'        => 'writer',
		) );

		$this->supported_parameters = $supported_parameters;
	}

	/**
	 * Singleton.
	 *
	 * @since    3.0
	 * 
	 * @return   null
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register additional fields for the REST API data response objects.
	 * 
	 * Add posters and backdrops to movies, pictures to actors and thumbnails
	 * to collections and genres.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_fields() {

		// Movie poster
		register_rest_field( 'movie',
			'poster',
			array(
				'get_callback'    => array( $this, 'get_movie_poster' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);

		// Movie posters
		register_rest_field( 'movie',
			'posters',
			array(
				'get_callback'    => array( $this, 'get_movie_posters' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);

		// Movie backdrop
		register_rest_field( 'movie',
			'backdrop',
			array(
				'get_callback'    => array( $this, 'get_movie_backdrop' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);

		// Movie backdrops
		register_rest_field( 'movie',
			'backdrops',
			array(
				'get_callback'    => array( $this, 'get_movie_backdrops' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);

		// Terms thumbnails
		register_rest_field( array( 'actor', 'collection', 'genre' ),
			'thumbnail',
			array(
				'get_callback'    => array( $this, 'get_term_thumbnail' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);

		register_rest_field( 'grid',
			'support',
			array(
				'get_callback'    => array( $this, 'get_grid_support' ),
				'update_callback' => null,
				'schema'          => array(
					'description' => 'Meeeeh',
					'type'        => 'array',
					'context'     => array( 'edit' ),
				),
			)
		);
	}

	/**
	 * Add custom REST API query params.
	 * 
	 * Add support for letter filtering.
	 * 
	 * @since    3.0
	 * 
	 * @param    array              $args    Key value array of query var to query value.
	 * @param    WP_REST_Request    $request The request used.
	 * 
	 * @return   array
	 */
	public function add_query_params( $args, $request ) {

		if ( 'movie' === $args['post_type'] ) {

			if ( ! empty( $request['letter'] ) ) {
				$args['letter'] = $request['letter'];
			}

			if ( ! isset( $args['meta_query'] ) ) {
				$args['meta_query'] = array();
			}

			foreach ( $this->supported_parameters as $param => $key ) {
				if ( ! empty( $request[ $param ] ) ) {
					$args = apply_filters( "wpmoly/filter/query/movies/{$param}/param", $args, $key, $param, $request );
				}
			}
		}

		return $args;
	}

// 	/**
// 	 * Add custom parameters to query movies of a specific .
// 	 * 
// 	 * @TODO This does not work with hyphened names.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    array               $args    Key value array of query var to query value.
// 	 * @param    string              $key     Meta key.
// 	 * @param    mixed               $param   Meta value.
// 	 * @param    WP_REST_Request     $request The request used.
// 	 *
// 	 * @return   array
// 	 */
// 	public function add_budget_query_param
// 	
// 	/**
// 	 * Add custom parameters to query movies of a specific .
// 	 * 
// 	 * @TODO This does not work with hyphened names.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    array               $args    Key value array of query var to query value.
// 	 * @param    string              $key     Meta key.
// 	 * @param    mixed               $param   Meta value.
// 	 * @param    WP_REST_Request     $request The request used.
// 	 *
// 	 * @return   array
// 	 */
// 	public function add_company_query_param
// 	
// 	/**
// 	 * Add custom parameters to query movies of a specific .
// 	 * 
// 	 * @TODO This does not work with hyphened names.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    array               $args    Key value array of query var to query value.
// 	 * @param    string              $key     Meta key.
// 	 * @param    mixed               $param   Meta value.
// 	 * @param    WP_REST_Request     $request The request used.
// 	 *
// 	 * @return   array
// 	 */
// 	public function add_country_query_param
// 
// 	/**
// 	 * Add custom parameters to query movies of a specific director.
// 	 * 
// 	 * @TODO This does not work with hyphened names.
// 	 * 
// 	 * @since    3.0
// 	 * 
// 	 * @param    array               $args    Key value array of query var to query value.
// 	 * @param    string              $key     Meta key.
// 	 * @param    mixed               $param   Meta value.
// 	 * @param    WP_REST_Request     $request The request used.
// 	 *
// 	 * @return   array
// 	 */
// 	public function add_director_query_param( $args, $key, $param, $request ) {
// 
// 		if ( empty( $key ) || empty( $param ) ) {
// 			return $args;
// 		}
// 
// 		/**
// 		 * Filter meta key.
// 		 * 
// 		 * @since    3.0
// 		 * 
// 		 * @param    string    $key
// 		 */
// 		$key = apply_filters( 'wpmoly/filter/movie/meta/key', $key );
// 
// 		/**
// 		 * Filter meta value.
// 		 * 
// 		 * @since    3.0
// 		 * 
// 		 * @param    string    $value
// 		 */
// 		$value = apply_filters( 'wpmoly/filter/query/movies/director/value', $request[ $param ] );
// 
// 		/**
// 		 * Filter meta comparison operator.
// 		 * 
// 		 * @since    3.0
// 		 * 
// 		 * @param    string    $compare
// 		 */
// 		$compare = apply_filters( 'wpmoly/filter/query/movies/director/compare', 'LIKE' );
// 
// 		$args['meta_query'][] = compact( 'key', 'value', 'compare' );
// 
// 		return $args;
// 	}
// 
// 	add_genre_query_param
// 	add_language_query_param
// 	add_release_query_param
// 	add_rating_query_param
// 	add_revenue_query_param
// 	add_runtime_query_param
// 	add_subtitles_query_param

	/**
	 * Add custom parameters to query movies of a specific meta interval.
	 * 
	 * @since    3.0
	 * 
	 * @param    array               $args    Key value array of query var to query value.
	 * @param    string              $key     Meta key.
	 * @param    mixed               $param   Meta value.
	 * @param    WP_REST_Request     $request The request used.
	 *
	 * @return   array
	 */
	public function add_meta_interval_query_param( $args, $key, $param, $request ) {

		if ( empty( $key ) || empty( $param ) ) {
			return $args;
		}

		/**
		 * Filter meta value.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $value
		 */
		$value = apply_filters( "wpmoly/filter/query/movies/{$param}/value", $request[ $param ] );

			/** This filter is documented in includes/helpers/utils.php */
			$key = apply_filters( 'wpmoly/filter/movie/meta/key', $key );

		if ( ! is_array( $value ) ) {

			/**
			 * Filter meta comparison operator.
			 * 
			 * @since    3.0
			 * 
			 * @param    string    $compare
			 */
			$compare = apply_filters( "wpmoly/filter/query/movies/{$param}/compare", 'LIKE' );

			$args['meta_query'][] = compact( 'key', 'value', 'compare' );

		} else {

			$args['meta_query'] = array(
				array(
					'key'     => $key,
					'value'   => $value[0],
					'type'    => 'NUMERIC',
					'compare' => '>=',
				),
				array(
					'key'     => $key,
					'value'   => $value[1],
					'type'    => 'NUMERIC',
					'compare' => '<=',
				),
				'relation' => 'AND',
			);

			
		}

		return $args;
	}

	/**
	 * Add custom parameters to query movies of a specific meta.
	 * 
	 * @since    3.0
	 * 
	 * @param    array               $args    Key value array of query var to query value.
	 * @param    string              $key     Meta key.
	 * @param    mixed               $param   Meta value.
	 * @param    WP_REST_Request     $request The request used.
	 *
	 * @return   array
	 */
	public function add_meta_query_param( $args, $key, $param, $request ) {

		if ( empty( $key ) || empty( $param ) ) {
			return $args;
		}

		/**
		 * Filter meta value.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $value
		 */
		$value = apply_filters( "wpmoly/filter/query/movies/{$param}/value", $request[ $param ] );

		/**
		 * Filter meta comparison operator.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $compare
		 */
		$compare = apply_filters( "wpmoly/filter/query/movies/{$param}/compare", 'LIKE' );

		/** This filter is documented in includes/helpers/utils.php */
		$key = apply_filters( 'wpmoly/filter/movie/meta/key', $key );

		$args['meta_query'][] = compact( 'key', 'value', 'compare' );

		return $args;
	}

	/**
	 * Register custom REST API collection params.
	 * 
	 * Add support for letter filtering.
	 * 
	 * @since    3.0
	 * 
	 * @param    array           $query_params JSON Schema-formatted collection parameters.
	 * @param    WP_Post_Type    $post_type    Post type object.
	 * 
	 * @return   array
	 */
	public function register_collection_params( $query_params, $post_type ) {

		if ( 'movie' === $post_type->name ) {

			$query_params['letter'] = array(
				'description' => __( 'Filter movies by letter.', 'wpmovielibrary' ),
				'type'        => 'string',
				'default'     => '',
				'enum'        => array( '' ) + str_split( '#0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ),
			);

			$metadata = get_registered_meta_keys( 'post' );

			foreach ( $this->supported_parameters as $param => $key ) {

				$meta_key = prefix_movie_meta_key( $key );

				if ( ! empty( $metadata[ $meta_key ] ) ) {
					$query_params[ $param ] = array(
						'description' => $metadata[ $meta_key ]['description'],
						'type'        => $metadata[ $meta_key ]['type'],
					);
				}
			}

		}

		return $query_params;
	}

	/**
	 * Filter the movie post data for REST API response.
	 *
	 * @since    3.0
	 *
	 * @param    WP_REST_Response    $response The response object.
	 * @param    WP_Post             $post     Post object.
	 * @param    WP_REST_Request     $request  Request object.
	 *
	 * @return   array
	 */
	public function prepare_movie_for_response( $response, $post, $request ) {

		$metadata = get_registered_meta_keys( 'post' );
		$meta_key = prefix_movie_meta_key( 'release_date' );
		if ( empty( $response->data['meta']['release_date'] ) || empty( $metadata[ $meta_key ] ) ) {
			return $response;
		}

		$year = $response->data['meta']['release_date']['raw'];
		$year = date( 'Y', strtotime( $year ) );

		$response->data['meta']['year'] = array(
			'rendered' => get_formatted_movie_year( $year, array( 'is_link' => false ) ),
			'raw'      => $year
		);

		// Content is overkilled for grids.
		$response->data['content']['rendered'] = '';

		return $response;
	}

	/**
	 * Add movie poster to the data response.
	 * 
	 * @since    3.0
	 * 
	 * @param    array              $object Post object.
	 * @param    string             $field_name Field name.
	 * @param    WP_REST_Request    $request Current REST Request.
	 * 
	 * @return   \wpmoly\Node\Image
	 */
	public function get_movie_poster( $object, $field_name, $request ) {

		$movie = get_movie( $object['id'] );

		return $movie->get_poster();
	}

	/**
	 * Add movie posters list to the data response.
	 * 
	 * @since    3.0
	 * 
	 * @param    array              $object Post object.
	 * @param    string             $field_name Field name.
	 * @param    WP_REST_Request    $request Current REST Request.
	 * 
	 * @return   \wpmoly\Node\NodeList
	 */
	public function get_movie_posters( $object, $field_name, $request ) {

		$movie = get_movie( $object['id'] );

		return $movie->get_posters();
	}
	
	/**
	 * Add movie backdrop to the data response.
	 * 
	 * @since    3.0
	 * 
	 * @param    array              $object Post object.
	 * @param    string             $field_name Field name.
	 * @param    WP_REST_Request    $request Current REST Request.
	 * 
	 * @return   \wpmoly\Node\Image
	 */
	public function get_movie_backdrop( $object, $field_name, $request ) {

		$movie = get_movie( $object['id'] );

		return $movie->get_backdrop();
	}

	/**
	 * Add movie backdrops list to the data response.
	 * 
	 * @since    3.0
	 * 
	 * @param    array              $object Post object.
	 * @param    string             $field_name Field name.
	 * @param    WP_REST_Request    $request Current REST Request.
	 * 
	 * @return   \wpmoly\Node\NodeList
	 */
	public function get_movie_backdrops( $object, $field_name, $request ) {

		$movie = get_movie( $object['id'] );

		return $movie->get_backdrops();
	}

	/**
	 * Add term thumbnail to the data response.
	 * 
	 * @since    3.0
	 * 
	 * @param    array              $object Post object.
	 * @param    string             $field_name Field name.
	 * @param    WP_REST_Request    $request Current REST Request.
	 * 
	 * @return   \wpmoly\Node\Image
	 */
	public function get_term_thumbnail( $object, $field_name, $request ) {

		if ( 'actor' == $object['taxonomy'] ) {

			$term = get_actor( $object['id'] );

			return $term->get_picture();

		} elseif ( 'collection' == $object['taxonomy'] ) {

			$term = get_collection( $object['id'] );

			return $term->get_thumbnail();

		} elseif ( 'genre' == $object['taxonomy'] ) {

			$term = get_genre( $object['id'] );

			return $term->get_thumbnail();

		}

		return null;
	}

	/**
	 * Filter the grid post data for REST API response.
	 *
	 * @since    3.0
	 *
	 * @param    array              $object Post object.
	 * @param    string             $field_name Field name.
	 * @param    WP_REST_Request    $request Current REST Request.
	 *
	 * @return   array
	 */
	public function get_grid_support( $object, $field_name, $request ) {

		if ( 'edit' !== $request['context'] ) {
			return array();
		}

		$grid = get_grid( $object['id'] );

		return $grid->get_supported_types();
	}

}