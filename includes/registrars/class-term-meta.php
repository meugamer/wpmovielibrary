<?php
/**
 * Define the Term Meta Registrar class.
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
 * Register the plugin term meta.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Term_Meta {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		$term_meta = array();

		/**
		 * Filter the Custom Term Meta prior to registration.
		 *
		 * @since 3.0.0
		 *
		 * @param array $term_meta Term Meta list.
		 */
		$this->term_meta = apply_filters( 'wpmoly/filter/term_meta', $term_meta );
	}

	/**
	 * Register Custom Term Meta.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_term_meta() {

		if ( empty( $this->term_meta ) ) {
			return false;
		}

		foreach ( $this->term_meta as $slug => $params ) {

			$args = wp_parse_args( $params, array(
				'type'              => 'string',
				'taxonomy'          => '',
				'description'       => '',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => null,
			) );

			foreach ( (array) $args['taxonomy'] as $taxonomy ) {

				/**
				 * Filter meta_key.
				 *
				 * Add a '_wpmoly_{$taxonomy}_' prefix to meta_keys.
				 *
				 * @since 3.0.0
				 *
				 * @param string $slug Post meta slug.
				 */
				$meta_key = apply_filters( "wpmoly/filter/{$taxonomy}/meta/key", $slug );

				register_meta( 'term', $meta_key, $args );
			}
		}
	}

}
