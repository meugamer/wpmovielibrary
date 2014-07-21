<?php
/**
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */


/**
 * Collections Widget.
 * 
 * Display a list of the Movies Collections. Options: Title, Show as dropdown,
 * Show Movie count, custom style.
 * 
 * @since    1.0.0
 */
class WPML_Collections_Widget extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'wpml-collections-widget',
			__( 'WPML Collections', WPML_SLUG ),
			array(
				'classname'	=>	'wpml-collections-widget',
				'description'	=>	__( 'Display Movie Collections Lists', WPML_SLUG )
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );
		extract( $instance );

		$title = $before_title . apply_filters( 'widget_title', $title ) . $after_title;

		$list  = ( 1 == $list ? true : false );
		$css   = ( 1 == $css ? true : false );
		$count = ( 1 == $count ? true : false );
		$limit = ( isset( $limit ) ? intval( $limit ) : WPML_MAX_TAXONOMY_LIST );
		$archive = WPML_Settings::taxonomies__collection_rewrite();

		$args = '';
		if ( 0 < $limit )
			$args = 'order=DESC&orderby=count&number=' . $limit;

		$collections = get_terms( array( 'collection' ), $args );

		if ( $collections && ! is_wp_error( $collections ) ) {

			$items = array();
			$style = 'wpml-widget wpml-collection-list';

			if ( $css )
				$style = 'wpml-widget wpml-collection-list wpml-list custom';

			foreach ( $collections as $collection )
				$items[] = array(
					'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $collection->name ),
					'link'        => get_term_link( sanitize_term( $collection, 'collection' ), 'collection' ),
					'title'       => esc_attr( $collection->name . ( $count ? sprintf( '&nbsp;(%d)', $collection->count ) : '' ) )
				);

			if ( $limit )
				$items[] = array(
					'attr_title'  => __( 'View all collections', WPML_SLUG ),
					'link'        => home_url( '/' . $archive ),
					'title'       => __( 'View the complete list', WPML_SLUG )
				);

			$items = apply_filters( 'wpml_widget_collection_list', $items, $list, $css );
			$attributes = array( 'items' => $items, 'description' => $description, 'default_option' => __( 'Select a collection', WPML_SLUG ), 'style' => $style );

			if ( $list )
				$html = WPMovieLibrary::render_template( 'collection-widget/collection-dropdown-list.php', $attributes );
			else
				$html = WPMovieLibrary::render_template( 'collection-widget/collection-list.php', $attributes );
		}
		else {
			$html = WPMovieLibrary::render_template( 'empty.php', array( 'message' => __( 'Nothing to display for "Collection" taxonomy.', WPML_SLUG ) ) );
		}

		echo $before_widget . $title . $html . $after_widget;
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['list']  = intval( $new_instance['list'] );
		$instance['count'] = intval( $new_instance['count'] );
		$instance['css']   = intval( $new_instance['css'] );
		$instance['limit'] = intval( $new_instance['limit'] );

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance
		);

		$title = ( isset( $instance['title'] ) ? $instance['title'] : __( 'Movie Collections', WPML_SLUG ) );
		$list  = ( isset( $instance['list'] ) && 1 == $instance['list'] ? true : false );
		$count = ( isset( $instance['count'] )  && 1 == $instance['count'] ? true : false );
		$css   = ( isset( $instance['css'] )  && 1 == $instance['css'] ? true : false );
		$limit = ( isset( $instance['limit'] ) ? $instance['limit'] : WPML_MAX_TAXONOMY_LIST );

		// Display the admin form
		echo WPMovieLibrary::render_template( 'collection-widget/collection-admin.php', array( 'widget' => $this, 'instance' => $instance ), $require = 'always' );
	}

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'wpml', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}

}
