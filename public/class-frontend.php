<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 */

namespace wpmoly;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Frontend {

	/**
	 * Single instance.
	 *
	 * @var    Frontend
	 */
	private static $instance = null;

	/**
	 * Public stylesheets.
	 *
	 * @var    array
	 */
	private $styles = array();

	/**
	 * Public scripts.
	 *
	 * @var    array
	 */
	private $scripts = array();

	/**
	 * Initialize the class and set its properties.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function __construct() {

		$styles = array(

			// Plugin-wide normalize
			'normalize' => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-normalize-min.css' ),

			// Main stylesheet
			''          => array( 'file' => WPMOLY_URL . 'public/css/wpmoly.css', 'deps' => array( WPMOLY_SLUG . '-normalize' ) ),

			// Common stylesheets
			'common'    => array( 'file' => WPMOLY_URL . 'public/css/common.css' ),
			'headboxes' => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-headboxes.css' ),
			'grids'     => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-grids.css' ),
			'flags'     => array( 'file' => WPMOLY_URL . 'public/css/wpmoly-flags.css' ),

			// Plugin icon font
			'font'      => array( 'file' => WPMOLY_URL . 'public/fonts/wpmovielibrary/style.css' )
		);

		/**
		 * Filter the default styles to register.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $styles
		 */
		$this->styles = apply_filters( 'wpmoly/filter/default/public/styles', $styles );

		$scripts = array(

			// Vendor
			'sprintf' => array(
				'file'    => WPMOLY_URL . 'public/js/sprintf.min.js',
				'deps'    => array( 'jquery', 'underscore' ),
				'version' => '1.0.3'
			),
			'underscore-string' => array(
				'file' => WPMOLY_URL . 'public/js/underscore.string.min.js',
				'deps'    => array( 'jquery', 'underscore' ),
				'version' => '3.3.4'
			),

			// Base
			'' => array( 'file' => WPMOLY_URL . 'public/js/wpmoly.js', 'deps' => array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) ),

			// Utils
			'utils' => array( 'file' => WPMOLY_URL . 'public/js/wpmoly-utils.js' ),

			// Models
			'content-model'  => array( 'file' => WPMOLY_URL . 'public/js/models/grid/content.js' ),
			'settings-model' => array( 'file' => WPMOLY_URL . 'public/js/models/grid/settings.js' ),

			// Controllers
			'query-controller' => array( 'file' => WPMOLY_URL . 'public/js/controllers/query.js' ),
			'grid-controller'  => array( 'file' => WPMOLY_URL . 'public/js/controllers/grid.js' ),

			// Views
			'grid-view'            => array( 'file' => WPMOLY_URL . 'public/js/views/grid.js' ),
			'grid-menu-view'       => array( 'file' => WPMOLY_URL . 'public/js/views/grid/menu.js' ),
			'grid-pagination-view' => array( 'file' => WPMOLY_URL . 'public/js/views/grid/pagination.js' ),
			'grid-settings-view'   => array( 'file' => WPMOLY_URL . 'public/js/views/grid/settings.js' ),
			'grid-customs-view'    => array( 'file' => WPMOLY_URL . 'public/js/views/grid/customs.js' ),
			'grid-content-view'    => array( 'file' => WPMOLY_URL . 'public/js/views/grid/content.js' ),

