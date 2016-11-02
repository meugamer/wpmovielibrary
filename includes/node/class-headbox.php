<?php
/**
 * Define the Headbox class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * General Headbox class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Headbox extends Node {

	/**
	 * Headbox related Node object
	 * 
	 * @var    Node
	 */
	//public $node;

	/**
	 * Headbox type.
	 * 
	 * @var    string
	 */
	protected $type;
	
	/**
	 * Headbox mode.
	 * 
	 * @var    string
	 */
	protected $mode;
	
	/**
	 * Headbox theme.
	 * 
	 * @var    string
	 */
	protected $theme;

	/**
	 * Supported Headbox types.
	 * 
	 * @var    array
	 */
	private $supported_types = array();

	/**
	 * Supported Headbox modes.
	 * 
	 * @var    array
	 */
	private $supported_modes = array();

	/**
	 * Supported Headbox themes.
	 * 
	 * @var    array
	 */
	private $supported_themes = array();

	/**
	 * Initialize the Headbox.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract public function init();

	/**
	 * Build the Headbox.
	 * 
	 * Load items depending on presets or custom settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	abstract public function build();

	/**
	 * Retrieve current headbox type.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	abstract public function get_type();

	/**
	 * Set headbox type.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type
	 * 
	 * @return   string
	 */
	abstract public function set_type( $type );

	/**
	 * Retrieve current headbox mode.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	abstract public function get_mode();

	/**
	 * Set headbox mode.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $mode
	 * 
	 * @return   string
	 */
	abstract public function set_mode( $mode );

	/**
	 * Retrieve current headbox theme.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	abstract public function get_theme();

	/**
	 * Set headbox theme.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $theme
	 * 
	 * @return   string
	 */
	abstract public function set_theme( $theme );

	/**
	 * Is this a posts headbox?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_post() {

		return post_type_exists( $this->get_type() );
	}

	/**
	 * Is this a terms headbox?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_taxonomy() {

		return taxonomy_exists( $this->get_type() );
	}
}