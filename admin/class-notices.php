<?php
/**
 * Define the Notices class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Admin;

/**
 * Create a register a set of admin notices for the plugin.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Notices {

	private $notices = array();

	/**
	 * Magic method.
	 * 
	 * Provide a default callback for notices. This can be overriden by
	 * simply adding a 'register_{$slug}_notice' method.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $method
	 * @param    array     $arguments
	 * 
	 * @return   void
	 */
	public function __call( $method, $arguments ) {

		$slug = str_replace(
			array( 'register_', '_notice', '_' ),
			array( '', '', '-' ),
			$method
		);

		if ( isset( $this->notices[ $slug ] ) ) {
			$this->register_notice( $slug );
		}
	}

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

		$notices = array(
			'reset-permalinks' => array(
				'type'        => 'info',
				'dismissible' => true,
				'message'     => sprintf( __( 'Changes have been made to the archive pages, permalinks settings need to be update. Simply reload the <a href="%s" target="_blank">Permalinks page</a>, no saving required.', 'wpmovielibrary' ), esc_url( admin_url( 'options-permalink.php' ) ) )
			)
		);

		/**
		 * Filter notices.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     $notices Notices list.
		 * @param    object    Notices instance.
		 */
		$this->notices = apply_filters( 'wpmoly/filter/admin_notices', $notices, $this );
	}

	/**
	 * Register notices.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_notices() {

		foreach ( $this->notices as $id => $notice ) {

			if ( empty( $notice['callback'] ) || ! is_callable( $notice['callback'] ) ) {
				$slug = str_replace( '-', '_', $id );
				$callback = array( $this, "register_{$slug}_notice" );
			} else {
				$callback = $notice['callback'];
			}

			add_action( 'admin_notices', $callback, 10, 1 );
		}
	}

	/**
	 * Register 'reset-permalinks' notice callback.
	 * 
	 * 
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function register_reset_permalinks_notice() {

		$check = get_transient( '_wpmoly_reset_permalinks_notice' );
		if ( ! $check ) {
			return false;
		}

		$this->register_notice( 'reset-permalinks' );
	}

	/**
	 * Register notice callback. Output the actual HTML markup.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $notice_id Notice ID.
	 * 
	 * @return   void
	 */
	private function register_notice( $notice_id ) {

		$notice = $this->get_notice( $notice_id );

		$class = 'notice notice-wpmoly';
		if ( in_array( $notice['type'], array( 'error', 'warning', 'success', 'info' ) ) ) {
			$class .= ' notice-' . $notice['type'];
		}

		if ( true === $notice['dismissible'] ) {
			$class .= ' is-dismissible';
		}

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $notice['message'] );
	}

	/**
	 * Retrieve a specific notice.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $notice_id Notice ID.
	 * 
	 * @return   array
	 */
	public function get_notice( $notice_id ) {

		if ( ! isset( $this->notices[ $notice_id ] ) ) {
			return false;
		}

		return $this->notices[ $notice_id ];
	}

}
