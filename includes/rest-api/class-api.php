<?php
/**
 * Define the Rest API extension class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\rest;

use WP_Error;
use WP_Taxonomy;
use WP_Post_Type;

/**
 * Handle the custom WordPress Rest API endpoints.
 *
 * @since      3.0
 * @package    WPMovieLibrary
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
			'preset'        => 'preset',
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
					'description' => '',
					'type'        => 'array',
					'context'     => array( 'edit' ),
				),
			)
		);
	}

	/**
	 * Add custom REST API post query params.
	 *
	 * @since    3.0
	 *
	 * @param    array              $args    Key value array of query var to query value.
	 * @param    WP_REST_Request    $request The request used.
	 *
	 * @return   array
	 */
	public function add_post_query_params( $args, $request ) {

		// Preset should be overriden by custom order request.
		// We have to use $_REQUEST instead of $request to ignore defaults.
		if ( ! empty( $request['preset'] ) && ! ( empty( $_REQUEST['order'] ) || empty( $_REQUEST['orderby'] ) ) ) {
			unset( $request['preset'] );
		}

		if ( ! empty( $request['preset'] ) ) {

			$preset = str_replace( '-movies', '', $request['preset'] );

			/** This filter is documented in includes/core/class-query.php */
			$args = apply_filters( "wpmoly/filter/query/movies/{$preset}/preset/param", $args );
		}

		if ( ! empty( $request['letter'] ) ) {
			$args['letter'] = $request['letter'];
		}

		if ( ! isset( $args['meta_query'] ) ) {
			$args['meta_query'] = array();
		}

		foreach ( $this->supported_parameters as $param => $key ) {
			if ( ! empty( $request[ $param ] ) ) {

				/**
				 * Filter query parameters.
				 *
				 * @since    3.0
				 *
				 * @param    array              $args Query parameters.
				 * @param    string             $key Meta key.
				 * @param    string             $param Parameter slug.
				 * @param    WP_REST_Request    $request The request used.
				 */
				$args = apply_filters( "wpmoly/filter/query/movies/{$param}/param", $args, $key, $param, $request );
			}
		}

		return $args;
	}

	/**
	 * Add custom REST API term query params.
	 *
	 * @since    3.0
	 *
	 * @param    array              $args    Key value array of query var to query value.
	 * @param    WP_REST_Request    $request The request used.
	 *
	 * @return   array
	 */
	public function add_term_query_params( $args, $request ) {

		// Preset should be overriden by custom order request.
		// We have to use $_REQUEST instead of $request to ignore defaults.
		if ( ! empty( $request['preset'] ) && ( ! empty( $_REQUEST['order'] ) || ! empty( $_REQUEST['orderby'] ) ) ) {
			unset( $request['preset'] );
		}

		$taxonomy = str_replace( array( 'rest_', '_query' ), '', current_filter() );
		if ( ! in_array( $taxonomy, array( 'actor', 'collection', 'genre' ) ) ) {
			return $args;
		}

		$taxonomy = get_taxonomy( $taxonomy );
		if ( ! $taxonomy ) {
			return $args;
		}

		if ( ! empty( $request['preset'] ) ) {

			$preset = str_replace( "-{$taxonomy->rest_base}", '', $request['preset'] );

			/** This filter is documented in includes/core/class-query.php */
			$args = apply_filters( "wpmoly/filter/query/{$taxonomy->rest_base}/{$preset}/preset/param", $args );
		}

		return $args;
	}

	/**
	 * Register custom REST API collection params.
	 *
	 * @since    3.0
	 *
	 * @param    array    $query_params JSON Schema-formatted collection parameters.
	 * @param    mixed    $object       WP_Post_Type or WP_Taxonomy object.
	 *
	 * @return   array
	 */
	public function register_collection_params( $query_params, $object ) {

		if ( $object instanceof WP_Post_Type ) {
			return $this->register_post_collection_params( $query_params, $object );
		} elseif ( $object instanceof WP_Taxonomy ) {
			return $this->register_term_collection_params( $query_params, $object );
		}

		return $query_params;
	}

	/**
	 * Register custom REST API post collection params.
	 *
	 * Add support for letter and meta filtering, presets, fields selection.
	 *
	 * @since    3.0
	 *
	 * @param    array           $query_params JSON Schema-formatted collection parameters.
	 * @param    WP_Post_Type    $post_type    Post Type object.
	 *
	 * @return   array
	 */
	private function register_post_collection_params( $query_params, $post_type ) {

		if ( 'movie' === $post_type->name ) {

			$supported = $this->supported_parameters;

			// Support grid presets.
			$query_params['preset'] = array(
				'description' => __( 'Limit result set using presets.', 'wpmovielibrary' ),
				'type'        => 'string',
				'default'     => 'custom',
				//'sanitize_callback' => '',
			);

			// Filter movies by first letter.
			$query_params['letter'] = array(
				'description' => __( 'Filter movies by letter.', 'wpmovielibrary' ),
				'type'        => 'string',
				'default'     => '',
				'enum'        => array( '' ) + str_split( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ),
			);

			// Avoid loading all available meta.
			$query_params['fields'] = array(
				'description' => __( 'Limit result meta set to specific fields.', 'wpmovielibrary' ),
				'type'        => 'array',
				'default'     => array( 'title', 'genres', 'director', 'rating', 'release_date', 'runtime', 'year' ),
				'items'       => array(
					'type' => 'string',
				),
				//'sanitize_callback' => '',
			);

			// Authors are WordPress users; we want to be able to use
			// that option to match movie authors.
			if ( ! empty( $query_params['author']['items']['type'] ) ) {

				unset( $supported['author'] );

				$query_params['author']['description'] = __( 'Limit result set to posts assigned to specific authors. Use integers to match WordPress users, strings to match movie authors.', 'wpmovielibrary' );
				$query_params['author']['items']['type'] = 'string';
			}

			$metadata = get_registered_meta_keys( 'post' );

			foreach ( $supported as $param => $key ) {

				$meta_key = prefix_movie_meta_key( $key );

				if ( ! empty( $metadata[ $meta_key ] ) ) {
					$query_params[ $param ] = array(
						'description' => $metadata[ $meta_key ]['description'],
						'type'        => $metadata[ $meta_key ]['type'],
					);
				}
			}
		} // End if().

		return $query_params;
	}

	/**
	 * Register custom REST API term collection params.
	 *
	 * Add support for presets.
	 *
	 * @since    3.0
	 *
	 * @param    array          $query_params JSON Schema-formatted collection parameters.
	 * @param    WP_Taxonomy    $taxonomy     Taxonomy object.
	 *
	 * @return   array
	 */
	private function register_term_collection_params( $query_params, $taxonomy ) {

		if ( in_array( $taxonomy->name, array( 'actor', 'collection', 'genre' ) ) ) {

			// Support grid presets.
			$query_params['preset'] = array(
				'description' => __( 'Limit result set using presets.', 'wpmovielibrary' ),
				'type'        => 'string',
				'default'     => 'custom',
				//'sanitize_callback' => '',
			);
		}

		return $query_params;
	}

	/**
	 * Filter the movie post data for REST API response.
	 *
	 * @TODO Allow rendered content when specifically requested.
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

		// Content/excerpt are overkill.
		$response->data['excerpt']['rendered'] = '';
		$response->data['content']['rendered'] = '';

		$requested_fields = array_filter( $request['fields'] );
		if ( empty( $requested_fields ) || ! in_array( 'year', $requested_fields ) ) {
			return $response;
		}

		$metadata = get_registered_meta_keys( 'post' );
		$meta_key = prefix_movie_meta_key( 'release_date' );

		// Release date not supported. Unusual, but...
		if ( empty( $metadata[ $meta_key ] ) ) {
			return $response;
		}

		if ( ! empty( $response->data['meta']['release_date'] ) ) {
			$date = $response->data['meta']['release_date']['raw'];
		} else {
			$date = get_movie_meta( $post->ID, 'release_date' );
			if ( empty( $date ) ) {
				return $response;
			}
		}

		$year = date( 'Y', strtotime( $date ) );

		$response->data['meta']['year'] = array(
			'rendered' => get_formatted_movie_year( $year, array(
				'is_link' => false,
			) ),
			'raw' => $year,
		);

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
	 * @return   \wpmoly\nodes\images\Image
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
	 * @return   \wpmoly\nodes\Nodes
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
	 * @return   \wpmoly\notes\images\Image
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
	 * @return   \wpmoly\nodes\Nodes
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
	 * @return   \wpmoly\notes\images\Image
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
