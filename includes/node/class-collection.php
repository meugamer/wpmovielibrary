<?php

/**
 * Define the collection class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * 
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Collection extends Node {

	/**
	 * Collection Term object
	 * 
	 * @var    WP_Term
	 */
	public $term;

	/**
	 * Collection thumbnail.
	 * 
	 * @var    Picture
	 */
	protected $thumbnail;

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 *
	 * @param    int|Node|WP_Term    $node Node ID, node instance or term object
	 */
	public function __construct( $node = null ) {

		if ( is_numeric( $node ) ) {
			$this->id   = absint( $node );
			$this->term = get_term( $this->id );
		} elseif ( $node instanceof Node ) {
			$this->id   = absint( $node->id );
			$this->term = $node->term;
		} elseif ( isset( $node->term_id ) ) {
			$this->id   = absint( $node->term_id );
			$this->term = $node;
		}

		$this->init();
	}

	/**
	 * Initialize the Collection.
	 * 
	 * @since    3.0
	 *
	 * @return   void
	 */
	public function init() {

		$this->suffix = '_wpmoly_collection_';

		/**
		 * Filter the default collection meta list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_meta
		 */
		$this->default_meta = apply_filters( 'wpmoly/filter/default/collection/meta', array( 'name', 'thumbnail', 'person_id' ) );
	}

	/**
	 * Magic.
	 * 
	 * Add support for Collection::get_{$property}() and Collection::the_{$property}()
	 * methods.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $method 
	 * @param    array     $arguments 
	 * 
	 * @return   mixed
	 */
	public function __call( $method, $arguments ) {

		if ( preg_match( '/get_[a-z_]+/i', $method ) ) {
			$name = str_replace( 'get_', '', $method );
			return $this->get_the( $name );
		} elseif ( preg_match( '//i', $method ) ) {
			$name = str_replace( 'the_', '', $method );
			$this->the( $name );
		}
	}

	/**
	 * Load metadata.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Property name
	 * 
	 * @return   mixed
	 */
	protected function get_property( $name ) {

		// Load metadata
		$value = get_term_meta( $this->id, $this->suffix . $name, $single = true );

		return $value;
	}

	/**
	 * Property accessor.
	 * 
	 * Override Node::get() to add support for additional data like 'name'.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Property name
	 * @param    mixed     $default Default value
	 * 
	 * @return   mixed
	 */
	public function get( $name, $default = null ) {

		if ( 'name' == $name ) {
			return $this->term->name;
		}

		if ( 'description' == $name ) {
			return $this->term->description;
		}

		return parent::get( $name, $default );
	}

	/**
	 * Enhanced property accessor. Unlike Node::get() this method automatically
	 * escapes the property requested and therefore should be used when the
	 * property is meant for display.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Property name
	 * 
	 * @return   void
	 */
	public function get_the( $name ) {

		$hook_name = sanitize_key( $name );

		return apply_filters( 'wpmoly/filter/the/collection/' . $hook_name, $this->get( $name ), $this );
	}

	/**
	 * Simple property echoer. Use Node::get_the() to automatically escape
	 * the requested property.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name Property name
	 * 
	 * @return   void
	 */
	public function the( $name ) {

		echo $this->get_the( $name );
	}

	/**
	 * Simple accessor for Collection's Picture.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $variant Poster variant.
	 * 
	 * @return   Poster|DefaultPoster
	 */
	public function get_thumbnail( $variant = '', $size = 'thumb' ) {

		if ( empty( $variant ) ) {
			$variant = $this->get( 'thumbnail' );
			if ( empty( $variant ) ) {
				$variant = strtoupper( substr( $this->get( 'name' ), 0, 1 ) );
			}
		}

		/**
		 * Filter default collection thumbnail variants
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $variants
		 */
		$variants = apply_filters( 'wpmoly/filter/default/collection/thumbnail/variants', array_merge( range( 'A', 'Z' ), array( 'default' ) ) );
		if ( ! in_array( $variant, $variants ) ) {
			$variant = 'default';
		}

		/**
		 * Filter default collection thumbnail
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $thumbnail
		 */
		$sizes = apply_filters( 'wpmoly/filter/default/collection/thumbnail/sizes', array( 'original', 'full', 'medium', 'small', 'thumb', 'thumbnail', 'tiny' ) );
		if ( ! in_array( $size, $sizes ) ) {
			$size = 'medium';
		}

		if ( 'original' !== $size ) {
			$size = '-' . $size;
		}

		/**
		 * Filter default collection thumbnail
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $thumbnail
		 */
		$thumbnail = apply_filters( 'wpmoly/filter/default/collection/thumbnail', WPMOLY_URL . "public/img/collection-{$variant}{$size}.png" );

		return $this->thumbnail = $thumbnail;
	}

	/**
	 * Save collection.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function save() {

		$this->save_meta();
	}

	/**
	 * Save collection metadata.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function save_meta() {

		foreach ( $this->default_meta as $key ) {
			if ( isset( $this->$key ) ) {
				update_term_meta( $this->id, $this->suffix . $key, $this->$key );
			}
		}
	}

}
