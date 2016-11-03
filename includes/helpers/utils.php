<?php
/**
 * The file that defines the plugin public functions.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes
 */

/**
 * Get WPMovieLibrary instance.
 * 
 * @since    3.0
 * 
 * @return   Library
 */
function get_wpmoly() {

	$wpmoly = wpmoly\Library::get_instance();

	return $wpmoly;
}

/**
 * Retrieve a specific option.
 * 
 * @since    3.0
 * 
 * @param    string    $name Option name
 * @param    mixed     $default Option default value to return if needed
 * 
 * @return   mixed
 */
function wpmoly_o( $name, $default = null ) {

	$options = wpmoly\Core\Options::get_instance();

	return $options->get( $name, $default );
}

/**
 * Retrieve a specific option in a boolean form.
 * 
 * @since    3.0
 * 
 * @param    string    $name Option name
 * @param    mixed     $default Option default value to return if needed
 * 
 * @return   boolean
 */
function wpmoly_is_o( $name, $default = null ) {

	$value = wpmoly_o( $name, $default );

	return _is_bool( $value );
}


/**
 * Return a WPMoly-defined object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $data Node ID, object or array
 * 
 * @return   object
 */
function _get_object( $data, $object ) {

	if ( ! class_exists( $object ) ) {
		return (object) $data;
	}

	if ( $data instanceof $object ) {
		return $data;
	}

	$data = new $object( $data );

	return $data;
}

/**
 * Return a movie object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $movie Movie ID, object or array
 * 
 * @return   Movie|boolean
 */
function get_movie( $movie ) {

	return _get_object( $movie, '\wpmoly\Node\Movie' );
}

/**
 * Return a headbox object.
 * 
 * $object has to be an \wpmoly\Node\Headbox instance in order to be handled
 * correctly. Headboxes support both Terms and Posts, need to make an
 * early distinction between the two.
 * 
 * TODO handle int parameter
 * 
 * @since    3.0
 * 
 * @param    object    $headbox Headbox object.
 * 
 * @return   Headbox|boolean
 */
function get_headbox( $headbox ) {

	if ( isset( $headbox->post ) ) {
		return get_post_headbox( $headbox );
	} elseif ( isset( $headbox->term ) ) {
		return get_term_headbox( $headbox );
	}

	return false;
}

/**
 * Return a post headbox object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $post Post ID, object or array
 * 
 * @return   PostHeadbox|boolean
 */
function get_post_headbox( $post ) {

	return _get_object( $post, '\wpmoly\Node\PostHeadbox' );
}

/**
 * Return a term headbox object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $term Term ID, object or array
 * 
 * @return   TermHeadbox|boolean
 */
function get_term_headbox( $term ) {

	return _get_object( $term, '\wpmoly\Node\TermHeadbox' );
}

/**
 * Return a grid object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $grid Grid ID, object or array
 * 
 * @return   Grid|boolean
 */
function get_grid( $grid ) {

	return _get_object( $grid, '\wpmoly\Node\Grid' );
}

/**
 * Return an actor object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $actor Actor ID, object or array
 * 
 * @return   Actor|boolean
 */
function get_actor( $actor ) {

	return _get_object( $actor, '\wpmoly\Node\Actor' );
}

/**
 * Return a collection object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $collection Collection ID, object or array
 * 
 * @return   Collection|boolean
 */
function get_collection( $collection ) {

	return _get_object( $collection, '\wpmoly\Node\Collection' );
}

/**
 * Return an genre object.
 * 
 * @since    3.0
 * 
 * @param    mixed    $genre Genre ID, object or array
 * 
 * @return   Genre|boolean
 */
function get_genre( $genre ) {

	return _get_object( $genre, '\wpmoly\Node\Genre' );
}

/**
 * Return a movie metadata.
 * 
 * @since    3.0
 * 
 * @param    int        $movie_id Movie ID, object or array
 * @param    string     $key Movie Meta key to return.
 * @param    boolean    $single Whether to return a single value
 * 
 * @return   Movie|boolean
 */
function get_movie_meta( $movie_id, $key = '', $single = false ) {

	$key = (string) $key;

	$value = '';
	if ( 'movie' != get_post_type( $movie_id ) ) {
		return $value;
	}

	if ( ! empty( $key ) ) {
		$key = '_wpmoly_movie_' . $key;
	}

	$value = get_post_meta( $movie_id, $key, $single );

	return $value;
}

