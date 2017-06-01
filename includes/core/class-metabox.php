<?php
/**
 * Define the Meta Box class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 */

namespace wpmoly\Core;

/**
 * 
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/core
 * @author     Charlie Merland <charlie@caercam.org>
 */
abstract class Metabox {

	/**
	 * Metaboxes.
	 * 
	 * @var    array
	 */
	private $metaboxes = array();

	/**
	 * Post Meta Managers.
	 * 
	 * @var    array
	 */
	private $post_managers = array();

	/**
	 * Term Meta Managers.
	 * 
	 * @var    array
	 */
	private $term_managers = array();

	/**
	 * Define metaboxes.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function add_metaboxes();

	/**
	 * Define meta managers.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	abstract protected function add_managers();

	/**
	 * Add a new metabox.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $metabox_id Metabox ID.
	 * @param    array     $args Metabox parameters.
	 * 
	 * @return   void
	 */
	public function add_metabox( $metabox_id, $args = array() ) {

		$defaults = array(
			'id'            => '',
			'title'         => '',
			'callback'      => '',
			'screen'        => null,
			'context'       => 'advanced',
			'priority'      => 'default',
			'callback_args' => null
		);
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['id'] ) ) {
			$args['id'] = (string) $metabox_id;
		}

		$this->metaboxes[ (string) $metabox_id ] = $args;
	}

	/**
	 * Add a new metadata manager.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $manager_id Manager ID.
	 * @param    array     $args Manager parameters
	 * 
	 * @return   mixed
	 */
	public function add_manager( $manager_id, $args = array() ) {

		if ( ! empty( $args['post_type'] ) ) {
			return $this->add_post_meta_manager( $manager_id, $args );
		} elseif ( ! empty( $args['taxonomy'] ) ) {
			return $this->add_term_meta_manager( $manager_id, $args );
		}

		return false;
	}

	/**
	 * Add a new Term Meta manager.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $manager_id Manager ID.
	 * @param    array     $args Manager parameters
	 * 
	 * @return   array
	 */
	public function add_term_meta_manager( $manager_id, $args = array() ) {

		$defaults = array(
			'label'    => '',
			'taxonomy' => ''
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->term_managers[ (string) $manager_id ] = $args;
	}

	/**
	 * Add a new Post Meta manager.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $manager_id Manager ID.
	 * @param    array     $args Manager parameters
	 * 
	 * @return   array
	 */
	public function add_post_meta_manager( $manager_id, $args = array() ) {

		$defaults = array(
			'label'     => '',
			'post_type' => '',
			'context'   => '',
			'priority'  => ''
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->post_managers[ (string) $manager_id ] = $args;
	}

	/**
	 * Load frameworks.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function load_meta_frameworks() {

		$this->add_managers();

		if ( ! empty( $this->post_managers ) ) {
			$this->load_post_meta_framework();
		}

		if ( ! empty( $this->term_managers ) ) {
			$this->load_term_meta_framework();
		}
	}

	/**
	 * Load ButterBean framework.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function load_post_meta_framework() {

		require_once WPMOLY_PATH . 'vendor/butterbean/butterbean.php';

		// Let's do this thang!
		if ( function_exists( 'butterbean_loader_100' ) ) {
			butterbean_loader_100();
		}
	}

	/**
	 * Load Haricot framework.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	private function load_term_meta_framework() {

		require_once WPMOLY_PATH . 'vendor/haricot/haricot.php';

		// Let's do this thang!
		if ( function_exists( 'haricot_loader_101' ) ) {
			haricot_loader_101();
		}
	}

	/**
	 * Register Post Meta managers.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $butterbean ButterBean instance.
	 * @param    string    $post_type Current Post Type.
	 * 
	 * @return   void
	 */
	public function register_post_meta_managers( $butterbean, $post_type ) {

		/**
		 * Filter Post Meta managers.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     $post_managers Post Meta managers.
		 */
		$managers = apply_filters( "wpmoly/filter/{$post_type}/managers", $this->post_managers );

		foreach ( $managers as $manager_id => $manager ) {

			$sections = $manager['sections'];
			$manager  = array(
				'label'     => $manager['label'],
				'post_type' => $manager['post_type'],
				'context'   => $manager['context'],
				'priority'  => $manager['priority']
			);

			$manager  = $this->register_manager( $butterbean, $manager_id, $manager );
			$sections = $this->register_sections( $manager, $sections );
		}
	}

	/**
	 * Register Term Meta managers.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $haricot Haricot instance.
	 * @param    string    $post_type Current Post Type.
	 * 
	 * @return   void
	 */
	public function register_term_meta_managers( $haricot, $taxonomy ) {

		/**
		 * Filter Term Meta managers.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     $term_managers Term Meta managers.
		 */
		$managers = apply_filters( "wpmoly/filter/{$taxonomy}/managers", $this->term_managers );

		foreach ( $managers as $manager_id => $manager ) {

			$sections = $manager['sections'];
			$manager  = array(
				'label'    => $manager['label'],
				'taxonomy' => $manager['taxonomy']
			);

			$manager  = $this->register_manager( $haricot, $manager_id, $manager );
			$sections = $this->register_sections( $manager, $sections );
		}
	}

	/**
	 * Register Meta manager.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $framework Meta framework instance.
	 * @param    string    $manager_id Manager ID.
	 * @param    string    $manager Manager parameters.
	 * 
	 * @return   object
	 */
	private function register_manager( $framework, $manager_id, $manager ) {

		$framework->register_manager( $manager_id, $manager );

		return $framework->get_manager( $manager_id );
	}

	/**
	 * Register Meta manager sections.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $manager Manager instance.
	 * @param    string    $sections Manager Sections.
	 * 
	 * @return   void
	 */
	private function register_sections( $manager, $sections ) {

		foreach ( $sections as $section_id => $section ) {

			$settings = $section['settings'];
			$section  = array(
				'label' => $section['label'],
				'icon'  => $section['icon']
			);

			$section  = $manager->register_section( $section_id, $section );
			$settings = $this->register_settings( $manager, $section_id, $settings );
		}
	}

	/**
	 * Register Meta manager settings and controls.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $manager Manager instance.
	 * @param    string    $section_id Settings section ID.
	 * @param    string    $settings Section Settings.
	 * 
	 * @return   void
	 */
	private function register_settings( $manager, $section_id, $settings ) {

		foreach ( $settings as $control_id => $control ) {

			$control_id = '_wpmoly_' . str_replace( '-', '_', $control_id );

			$control = (object) $control;
			$manager->register_control(
				$control_id,
				array(
					'section'     => $section_id,
					'type'        => isset( $control->type )        ? $control->type        : false,
					'label'       => isset( $control->label )       ? $control->label       : false,
					'attr'        => isset( $control->attr )        ? $control->attr        : false,
					'choices'     => isset( $control->choices )     ? $control->choices     : false,
					'description' => isset( $control->description ) ? $control->description : false,
					'post_type'   => isset( $control->post_type )   ? $control->post_type   : false,
					'taxonomy'    => isset( $control->taxonomy )    ? $control->taxonomy    : false,
					'size'        => isset( $control->size )        ? $control->size        : false
				)
			);

			$manager->register_setting(
				$control_id,
				array(
					'sanitize_callback' => isset( $control->sanitize ) ? $control->sanitize : false,
					'default'           => isset( $control->default )  ? $control->default  : false,
					'value'             => isset( $control->value )    ? $control->value    : '',
				)
			);
		}
	}

	/**
	 * Register metaboxes.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function add_meta_boxes() {

		$this->add_metaboxes();

		foreach ( $this->metaboxes as $metabox ) {

			$metabox = (object) $metabox;

			foreach ( (array) $metabox->screen as $screen ) {
				add_action( "add_meta_boxes_{$screen}", function() use ( $metabox ) {
					add_meta_box( $metabox->id . '-metabox', $metabox->title, $metabox->callback, $metabox->screen, $metabox->context, $metabox->priority, $metabox->callback_args );
				} );
			}
		}
	}
}
