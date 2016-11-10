<?php
/**
 * Define the Widget class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/widgets
 */

namespace wpmoly\Widgets;

use WP_Widget;

/**
 * Abstract Widget class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/widgets
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Widget extends WP_Widget {

	/**
	 * Widget Root ID.
	 * 
	 * @var    string
	 */
	public $id_base;

	/**
	 * Widget Name.
	 * 
	 * @var    string
	 */
	public $title;

	/**
	 * Widget .
	 * 
	 * @var    string
	 */
	public $classname;

	/**
	 * Widget .
	 * 
	 * @var    string
	 */
	public $description;

	/**
	 * Widget parameters.
	 * 
	 * @var    array
	 */
	private $args = array();

	/**
	 * Widget current instance.
	 * 
	 * @var    array
	 */
	private $instance = array();

	/**
	 * Widget default attributes.
	 * 
	 * @var    array
	 */
	protected $defaults = array();

	/**
	 * Widget data.
	 * 
	 * @var    array
	 */
	protected $data = array();

	/**
	 * Widget form data.
	 * 
	 * @var    array
	 */
	protected $formdata = array();

	/**
	 * Class constructor.
	 * 
	 * Initialize properties and run Widget constructor.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function __construct() {

		$this->make();

		parent::__construct(
			$this->id_base, $this->name,
			array(
				'classname'   => $this->classname,
				'description' => $this->description
			)
		);
	}

	/**
	 * Set default properties.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function make();

	/**
	 * Build Widget content.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function build();

	/**
	 * Build Widget form content.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function build_form();

	/**
	 * Echoes the widget content.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Widget parameters.
	 * @param    array    $instance Widget settings.
	 * 
	 * @return   void
	 */
	public function widget( $args, $instance ) {

		$this->args = $args;
		$this->instance = $instance;
		$this->build();

		$classname = wp_parse_args(
			array(
				'widget',
				'wpmoly-widget',
				$this->id_base . '-widget'
			),
			$this->classname
		);
		$this->classname = implode( ' ', $classname );

		$template = get_widget_template( $this->id_base );
		$template->set_data( array(
			'widget' => $this,
			'data'   => $this->data
		) );

		echo $template->render( $require = 'always' );
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * This function should check that `$new_instance` is set correctly. The newly-calculated
	 * value of `$instance` should be returned. If false is returned, the instance won't be
	 * saved/updated.
	 *
	 * @since    3.0
	 * 
	 * @param    array    $new_instance New settings for this instance.
	 * @param    array    $old_instance Old settings for this instance.
	 * 
	 * @return   array
	 */
	public function update( $new_instance, $old_instance ) {

		$this->instance = $old_instance;

		$new_instance = wp_parse_args( $new_instance, $this->defaults );
		foreach ( $new_instance as $key => $value ) {
			$this->set_attr( $key, $value );
		}

		return $this->instance;
	}

	/**
	 * Echoes Widget settings update form.
	 *
	 * @since    3.0
	 * 
	 * @param    array    $instance Current settings.
	 * 
	 * @return   void
	 */
	public function form( $instance ) {

		$this->instance = $instance;
		$this->build_form();

		$template = get_widget_template( $this->id_base );
		$template->set_data( array(
			'widget' => $this,
			'data'   => $this->formdata
		) );

		echo $template->render( $require = 'always' );
	}

	/**
	 * Widget properties accessor.
	 *
	 * @since    3.0
	 * 
	 * @param    string    $name Property name.
	 * 
	 * @return   mixed
	 */
	public function get_arg( $name ) {

		return isset( $this->args[ $name ] ) ? $this->args[ $name ] : '';
	}

	/**
	 * Set Widget property.
	 *
	 * @since    3.0
	 * 
	 * @param    string    $name Property name.
	 * @param    mixed     $value New property value.
	 * 
	 * @return   mixed
	 */
	public function set_arg( $name, $value ) {

		if ( $value === $this->get_arg( $name ) ) {
			return $value;
		}

		return $this->args[ $name ] = $value;
	}

	/**
	 * Get Widget instance attribute.
	 *
	 * @since    3.0
	 * 
	 * @param    string    $name Attribute name.
	 * 
	 * @return   mixed
	 */
	public function get_attr( $name ) {

		return isset( $this->instance[ $name ] ) ? $this->instance[ $name ] : '';
	}

	/**
	 * Set Widget instance attribute.
	 *
	 * @since    3.0
	 * 
	 * @param    string    $name Attribute name.
	 * @param    mixed     $value New Attribute value.
	 * 
	 * @return   mixed
	 */
	public function set_attr( $name, $value ) {

		if ( $value === $this->get_attr( $name ) ) {
			return $value;
		}

		return $this->instance[ $name ] = $value;
	}
}
