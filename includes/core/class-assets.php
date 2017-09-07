<?php
/**
 * .
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * .
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Assets {

	/**
	 * Class constructor.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	final public function __construct() {

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

		if ( ! isset( $wpmoly_templates ) ) {
			$wpmoly_templates = array();
		}
	}

	/**
	 * Register scripts.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	abstract protected function register_scripts();

	/**
	 * Register stylesheets.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	abstract protected function register_styles();

	/**
	 * Register templates.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	abstract protected function register_templates();

	/**
	 * Enqueue stylesheets.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	abstract public function enqueue_scripts();

	/**
	 * Enqueue stylesheets.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	abstract public function enqueue_styles();

	/**
	 * Enqueue templates.
	 *
	 * @since    3.0
	 *
	 * @return   null
	 */
	abstract public function enqueue_templates();

	/**
	 * Register single script.
	 *
	 * @since    3.0
	 *
	 * @param    string     $handle Script handle name.
	 * @param    string     $src Script full URL.
	 * @param    array      $deps Script dependencies.
	 * @param    string     $version Script version.
	 * @param    boolean    $footer Include in footer?
	 *
	 * @return   boolean
	 */
	protected function register_script( $handle, $src, $deps = array(), $version = false, $in_footer = true ) {

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
	 * Register single stylesheet.
	 *
	 * @since    3.0
	 *
	 * @param    string    $handle Stylesheet handle name.
	 * @param    string    $src Stylesheet full URL.
	 * @param    array     $deps Stylesheet dependencies.
	 * @param    string    $version Stylesheet version.
	 * @param    string    $media Stylesheet media.
	 *
	 * @return   boolean
	 */
	protected function register_style( $handle, $src, $deps = array(), $version = false, $media = 'all' ) {

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
	 * @since    3.0
	 *
	 * @return   null
	 */
	protected function register_template( $handle, $src ) {

		global $wpmoly_templates;

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		$wpmoly_templates[ $handle ] = wpmoly_get_js_template( $src );
	}

	/**
	 * Enqueue single script.
	 *
	 * @since    3.0
	 *
	 * @param    string    $handle Script handle name.
	 *
	 * @return   null
	 */
	protected function enqueue_script( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		wp_enqueue_script( $handle );
	}

	/**
	 * Enqueue single stylesheet.
	 *
	 * @since    3.0
	 *
	 * @param    string    $handle Stylesheet handle name.
	 *
	 * @return   null
	 */
	protected function enqueue_style( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		wp_enqueue_style( $handle );
	}

	/**
	 * Enqueue single template.
	 *
	 * @since    3.0
	 *
	 * @param    string    $handle Template handle name.
	 *
	 * @return   null
	 */
	protected function enqueue_template( $handle ) {

		/** This filter is defined in includes/core/class-assets.php */
		$handle = apply_filters( 'wpmoly/filter/assets/handle', $handle );

		global $wpmoly_templates;

		if ( ! isset( $wpmoly_templates[ $handle ] ) || ! $wpmoly_templates[ $handle ] instanceof \wpmoly\Templates\Template ) {
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
	 * @since    3.0
	 *
	 * @param    string    $handle Asset handle.
	 *
	 * @return   string
	 */
	public function prefix_handle( $handle ) {

		return WPMOLY_SLUG . '-' . $handle;
	}

	/**
	 * Prefix the Asset URL with plugin URL.
	 *
	 * @since    3.0
	 *
	 * @param    string    $src Asset URL.
	 *
	 * @return   string
	 */
	public function prefix_src( $src ) {

		return WPMOLY_URL . $src;
	}

	/**
	 * Set a default Asset version is needed.
	 *
	 * @since    3.0
	 *
	 * @param    string    $version Asset version.
	 *
	 * @return   string
	 */
	public function default_version( $version ) {

		if ( empty( $version ) ) {
			$version = WPMOLY_VERSION;
		}

		return $version;
	}

}
