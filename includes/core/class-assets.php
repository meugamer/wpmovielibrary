<?php
/**
 * .
 *
 * @link http://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\core;

/**
 * .
 *
 * @package WPMovieLibrary
 * 
 * @author Charlie Merland <charlie@caercam.org>
 */
class Assets {

	/**
	 * The single instance of the class.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var Library
	 */
	private static $_instance = null;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return \wpmoly\Library
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new static;
			self::$_instance->init();
		}
		
		return self::$_instance;
	}

	/**
	 * Initialize core.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {

		global $wpmoly_templates;

		if ( ! has_filter( 'wpmoly/filter/assets/handle' ) ) {
			add_filter( 'wpmoly/filter/assets/handle', array( $this, 'prefix_handle' ) );
		}

		if ( ! has_filter( 'wpmoly/filter/assets/src' ) ) {
			add_filter( 'wpmoly/filter/assets/src', array( $this, 'prefix_src' ) );
		}

		if ( ! has_filter( 'wpmoly/filter/assets/version' ) ) {
			add_filter( 'wpmoly/filter/assets/version', array( $this, 'default_version' ) );
		}

		add_filter( 'wpmoly/filter/admin/style/src',   array( $this, 'prefix_admin_style_src' ) );
		add_filter( 'wpmoly/filter/admin/script/src',  array( $this, 'prefix_admin_script_src' ) );
		add_filter( 'wpmoly/filter/public/style/src',  array( $this, 'prefix_public_style_src' ) );
		add_filter( 'wpmoly/filter/public/script/src', array( $this, 'prefix_public_script_src' ) );

		if ( ! isset( $wpmoly_templates ) ) {
			$wpmoly_templates = array();
		}
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_admin_scripts() {

		if ( ! is_admin() ) {
			return false;
		}

		$this->register_admin_scripts();

		
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_scripts() {

		$this->register_public_scripts();

		// Vendor
		$this->enqueue_script( 'sprintf' );
		$this->enqueue_script( 'underscore-string' );

		// Base
		$this->enqueue_script( 'core' );
		$this->enqueue_script( 'utils' );

		// Runners
		$this->enqueue_script( 'grids' );
		$this->enqueue_script( 'headboxes' );
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_admin_styles() {

		if ( ! is_admin() ) {
			return false;
		}

		$this->register_admin_styles();

		
	}

	/**
	 * Enqueue stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_styles() {

		$this->register_public_styles();

		$this->enqueue_style( 'core' );
		$this->enqueue_style( 'common' );
		$this->enqueue_style( 'headboxes' );
		$this->enqueue_style( 'grids' );
		$this->enqueue_style( 'flags' );
		$this->enqueue_style( 'font' );
	}

	/**
	 * Enqueue templates.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_admin_templates() {

		if ( ! is_admin() ) {
			return false;
		}

		$this->register_admin_templates();

		
	}

	/**
	 * Enqueue templates.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function enqueue_public_templates() {

		$this->register_public_templates();

		$this->enqueue_template( 'grid' );
		$this->enqueue_template( 'grid-menu' );
		$this->enqueue_template( 'grid-customs' );
		$this->enqueue_template( 'grid-settings' );
		$this->enqueue_template( 'grid-pagination' );

		$this->enqueue_template( 'grid-empty' );
		$this->enqueue_template( 'grid-error' );

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
	}

	/**
	 * Register scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_scripts() {

		if ( ! is_admin() ) {
			return false;
		}

		
	}

	/**
	 * Register scripts.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_scripts() {

		// Vendor
		$this->add_public_js( 'sprintf',           'sprintf.min.js',           array( 'jquery', 'underscore' ), '1.0.3' );
		$this->add_public_js( 'underscore-string', 'underscore.string.min.js', array( 'jquery', 'underscore' ), '3.3.4' );

		// Base
		$this->add_public_js( 'core',  'wpmoly.js', array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) );
		$this->add_public_js( 'utils', 'wpmoly-utils.js' );

		// Runners
		$this->add_public_js( 'grids',     'wpmoly-grids.js',     array( 'jquery', 'underscore', 'backbone', 'wp-backbone', 'wp-api' ) );
		$this->add_public_js( 'headboxes', 'wpmoly-headboxes.js', array( 'jquery', 'underscore', 'backbone', 'wp-backbone' ) );
	}

	/**
	 * Register stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_styles() {

		if ( ! is_admin() ) {
			return false;
		}

		
	}

	/**
	 * Register stylesheets.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_styles() {

		// Plugin-wide normalize
		$this->register_style( 'normalize', 'public/assets/css/wpmoly-normalize-min.css' );

		// Main stylesheet
		$this->register_style( 'core',      'public/assets/css/wpmoly.css' );

		// Common stylesheets
		$this->register_style( 'common',    'public/assets/css/common.css' );
		$this->register_style( 'headboxes', 'public/assets/css/wpmoly-headboxes.css' );
		$this->register_style( 'grids',     'public/assets/css/wpmoly-grids.css' );
		$this->register_style( 'flags',     'public/assets/css/wpmoly-flags.css' );

		// Plugin icon font
		$this->register_style( 'font',      'public/assets/fonts/wpmovielibrary/style.css' );
	}

	/**
	 * Register templates.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_admin_templates() {

		if ( ! is_admin() ) {
			
		}

		
	}

	/**
	 * Register templates.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function register_public_templates() {

		$this->register_template( 'grid',                      'public/assets/js/templates/grid/grid.php' );
		$this->register_template( 'grid-menu',                 'public/assets/js/templates/grid/menu.php' );
		$this->register_template( 'grid-customs',              'public/assets/js/templates/grid/customs.php' );
		$this->register_template( 'grid-settings',             'public/assets/js/templates/grid/settings.php' );
		$this->register_template( 'grid-pagination',           'public/assets/js/templates/grid/pagination.php' );

		$this->register_template( 'grid-error',                'public/assets/js/templates/grid/content/error.php' );
		$this->register_template( 'grid-empty',                'public/assets/js/templates/grid/content/empty.php' );

		$this->register_template( 'grid-movie-grid',           'public/assets/js/templates/grid/content/movie-grid.php' );
		$this->register_template( 'grid-movie-grid-variant-1', 'public/assets/js/templates/grid/content/movie-grid-variant-1.php' );
		$this->register_template( 'grid-movie-grid-variant-2', 'public/assets/js/templates/grid/content/movie-grid-variant-2.php' );
		$this->register_template( 'grid-movie-list',           'public/assets/js/templates/grid/content/movie-list.php' );
		$this->register_template( 'grid-actor-grid',           'public/assets/js/templates/grid/content/actor-grid.php' );
		$this->register_template( 'grid-actor-list',           'public/assets/js/templates/grid/content/actor-list.php' );
		$this->register_template( 'grid-collection-grid',      'public/assets/js/templates/grid/content/collection-grid.php' );
		$this->register_template( 'grid-collection-list',      'public/assets/js/templates/grid/content/collection-list.php' );
		$this->register_template( 'grid-genre-grid',           'public/assets/js/templates/grid/content/genre-grid.php' );
		$this->register_template( 'grid-genre-list',           'public/assets/js/templates/grid/content/genre-list.php' );
	}

	/**
	 * Register an admin style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 */
	private function add_admin_css( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/**
		 * Filter the admin style URL.
		 *
		 * @since    3.0
		 *
		 * @param    string    $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/admin/style/src', $src );

		$this->register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register a public style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 */
	private function add_public_css( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/**
		 * Filter the public style URL.
		 *
		 * @since    3.0
		 *
		 * @param    string    $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/public/style/src', $src );

		$this->register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register an admin script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 */
	private function add_admin_js( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the admin script URL.
		 *
		 * @since    3.0
		 *
		 * @param    string    $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/admin/script/src', $src );

		$this->register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register a public script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 */
	private function add_public_js( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the public script URL.
		 *
		 * @since    3.0
		 *
		 * @param    string    $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/public/script/src', $src );

		$this->register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register single script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string  $handle    Script handle name.
	 * @param string  $src       Script full URL.
	 * @param array   $deps      Script dependencies.
	 * @param string  $version   Script version.
	 * @param boolean $in_footer Include in footer?
	 *
	 * @return boolean
	 */
	private function register_script( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

		/**
		 * Filter the Asset handle.
		 *
		 * @since    3.0
		 *
		 * @param    string    $handle Asset handle.
		 */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		/**
		 * Filter the Asset URL.
		 *
		 * @since    3.0
		 *
		 * @param    string    $src Asset URL.
		 */
		$src = apply_filters( 'wpmoly/filter/assets/src', $src );

		/**
		 * Filter the Asset version.
		 *
		 * @since    3.0
		 *
		 * @param    string    $version Asset version.
		 */
		$version = apply_filters( 'wpmoly/filter/assets/version', $version );

		return wp_register_script( $handle, $src, $deps, $version, $in_footer );
	}

	/**
	 * Register single style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle  Style handle name.
	 * @param string $src     Style full URL.
	 * @param array  $deps    Style dependencies.
	 * @param string $version Style version.
	 * @param string $media   Style media.
	 *
	 * @return boolean
	 */
	private function register_style( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		/** This filter is defined in includes/core/class-assets.php */
		$src = apply_filters( 'wpmoly/filter/assets/src', $src );

		/** This filter is defined in includes/core/class-assets.php */
		$version = apply_filters( 'wpmoly/filter/assets/version', $version );

		return wp_register_style( $handle, $src, $deps, $version, $media );
	}

	/**
	 * Register single template.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Template handle name.
	 * @param string $src    Template URL.
	 */
	private function register_template( $handle, $src ) {

		global $wpmoly_templates;

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		$wpmoly_templates[ $handle ] = wpmoly_get_js_template( $src );
	}

	/**
	 * Enqueue single script.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Script handle name.
	 */
	private function enqueue_script( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		wp_enqueue_script( $handle );
	}

	/**
	 * Enqueue single style.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Style handle name.
	 */
	private function enqueue_style( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		wp_enqueue_style( $handle );
	}

	/**
	 * Enqueue single template.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 *
	 * @param string $handle Template handle name.
	 */
	private function enqueue_template( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		global $wpmoly_templates;

		if ( ! isset( $wpmoly_templates[ $handle ] ) || ! $wpmoly_templates[ $handle ] instanceof \wpmoly\templates\Template ) {
			return false;
		}
?>
	<script type="text/html" id="tmpl-<?php echo esc_attr( $handle ); ?>"><?php $wpmoly_templates[ $handle ]->render( 'always' ); ?>
	</script>

<?php
	}

	/**
	 * Prefix the Asset handle with plugin slug.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset handle.
	 *
	 * @return string
	 */
	public function prefix_handle( $handle ) {

		return "wpmoly-{$handle}";
	}

	/**
	 * Prefix the styles URL with admin folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_admin_style_src( $src ) {

		return "admin/assets/css/{$src}";
	}

	/**
	 * Prefix the scripts URL with admin folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_admin_script_src( $src ) {

		return "admin/assets/js/{$src}";
	}

	/**
	 * Prefix the styles URL with public folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_public_style_src( $src ) {

		return "public/assets/css/{$src}";
	}

	/**
	 * Prefix the scripts URL with public folder.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_public_script_src( $src ) {

		return "public/assets/js/{$src}";
	}

	/**
	 * Prefix the Asset URL with plugin URL.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset URL.
	 *
	 * @return string
	 */
	public function prefix_src( $src ) {

		return WPMOLY_URL . $src;
	}

	/**
	 * Set a default Asset version is needed.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $src Asset Version.
	 *
	 * @return string
	 */
	public function default_version( $version ) {

		if ( empty( $version ) ) {
			$version = WPMOLY_VERSION;
		}

		return $version;
	}

}
