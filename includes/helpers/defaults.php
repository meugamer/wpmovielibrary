<?php
/**
 * The file that defines the plugin defaults functions and values.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly;

function get_default_rewrite_tags() {

	/**
	 * Filters the default available custom tags for URL rewriting.
	 *
	 * @since 3.0
	 *
	 * @param array $tags Defaults rewrite tags.
	 */
	$tags = apply_filters( 'wpmoly/filter/default/rewrite/tags', array(
		'%imdb_id%'          => '(tt[0-9]+)',
		'%tmdb_id%'          => '([0-9]+)',
		'%year%'             => '([0-9]{4})',
		'%monthnum%'         => '([0-9]{1,2})',
		'%day%'              => '([0-9]{1,2})',
		'%release_year%'     => '([0-9]{4})',
		'%release_monthnum%' => '([0-9]{1,2})',
		'%release_day%'      => '([0-9]{1,2})',
	) );

	return (array) $tags;
}