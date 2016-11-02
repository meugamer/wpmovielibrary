<?php
/**
 * Define the Term Headbox class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * General Term Headbox class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 */
class TermHeadbox extends Headbox {

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
			if ( $node instanceof Actor ) {
				$this->id    = absint( $node->id );
				$this->actor = $node;
				$this->type  = 'actor';
			} elseif ( $node instanceof Collection ) {
				$this->id         = absint( $node->id );
				$this->collection = $node;
				$this->type       = 'collection';
			} elseif ( $node instanceof Genre ) {
				$this->id    = absint( $node->id );
				$this->genre = $node;
				$this->type  = 'genre';
			} else {
				$this->id   = absint( $node->id );
				$this->term = $node->term;
			}
		} elseif ( isset( $node->term_id ) ) {
			$this->id   = absint( $node->term_id );
			$this->term = $node;
		}

		$this->init();
	}

	/**
	 * Initialize the Headbox.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function init() {

		$headbox_types = array(
			'collection' => array(
				'label'  => __( 'Collection', 'wpmovielibrary' ),
				'themes' => array(
					'default' =>  __( 'Default', 'wpmovielibrary' )
				)
			),
			'actor' => array(
				'label'  => __( 'Actor', 'wpmovielibrary' ),
				'themes' => array(
					'default' => __( 'Default', 'wpmovielibrary' )
				)
			),
			'genre' => array(
				'label'  => __( 'Genre', 'wpmovielibrary' ),
				'themes' => array(
					'default' => __( 'Default', 'wpmovielibrary' )
				)
			)
		);

		/**
		 * Filter the supported Headbox types.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $headbox_types
		 */
		$this->supported_types = apply_filters( 'wpmoly/filter/headbox/supported/types', $headbox_types );

		foreach ( $this->supported_types as $type_id => $type ) {

			/**
			 * Filter the supported Headbox themes.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $default_modes
			 */
			$this->supported_modes[ $type_id ] = apply_filters( 'wpmoly/filter/headbox/supported/' . $type_id . '/themes', $type['themes'] );
		}

		$this->build();
	}

	/**
	 * Build the Headbox.
	 * 
	 * Load items depending on presets or custom settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function build() {

		if ( is_null( $this->get( 'type' ) ) ) {
			return false;
		}

		$function = "get_" . $this->get( 'type' );
		if ( function_exists( $function ) ) {
			$this->node = $function( $this->id );
		}
	}

	/**
	 * Retrieve current headbox type.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_type() {

		/**
		 * Filter headbox default type.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $default_type
		 */
		$default_type = apply_filters( 'wpmoly/filter/headbox/default/type', '' );

		if ( is_null( $this->type ) ) {
			$this->type = $default_type;
		}

		return $this->type;
	}

	/**
	 * Set headbox type.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type
	 * 
	 * @return   string
	 */
	public function set_type( $type ) {

		if ( ! isset( $this->supported_types[ $type ] ) ) {
			$type = '';
		}

		return $this->type = $type;
	}

	/**
	 * Retrieve current headbox mode.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_mode() {

		if ( is_null( $this->mode ) ) {
			$this->mode = 'default';
		}

		return $this->mode;
	}

	/**
	 * Set headbox mode.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $mode
	 * 
	 * @return   string
	 */
	public function set_mode( $mode ) {

		if ( ! isset( $this->supported_modes[ $this->type ][ $mode ] ) ) {
			$mode = 'default';
		}

		return $this->mode = $mode;
	}

	/**
	 * Retrieve current headbox theme.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_theme() {

		if ( is_null( $this->theme ) ) {
			$this->theme = 'default';
		}

		return $this->theme;
	}

	/**
	 * Set headbox theme.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $theme
	 * 
	 * @return   string
	 */
	public function set_theme( $theme ) {

		if ( ! isset( $this->supported_modes[ $this->type ][ $this->mode ][ $theme ] ) ) {
			$theme = 'default';
		}

		return $this->theme = $theme;
	}
}