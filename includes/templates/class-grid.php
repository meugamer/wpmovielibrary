<?php
/**
 * Define the Grid Template classes.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Templates;

use WP_Error;

/**
 * Grid Template class.
 * 
 * This class acts as a controller for grid templates, determining which template
 * file to use and preseting data for it.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Grid extends Front {

	/**
	 * Grid instance.
	 * 
	 * @var    Grid
	 */
	private $grid;

	/**
	 * Grid JSON.
	 * 
	 * @var    object
	 */
	protected $json;

	/**
	 * Class Constructor.
	 * 
	 * @since    3.0
	 * 
	 * @param    mixed    $grid Grid instance or ID.
	 * 
	 * @return   Template|WP_Error
	 */
	public function __construct( $grid ) {

		if ( is_int( $grid ) ) {
			$grid = get_grid( $grid );
			if ( empty( $grid->post ) ) {
				return null;
			}
			$this->grid = $grid;
		} elseif ( is_object( $grid ) ) {
			$this->grid = $grid;
		} else {
			return null;
		}

		$this->set_path();

		$this->set_content();

		return $this;
	}

	/**
	 * __call().
	 * 
	 * Allows access to $this->grid methods through this class.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name
	 * 
	 * @return   mixed
	 */
	public function __call( $method, $arguments ) {

		if ( method_exists( $this->grid, $method ) ) {
			return call_user_func_array( array( $this->grid, $method ), $arguments );
		}

		return null;
	}

	/**
	 * __get().
	 * 
	 * Allows access to $this->grid properties through this class.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $name
	 * 
	 * @return   mixed
	 */
	public function __get( $name ) {

		if ( ! isset( $this->$name ) ) {
			$method = "get_$name";
			if ( method_exists( $this->grid, $method ) ) {
				return $this->grid->$method();
			} elseif ( $this->grid->get( $name ) ) {
				return $this->grid->get( $name );
			}
		}

		return null;
	}

	/**
	 * Determine the grid template path based on the grid's type and mode.
	 * 
	 * TODO make use of that WP_Error.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	private function set_path() {

		$path = 'public/templates/grids/grid.php';
		if ( ! file_exists( WPMOLY_PATH . $path ) ) {
			return new WP_Error( 'missing_template_path', sprintf( __( 'Error: "%s" does not exists.', 'wpmovielibrary' ), esc_attr( WPMOLY_PATH . $path ) ) );
		}

		return $this->path = $path;
	}

	/**
	 * Set the content Template.
	 * 
	 * Grids uses a subview Template to show content in no-js mode.
	 * 
	 * @since    3.0
	 * 
	 * @return   \wpmoly\Templates\Template
	 */
	private function set_content() {

		$type = $this->grid->get_type();
		$mode = $this->grid->get_mode();

		$content = wpmoly_get_template( "grids/content/{$type}-{$mode}.php" );

		return $this->content = $content;
	}

	/**
	 * Shall we show the grid pagination menu?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function show_pagination() {

		return 1 === (int) $this->enable_pagination;
	}

	/**
	 * Retrieve current page number.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_current_page() {

		return (int) $this->grid->query->get_current_page();
	}

	/**
	 * Retrieve previous page number.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_previous_page() {

		return (int) $this->grid->query->get_current_page();
	}

	/**
	 * Retrieve next page number.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_next_page() {

		return (int) $this->grid->query->get_next_page();
	}

	/**
	 * Retrieve total pages number.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_total_pages() {

		return (int) $this->grid->query->get_total_pages();
	}

	/**
	 * Are we on the first available grid page?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_first_page() {

		return 1 === $this->get_current_page();
	}

	/**
	 * Did we reach the last available grid page?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_last_page() {

		return $this->get_current_page() === $this->get_total_pages();
	}

	/**
	 * Get previous grid page URL.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_previous_page_url() {

		$page = $this->grid->query->get_previous_page();

		$args = $this->grid->get_settings();
		$args['paged'] = $page;

		return $this->build_url( $args );
	}

	/**
	 * Get next grid page URL.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_next_page_url() {

		$page = $this->grid->query->get_next_page();

		$args = $this->grid->get_settings();
		$args['paged'] = $page;

		return $this->build_url( $args );
	}

	/**
	 * Build custom grid URLs.
	 * 
	 * Generate an URL from the current page's permalink with an additional
	 * 'grid' URL parameter containing a formatted string of grid settings.
	 * Grid ID should always be contained in this string in order to apply
	 * settings on the intended grid.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $args Query parameters
	 * 
	 * @return   string
	 */
	private function build_url( $args ) {

		global $wp;

		// Grid ID is required.
		if ( ! isset( $args['id'] ) ) {
			$args = array_merge( array( 'id' => $this->id ), $args );
		}

		// Filter empty rows
		$args = array_filter( $args );

		// Don't need pagination here
		unset( $args['posts_per_page'] );

		// Build custom query.
		$args = array( 'grid' => build_query( $args ) );
		$args = str_replace( array( '&', '=' ), array( ',', ':' ), $args );

		// Build URL
		$url = add_query_arg( $args, home_url( $wp->request ) );

		return $url;
	}

	/**
	 * Render the Template.
	 * 
	 * Default parameters are the opposite of Template::render(): always
	 * require and never echo.
	 * 
	 * @since    3.0
	 * 
	 * @param    string     $require Use 'once' to use require_once(), 'always' to use require()
	 * @param    boolean    $echo Use true to display, false to return
	 * 
	 * @return   string
	 */
	public function render( $require = 'always', $echo = false ) {

		if ( ! $this->grid->ready() ) {
			$this->grid->build();
		}

		if ( empty( $this->data ) ) {

			$this->content->set_data( array(
				'grid'  => $this,
				'items' => $this->grid->items
			) );

			$this->set_data( array(
				'grid'    => $this,
				'items'   => $this->grid->items,
				'content' => $this->content
			) );
		}

		$this->prepare( $require );

		if ( true !== $echo ) {
			return $this->template;
		}

		echo $this->template;
	}

	/**
	 * JSONify the Grid Template instance.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function toJSON() {

		$json = array();

		$json['settings'] = array();
		foreach ( $this->default_settings as $setting ) {
			$json['settings'][ $setting ] = $this->get( $setting );
		}

		$json['query_args'] = $this->get_query_args();
		$json['query_data'] = array(
			'current_page' => $this->get_current_page(),
			'total_page'   => $this->get_total_pages()
		);

		return $this->json = wp_json_encode( $json );
	}
}