/**
 * Get a translation country instance.
 * 
 * @since    3.0
 * 
 * @param    string    $country Country name or ISO code
 * 
 * @return   \wpmoly\Helpers\Country
 */
function get_country( $country ) {

	return \wpmoly\Helpers\Country::get( $country );
}

/**
 * Get a translation language instance.
 * 
 * @since    3.0
 * 
 * @param    string    $language Language name or ISO code
 * 
 * @return   \wpmoly\Helpers\Language
 */
function get_language( $language ) {

	return \wpmoly\Helpers\Language::get( $language );
}

/**
 * Check if a specific page is an archive page.
 * 
 * @since    3.0
 * 
 * @param    int    $post_id Page Post ID.
 * 
 * @return   boolean
 */
function is_archive_page( $post_id ) {

	$pages = get_option( '_wpmoly_archive_pages', array() );

	return ! empty( $pages[ $post_id ] );
}

/**
 * Retrieve taxonomies archive page links.
 * 
 * If the submitted taxonomy does not have a set archive page, return false.
 * Otherwise, return the page's permalink with or without the site home url
 * depending on $format.
 * 
 * @since    3.0
 * 
 * @param    string    $type Taxonomy type.
 * @param    string    $format URL format, 'relative' or 'absolute'.
 * 
 * @return   string|boolean
 */
function get_taxonomy_archive_link( $type = '', $format = 'absolute' ) {

	if ( ! has_archives_page( $type ) ) {
		return '';
	}

	$page_id = get_archives_page_id( $type );
	if ( false === $page_id ) {
		return false;
	}

	$permalink = get_permalink( $page_id );
	if ( 'relative' == $format ) {
		$permalink = str_replace( home_url(), '', $permalink );
	}

	return $permalink;
}

/**
 * Retrieve an archive page ID.
 * 
 * @since    3.0
 * 
 * @param    string    $type Archive type.
 * 
 * @return   int
 */
function get_archives_page_id( $type = '' ) {

	$pages = get_option( '_wpmoly_archive_pages', array() );

	$page_id = array_search( $type, $pages );
	if ( false === $page_id ) {
		return false;
	}

	return (int) $page_id;
}

/**
 * Get a post type archive page if any.
 * 
 * @since    3.0
 * 
 * @param    string    $type Archive type.
 * 
 * @return   WP_Post|null
 */
function get_archives_page( $type = '' ) {

	$post_id = get_archives_page_id( $type );

	return $page = get_post( $post_id );
}

/**
 * Check if there is an archive page set.
 * 
 * @since    3.0
 * 
 * @param    string    $type Archive type.
 * 
 * @return   boolean
 */
function has_archives_page( $type = '' ) {

	$page = get_archives_page( $type );

	return ! is_null( $page );
}

/**
 * Retrieve movies archive page link.
 * 
 * @since    3.0
 * 
 * @param    string    $format URL format, 'relative' or 'absolute'.
 * 
 * @return   string|boolean
 */
function get_movie_archive_link( $format = 'absolute' ) {

	$link = get_post_type_archive_link( 'movie' );
	if ( 'relative' == $format ) {
		$link = str_replace( home_url(), '', $link );
	}

	return $link;
}

/**
 * Retrieve 'movie' post type archive page ID.
 * 
 * @since    3.0
 * 
 * @return   int
 */
function get_movie_archives_page_id() {

	return get_archives_page_id( 'movies' );
}

/**
 * Get 'movie' post type archive page if any.
 * 
 * @since    3.0
 * 
 * @return   WP_Post|null
 */
function get_movie_archives_page() {

	$post_id = get_movie_archives_page_id();

	return $page = get_post( $post_id );
}

/**
 * Check if there is an archive page set for 'movie' post type.
 * 
 * @since    3.0
 * 
 * @return   boolean
 */
function has_movie_archives_page() {

	$page = get_movie_archives_page();

	return ! is_null( $page );
}

/**
 * Retrieve actors archive page link.
 * 
 * @since    3.0
 * 
 * @param    string    $format URL format, 'relative' or 'absolute'.
 * 
 * @return   string|boolean
 */
function get_actor_archive_link( $format = 'absolute' ) {

	return get_taxonomy_archive_link( 'actors', $format );
}

/**
 * Retrieve 'actor' taxonomy archive page ID.
 * 
 * @since    3.0
 * 
 * @return   int
 */
function get_actor_archives_page_id() {

	return get_archives_page_id( 'actors' );
}

