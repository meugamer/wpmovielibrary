<?php
/**
 * WPMOLY OMDb API class
 * 
 * @uses WordPress WP_Http Class instead of CURL like the original class.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'OMDb' ) ) :

	class OMDb
	{
		private $url = 'http://www.omdbapi.com/';

		/**
		 * Default constructor
		 * 
		 * @since    2.1.5
		 */
		public function __construct() {

			if ( ! is_admin() )
				return false;
		}

		/**
		 * Check the submitted API Key is valid.
		 * 
		 * @since    2.1.5
		 * 
		 * @param    string    $key API Key
		 * 
		 * @return   null
		 */
		public function checkApiKey( $key ) {}

		/**
		 * Getter for the TMDB-config
		 * 
		 * @since    2.1.5
		 *
		 * @return   null
		 */
		public function getConfig() {}

		/**
		 * Search a movie by querystring
		 * 
		 * @since    2.1.5
		 *
		 * @param    string    $query Query to search after in the TMDb database
		 * @param    int       $page Number of the page with results (default first page)
		 * @param    bool      $adult Whether of not to include adult movies in the results (default false)
		 * @param    mixed     $year Filter the result with a year
		 * @param    mixed     $lang Filter the result with a language
		 * 
		 * @return   array     TMDb result 
		 */
		public function searchMovie( $query, $page = 1, $adult = false, $year = null, $lang = null ) {

			$params = array(
				't' => $query,
			);

			return $this->query( $params );
		}

		/**
		 * Retrieve all basic information for a particular movie
		 * 
		 * @since    2.1.5
		 *
		 * @param    int       $id TMDb-id or IMDB-id
		 * @param    string    $lang Filter the result with a language
		 * 
		 * @return   array    TMDb result 
		 */
		public function getMovie( $id, $lang = null ) {

			$params = array( 'language' => is_null( $lang ) ? wpmoly_o( 'api-language' ) : $lang );

			return $this->query( 'movie/' . $id, $params );
		}

		/**
		 * Retrieve all of the movie cast information for a particular movie
		 * 
		 * @since    2.1.5
		 *
		 * @param    int    $id TMDb-id or IMDB-id
		 * 
		 * @return   array    TMDb result
		 */
		public function getMovieCast( $id ) {}

		/**
		 * Retrieve all images for a particular movie
		 * 
		 * @since    2.1.5
		 * 
		 * @param    int       $id TMDb-id or IMDB-id
		 * @param    string    $lang Filter the result with a language
		 * 
		 * @return   array    TMDb result
		 */
		public function getMovieImages( $id, $lang = null ) {}

		/**
		 * Retrieve all release information for a particular movie
		 * 
		 * @since    2.0
		 *
		 * @param    int       $id TMDb-id or IMDB-id
		 * 
		 * @return   null
		 */
		public function getMovieRelease( $id ) {}

		/**
		 * Get configuration from TMDb
		 * 
		 * @since    2.1.5
		 *
		 * @return   null
		 */
		public function getConfiguration() {}

		/**
		 * Get Image URL
		 * 
		 * @since    2.1.5
		 *
		 * @param    string    $filepath Filepath to image
		 * @param    const     $imagetype Image type
		 * @param    string    $size Valid size for the image
		 * 
		 * @return   null
		 */
		public function getImageUrl( $filepath, $imagetype, $size ) {}

		/**
		 * Get available image sizes for a particular image type
		 * 
		 * @since    2.1.5
		 *
		 * @param    string    $imagetype Image type
		 * 
		 * @return   null
		 */
		public function getAvailableImageSizes( $imagetype ) {}

		/**
		 * Makes the call to the API
		 * 
		 * @since    2.1.5
		 *
		 * @param    string    $function API specific function name for in the URL
		 * @param    array     $params Unencoded parameters for in the URL
		 * @param    string    $session_id Session_id for authentication to the API for specific API methods
		 * 
		 * @return   mixed     TMDb result or error message
		 */
		protected function query( $params = null, $session_id = null, $method = 'get' ) {

			$params = ( ! is_array( $params ) ) ? array() : $params;
			$url = $this->url . '?' . http_build_query( $params, '', '&' );

			$results = array();
			$request  = new WP_Http;
			$headers  = array( 'Accept' => 'application/json' );
			$response = $request->request( $url, array( 'headers' => $headers ) );

			if ( is_wp_error( $response ) )
				return $response;

			if ( isset( $response['response']['code'] ) && 200 != $response['response']['code'] )
				return new WP_Error( 'connect_failed', sprintf( __( 'API Error: server connection to "%s" returned error %s: %s', 'wpmovielibrary' ), $url, $response['response']['code'], $response['response']['message'] ) );

			$header = $response['headers'];
			$body   = $response['body'];

			$results = json_decode( $body, true );

			// Using array_key_exists() instead of isset() to prevent weird bug in PHP 5.3
			if ( is_array( $body ) && array_key_exists( 'status_code', $body ) && array_key_exists( 'status_message', $body ) )
				return new WP_Error( 'connect_failed', sprintf( __( 'API Error: connection to TheMovieDB API failed with message "%s" (code %s)', 'wpmovielibrary' ), $body['status_code'], $body['status_message'] ) );

			if ( is_null( $results ) )
				return new WP_Error( 'unknown_error', __( 'API Error: unknown server error, unable to perform request.', 'wpmovielibrary' ) );

			return $results;
		}
	}

endif;