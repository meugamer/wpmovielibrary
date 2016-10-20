<?php
/**
 * Define the Archives class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 */

namespace wpmoly\Admin;

use wpmoly\Core\Rewrite;

/**
 * Provide a tool to manage custom Archive Pages.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class ArchivePages {

	/**
	 * Current page Post ID.
	 * 
	 * @var    int
	 */
	private $post_id;

	/**
	 * Archive pages.
	 * 
	 * @var    array
	 */
	private $pages = array();

	/**
	 * Archive Types.
	 * 
	 * @var    array
	 */
	private $types = array();

	/**
	 * Metaboxes Managers.
	 * 
	 * @var    array
	 */
	private $managers = array();

	/**
	 * Class constructor.
	 * 
	 * Define supported types and ButterBean managers. This runs very early
	 * in WordPress wp-admin/admin, even before post.php or post-new.php are
	 * loaded, which means we need to grab $post_id directly from the URL.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

		// Grap current page ID from URL
		if ( isset( $_GET['post'] ) ) {
			$this->post_id = (int) $_GET['post'];
		}

		$types = array(
			'movies'      => __( 'Movies', 'wpmovielibrary' ),
			'actors'      => __( 'Actors', 'wpmovielibrary' ),
			'collections' => __( 'Collections', 'wpmovielibrary' ),
			'genres'      => __( 'Genres', 'wpmovielibrary' )
		);

		/**
		 * Filter archive pages types.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     $types Default types.
		 * @param    object    ArchivePages instance.
		 */
		$this->types = apply_filters( 'wpmoly/filter/archive_pages/types', $types, $this );

		// Load archive pages
		$this->pages = get_option( '_wpmoly_archive_pages', array() );

		// Only register archive pages' manager when needed
		if ( ! empty( $this->pages[ $this->post_id ] ) ) {
			$this->managers['archive-page-settings'] = array(
				'label'     => esc_html__( 'Archive Page Settings', 'wpmovielibrary' ),
				'post_type' => 'page',
				'context'   => 'normal',
				'priority'  => 'high',
				'sections'  => array(
					'grid-settings' => array(
						'label' => esc_html__( 'Grid', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-grid',
						'settings' => array(
							'grid-id' => array(
								'type'     => 'posts',
								'post_type'=> 'grid',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Grid ID', 'wpmovielibrary' ),
								'description' => sprintf( esc_html__( 'Select a Grid to show in the page content. %s', 'wpmovielibrary' ), sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( admin_url( 'post-new.php?post_type=grid' ) ), __( 'Create a new one?', 'wpmovielibrary' ) ) ),
								'attr'     => array( 'class' => 'half-col', 'size' => '4' ),
								'default'  => ''
							),
							'grid-position' => array(
								'type'     => 'radio',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Grid Position', 'wpmovielibrary' ),
								'description' => esc_html__( 'Where should the Grid be displayed.', 'wpmovielibrary' ),
								//'description' => esc_html__( 'Where should the Grid be displayed. You can include the Grid in a custom place in your content by adding inserting the <code>[archives_grid]</code> in your content.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' ),
								'choices'  => array(
									'top'    => __( 'Before Post content', 'wpmovielibrary' ),
									'bottom' => __( 'After Post content', 'wpmovielibrary' ),
									//'custom' => __( 'Custom', 'wpmovielibrary' )
								),
								'default'  => 'top'
							)
						)
					),
					'page-customization' => array(
						'label' => esc_html__( 'Customization', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-advanced',
						'description' => esc_html__( 'Try to customize the page behaviour and appearance.<br /><strong>Note:</strong> this options may be ineffective depending on your theme and other plugins you are running that may interfere.', 'wpmovielibrary' ),
						'settings' => array(
							'adapt-page-title' => array(
								'type'     => 'checkbox',
								'post_type'=> 'grid',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Adapt Page Title', 'wpmovielibrary' ),
								'description' => esc_html__( 'Try to adapt the page’s title to fit the grid content changes: ordering, sorting…', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col' )
							)
						)
					)
				)
			);
		}

		/**
		 * Filter archive pages managers for the grid builder.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     $managers Default managers.
		 * @param    object    ArchivePages instance.
		 */
		$this->managers = apply_filters( 'wpmoly/filter/archive_pages/managers', $this->managers, $this );
	}

	/**
	 * Load ButterBean if needed.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function load() {

		// Bail if not our post type.
		if ( 'page' !== get_current_screen()->post_type ) {
			return;
		}

		require_once WPMOLY_PATH . 'vendor/butterbean/butterbean.php';

		// Let's do this thang!
		if ( function_exists( 'butterbean_loader_100' ) ) {
			butterbean_loader_100();
		}
	}

	/**
	 * Register ButterBean's managers.
	 * 
	 * @since    3.0
	 * 
	 * @param    object    $butterbean ButterBean instance.
	 * @param    string    $post_type Current Post Type.
	 * 
	 * @return   void
	 */
	public function register_butterbean( $butterbean, $post_type ) {

		foreach ( $this->managers as $id => $manager ) {

			$manager = (object) $manager;
			$sections = $manager->sections;

			$butterbean->register_manager(
				$id,
				array(
					'label'     => $manager->label,
					'post_type' => $manager->post_type,
					'context'   => $manager->context,
					'priority'  => $manager->priority
				)
			);
			$manager = $butterbean->get_manager( $id );

			foreach ( $sections as $section_id => $section ) {

				$section = (object) $section;
				$manager->register_section(
					$section_id,
					array(
						'label' => $section->label,
						'icon'  => $section->icon
					)
				);

				foreach ( $section->settings as $control_id => $control ) {

					$control_id = '_wpmoly_' . str_replace( '-', '_', $control_id );

					$control = (object) $control;
					$manager->register_control(
						$control_id,
						array(
							'section'     => $section_id,
							'type'        => isset( $control->type )        ? $control->type        : false,
							'post_type'   => isset( $control->post_type )   ? $control->post_type   : false,
							'label'       => isset( $control->label )       ? $control->label       : false,
							'attr'        => isset( $control->attr )        ? $control->attr        : false,
							'choices'     => isset( $control->choices )     ? $control->choices     : false,
							'description' => isset( $control->description ) ? $control->description : false
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
		}
	}

	/**
	 * Add a custom section to the page editor submitdiv metabox.
	 * 
	 * @since    3.0
	 * 
	 * @param    WP_Post    $post
	 * 
	 * @return   void
	 */
	public function archive_pages_select( $post ) {

		if ( 'page' !== $post->post_type ) {
			return false;
		}

		$page_type = isset( $this->pages[ $post->ID ] ) ? $this->pages[ $post->ID ] : '';

		$json = array(
			'types' => $this->types,
			'pages' => $this->pages
		);
?>

		<div id="wpmoly-archives-page-type" class="misc-pub-section archive-page misc-pub-archive-page">
			<span class="wpmolicon icon-wpmoly"></span><?php _e( 'Type of Archives:', 'wpmovielibrary' ); ?> <span id="wpmoly-archive-page-type"><?php echo $page_type ? $this->types[ $page_type ] : __( 'None' ); ?></span> <a id="wpmoly-edit-archive-page" href="#"><?php _e( 'Edit' ); ?></a>
			<div id="wpmoly-edit-archive-page-type" class="hide-if-js">
				<p>
					<select id="wpmoly-archive-page-types" name="wpmoly[archive_page_type]">
						<option value=""></option>
<?php foreach ( $this->types as $type => $name ) : ?>
						<option value="<?php echo esc_attr( $type ); ?>"<?php selected( $page_type, $type ); ?>><?php echo esc_attr( $name ); ?></option>
<?php endforeach; ?>
					</select>
				</p>
				<p>
					<a href="#visibility" id="wpmoly-save-archive-page" class="hide-if-no-js button"><?php _e( 'OK' ); ?></a>
					<a href="#visibility" id="wpmoly-cancel-archive-page" class="hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
				</p>
			</div>
		</div>
		<script type="text/javascript">var _wpmolyArchivePagesData = <?php echo json_encode( $json ); ?>;</script>
<?php
	}

	/**
	 * Save current page as an archive page.
	 * 
	 * @since    3.0
	 * 
	 * @param    int        $post_id
	 * @param    WP_Post    $post
	 * @param    boolean    $update
	 * 
	 * @return   void
	 */
	public function set_archive_page_type( $post_id, $post, $update ) {

		if ( empty( $_POST['wpmoly'] ) ) {
			return false;
		}

		if ( ! isset( $_POST['wpmoly']['archive_page_type'] ) || ! in_array( $_POST['wpmoly']['archive_page_type'], array_keys( $this->types ) ) ) {
			return false;
		}

		$archive = $_POST['wpmoly']['archive_page_type'];
		$oldpage = array_search( $archive, $this->pages );

		// Same page, nothing to change
		if ( $oldpage == $post_id ) {
			return false;
		}

		// Archive page already set for this type
		if ( $oldpage && $oldpage !== $post_id ) {
			unset( $this->pages[ $oldpage ] );
		}

		$this->pages[ $post_id ] = $archive;

		$this->set_notice();
		$this->set_archive_pages();
	}

	/**
	 * Update archive pages option.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function set_archive_pages() {

		return update_option( '_wpmoly_archive_pages', $this->pages );
	}

	/**
	 * Changes have been made, permalinks need to be updated: set a transient
	 * to trigger an admin notice.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function set_notice() {

		Rewrite::set_notice();
	}

}
