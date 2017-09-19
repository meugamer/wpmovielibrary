<?php
/**
 * Define the Post Types Registrar class.
 *
 * Register required Custom Post Types.
 *
 * @link http://wpmovielibrary.com
 * @since 3.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\registrars;

/**
 * Register the plugin custom post types and custom post statuses.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 * 
 * @author Charlie Merland <charlie@caercam.org>
 */
class Post_Types {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		$post_types = array(
			'movie' => array(
				'labels' => array(
					'name'               => __( 'Movies', 'wpmovielibrary' ),
					'singular_name'      => __( 'Movie', 'wpmovielibrary' ),
					'add_new'            => __( 'Add New', 'wpmovielibrary' ),
					'add_new_item'       => __( 'Add New Movie', 'wpmovielibrary' ),
					'edit_item'          => __( 'Edit Movie', 'wpmovielibrary' ),
					'new_item'           => __( 'New Movie', 'wpmovielibrary' ),
					'all_items'          => __( 'All Movies', 'wpmovielibrary' ),
					'view_item'          => __( 'View Movie', 'wpmovielibrary' ),
					'search_items'       => __( 'Search Movies', 'wpmovielibrary' ),
					'not_found'          => __( 'No movies found', 'wpmovielibrary' ),
					'not_found_in_trash' => __( 'No movies found in Trash', 'wpmovielibrary' ),
					'parent_item_colon'  => '',
					'menu_name'          => __( 'Movie Library', 'wpmovielibrary' ),
				),
				'rewrite' => array(
					'slug' => 'movies',
				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'rest_base'          => 'movies',
				'rest_controller_class' => '\wpmoly\rest\controllers\Movies',
				'show_in_menu'       => 'wpmovielibrary',
				'has_archive'        => 'movies',
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
				'menu_position'      => 2,
				'menu_icon'          => 'dashicons-wpmoly',
			),
			'grid' => array(
				'labels' => array(
					'name'               => __( 'Grids', 'wpmovielibrary' ),
					'singular_name'      => __( 'Grid', 'wpmovielibrary' ),
					'add_new'            => __( 'Add New', 'wpmovielibrary' ),
					'add_new_item'       => __( 'Add New Grid', 'wpmovielibrary' ),
					'edit_item'          => __( 'Edit Grid', 'wpmovielibrary' ),
					'new_item'           => __( 'New Grid', 'wpmovielibrary' ),
					'all_items'          => __( 'Grids', 'wpmovielibrary' ),
					'view_item'          => __( 'View Grid', 'wpmovielibrary' ),
					'search_items'       => __( 'Search Grids', 'wpmovielibrary' ),
					'not_found'          => __( 'No grids found', 'wpmovielibrary' ),
					'not_found_in_trash' => __( 'No grids found in Trash', 'wpmovielibrary' ),
					'parent_item_colon'  => '',
					'menu_name'          => __( 'Grids', 'wpmovielibrary' ),
				),
				'rewrite'            => false,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'rest_controller_class' => '\wpmoly\rest\controllers\Grids',
				'show_in_menu'       => 'wpmovielibrary',
				'has_archive'        => false,
				'supports'           => array( 'title', 'custom-fields' ),
			),
		);

		/**
		 * Filter the Custom Post Types parameters prior to registration.
		 *
		 * @since 3.0.0
		 *
		 * @param array $post_types Post Types list.
		 */
		$this->post_types = apply_filters( 'wpmoly/filter/post_types', $post_types );
	}

	/**
	 * Register Custom Post Types.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_post_types() {

		if ( empty( $this->post_types ) ) {
			return false;
		}

		foreach ( $this->post_types as $slug => $params ) {

			/**
			 * Filter the Custom Post Type parameters prior to registration.
			 *
			 * @since 3.0.0
			 *
			 * @param array $args Post Type args
			 */
			$args = apply_filters( "wpmoly/filter/post_type/{$slug}", $params );

			$args = array_merge( array(
				'labels'             => array(),
				'rewrite'            => true,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_rest'       => true,
				'show_in_menu'       => true,
				'has_archive'        => true,
				'menu_position'      => null,
				'menu_icon'          => null,
				'taxonomies'         => array(),
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			), $args );

			register_post_type( $slug, $args );
		}
	}

	/**
	 * Register Custom Post Statuses.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_post_statuses() {

		$post_statuses = array(
			array(
				'slug' => 'import-draft',
				'args' => array(
					'label'       => _x( 'Imported Draft', 'wpmovielibrary' ),
					'label_count' => _n_noop( 'Imported Draft <span class="count">(%s)</span>', 'Imported Draft <span class="count">(%s)</span>', 'wpmovielibrary' ),
				),
			),
			array(
				'slug' => 'import-queued',
				'args' => array(
					'label'       => _x( 'Queued Movie', 'wpmovielibrary' ),
					'label_count' => _n_noop( 'Queued Movie <span class="count">(%s)</span>', 'Queued Movies <span class="count">(%s)</span>', 'wpmovielibrary' ),
				),
			),
		);

		/**
		 * Filter the Custom Post Statuses parameters prior to registration.
		 *
		 * @since 3.0.0
		 *
		 * @param array $post_statuses Post Statuses list
		 */
		$this->post_statuses = apply_filters( 'wpmoly/filter/post_statuses', $post_statuses );

		foreach ( $this->post_statuses as $post_status ) {

			/**
			 * Filter the Custom Post Status parameters prior to registration.
			 *
			 * @since 3.0.0
			 *
			 * @param array $args Post Status args
			 */
			$args = apply_filters( "wpmoly/filter/post_status/{$post_status['slug']}", $post_status['args'] );
			$args = array_merge( array(
				'label'                     => false,
				'label_count'               => false,
				'public'                    => false,
				'internal'                  => true,
				'private'                   => true,
				'publicly_queryable'        => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => false,
			), $args );

			register_post_status( $post_status['slug'], $args );
		}
	}

}
