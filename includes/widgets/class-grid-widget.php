<?php
/**
 * Define the Grid Widget class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/widgets
 */

namespace wpmoly\Widgets;

/**
 * Grid Widget class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/widgets
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Grid extends Widget {

	/**
	 * Widget default attributes.
	 * 
	 * @var    array
	 */
	protected $defaults = array(
		'title'       => '',
		'description' => '',
		'grid_id'     => ''
	);

	/**
	 * Set default properties.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function make() {

		$this->id_base = 'grid';
		$this->name = __( 'WPMovieLibrary Grid', 'wpmovielibrary' );
		$this->description = __( 'Display a Grid of movies, actors, collections, genres… ', 'wpmovielibrary' );
	}

	/**
	 * Build Widget content.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function build() {

		$before_title = $this->get_arg( 'before_title' );
		$after_title  = $this->get_arg( 'after_title' );
		$widget_title = apply_filters( 'widget_title', $this->get_attr( 'title' ) );

		$this->data['title'] = $before_title . $widget_title . $after_title;
		$this->data['description'] = $this->get_attr( 'description' );

		$grid = get_grid( (int) $this->get_attr( 'grid_id' ) );
		$grid->is_widget = true;
		$grid->prepare();

		$template = get_grid_template( $grid );

		$this->data['grid'] = $template->render( $require = 'always', $echo = false );
	}

	/**
	 * Build Widget form content.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function build_form() {

		$grids = get_posts( array(
			'post_type'   => 'grid',
			'post_status' => 'publish',
			'numberposts' => -1
		) );
		$this->formdata['grids'] = $grids;
	}
}
