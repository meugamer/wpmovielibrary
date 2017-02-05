<?php
/**
 * Define the grid class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * Handle grids.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 * 
 * @property    int       $id Grid ID.
 * @property    string    $type Grid type: movie, actor, genre…
 * @property    string    $mode Grid mode: grid, list or archive
 * @property    string    $preset Grid content preset.
 * @property    string    $order_by Grid content order by.
 * @property    string    $order Grid content order.
 * @property    int       $columns Number of columns to use.
 * @property    int       $rows Number of rows to use.
 * @property    int       $total Number of Nodes to use.
 * @property    int       $enable_ajax Enable Ajax browsing.
 * @property    int       $enable_pagination Enable pagination.
 * @property    int       $settings_control Enable grid settings menu.
 * @property    int       $custom_letter Enable grid customization menu.
 * @property    int       $custom_order Enable custom ordering.
 * @property    int       $customs_control Enable grid customization menu.
 * @property    int       $custom_mode Enable custom grid mode.
 * @property    int       $custom_content Enable custom content settings.
 * @property    int       $custom_display Enable custom display settings.
 */
class Grid extends Node {

	/**
	 * Grid type.
	 * 
	 * @var    string
	 */
	protected $type;
	
	/**
	 * Grid mode.
	 * 
	 * @var    string
	 */
	protected $mode;
	
	/**
	 * Grid theme.
	 * 
	 * @var    string
	 */
	protected $theme;

	/**
	 * Grid preset.
	 * 
	 * @var    string
	 */
	protected $preset;

	/**
	 * Custom settings.
	 * 
	 * @var    array
	 */
	protected $settings;

	/**
	 * Grid JSON.
	 * 
	 * @var    object
	 */
	protected $json;

	/**
	 * Grid node list.
	 * 
	 * @var    NodeList
	 */
	public $items;

	/**
	 * Grid Query.
	 * 
	 * @var    Query
	 */
	public $query;

	/**
	 * Supported Grid types.
	 * 
	 * @var    array
	 */
	private $supported_types = array();

	/**
	 * Supported Grid modes.
	 * 
	 * @var    array
	 */
	private $supported_modes = array();

	/**
	 * Supported Grid themes.
	 * 
	 * @var    array
	 */
	private $supported_themes = array();

	/**
	 * Grid build status.
	 * 
	 * @var    boolean
	 */
	private $is_built = false;

	/**
	 * Main grid status.
	 * 
	 * @var    boolean
	 */
	public $is_main_grid = false;

	/**
	 * Initialize the Grid.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function init() {

		$this->suffix = '_wpmoly_grid_';
		$this->items = new NodeList;

		/**
		 * Filter the default grid settings list.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_settings
		 */
		$this->default_settings = apply_filters( 'wpmoly/filter/default/' . $this->get_type() . '/grid/settings', array(
			'type',
			'mode',
			'theme',
			'preset',
			'columns',
			'rows',
			'column_width',
			'row_height',
			'enable_ajax',
			'enable_pagination',
			'settings_control',
			'custom_letter',
			'custom_order',
			'customs_control',
			'custom_mode',
			'custom_content',
			'custom_display'
		) );

