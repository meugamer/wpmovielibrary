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
	 * Grid instance.
	 * 
	 * @var    Grid
	 */
	private $grid;

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

		$this->add_metabox( 'type', array(
			'id'            => 'wpmoly-grid-type',
			'title'         => __( 'Type', 'wpmovielibrary' ),
			'callback'      => array( $this, 'type_metabox' ),
			'screen'        => 'grid',
			'context'       => 'side',
			'priority'      => 'high',
			'callback_args' => null
		) );

		$this->add_manager( 'movie-grid-settings', array(
				'label'     => esc_html__( 'Réglages', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'normal',
				'priority'  => 'high',
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
					/*'grid-content' => array(
						'label'    => esc_html__( 'Content', 'wpmovielibrary' ),
						'icon'     => 'dashicons-filter',
						'settings' => array(
							'grid-total' => array(
								'type'     => 'text',
								'section'  => 'grid-content',
								'label'    => esc_html__( 'Number of movies', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of movies for the grid. Setting a number of movie will result in the rows number to be ignored. Default is 5.', 'wpmovielibrary' ),
								'attr'     => array( 'size' => '2' ),
								'sanitize' => 'intval',
								'default'  => 5
							),
							'text' => array(
								'type'     => 'text',
								'section'  => 'grid-content',
								'label'    => esc_html__( 'Text input', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'widefat' ),
								'sanitize' => 'wp_filter_nohtml_kses'
							)
						)
					),
					'grid-ordering' => array(
						'label' => esc_html__( 'Ordering', 'wpmovielibrary' ),
						'icon'  => 'dashicons-randomize',
						'settings' => array(
							'grid-order-by' => array(
								'type'     => 'select',
								'section'  => 'grid-ordering',
								'label'    => esc_html__( 'Order By…', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'choices' => array(
									'post-date'           => esc_html__( 'Post Date', 'wpmovielibrary' ),
									'released-date'       => esc_html__( 'Release Date', 'wpmovielibrary' ),
									'local-released-date' => esc_html__( 'Local Release Date', 'wpmovielibrary' ),
									'rating'              => esc_html__( 'Rating', 'wpmovielibrary' ),
									'alpabetical'         => esc_html__( 'Alphabetically', 'wpmovielibrary' ),
									'random'              => esc_html__( 'Random', 'wpmovielibrary' ),
								),
								'sanitize' => 'esc_attr'
							),
							'grid-order' => array(
								'type'     => 'select',
								'section'  => 'grid-ordering',
								'label'    => esc_html__( 'Order', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'choices' => array(
									'asc'  => esc_html__( 'Ascendingly', 'wpmovielibrary' ),
									'desc' => esc_html__( 'Descendingly', 'wpmovielibrary' ),
								),
								'sanitize' => 'esc_attr'
							)
						)
					),*/
					'grid-appearance' => array(
						'label' => esc_html__( 'Appearance', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-appearance',
						'settings' => array(
							'grid-columns' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 5
							),
							'grid-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 4
							),
							'grid-column-width' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Movie Poster ideal width', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal width for posters. Grid columns will never exceed that width. Default is 160.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 160
							),
							'grid-row-height' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Movie Poster ideal height', 'wpmovielibrary' ),
								'description' => esc_html__( 'Ideal height for posters. Grid rows will never exceed that height. Tip: that value should be equal to ideal width times 1.5. Default is 240.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 240
							),
						)
					),
					'grid-controls' => array(
						'label' => esc_html__( 'User Control', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-tools',
						'settings' => array(
							'grid-show-menu' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Show Menu', 'wpmovielibrary' ),
								'description' => esc_html__( 'Enable the grid menu. Visitors will be able to change some settings to alter the grid appearance to their liking. The changes are local not persitent and will never be stored anywhere on your site. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array(),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-mode-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Mode', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid mode. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-content-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Content', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid content, ie. number of movies, rows, columns… Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-display-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Display', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid display, ie. showing/hiding titles, ratings, genres… Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-order-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Ordering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-show-pagination' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Show Pagination', 'wpmovielibrary' ),
								'description' => esc_html__( 'Enable the pagination menu for visitors. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							)
						)
					)
				)
			)
		);

		$this->add_manager( 'actor-grid-settings', array(
				'label'     => esc_html__( 'Réglages', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'normal',
				'priority'  => 'high',
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
								'label'    => esc_html__( 'Number of rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 5
							),
							'grid-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
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
						)
					),
					'grid-controls' => array(
						'label' => esc_html__( 'User Control', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-tools',
						'settings' => array(
							'grid-show-menu' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Show Menu', 'wpmovielibrary' ),
								'description' => esc_html__( 'Enable the grid menu. Visitors will be able to change some settings to alter the grid appearance to their liking. The changes are local not persitent and will never be stored anywhere on your site. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array(),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-mode-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Mode', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid mode. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-content-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Content', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid content, ie. number of actors, rows, columns… Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-display-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Display', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid display. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-order-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Ordering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-show-pagination' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Show Pagination', 'wpmovielibrary' ),
								'description' => esc_html__( 'Enable the pagination menu for visitors. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							)
						)
					)
				)
			)
		);

		$this->add_manager( 'genre-grid-settings', array(
				'label'     => esc_html__( 'Réglages', 'wpmovielibrary' ),
				'post_type' => 'grid',
				'context'   => 'normal',
				'priority'  => 'high',
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
								'label'    => esc_html__( 'Number of rows', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of rows for the grid. Default is 5.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '2' ),
								//'sanitize' => 'intval',
								'default'  => 5
							),
							'grid-rows' => array(
								'type'     => 'text',
								'section'  => 'grid-appearance',
								'label'    => esc_html__( 'Number of columns', 'wpmovielibrary' ),
								'description' => esc_html__( 'Number of columns for the grid. Default is 4.', 'wpmovielibrary' ),
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
						)
					),
					'grid-controls' => array(
						'label' => esc_html__( 'User Control', 'wpmovielibrary' ),
						'icon'  => 'dashicons-admin-tools',
						'settings' => array(
							'grid-show-menu' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Show Menu', 'wpmovielibrary' ),
								'description' => esc_html__( 'Enable the grid menu. Visitors will be able to change some settings to alter the grid appearance to their liking. The changes are local not persitent and will never be stored anywhere on your site. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array(),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-mode-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Mode', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid mode. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-content-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Content', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid content, ie. number of genres, rows, columns… Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-display-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Display', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid display. Default is disabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 0
							),
							'grid-order-control' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Grid Ordering', 'wpmovielibrary' ),
								'description' => esc_html__( 'Allow visitors can change the grid ordering, ie. the sorting and ordering settings. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							),
							'grid-show-pagination' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-controls',
								'label'    => esc_html__( 'Show Pagination', 'wpmovielibrary' ),
								'description' => esc_html__( 'Enable the pagination menu for visitors. Default is enabled.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'sanitize' => '_is_bool',
								'default'  => 1
							)
						)
					)
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

		$grid = get_grid( $post->ID );
?>
		<div id="wpmoly-grid-builder">

			<script type="text/javascript">var _wpmolyGridBuilderData = <?php echo $grid->toJSON(); ?>;</script>
			<?php wp_nonce_field( 'save-grid-setting', 'wpmoly_save_grid_setting_nonce', $referer = false ); ?>

			<div id="wpmoly-grid-builder-shortcuts">
				<div id="wpmoly-grid-builder-id">Id: <code><?php the_ID(); ?></code></div>
				<div id="wpmoly-grid-builder-shortcode">ShortCode: <code>[movies id=<?php the_ID(); ?>]</code></div>
			</div>
<?php
	}

	/**
	 * Add a separator before ButterBean metaboxes.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $manager Manager instance.
	 * @param    object    $post Current Post instance.
	 * @param    array     $metabox Current Metabox properties.
	 * @param    object    $butterbean ButterBean instance.
	 * 
	 * @return   void
	 */
	public function separator( $manager, $post, $metabox, $butterbean ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}

		/*if ( ! in_array( $manager->name, array_keys( $this->get_managers() ) ) ) {
			return false;
		}*/
