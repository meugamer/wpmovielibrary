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
 * @property    int       $show_menu Show the Grid menu to users.
 * @property    int       $mode_control Allow users to control the Grid mode.
 * @property    int       $content_control Allow users to control the Grid content.
 * @property    int       $display_control Allow users to control the Grid display.
 * @property    int       $order_control Allow users to control the Grid content ordering.
 * @property    int       $show_pagination Show the Grid pagination to users.
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
			'show_menu',
			'mode_control',
			'content_control',
			'display_control',
			'order_control',
			'show_pagination'
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

		// Don't build unexisting grids
		if ( is_null( $this->id ) ) {
			return $this;
		}

		$this->build();
	}

	/**
	 * Build the Grid.
	 * 
	 * Load items depending on presets or custom settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	public function build() {

		if ( ! is_admin() ) {
			$this->prepare();
		}

		$query = $this->get_query();

		$method = str_replace( '-', '_', $this->get_preset() );
		if ( method_exists( $query, $method ) ) {

			$items = $query->$method( $this->settings );
			foreach ( (array) $items as $item ) {
				$this->items->add( $item );
			}

			// Clean up settings
			unset( $this->settings['taxonomy'] );
			unset( $this->settings['post_type'] );

			return $this->items;
		}
	}

	/**
	 * Prepare the Grid.
	 * 
	 * Parse custom settings. Settings can be passed through the URL 'grid'
	 * parameter: domain.ltd/movies/?grid=id:123|orderby:post_title|order:ASC
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function prepare() {

		$custom = get_query_var( 'grid_preset' );
		if ( ! empty( $custom ) ) {
			$this->preset = 'custom';
		}

		$settings = get_query_var( 'grid' );
		if ( empty( $settings ) ) {
			return false;
		}

		// Extract values
		$settings = str_replace( array( ':', ',' ), array( '=', '&' ), $settings );
		$defaults = array(
			'id'      => '',
			'order'   => '',
			'orderby' => ''
		);
		$settings = wp_parse_args( $settings, $defaults );

		// Not this grid? Bail.
		if ( $this->id != $settings['id'] ) {
			return false;
		}

		// Distinction required for query.
		if ( $this->is_taxonomy() ) {
			$settings['taxonomy'] = $this->get_type();
		} elseif ( $this->is_post() ) {
			$settings['post_type'] = $this->get_type();
		}

		$this->settings = $settings;
		$this->preset = 'custom';
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

		return $this->json = json_encode( $json );
	}
}