		$grid_types = array(
			'movie' => array(
				'label' => __( 'Movie', 'wpmovielibrary' ),
				'icon'  => 'wpmolicon icon-video',
				'modes' => array(
					'grid' => array(
						'label'  => __( 'Grid', 'wpmovielibrary' ),
						'icon'   => 'wpmolicon icon-th',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							),
							'variant-1' => array(
								'label' => __( 'Variant #1' ),
								'icon'  => 'wpmolicon icon-style'
							),
							'variant-2' => array(
								'label' => __( 'Variant #2' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'archive' => array(
						'label' => __( 'Archive', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							),
							'variant-1' => array(
								'label' => __( 'Variant #1' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					)
				)
			),
			'collection' => array(
				'label' => __( 'Collection', 'wpmovielibrary' ),
				'icon'  => 'wpmolicon icon-collection',
				'modes' => array(
					'grid' => array(
						'label' => __( 'Grid', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'archive' => array(
						'label' => __( 'Archive', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					)
				)
			),
			'actor' => array(
				'label' => __( 'Actor', 'wpmovielibrary' ),
				'icon'  => 'wpmolicon icon-actor-alt',
				'modes' => array(
					'grid' => array(
						'label' => __( 'Grid', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'archive' => array(
						'label' => __( 'Archive', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					)
				)
			),
			'genre' => array(
				'label' => __( 'Genre', 'wpmovielibrary' ),
				'icon'  => 'wpmolicon icon-tag',
				'modes' => array(
					'grid' => array(
						'label' => __( 'Grid', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					),
					'archive' => array(
						'label' => __( 'Archive', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-th-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style'
							)
						)
					)
				)
			)
		);

		/**
		 * Filter the supported Grid types.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $default_types
		 */
		$this->supported_types = apply_filters( 'wpmoly/filter/grid/supported/types', $grid_types );

		foreach ( $this->supported_types as $type_id => $type ) {

			/**
			 * Filter the supported Grid modes.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $default_modes
			 */
			$this->supported_modes[ $type_id ] = apply_filters( 'wpmoly/filter/grid/supported/' . $type_id . '/modes', $type['modes'] );

			foreach ( $this->supported_modes[ $type_id ] as $mode_id => $mode ) {

				/**
				 * Filter the supported Grid themes.
				 * 
				 * @since    3.0
				 * 
				 * @param    array    $default_themes
				 */
				$this->supported_themes[ $type_id ][ $mode_id ] = apply_filters( 'wpmoly/filter/grid/supported/' . $type_id . '/' . $mode_id . '/themes', $mode['themes'] );
			}
		}
	}

	/**
	 * Build the Grid.
	 * 
	 * Load items depending on presets and/or custom settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function build() {

		$this->prepare();
		$this->get_query();

		$method = str_replace( '-', '_', $this->get_preset() );
		if ( method_exists( $this->query, $method ) ) {

			$items = $this->query->$method( $this->settings );
			foreach ( (array) $items as $item ) {
				$this->items->add( $item );
			}

			// Clean up settings
			unset( $this->settings['taxonomy'] );
			unset( $this->settings['post_type'] );

			$this->built = true;

			return $this->items;
		}
	}

	/**
	 * Prepare the Grid by parsing custom settings. This is used to browse
	 * grids from URLs when JavaScript disabled, and for now until Ajax
	 * browsing is implemented. Three main cases are handled here:
	 * 
	 * 1/ Browsing standard grids using basic URLs: https://domain.ltd/movies/?grid=id:123|paged:3
	 *    This will show the third page of the grid.
	 * 2/ Browsing advanced standard grids: https://domain.ltd/movies/?grid=id:123|paged:3|orderby:post_title|order:ASC
	 *    This will show the third page of a basic grid sorted by post title ascendingly.
	 * 3/ Browsing dynamic grids generated from URLs: https://domain.ltd/movies/subtitles/english
	 *    This will show the first page of a dynamic grid listing all english-subtitled movies.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function prepare() {

		// Find out custom preset, if any
		$custom = get_query_var( 'grid_preset' );
		if ( $this->is_main_grid && ! empty( $custom ) ) {
			$this->preset = 'custom';
		}

		// Get grid settings from URL
		$settings = get_query_var( 'grid' );
		if ( empty( $settings ) ) {
			return false;
		}

		// Extract settings
		$settings = str_replace( array( ':', ',' ), array( '=', '&' ), $settings );
		$defaults = array(
			'id'      => '',
			'order'   => '',
			'orderby' => ''
		);
		$settings = wp_parse_args( $settings, $defaults );

		// Not the grid? Don't go any further.
		if ( $this->id != $settings['id'] ) {
			return false;
		}

		// Distinction required for query.
		if ( $this->is_taxonomy() ) {
			$settings['taxonomy'] = $this->get_type();
		} elseif ( $this->is_post() ) {
			$settings['post_type'] = $this->get_type();
		}

		return $this->settings = $settings;
	}

	/**
	 * Retrieve current grid preset.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_preset() {

		/**
		 * Filter grid default preset.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $default_preset
		 */
		$default_preset = apply_filters( 'wpmoly/filter/default/' . $this->get_type() . '/grid/preset', 'default_preset' );

		if ( empty( $this->preset ) ) {
			$preset = $this->get( 'preset' );
			if ( empty( $preset ) ) {
				$preset = $default_preset;
			}
			return $this->preset = $preset;
		}

		return $this->preset;
	}

	/**
	 * Set grid preset.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $preset New preset.
	 * 
	 * @return   string
	 */
	public function set_preset( $preset ) {

		return $this->preset = $preset;
	}

	/**
	 * Retrieve current grid settings.
	 * 
	 * Settings differs from parameters in that they should be temporary
	 * and therefore never saved. They're used to generate URLs and run
	 * queries.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_settings() {

		if ( is_null( $this->settings ) ) {
			return $this->settings = array();
		}

		return $this->settings;
	}

	/**
	 * Set grid settings.
	 * 
	 * @since    3.0
	 * 
	 * @param    array    $settings New settings.
	 * 
	 * @return   string
	 */
	public function set_settings( $settings ) {

		$settings = wp_parse_args( $settings, $this->settings );

		return $this->settings = $settings;
	}

	/**
	 * Get the Node Query parameters.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function get_query_args() {

		return $this->query->get_args();
	}

	/**
	 * Get the Node Query.
	 * 
	 * If the Query is not yet set, do it.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function get_query() {

		if ( is_null( $this->query ) ) {
			return $this->set_query();
		}

		return $this->query;
	}

	/**
	 * Set the Node Query.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function set_query() {

		if ( ! is_null( $this->query ) ) {
			return $this->query;
		}

		$classes = array(
			'movie'      => '\wpmoly\Query\Movies',
			'actor'      => '\wpmoly\Query\Actors',
			'collection' => '\wpmoly\Query\Collections',
			'genre'      => '\wpmoly\Query\Genres'
		);

		if ( ! isset( $classes[ $this->get_type() ] ) ) {
			return false;
		}

		return $this->query = new $classes[ $this->get_type() ];
	}

	/**
	 * Retrieve current grid type.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_type() {

		/**
		 * Filter grid default type.
		 * 
		 * @since    3.0
		 * 
		 * @param    string    $default_type
		 */
		$default_type = apply_filters( 'wpmoly/filter/grid/default/type', 'movie' );

		if ( is_null( $this->type ) ) {
			$this->type = $this->get( 'type', $default_type );
		}

		return $this->type;
	}

	/**
	 * Set grid type.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type
	 * 
	 * @return   string
	 */
	public function set_type( $type ) {

		if ( ! isset( $this->supported_types[ $type ] ) ) {
			$type = 'movie';
		}

		return $this->type = $type;
	}

	/**
	 * Retrieve current grid mode.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_mode() {

		if ( is_null( $this->mode ) ) {
			$this->mode = $this->get( 'mode', 'grid' );
		}

		return $this->mode;
	}

	/**
	 * Set grid mode.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $mode
	 * 
	 * @return   string
	 */
	public function set_mode( $mode ) {

		if ( ! isset( $this->supported_modes[ $this->type ][ $mode ] ) ) {
			$mode = 'grid';
		}

		return $this->mode = $mode;
	}

	/**
	 * Retrieve current grid theme.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function get_theme() {

		if ( is_null( $this->theme ) ) {
			$this->theme = $this->get( 'theme', 'default' );
		}

		return $this->theme;
	}

	/**
	 * Set grid theme.
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $theme
	 * 
	 * @return   string
	 */
	public function set_theme( $theme ) {

		if ( ! isset( $this->supported_themes[ $this->type ][ $this->mode ][ $theme ] ) ) {
			$theme = 'default';
		}

		return $this->theme = $theme;
	}

	/**
	 * Retrieve current grid number of rows.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_rows() {

		/**
		 * Filter the default number of rows.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $default_rows Default number of rows.
		 */
		$default_rows = 4;

		if ( ! isset( $this->rows ) ) {
			return $this->rows = $this->get( 'rows', $default_rows );
		}

		return $this->rows;
	}

	/**
	 * Retrieve current grid number of columns.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_columns() {

		/**
		 * Filter the default number of columns.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $default_columns Default number of columns.
		 */
		$default_columns = 5;

		if ( ! isset( $this->columns ) ) {
			return $this->columns = $this->get( 'columns', $default_columns );
		}

		return $this->columns;
	}

	/**
	 * Retrieve current grid row height.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_row_height() {

		/**
		 * Filter the default row height.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $default_row_height Default row height.
		 */
		$default_row_height = 200;

		if ( ! isset( $this->row_height ) ) {
			return $this->row_height = $this->get( 'row_height', $default_row_height );
		}

		return $this->row_height;
	}

	/**
	 * Retrieve current grid column width.
	 * 
	 * @since    3.0
	 * 
	 * @return   int
	 */
	public function get_column_width() {

		/**
		 * Filter the default column width.
		 * 
		 * @since    3.0
		 * 
		 * @param    int    $default_column_width Default column width.
		 */
		$default_column_width = 160;

		if ( ! isset( $this->column_width ) ) {
			return $this->column_width = $this->get( 'column_width', $default_column_width );
		}

		return $this->column_width;
	}

	/**
	 * Has the grid been built?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function ready() {

		return !! $this->is_built;
	}

	/**
	 * Is this the main grid?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_main_grid() {

		return !! $this->is_main_grid;
	}

	/**
	 * Is this a posts grid?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_post() {

		return post_type_exists( $this->get_type() );
	}

	/**
	 * Is this a terms grid?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_taxonomy() {

		return taxonomy_exists( $this->get_type() );
	}

	/**
	 * Is this a grid inside a Widget?
	 * 
	 * @since    3.0
	 * 
	 * @return   boolean
	 */
	public function is_widget() {

		return isset( $this->is_widget );
	}

	/**
	 * Save grid settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function save() {

		foreach ( $this->default_settings as $setting ) {
			if ( isset( $this->$setting ) ) {
				update_post_meta( $this->id, $this->suffix . $setting, $this->$setting );
			}
		}
	}

	/**
	 * JSONify the Grid instance.
	 * 
	 * @since    3.0
	 * 
	 * @return   string
	 */
	public function toJSON() {

		$json = array();

		$json['types'] = $this->supported_types;
		$json['modes'] = $this->supported_modes;
		$json['themes'] = $this->supported_themes;

		$json['settings'] = array();
		foreach ( $this->default_settings as $setting ) {
			$json['settings'][ $setting ] = $this->get( $setting );
		}

		return $this->json = wp_json_encode( $json );
	}
}