/**
 * Get 'actor' taxonomy archive page if any.
 * 
 * @since    3.0
 * 
 * @return   WP_Post|null
 */
function get_actor_archives_page() {

	$post_id = get_actor_archives_page_id();

	return $page = get_post( $post_id );
}

/**
 * Check if there is an archive page set for 'actor' taxonomy.
 * 
 * @since    3.0
 * 
 * @return   boolean
 */
function has_actor_archives_page() {

	$page = get_actor_archives_page();

	return ! is_null( $page );
}

/**
 * Retrieve collections archive page link.
 * 
 * @since    3.0
 * 
 * @param    string    $format URL format, 'relative' or 'absolute'.
 * 
 * @return   string|boolean
 */
function get_collection_archive_link( $format = 'absolute' ) {

	return get_taxonomy_archive_link( 'collections', $format );
}

/**
 * Retrieve 'collection' taxonomy archive page ID.
 * 
 * @since    3.0
 * 
 * @return   int
 */
function get_collection_archives_page_id() {

	return get_archives_page_id( 'collections' );
}

/**
 * Get 'collection' taxonomy archive page if any.
 * 
 * @since    3.0
 * 
 * @return   WP_Post|null
 */
function get_collection_archives_page() {

	$post_id = get_collection_archives_page_id();

	return $page = get_post( $post_id );
}

/**
 * Check if there is an archive page set for 'collection' taxonomy.
 * 
 * @since    3.0
 * 
 * @return   boolean
 */
function has_collection_archives_page() {

	$page = get_collection_archives_page();

	return ! is_null( $page );
}

/**
 * Retrieve genres archive page link.
 * 
 * @since    3.0
 * 
 * @param    string    $format URL format, 'relative' or 'absolute'.
 * 
 * @return   string|boolean
 */
function get_genre_archive_link( $format = 'absolute' ) {

	return get_taxonomy_archive_link( 'genres', $format );
}

/**
 * Retrieve 'genre' taxonomy archive page ID.
 * 
 * @since    3.0
 * 
 * @return   int
 */
function get_genre_archives_page_id() {

	return get_archives_page_id( 'genres' );
}

/**
 * Get 'genre' taxonomy archive page if any.
 * 
 * @since    3.0
 * 
 * @return   WP_Post|null
 */
function get_genre_archives_page() {

	$post_id = get_genre_archives_page_id();

	return $page = get_post( $post_id );
}

/**
 * Check if there is an archive page set for 'genre' taxonomy.
 * 
 * @since    3.0
 * 
 * @return   boolean
 */
function has_genre_archives_page() {

	$page = get_genre_archives_page();

	return ! is_null( $page );
}

/**
 * Strictly merge user defined arguments into defaults array.
 * 
 * This function is a alternative to wp_parse_args() to merge arrays strictly,
 * ie without adding used arguments that are not explicitely defined in the
 * default array.
 * 
 * @since    3.0
 * 
 * @param    string|array    $args Value to merge with $detaults
 * @param    string|array    $defaults Array that serves as the defaults
 * 
 * @return   array           Strictly merged array
 */
function parse_args_strict( $args, $defaults ) {

	if ( is_object( $args ) ) {
		$r = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$r =& $args;
	} else {
		wp_parse_str( $args, $r );
	}

	if ( is_array( $defaults ) ) {
		_parse_args_strict( $r, $defaults );
	}

	return $r;
}

/**
 * Strictly merge arrays. Any key from $args that is not present in $default
 * will be stripped from the result array.
 * 
 * @since    3.0
 * 
 * @param    array    $args Array to merge with $detaults
 * @param    array    $default Array that serves as the defaults
 * 
 * @return   array    Strictly merged array
 */
function _parse_args_strict( $args, $default ) {

	$parsed = array();
	foreach ( $default as $key => $value ) {
		if ( isset( $args[ $key ] ) ) {
			$parsed[ $key ] = $args[ $key ];
		} else {
			$parsed[ $key ] = $value;
		}
	}

	return $parsed;
}

/**
 * Literal boolean check.
 * 
 * @since    3.0
 * 
 * @param    mixed    $var
 * 
 * @return   boolean
 */
function _is_bool( $var ) {

	if ( ! is_string( $var ) ) {
		return (boolean) $var;
	}

	return in_array( strtolower( $var ), array( '1', 'y', 'on', 'yes', 'true' ) );
}
