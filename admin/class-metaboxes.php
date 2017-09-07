<?php
/**
 * Define the Metabox class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Metabox;

/**
 * Create a set of metaboxes for the plugin to display data in a nicer way
 * than standard WP Metaboxes.
 *
 * Also handle the Post Convertor Metabox, if needed.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Metaboxes {

	/**
	 * Plugin Metaboxes
	 *
	 * @since    3.0
	 *
	 * @var      string
	 */
	public $metaboxes;

	/**
	 * Plugin Post Convertor Metabox
	 *
	 * @since    3.0
	 *
	 * @var      array
	 */
	public $convertor;

	public $hooks = array();

	/**
	 * Class constructor.
	 *
	 * @since    3.0
	 */
	public function __construct( $params = array() ) {

		if ( ! is_admin() ) {
			return;
		}

		$metaboxes = array(
			array(
				'id'        => 'wpmoly',
				'title'     => __( 'WordPress Movie Library', 'wpmovielibrary' ),
				'callback'  => array( 'wpmoly\Metabox\Editor_Metabox', 'editor' ),
				'screen'    => 'movie',
				'context'   => 'normal',
				'priority'  => 'high',
				'condition' => null,
				'panels'    => array(
					'meta' => array(
						'title'    => __( 'Metadata', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-meta',
						'callback' => array( 'wpmoly\Metabox\Editor_Metabox', 'meta_panel' ),
					),
					'details' => array(
						'title'    => __( 'Details', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-details',
						'callback' => array( 'wpmoly\Metabox\Editor_Metabox', 'details_panel' ),
					),
					'backdrops' => array(
						'title'    => __( 'Images', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-images-alt',
						'callback' => array( 'wpmoly\Metabox\Editor_Metabox', 'backdrops_panel' ),
					),
					'posters' => array(
						'title'    => __( 'Posters', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-poster',
						'callback' => array( 'wpmoly\Metabox\Editor_Metabox', 'posters_panel' ),
					),
				),
			),
		);

		/**
		 * Filter the plugin metaboxes
		 *
		 * @since    3.0
		 *
		 * @param    array    $metaboxes Available metaboxes parameters
		 */
		$this->metaboxes = apply_filters( 'wpmoly/filter/metaboxes', $metaboxes );

		$this->hooks['actions'] = array();
		$this->hooks['filters'] = array();

		// Instanciate metaboxes
		$this->make();
	}

	/**
	 * Register all of the metaboxes hooks.
	 *
	 * @since    3.0
	 */
	public function define_admin_hooks() {

		foreach ( $this->metaboxes as $metabox ) {

			$metabox->define_admin_hooks();

			// Add metabox
			foreach ( (array) $metabox->screen as $screen ) {
				$this->hooks['actions'][] = array( "add_meta_boxes_{$screen}", $metabox, 'create', null, null );
			}

			// Register hooks
			if ( ! empty( $metabox->actions ) ) {
				foreach ( $metabox->actions as $action ) {
					$this->hooks['actions'][] = $action;
				}
			}

			if ( ! empty( $metabox->filters ) ) {
				foreach ( $metabox->filters as $filter ) {
					$this->hooks['actions'][] = $filter;
				}
			}
		}

		if ( isset( $this->convertor ) ) {
			$this->hooks['actions'][] = array( 'add_meta_boxes', $this, 'add_convertor_meta_box', null, null );
		}

		return $this->hooks;
	}

	/**
	 * Instanciate all defined Metaboxes.
	 *
	 * @since    3.0
	 */
	public function make() {

		foreach ( $this->metaboxes as $slug => $metabox ) {

			$callback = $metabox['callback'];
			if ( ! class_exists( $callback[0] ) || ! method_exists( $callback[0], $callback[1] ) ) {
				continue;
			}

			$callback_class  = $callback[0];
			$callback_method = $callback[1];

			$this->metaboxes[ $slug ] = new $callback_class( $metabox );
		}

		$this->define_admin_hooks();
	}

}
