<?php
/**
 * Define the Grid Builder class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Admin;

use wpmoly\Core\Metabox;

/**
 * Provide a tool to create, build, and save grids.
 * 
 * Currently supports movies, actors and genres.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class GridBuilder extends Metabox {

	/**
	 * Current Post ID.
	 * 
	 * @var    int
	 */
	private $post_id = 0;

	/**
	 * Class constructor.
	 * 
	 * Mostly set the grid instance.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function __construct() {
		
		if ( isset( $_REQUEST['post'] ) ) {
			$this->post_id = (int) $_REQUEST['post'];
		} elseif ( isset( $_REQUEST['post_ID'] ) ) {
			$this->post_id = (int) $_REQUEST['post_ID'];
		}
	}

	/**
	 * Define metaboxes.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function add_metaboxes() {

		$this->add_metabox( 'parameters', array(
			'id'            => 'wpmoly-grid-parameters',
			'title'         => __( 'Type', 'wpmovielibrary' ),
			'callback'      => array( $this, 'grid_parameters_metabox' ),
			'screen'        => 'grid',
			'context'       => 'side',
			'priority'      => 'low',
			'callback_args' => null
		) );

		$this->add_metabox( 'preview', array(
			'id'            => 'wpmoly-grid-preview',
			'title'         => __( 'Preview', 'wpmovielibrary' ),
			'callback'      => array( $this, 'grid_preview_metabox' ),
			'screen'        => 'grid',
			'context'       => 'normal',
			'priority'      => 'high',
			'callback_args' => null
		) );

	}

	/**
	 * Define meta managers.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	protected function add_managers() {

		$this->add_manager( 'movie-grid-settings', array(
				'label'     => esc_html__( 'Movies Grid Settings ', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'advanced',
				'priority'  => 'low',
				'sections'  => array(
					'grid-presets' => array(
						'label'    => esc_html__( 'Presets', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-cogs',
						'settings' => array(
							'grid-preset' => array(
								'type'    => 'radio-image',
								'section' => 'grid-presets',
								'label'   => esc_html__( 'Grid preset', 'wpmovielibrary' ),
								'description' => esc_html__( 'Select a preset to apply to the grid. Presets override any filters and ordering settings you might define, be sure to select "Custom" for those settings to be used.', 'wpmovielibrary' ),
								'attr'    => array( 'class' => 'visible-labels half-col' ),
								'choices' => array(
									'alphabetical-movies' => array(
										'label' => esc_html__( 'Alphabetical Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/alphabetical-movies.png'
									),
									'unalphabetical-movies' => array(
										'label' => esc_html__( 'Unalphabetical Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/unalphabetical-movies.png'
									),
									'current-year-movies' => array(
										'label' => esc_html__( 'This Year Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/current-year-movies.png'
									),
									'last-year-movies' => array(
										'label' => esc_html__( 'Last Year Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/last-year-movies.png'
									),
									'last-added-movies' => array(
										'label' => esc_html__( 'Latest Added Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/last-added-movies.png'
									),
									'first-added-movies' => array(
										'label' => esc_html__( 'Earliest Added Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/first-added-movies.png'
									),
									'last-released-movies' => array(
										'label' => esc_html__( 'Latest Released Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/last-released-movies.png'
									),
									'first-released-movies' => array(
										'label' => esc_html__( 'Earliest Released Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/first-released-movies.png'
									),
									'incoming-movies' => array(
										'label' => esc_html__( 'Incoming Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/incoming-movies.png'
									),
									'most-rated-movies' => array(
										'label' => esc_html__( 'Most Rated Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/most-rated-movies.png'
									),
									'least-rated-movies' => array(
										'label' => esc_html__( 'Least Rated Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/least-rated-movies.png'
									),
									'custom' => array(
										'label' => esc_html__( 'Custom', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/custom.png'
									)
								),
								'sanitize' => 'esc_attr'
							)
						)
					),
					'grid-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'grid-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								'default'  => 5
							),
							'grid-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								'default'  => 4
							),
							'grid-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Movie Poster ideal width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for posters. Grid columns will never exceed that width. Default is 160.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								'default'  => 134
							),
							'grid-row-height' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Movie Poster ideal height', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal height for posters. Grid rows will never exceed that height. Tip: that value should be equal to ideal width times 1.5. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								'default'  => 200
							),
							'grid-list-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of list columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid in list mode. Default is 3.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 3
							),
							'grid-list-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Ideal column width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for columns in list mode. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 240
							),
							'grid-list-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of list rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid in list mode. Default is 8.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 8
							),
						)
					),
					'grid-settings' => array(
						'label' => esc_html__( 'Settings', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-settings',
						'settings' => array(
							'grid-enable-pagination' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-pagination',
								'label'    => esc_html__( 'Enable Pagination', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to browse through the grid. Is Ajax browsing is enabled, pagination will use Ajax to dynamically load new pages instead of reloading. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-settings-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable user settings', 'wpmovielibrary' ),
								'description' => esc_html__( 'Visitors will be able to change some settings to browse the grid differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-letter' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable letter filtering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to filter the grid by letters. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-order' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable custom ordering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							)
						),
					),
					/*'grid-customization' => array(
						'label' => esc_html__( 'Customization', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-tools',
						'settings' => array(
							'grid-customs-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable user customizations', 'wpmovielibrary' ),
								'description' => esc_html__( 'Visitors will be able to change some settings to make the grid appear differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array(),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-mode' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid mode', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid mode. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-custom-content' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid content', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid content, ie. number of movies, rows, columns… Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-display' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid display', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid display, ie. showing/hiding titles, ratings, genres… Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							)
						)
					)*/
				)
			)
		);

		$this->add_manager( 'actor-grid-settings', array(
				'label'     => esc_html__( 'Actors Grid Settings', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'advanced',
				'priority'  => 'low',
				'sections'  => array(
					'grid-presets' => array(
						'label'    => esc_html__( 'Presets', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-cogs',
						'settings' => array(
							'grid-preset' => array(
								'type'    => 'radio-image',
								'section' => 'grid-presets',
								'label'   => esc_html__( 'Grid preset', 'wpmovielibrary' ),
								'description' => esc_html__( 'Select a preset to apply to the grid. Presets override any filters and ordering settings you might define, be sure to select "Custom" for those settings to be used.', 'wpmovielibrary' ),
								'attr'    => array( 'class' => 'visible-labels half-col' ),
								'choices' => array(
									'alphabetical-actors' => array(
										'label' => esc_html__( 'Alphabetical Actors', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/alphabetical-actors.png'
									),
									'unalphabetical-actors' => array(
										'label' => esc_html__( 'Unalphabetical Actors', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/unalphabetical-actors.png'
									),
									'alphabetical-persons' => array(
										'label' => esc_html__( 'Alphabetical Persons', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/alphabetical-persons.png'
									),
									'unalphabetical-persons' => array(
										'label' => esc_html__( 'Unalphabetical Persons', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/unalphabetical-persons.png'
									)
								),
								'sanitize' => 'esc_attr'
							),
							'grid-actor-person' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-preset',
								'label'    => esc_html__( 'Use Person', 'wpmovielibrary' ),
								'description' => esc_html__( 'Use the Person data when available?', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
						)
					),
					'grid-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'grid-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 5
							),
							'grid-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 4
							),
							'grid-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Actor picture ideal width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for posters. Grid columns will never exceed that width. Default is 160.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 160
							),
							'grid-row-height' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Actor picture ideal height', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal height for posters. Grid rows will never exceed that height. Tip: that value should be equal to ideal width times 1.5. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 200
							),
							'grid-list-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of list columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid in list mode. Default is 3.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 3
							),
							'grid-list-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Ideal column width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for columns in list mode. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 240
							),
							'grid-list-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of list rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid in list mode. Default is 8.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 8
							),
						)
					),
					'grid-settings' => array(
						'label' => esc_html__( 'Settings', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-settings',
						'settings' => array(
							'grid-enable-pagination' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-pagination',
								'label'    => esc_html__( 'Enable Pagination', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to browse through the grid. Is Ajax browsing is enabled, pagination will use Ajax to dynamically load new pages instead of reloading. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-settings-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable user settings', 'wpmovielibrary' ),
								'description' => esc_html__( 'Visitors will be able to change some settings to browse the grid differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-letter' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable letter filtering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to filter the grid by letters. <strong>Experimental!</strong> Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-custom-order' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable custom ordering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							)
						),
					),
					/*'grid-customization' => array(
						'label' => esc_html__( 'Customization', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-tools',
						'settings' => array(
							'grid-customs-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable user customizations', 'wpmovielibrary' ),
								'description' => esc_html__( 'Visitors will be able to change some settings to make the grid appear differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array(),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-mode' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid mode', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid mode. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-custom-content' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid content', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid content, ie. number of actors, rows, columns… Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-display' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid display', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid display, ie. showing/hiding names, pictures… Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							)
						)
					)*/
				)
			)
		);

		$this->add_manager( 'collection-grid-settings', array(
				'label'     => esc_html__( 'Collections Grid Settings', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'advanced',
				'priority'  => 'low',
				'sections'  => array(
					'grid-presets' => array(
						'label'    => esc_html__( 'Presets', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-cogs',
						'settings' => array(
							'grid-preset' => array(
								'type'    => 'radio-image',
								'section' => 'grid-presets',
								'label'   => esc_html__( 'Grid preset', 'wpmovielibrary' ),
								'description' => esc_html__( 'Select a preset to apply to the grid. Presets override any filters and ordering settings you might define, be sure to select "Custom" for those settings to be used.', 'wpmovielibrary' ),
								'attr'    => array( 'class' => 'visible-labels half-col' ),
								'choices' => array(
									'alphabetical-collections' => array(
										'label' => esc_html__( 'Alphabetical Collections', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/alphabetical-actors.png'
									),
									'unalphabetical-collections' => array(
										'label' => esc_html__( 'Unalphabetical Collections', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/unalphabetical-actors.png'
									)
								),
								'sanitize' => 'esc_attr'
							),
							'grid-collection-person' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-preset',
								'label'    => esc_html__( 'Use Person', 'wpmovielibrary' ),
								'description' => esc_html__( 'Use the Person data when available?', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
						)
					),
					'grid-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'grid-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 5
							),
							'grid-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 4
							),
							'grid-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Actor picture ideal width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for posters. Grid columns will never exceed that width. Default is 160.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 160
							),
							'grid-row-height' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Actor picture ideal height', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal height for posters. Grid rows will never exceed that height. Tip: that value should be equal to ideal width times 1.5. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 200
							),
							'grid-list-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of list columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid in list mode. Default is 3.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 3
							),
							'grid-list-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Ideal column width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for columns in list mode. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 240
							),
							'grid-list-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of list rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid in list mode. Default is 8.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 8
							),
						)
					),
					'grid-settings' => array(
						'label' => esc_html__( 'Settings', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-settings',
						'settings' => array(
							'grid-enable-pagination' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-pagination',
								'label'    => esc_html__( 'Enable Pagination', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to browse through the grid. Is Ajax browsing is enabled, pagination will use Ajax to dynamically load new pages instead of reloading. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-settings-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable user settings', 'wpmovielibrary' ),
								'description' => esc_html__( 'Visitors will be able to change some settings to browse the grid differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-letter' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable letter filtering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to filter the grid by letters. <strong>Experimental!</strong> Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-custom-order' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable custom ordering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							)
						),
					),
					/*'grid-customization' => array(
						'label' => esc_html__( 'Customization', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-tools',
						'settings' => array(
							'grid-customs-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable user customizations', 'wpmovielibrary' ),
								'description' => esc_html__( 'Visitors will be able to change some settings to make the grid appear differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array(),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-mode' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid mode', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid mode. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-custom-content' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid content', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid content, ie. number of collections, rows, columns… Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-display' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid display', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid display, ie. showing/hiding titles, descriptions… Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							)
						)
					)*/
				)
			)
		);

		$this->add_manager( 'genre-grid-settings', array(
				'label'     => esc_html__( 'Genres Grid Settings', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'advanced',
				'priority'  => 'low',
				'sections'  => array(
					'grid-presets' => array(
						'label'    => esc_html__( 'Presets', 'wpmovielibrary' ),
						'icon'     => 'wpmolicon icon-cogs',
						'settings' => array(
							'grid-preset' => array(
								'type'    => 'radio-image',
								'section' => 'grid-presets',
								'label'   => esc_html__( 'Grid preset', 'wpmovielibrary' ),
								'description' => esc_html__( 'Select a preset to apply to the grid. Presets override any filters and ordering settings you might define, be sure to select "Custom" for those settings to be used.', 'wpmovielibrary' ),
								'attr'    => array( 'class' => 'visible-labels half-col' ),
								'choices' => array(
									'alphabetical-genres' => array(
										'label' => esc_html__( 'Alphabetical Genres', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/alphabetical-movies.png'
									),
									'unalphabetical-genres' => array(
										'label' => esc_html__( 'Unalphabetical Genres', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/unalphabetical-movies.png'
									)
								),
								'sanitize' => 'esc_attr'
							)
						)
					),
					'grid-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'grid-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 5
							),
							'grid-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 4
							),
							'grid-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Actor picture ideal width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for posters. Grid columns will never exceed that width. Default is 160.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 160
							),
							'grid-row-height' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Actor picture ideal height', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal height for posters. Grid rows will never exceed that height. Tip: that value should be equal to ideal width times 1.5. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 200
							),
							'grid-list-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of list columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid in list mode. Default is 3.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 3
							),
							'grid-list-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Ideal column width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for columns in list mode. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 240
							),
							'grid-list-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of list rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid in list mode. Default is 8.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 8
							),
						)
					),
					'grid-settings' => array(
						'label' => esc_html__( 'Settings', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-settings',
						'settings' => array(
							'grid-enable-pagination' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-pagination',
								'label'    => esc_html__( 'Enable Pagination', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to browse through the grid. Is Ajax browsing is enabled, pagination will use Ajax to dynamically load new pages instead of reloading. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-settings-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable user settings', 'wpmovielibrary' ),
								'description' => esc_html__( 'Visitors will be able to change some settings to browse the grid differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-letter' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable letter filtering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to filter the grid by letters. <strong>Experimental!</strong> Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-custom-order' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable custom ordering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors to change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							)
						),
					),
					/*'grid-customization' => array(
						'label' => esc_html__( 'Customization', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-tools',
						'settings' => array(
							'grid-customs-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Enable user customizations', 'wpmovielibrary' ),
								'description' => esc_html__( 'Visitors will be able to change some settings to make the grid appear differently. The changes only impact the user’s view and are not kept between visits. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array(),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-mode' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid mode', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid mode. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-custom-content' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid content', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid content, ie. number of genres, rows, columns… Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-custom-display' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-customization',
								'label'    => esc_html__( 'Customize grid display', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid display, ie. showing/hiding titles, descriptions… Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							)
						)
					)*/
				)
			)
		);
	}

	/**
	 * Load frameworks if needed.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function load_meta_frameworks() {

		// Bail if not our post type.
		if ( 'grid' !== get_current_screen()->post_type ) {
			return;
		}

		parent::load_meta_frameworks();
	}

	/**
	 * Grid Builder container opening.
	 * 
	 * Open the grid builder container and show a couple of useful snippets.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function header( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}
?>
		<div id="wpmoly-grid-builder">

			<div id="wpmoly-grid-builder-shortcuts">
				<div id="wpmoly-grid-builder-id">Id: <code><?php the_ID(); ?></code></div>
				<div id="wpmoly-grid-builder-shortcode">ShortCode: <code>[movies id=<?php the_ID(); ?>]</code></div>
			</div>
<?php
	}

	/**
	 * Grid Submit Metabox additional/custom buttons.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function submitbox() {

		global $post;

		if ( 'grid' !== $post->post_type ) {
			return false;
		}
?>
		<div id="customize-action">
			<button type="button" name="customize" id="customize-grid" data-action="customize-grid"><span class="dashicons dashicons-admin-settings"></span></button>
		</div>
		<div id="save-action">
			<button type="submit" name="save" id="save-grid"><span class="dashicons dashicons-upload"></span></button>
		</div>
<?php
	}

	/**
	 * Grid Type Metabox callback.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function grid_parameters_metabox( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}

?>

		<div id="wpmoly-grid-builder-parameters-metabox"></div>

<?php

	}

	/**
	 * Grid Preview metabox.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function grid_preview_metabox( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}

		$type = get_grid_meta( $this->post_id, 'type' );
?>
		<div class="wpmoly grid-builder">
<?php
		if ( empty( $type ) ) {
?>
			<div id="wpmoly-grid-builder-preview" class="grid-builder-preview with-tutorial">
				<div id="wpmoly-grid-builder-tutorial" class="grid-builder-tutorial" data-tutorial-grid="<?php echo esc_attr( $this->post_id ); ?>">
					<div class="tutorial-notice">
						<?php _e( '<strong>This grid is empty!</strong> Follow these steps to create your new grid.', 'wpmovielibrary' ); ?>
						<span></span>
					</div>
					<div class="tutorial-step step-1 clearfix">
						<img class="step-capture" src="<?php echo WPMOLY_URL . 'admin/img/tutorial-grid-type.png'; ?>" alt="" />
						<div class="step-content">
							<h4 class="step-title"><?php _e( '1. Select a grid type', 'wpmovielibrary' ); ?></h4>
							<p class="step-description"><?php _e( 'First, select a type of content for your grid.', 'wpmovielibrary' ); ?></p>
							<span class="step-arrow"></span>
						</div>
					</div>
					<div class="tutorial-step step-2 clearfix">
						<img class="step-capture" src="<?php echo WPMOLY_URL . 'admin/img/tutorial-grid-mode.png'; ?>" alt="" />
						<div class="step-content">
							<h4><?php _e( '2. Select a display mode', 'wpmovielibrary' ); ?></h4>
							<p class="step-description"><?php _e( 'Then chose a display mode among those available for your selected content…', 'wpmovielibrary' ); ?></p>
							<span class="step-arrow"></span>
						</div>
					</div>
					<div class="tutorial-step step-3 clearfix">
						<img class="step-capture" src="<?php echo WPMOLY_URL . 'admin/img/tutorial-grid-theme.png'; ?>" alt="" />
						<div class="step-content">
							<h4><?php _e( '3. Select a theme', 'wpmovielibrary' ); ?></h4>
							<p class="step-description"><?php _e( '… And then a theme to apply to the grid items.', 'wpmovielibrary' ); ?></p>
							<span class="step-arrow"></span>
						</div>
					</div>
					<div class="tutorial-step step-4 clearfix">
						<div class="step-content">
							<h4><?php _e( '4. Pick your settings', 'wpmovielibrary' ); ?></h4>
							<p class="step-description"><?php _e( 'Finally, tune your grid to your liking using the settings!', 'wpmovielibrary' ); ?></p>
							<span class="step-arrow"></span>
						</div>
						<img class="step-capture" src="<?php echo WPMOLY_URL . 'admin/img/tutorial-grid-settings.png'; ?>" alt="" />
					</div>
				</div>
			</div>
<?php
		} else {
?>
			<div id="wpmoly-grid-builder-preview" class="grid-builder-preview">
				<div class="wpmoly grid" data-preview-grid="<?php echo esc_attr( $this->post_id ); ?>"></div>
			</div>
<?php
		}
?>
		</div>
<?php
	}

	/**
	 * Grid Builder container closing.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function footer( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}
?>
		</div><!-- /#wpmoly-grid-builder -->
<?php
	}

	/**
	 * Save the grid settings.
	 * 
	 * Actually does two things. 1) make sure grid type, mode and theme are
	 * saved; it should already be done through Ajax, but let's make sure it
	 * really did. 2) clean up POST data. We're using three ButterBean 
	 * managers with identical control IDs, meaning we have to remove the
	 * unwanted fields corresponding to different types than current type to
	 * avoid having values overflowed by the last manager in the list.
	 * 
	 * @since    3.0
	 * 
	 * @param    int        $post_id Post ID.
	 * @param    WP_Post    $post Post object.
	 * @param    boolean    $update Updating existing post?
	 * 
	 * @return   void
	 */
	public function save( $post_id, $post, $update ) {

		$grid = get_grid( $post_id );

		if ( ! empty( $_POST['_wpmoly_grid_type'] ) ) {
			$type = $_POST['_wpmoly_grid_type'];
			$grid->set_type( $type );
			foreach ( $_POST as $key => $value ) {
				if ( false !== strpos( $key, 'butterbean_' ) && false === strpos( $key, "{$type}-grid-settings" ) ) {
					unset( $_POST[ $key ] );
				}
			}
		}

		if ( ! empty( $_POST['_wpmoly_grid_mode'] ) ) {
			$grid->set_mode( $_POST['_wpmoly_grid_mode'] );
		}

		if ( ! empty( $_POST['_wpmoly_grid_theme'] ) ) {
			$grid->set_theme( $_POST['_wpmoly_grid_theme'] );
		}

		$grid->save();
	}

}