?>
		<div class="grid-builder-separator">
			<div class="button separator-label"><?php _e( 'Settings' ); ?></div>
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
	public function type_metabox( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}
?>

		<script id="tmpl-wpmoly-grid-builder-type-metabox" type="text/html">

		<div class="grid-builder-separator">
			<div class="button separator-label"><?php _e( 'Type' ); ?></div>
		</div>

		<input type="hidden" name="_wpmoly_grid_type" value="{{ data.type }}" />
		<input type="hidden" name="_wpmoly_grid_mode" value="{{ data.mode }}" />
		<input type="hidden" name="_wpmoly_grid_theme" value="{{ data.theme }}" />

		<div id="grid-types" class="supported-grid-types active">
			<# _.each( data.types, function( type, type_id ) { #>
			<button type="button" data-action="grid-type" data-value="{{ type_id }}" title="{{ type.label }}" class="<# if ( type_id == data.type ) { #>active<# } #>"><span class="{{ type.icon }}"></span></button>
			<# } ); #>
			<div class="clear"></div>
		</div>

		<div class="grid-builder-separator">
			<div class="button separator-label"><?php _e( 'Mode' ); ?></div>
		</div>

		<# _.each( data.types, function( type, type_id ) { #>
			<# if ( type_id == data.type ) { #>
		<div id="{{ type_id }}-grid-modes" class="supported-grid-modes active">
			<# _.each( data.modes[ type_id ], function( mode, mode_id ) { #>
			<button type="button" data-action="grid-mode" data-value="{{ mode_id }}" title="{{ mode.label }}" class="<# if ( mode_id == data.mode ) { #>active<# } #>"><span class="{{ mode.icon }}"></span></button>
			<# } ); #>
			<div class="clear"></div>
		</div>
			<# } #>
		<# } ); #>

		<div class="grid-builder-separator">
			<div class="button separator-label"><?php _e( 'Theme' ); ?></div>
		</div>

		<# _.each( data.types, function( type, type_id ) { #>
			<# if ( type_id == data.type ) { #>
				<# _.each( data.modes[ type_id ], function( mode, mode_id ) { #>
					<# if ( mode_id == data.mode ) { #>
		<div id="{{ type_id }}-grid-{{ mode_id }}-mode-themes" class="supported-grid-themes active">
						<# _.each( data.themes[ type_id ][ mode_id ], function( theme, theme_id ) { #>
			<button type="button" data-action="grid-theme" data-value="{{ theme_id }}" title="{{ theme.label }}" class="<# if ( theme_id == data.theme ) { #>active<# } #>"><span class="{{ theme.icon }}"></span></button>
						<# } ); #>
					<# } #>
			<div class="clear"></div>
		</div>
				<# } ); #>
			<# } #>
		<# } ); #>

		</script>

		<div id="wpmoly-grid-builder-type-metabox"></div>

		<div class="grid-builder-separator">
			<div class="button separator-label"><?php _e( 'Save' ); ?></div>
		</div>

<?php
	}

	/**
	 * Grid Preview editor toolbox.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $post Current Post instance.
	 * 
	 * @return   void
	 */
	public function preview( $post ) {

		if ( 'grid' !== $post->post_type ) {
			return false;
		}

		// Grid template setup
		//$template = get_grid_template( $this->grid );

?>
		<div class="wpmoly">
			<div class="grid-builder-separator">
				<button type="button" data-action="toggle-preview" class="button separator-label"><?php _e( 'Preview' ); ?></button>
			</div>
			<div id="wpmoly-grid-builder-preview"><?php //$template->render( 'always', $echo = true ); ?></div>
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
