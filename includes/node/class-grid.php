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
	 * Grid Widget.
	 * 
	 * @var    boolean
	 */
	public $is_widget = false;

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

		/** This filter is documented in includes/core/class-registrar.php */
		$this->suffix = apply_filters( 'wpmoly/filter/grid/meta/key', '' );

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
			'list_columns',
			'list_column_width',
			'list_rows',
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
								'icon'  => 'wpmolicon icon-style',
							),
						),
					),
				),
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
								'icon'  => 'wpmolicon icon-style',
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style',
							),
						),
					),
				),
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
								'icon'  => 'wpmolicon icon-style',
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style',
							),
						),
					),
				),
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
								'icon'  => 'wpmolicon icon-style',
							)
						)
					),
					'list' => array(
						'label' => __( 'List', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-list',
						'themes' => array(
							'default' => array(
								'label' => __( 'Default' ),
								'icon'  => 'wpmolicon icon-style',
							),
						),
					),
				),
			),
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

		$this->prepare();
	}

	/**
	 * Prepare the grid.
	 *
	 * Grid query args can be overriden by passing a custom preset
	 * value through URL parameters.
	 *
	 * @since    3.0
	 */
	public function prepare() {

		$preset = get_query_var( 'grid_preset' );
		if ( empty( $preset ) ) {
			return false;
		}

		$meta_key = 'wpmoly_movie_' . str_replace( '-', '_', $preset );
		$meta_value = get_query_var( $meta_key );
		if ( empty( $meta_value ) ) {
			return false;
		}

		$this->set_preset( array(
			$preset => $meta_value,
		) );
	}

	/**
	 * Retrieve a list of supported grid types.
	 *
	 * If no type is specified, return all supported types.
	 *
	 * @since    3.0
	 *
	 * @param    string    $type Grid type.
	 *
	 * @return   array
	 */
	public function get_supported_types( $type = '' ) {

		if ( ! empty( $this->supported_types[ $type ] ) ) {
			return $this->supported_types[ $type ];
		}

		return $this->supported_types;
	}

	/**
	 * Retrieve a list of supported grid modes.
	 *
	 * If no type is specified, return all supported modes.
	 *
	 * @since    3.0
	 *
	 * @param    string    $type Grid type.
	 *
	 * @return   array
	 */
	public function get_supported_modes( $type = '' ) {

		if ( ! empty( $this->supported_modes[ $type ] ) ) {
			return $this->supported_modes[ $type ];
		}

		return $this->supported_modes;
	}

	/**
	 * Retrieve a list of supported grid themes.
	 *
	 * If no type/mode is specified, return all supported themes.
	 *
	 * @since    3.0
	 *
	 * @param    string    $type Grid type.
	 * @param    string    $mode Grid mode.
	 *
	 * @return   array
	 */
	public function get_supported_themes( $type = '', $mode = '' ) {

		if ( ! empty( $this->supported_themes[ $type ][ $mode ] ) ) {
			return $this->supported_themes[ $type ][ $mode ];
		}

		return $this->supported_themes;
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

		return true === $this->is_widget;
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
}