			// Runners
			'grids' => array( 'file' => WPMOLY_URL . 'public/js/wpmoly-grids.js', 'deps' => array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) ),
		);

		/**
		 * Filter the default scripts to register.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $scripts
		 */
		$this->scripts = apply_filters( 'wpmoly/filter/default/public/scripts', $scripts );
	}

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   Singleton
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register frontend stylesheets.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function register_styles() {

		foreach ( $this->styles as $id => $style ) {

			if ( ! empty( $id ) ) {
				$id = '-' . $id;
			}
			$id = WPMOLY_SLUG . $id;

			$style = wp_parse_args( $style, array(
				'file'    => '',
				'deps'    => array(),
				'version' => WPMOLY_VERSION,
				'media'   => 'all'
			) );

			wp_register_style( $id, $style['file'], $style['deps'], $style['version'], $style['media'] );
		}
	}

	/**
	 * Enqueue a specific style.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $id Script ID.
	 * 
	 * @return   void
	 */
	private function enqueue_style( $id = '' ) {

		if ( ! empty( $id ) ) {
			$id = '-' . $id;
		}
		$id = WPMOLY_SLUG . $id;

		wp_enqueue_style( $id );
	}

	/**
	 * Register frontend JavaScript.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	public function register_scripts() {

		foreach ( $this->scripts as $id => $script ) {

			if ( ! empty( $id ) ) {
				$id = '-' . $id;
			}
			$id = WPMOLY_SLUG . $id;

			$script = wp_parse_args( $script, array(
				'file'    => '',
				'deps'    => array(),
				'version' => WPMOLY_VERSION,
				'footer'  => true
			) );

			wp_register_script( $id, $script['file'], $script['deps'], $script['version'], $script['footer'] );
		}
	}

	/**
	 * Enqueue a specific script.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $id Style ID.
	 * 
	 * @return   void
	 */
	private function enqueue_script( $id = '' ) {

		if ( ! empty( $id ) ) {
			$id = '-' . $id;
		}
		$id = WPMOLY_SLUG . $id;

		wp_enqueue_script( $id );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function enqueue_styles() {

		$this->register_styles();

		$this->enqueue_style();
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'headboxes' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'flags' );
		$this->enqueue_style( 'font' );
	}

	/**
	 * Print a JavaScript template.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $handle Template slug
	 * @param    mixed     $template Template file path or instance
	 * 
	 * @return   null
	 */
	private function print_template( $handle, $template ) {

		if ( is_string( $template ) && ! file_exists( WPMOLY_PATH . $template ) ) {
			return false;
		}

		echo "\n" . '<script type="text/html" id="tmpl-' . $handle . '">';

		if ( $template instanceof \wpmoly\Templates\Template ) {
			$template->set_data( array( 'is_json' => true ) );
			$template->render( 'once' );
		} else {
			require_once WPMOLY_PATH . $template;
		}

		echo '</script>' . "\n";
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function enqueue_scripts() {

		$this->register_scripts();

		// Vendor
		$this->enqueue_script( 'sprintf' );
		$this->enqueue_script( 'underscore-string' );

		// Base
		$this->enqueue_script();
		$this->enqueue_script( 'utils' );

		// Models
		$this->enqueue_script( 'content-model' );
		$this->enqueue_script( 'settings-model' );

		// Controllers
		$this->enqueue_script( 'query-controller' );
		$this->enqueue_script( 'grid-controller' );

		// Views
		$this->enqueue_script( 'grid-view' );
		$this->enqueue_script( 'grid-menu-view' );
		$this->enqueue_script( 'grid-pagination-view' );
		$this->enqueue_script( 'grid-settings-view' );
		$this->enqueue_script( 'grid-customs-view' );
		$this->enqueue_script( 'grid-content-view' );

		// Runners
		$this->enqueue_script( 'grids' );
	}

	/**
	 * Print the JavaScript templates for the frontend area.
	 * 
	 * TODO try not to include this where it's not needed.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function enqueue_templates() {

		$this->print_template( 'wpmoly-grid',                   'public/js/templates/grid/grid.php' );
		$this->print_template( 'wpmoly-grid-menu',              'public/js/templates/grid/menu.php' );
		$this->print_template( 'wpmoly-grid-customs',           'public/js/templates/grid/customs.php' );
		$this->print_template( 'wpmoly-grid-settings',          'public/js/templates/grid/settings.php' );
		$this->print_template( 'wpmoly-grid-pagination',        'public/js/templates/grid/pagination.php' );

		$this->print_template( 'wpmoly-grid-movie-grid',        'public/js/templates/grid/movie-grid.php' );
		$this->print_template( 'wpmoly-grid-movie-list',        'public/js/templates/grid/movie-list.php' );
		$this->print_template( 'wpmoly-grid-actor-grid',        'public/js/templates/grid/actor-grid.php' );
		$this->print_template( 'wpmoly-grid-actor-list',        'public/js/templates/grid/actor-list.php' );
		$this->print_template( 'wpmoly-grid-collection-grid',   'public/js/templates/grid/collection-grid.php' );
		$this->print_template( 'wpmoly-grid-collection-list',   'public/js/templates/grid/collection-list.php' );
		$this->print_template( 'wpmoly-grid-genre-grid',        'public/js/templates/grid/genre-grid.php' );
		$this->print_template( 'wpmoly-grid-genre-list',        'public/js/templates/grid/genre-list.php' );

		$this->print_template( 'wpmoly-grid-actor-archive',      wpmoly_get_template( 'headboxes/actor-default.php' ) );
		$this->print_template( 'wpmoly-grid-collection-archive', wpmoly_get_template( 'headboxes/collection-default.php' ) );
		$this->print_template( 'wpmoly-grid-genre-archive',      wpmoly_get_template( 'headboxes/genre-default.php' ) );
		$this->print_template( 'wpmoly-grid-movie-archive',      wpmoly_get_template( 'headboxes/movie-default.php' ) );
	}

	/**
	 * Register default filters for the plugin.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function set_default_filters() {

		$loader = Core\Loader::get_instance();

		// Shortcodes Meta Formatting
		$loader->add_filter( 'wpmoly/shortcode/format/adult/value',                '', 'get_formatted_movie_adult',              15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/author/value',               '', 'get_formatted_movie_author',             15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/budget/value',               '', 'get_formatted_movie_budget',             15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/certification/value',        '', 'get_formatted_movie_certification',      15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/composer/value',             '', 'get_formatted_movie_composer',           15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/director/value',             '', 'get_formatted_movie_director',           15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/homepage/value',             '', 'get_formatted_movie_homepage',           15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/cast/value',                 '', 'get_formatted_movie_cast',               15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/genres/value',               '', 'get_formatted_movie_genres',             15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/imdb_id/value',              '', 'get_formatted_movie_imdb_id',            15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/local_release_date/value',   '', 'get_formatted_movie_local_release_date', 15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/photography/value',          '', 'get_formatted_movie_photography',        15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/production_countries/value', '', 'get_formatted_movie_countries',          15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/production_companies/value', '', 'get_formatted_movie_production',         15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/producer/value',             '', 'get_formatted_movie_producer',           15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/release_date/value',         '', 'get_formatted_movie_release_date',       15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/revenue/value',              '', 'get_formatted_movie_revenue',            15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/runtime/value',              '', 'get_formatted_movie_runtime',            15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/spoken_languages/value',     '', 'get_formatted_movie_spoken_languages',   15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/tmdb_id/value',              '', 'get_formatted_movie_tmdb_id',            15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/writer/value',               '', 'get_formatted_movie_writer',             15, 2 );

		// Shortcodes Details Formatting
		$loader->add_filter( 'wpmoly/shortcode/format/format/value',               '', 'get_formatted_movie_format',             15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/language/value',             '', 'get_formatted_movie_language',           15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/media/value',                '', 'get_formatted_movie_media',              15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/rating/value',               '', 'get_formatted_movie_rating',             15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/status/value',               '', 'get_formatted_movie_status',             15, 2 );
		$loader->add_filter( 'wpmoly/shortcode/format/subtitles/value',            '', 'get_formatted_movie_subtitles',          15, 2 );

		// Widgets Details Formatting
		$loader->add_filter( 'wpmoly/widget/format/format/value',                  '', 'get_formatted_movie_format',             15, 2 );
		$loader->add_filter( 'wpmoly/widget/format/language/value',                '', 'get_formatted_movie_language',           15, 2 );
		$loader->add_filter( 'wpmoly/widget/format/media/value',                   '', 'get_formatted_movie_media',              15, 2 );
		$loader->add_filter( 'wpmoly/widget/format/rating/value',                  '', 'get_formatted_movie_rating',             15, 2 );
		$loader->add_filter( 'wpmoly/widget/format/status/value',                  '', 'get_formatted_movie_status',             15, 2 );
		$loader->add_filter( 'wpmoly/widget/format/subtitles/value',               '', 'get_formatted_movie_subtitles',          15, 2 );

		// Meta Permalinks
		$loader->add_filter( 'wpmoly/filter/meta/adult/url',              '', 'get_movie_adult_url',         15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/author/url',             '', 'get_movie_author_url',        15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/certification/url',      '', 'get_movie_certification_url', 15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/composer/url',           '', 'get_movie_composer_url',      15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/homepage/url',           '', 'get_movie_homepage_url',      15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/imdb_id/url',            '', 'get_movie_imdb_id_url',       15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/local_release_date/url', '', 'get_movie_release_date_url',  15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/photography/url',        '', 'get_movie_photography_url',   15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/producer/url',           '', 'get_movie_producer_url',      15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/production/url',         '', 'get_movie_production_url',    15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/country/url',            '', 'get_movie_country_url',       15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/release_date/url',       '', 'get_movie_release_date_url',     15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/spoken_languages/url',   '', 'get_movie_spoken_languages_url', 15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/tmdb_id/url',            '', 'get_movie_tmdb_id_url',       15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/writer/url',             '', 'get_movie_writer_url',        15, 2 );

		// Details Permalinks
		$loader->add_filter( 'wpmoly/filter/detail/format/url',           '', 'get_movie_format_url',    15, 2 );
		$loader->add_filter( 'wpmoly/filter/detail/language/url',         '', 'get_movie_language_url',  15, 2 );
		$loader->add_filter( 'wpmoly/filter/detail/media/url',            '', 'get_movie_media_url',     15, 2 );
		$loader->add_filter( 'wpmoly/filter/detail/rating/url',           '', 'get_movie_rating_url',    15, 2 );
		$loader->add_filter( 'wpmoly/filter/detail/status/url',           '', 'get_movie_status_url',    15, 2 );
		$loader->add_filter( 'wpmoly/filter/detail/subtitles/url',        '', 'get_movie_subtitles_url', 15, 2 );

		// Movie queries
		$query = Core\Query::get_instance();
		$loader->add_filter( 'wpmoly/filter/query/movies/author/value',                     $query, 'unsanitize_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/composer/value',                   $query, 'unsanitize_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/photography/value',                $query, 'unsanitize_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/producer/value',                   $query, 'unsanitize_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/production_companies/value',       $query, 'unsanitize_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/writer/value',                     $query, 'unsanitize_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/rating/value',                     $query, 'filter_rating_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/local_release_date/value',         $query, 'filter_date_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/release_date/value',               $query, 'filter_date_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/language/value',                   $query, 'filter_language_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/spoken_languages/value',           $query, 'filter_language_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/subtitles/value',                  $query, 'filter_language_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/production_countries/value',       $query, 'filter_country_query_var', 10, 2 );
		$loader->add_filter( 'wpmoly/filter/query/movies/certification/value',              '', 'strtoupper' );
		$loader->add_filter( 'wpmoly/filter/query/movies/status/value',                     '', 'strtolower' );
		$loader->add_filter( 'wpmoly/filter/query/movies/format/value',                     '', 'strtolower' );
		$loader->add_filter( 'wpmoly/filter/query/movies/media/value',                      '', 'strtolower' );

		// Templates
		$loader->add_filter( 'wpmoly/filter/template/data', $this, 'js_template_data' );

	}

	/**
	 * Add an 'is_json' value to JS Templates data.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $data
	 * 
	 * @return   array
	 */
	public function js_template_data( $data = array() ) {

		if ( empty( $data['is_json'] ) ) {
			$data['is_json'] = false;
		}

		return $data;
	}

	/**
	 * Register the Shortcodes.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_shortcodes() {

		if ( is_admin() ) {
			return false;
		}

		$shortcodes = array(
			'\wpmoly\Shortcodes\Grid',
			'\wpmoly\Shortcodes\Headbox',
			'\wpmoly\Shortcodes\Images',
			'\wpmoly\Shortcodes\Metadata',
			'\wpmoly\Shortcodes\Detail',
			'\wpmoly\Shortcodes\Countries',
			'\wpmoly\Shortcodes\Languages',
			'\wpmoly\Shortcodes\LocalReleaseDate',
			'\wpmoly\Shortcodes\ReleaseDate',
			'\wpmoly\Shortcodes\Runtime'
		);

		foreach ( $shortcodes as $shortcode ) {
			$shortcode::register();
		}
	}

	/**
	 * Display the movie Headbox along with movie content.
	 * 
	 * If we're in search or archive templates, show the default, minimal
	 * Headbox; if we're in single template, show the default full Headbox.
	 * 
	 * TODO implement other Headbox themes
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $content Post content.
	 * 
	 * @return   string
	 */
	public function the_headbox( $content ) {

		if ( 'movie' != get_post_type() ) {
			return $content;
		}

		$movie = get_movie( get_the_ID() );
		$headbox = get_movie_headbox_template( $movie );

		if ( is_single() ) {
			$headbox->set( 'theme', 'default' );
		} elseif ( is_archive() || is_search() ) {
			$headbox->set( 'theme', 'default' );
		}

		return $headbox->render() . $content;
	}

	/**
	 * Register Widgets.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_widgets() {

		$widgets = array(
			'\wpmoly\Widgets\Statistics',
			'\wpmoly\Widgets\Details',
			'\wpmoly\Widgets\Grid'
		);

		foreach ( $widgets as $widget ) {
			if ( class_exists( $widget ) ) {
				register_widget( $widget );
			}
		}
	}

}
