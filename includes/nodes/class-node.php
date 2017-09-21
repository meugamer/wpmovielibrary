<?php
/**
 * Define the Node class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\nodes;

/**
 * Define a generic Node class.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Node {

	/**
	 * Node ID.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Node meta suffix.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $suffix;

	/**
	 * Class Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int|Node|WP_Post $node Node ID, node instance or post object.
	 */
	public function __construct( $node = null ) {

		if ( is_numeric( $node ) ) {
			$this->id   = absint( $node );
			$this->post = get_post( $this->id );
		} elseif ( $node instanceof Node ) {
			$this->id   = absint( $node->id );
			$this->post = $node->post;
		} elseif ( isset( $node->ID ) ) {
			$this->id   = absint( $node->ID );
			$this->post = $node;
		}

		$this->init();
	}

	/**
	 * Load metadata.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param string $name Property name
	 *
	 * @return mixed
	 */
	protected function get_property( $name ) {

		// Load metadata
		$value = get_post_meta( $this->id, $this->suffix . $name, true );

		return $value;
	}

	/**
	 * Property accessor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Property name
	 * @param mixed $default Default value
	 *
	 * @return mixed
	 */
	public function get( $name, $default = null ) {

		if ( isset( $this->$name ) && ! is_null( $this->$name ) ) {
			return $this->$name;
		}

		$value = $this->get_property( $name );
		if ( false === $value ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Set Property.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function set( $name, $value = null ) {

		if ( is_object( $name ) ) {
			$name = get_object_vars();
		}

		if ( is_array( $name ) ) {
			foreach ( $name as $key => $value ) {
				$this->set( $key, $value );
			}
			return true;
		}

		$this->$name = $value;

		return $value;
	}

}
