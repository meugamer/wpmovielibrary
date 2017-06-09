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

use wpmoly\Core\Assets;

/**
 * The public-facing functionality of the plugin.
 *
 * Register and enqueue public scripts, styles and templates.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Frontend extends Assets {

	/**
	 * Single instance.
	 *
	 * @var    \wpmoly\Frontend
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 * 
	 * @since    3.0
	 * 
	 * @return   \wpmoly\Frontend
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Register scripts.
	 *
	 * @since    3.0
	 */
	protected function register_scripts() {

		// Vendor
		$this->register_script( 'sprintf',           'public/js/sprintf.min.js',           array( 'jquery', 'underscore' ), '1.0.3' );
		$this->register_script( 'underscore-string', 'public/js/underscore.string.min.js', array( 'jquery', 'underscore' ), '3.3.4' );

		// Base
		$this->register_script( 'core',              'public/js/wpmoly.js', array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) );
		$this->register_script( 'utils',             'public/js/wpmoly-utils.js' );

		// Views
		$this->register_script( 'headbox-view',      'public/js/views/headbox.js' );

		// Runners
		$this->register_script( 'grids',             'public/js/wpmoly-grids.js',     array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) );
		$this->register_script( 'headboxes',         'public/js/wpmoly-headboxes.js', array( 'jquery' ) );
	}

	/**
	 * Register frontend stylesheets.
	 *
	 * @since    3.0
	 */
	protected function register_styles() {

		// Plugin-wide normalize
		$this->register_style( 'normalize', 'public/css/wpmoly-normalize-min.css' );

		// Main stylesheet
		$this->register_style( 'core',      'public/css/wpmoly.css' );

		// Common stylesheets
		$this->register_style( 'common',    'public/css/common.css' );
		$this->register_style( 'headboxes', 'public/css/wpmoly-headboxes.css' );
		$this->register_style( 'grids',     'public/css/wpmoly-grids.css' );
		$this->register_style( 'flags',     'public/css/wpmoly-flags.css' );

		// Plugin icon font
		$this->register_style( 'font',      'public/fonts/wpmovielibrary/style.css' );
	}

	/**
	 * Register frontend templates.
	 *
	 * @since    3.0
	 */
	protected function register_templates() {

		$this->register_template( 'grid',                      'public/js/templates/grid/grid.php' );
		$this->register_template( 'grid-menu',                 'public/js/templates/grid/menu.php' );
		$this->register_template( 'grid-customs',              'public/js/templates/grid/customs.php' );
		$this->register_template( 'grid-settings',             'public/js/templates/grid/settings.php' );
		$this->register_template( 'grid-pagination',           'public/js/templates/grid/pagination.php' );

		$this->register_template( 'grid-movie-grid',           'public/templates/grids/content/movie-grid.php' );
		$this->register_template( 'grid-movie-grid-variant-1', 'public/templates/grids/content/movie-grid-variant-1.php' );
		$this->register_template( 'grid-movie-grid-variant-2', 'public/templates/grids/content/movie-grid-variant-2.php' );
		$this->register_template( 'grid-movie-list',           'public/templates/grids/content/movie-list.php' );
		$this->register_template( 'grid-actor-grid',           'public/templates/grids/content/actor-grid.php' );
		$this->register_template( 'grid-actor-list',           'public/templates/grids/content/actor-list.php' );
		$this->register_template( 'grid-collection-grid',      'public/templates/grids/content/collection-grid.php' );
		$this->register_template( 'grid-collection-list',      'public/templates/grids/content/collection-list.php' );
		$this->register_template( 'grid-genre-grid',           'public/templates/grids/content/genre-grid.php' );
		$this->register_template( 'grid-genre-list',           'public/templates/grids/content/genre-list.php' );

		$this->register_template( 'grid-actor-archive',        'public/templates/headboxes/actor-default.php' );
		$this->register_template( 'grid-collection-archive',   'public/templates/headboxes/collection-default.php' );
		$this->register_template( 'grid-genre-archive',        'public/templates/headboxes/genre-default.php' );
		$this->register_template( 'grid-movie-archive',        'public/templates/headboxes/movie-default.php' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * 
	 * @since    3.0
	 */
	public function enqueue_scripts() {

		$this->register_scripts();

		// Vendor
		$this->enqueue_script( 'sprintf' );
		$this->enqueue_script( 'underscore-string' );

		// Base
		$this->enqueue_script( 'core' );
		$this->enqueue_script( 'utils' );

		// Views
		$this->enqueue_script( 'headbox-view' );

		// Runners
		$this->enqueue_script( 'grids' );
		$this->enqueue_script( 'headboxes' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * 
	 * @since    3.0
	 */
	public function enqueue_styles() {

		$this->register_styles();

		$this->enqueue_style( 'core' );
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'headboxes' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'flags' );
		$this->enqueue_style( 'font' );
	}

	/**
	 * Print the JavaScript templates for the frontend area.
	 * 
	 * TODO try not to include this where it's not needed.
	 * 
	 * @since    3.0
	 */
	public function enqueue_templates() {

		$this->register_templates();

		$this->enqueue_template( 'grid' );
		$this->enqueue_template( 'grid-menu' );
		$this->enqueue_template( 'grid-customs' );
		$this->enqueue_template( 'grid-settings' );
		$this->enqueue_template( 'grid-pagination' );

		$this->enqueue_template( 'grid-movie-grid' );
		$this->enqueue_template( 'grid-movie-grid-variant-1' );
		$this->enqueue_template( 'grid-movie-grid-variant-2' );
		$this->enqueue_template( 'grid-movie-list' );
		$this->enqueue_template( 'grid-actor-grid' );
		$this->enqueue_template( 'grid-actor-list' );
		$this->enqueue_template( 'grid-collection-grid' );
		$this->enqueue_template( 'grid-collection-list' );
		$this->enqueue_template( 'grid-genre-grid' );
		$this->enqueue_template( 'grid-genre-list' );

		$this->enqueue_template( 'grid-actor-archive' );
		$this->enqueue_template( 'grid-collection-archive' );
		$this->enqueue_template( 'grid-genre-archive' );
		$this->enqueue_template( 'grid-movie-archive' );
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

		// Meta Formatting
		$loader->add_filter( 'wpmoly/filter/the/movie/actors',               '', 'get_formatted_movie_cast',               15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/adult',                '', 'get_formatted_movie_adult',              15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/author',               '', 'get_formatted_movie_author',             15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/budget',               '', 'get_formatted_movie_budget',             15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/certification',        '', 'get_formatted_movie_certification',      15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/composer',             '', 'get_formatted_movie_composer',           15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/director',             '', 'get_formatted_movie_director',           15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/homepage',             '', 'get_formatted_movie_homepage',           15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/cast',                 '', 'get_formatted_movie_cast',               15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/genres',               '', 'get_formatted_movie_genres',             15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/imdb_id',              '', 'get_formatted_movie_imdb_id',            15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/local_release_date',   '', 'get_formatted_movie_local_release_date', 15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/photography',          '', 'get_formatted_movie_photography',        15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/production_countries', '', 'get_formatted_movie_countries',          15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/production_companies', '', 'get_formatted_movie_production',         15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/producer',             '', 'get_formatted_movie_producer',           15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/release_date',         '', 'get_formatted_movie_release_date',       15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/revenue',              '', 'get_formatted_movie_revenue',            15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/runtime',              '', 'get_formatted_movie_runtime',            15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/spoken_languages',     '', 'get_formatted_movie_spoken_languages',   15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/tmdb_id',              '', 'get_formatted_movie_tmdb_id',            15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/writer',               '', 'get_formatted_movie_writer',             15, 2 );
		$loader->add_filter( 'wpmoly/filter/the/movie/year',                 '', 'get_formatted_movie_year',               15, 2 );

		// Meta Permalinks
		$loader->add_filter( 'wpmoly/filter/meta/adult/url',              '', 'get_movie_adult_url',            15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/author/url',             '', 'get_movie_author_url',           15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/certification/url',      '', 'get_movie_certification_url',    15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/composer/url',           '', 'get_movie_composer_url',         15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/homepage/url',           '', 'get_movie_homepage_url',         15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/imdb_id/url',            '', 'get_movie_imdb_id_url',          15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/local_release_date/url', '', 'get_movie_release_date_url',     15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/photography/url',        '', 'get_movie_photography_url',      15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/producer/url',           '', 'get_movie_producer_url',         15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/production/url',         '', 'get_movie_production_url',       15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/country/url',            '', 'get_movie_country_url',          15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/release_date/url',       '', 'get_movie_release_date_url',     15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/spoken_languages/url',   '', 'get_movie_spoken_languages_url', 15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/tmdb_id/url',            '', 'get_movie_tmdb_id_url',          15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/writer/url',             '', 'get_movie_writer_url',           15, 2 );
		$loader->add_filter( 'wpmoly/filter/meta/year/url',               '', 'get_movie_year_url',             15, 2 );

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
