<?php
/**
 * Define the Actor Node.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

use wpmoly\Helpers\Formatting;

/**
 * Actors are terms from the 'actor' taxonomy.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 * 
 * @property    string     $name Actor name.
 * @property    int        $person_id Actor related Person ID.
 */
class Actor extends Node {

	/**
	 * Actor Term object
	 * 
	 * @var    WP_Term
	 */
	public $term;

	/**
	 * Actor picture.
	 * 
	 * @var    Picture
	 */
	protected $picture;

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
	 * Initialize the Actor.
	 * 
	 * @since    3.0
	 *
	 * @return   void
	 */
	public function init() {

		/** This filter is documented in includes/helpers/utils.php */
		$this->suffix = apply_filters( 'wpmoly/filter/actor/meta/key', '' );

		/**
		 * Filter the default actor meta list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_meta
		 */
		$this->default_meta = apply_filters( 'wpmoly/filter/default/actor/meta', array( 'name', 'picture', 'person_id' ) );
	}

	/**
	 * Magic.
	 * 
	 * Add support for Actor::get_{$property}() and Actor::the_{$property}()
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

		return apply_filters( 'wpmoly/filter/the/actor/' . $hook_name, $this->get( $name ), $this );
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
	 * Simple accessor for Actor's Picture.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $variant Poster variant.
	 * 
	 * @return   Poster|DefaultPoster
	 */
	public function get_picture( $variant = '', $size = 'thumb' ) {

		$custom_picture = $this->get_custom_picture( $size );
		if ( ! empty( $custom_picture ) ) {
			return $this->picture = $custom_picture;
		}

		if ( empty( $variant ) ) {
			$variant = $this->get( 'picture' );
		}

		/**
		 * Filter default actor picture
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $picture
		 */
		$variants = apply_filters( 'wpmoly/filter/default/actor/picture/variants', array( 'neutral', 'female', 'male' ) );
		if ( ! in_array( $variant, $variants ) ) {
			$variant = 'neutral';
		}

		/**
		 * Filter default actor picture
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $picture
		 */
		$sizes = apply_filters( 'wpmoly/filter/default/actor/picture/sizes', array( 'original', 'full', 'medium', 'small', 'thumb', 'thumbnail', 'tiny' ) );
		if ( ! in_array( $size, $sizes ) ) {
			$size = 'medium';
		}

		if ( 'original' !== $size ) {
			$size = '-' . $size;
		}

		/**
		 * Filter default actor picture
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $picture
		 */
		$picture = apply_filters( 'wpmoly/filter/default/actor/picture', WPMOLY_URL . "public/img/actor-{$variant}{$size}.png" );

		return $this->picture = $picture;
	}

	/**
	 * Retrieve the Actor's custom picture, if any.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $size.
	 * 
	 * @return   string
	 */
	public function get_custom_picture( $size ) {

		$picture = $this->get( 'custom_picture' );
		if ( empty( $picture ) ) {
			return $picture;
		}

		$picture = wp_get_attachment_image_src( $picture, $size );
		if ( empty( $picture[0] ) ) {
			return '';
		}

		return $picture[0];
	}

	/**
	 * Save actor.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function save() {

		$this->save_meta();
	}

	/**
	 * Save actor metadata.
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