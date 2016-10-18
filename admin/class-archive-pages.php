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

use wpmoly\Core\Loader;
use wpmoly\Core\PublicTemplate;

/**
 * Provide a tool to manage custom Archive Pages.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class ArchivePages {

	/**
	 * Grid Post Type metaboxes.
	 * 
	 * @var    array
	 */
	private $metaboxes = array();

	/**
	 * Class constructor.
	 * 
	 * @since    3.0
	 */
	public function __construct() {

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

		$managers = array(
			'archive-page-settings' => array(
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
								'type'     => 'text',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Grid ID', 'wpmovielibrary' ),
								'description' => esc_html__( 'Grid to show in the page content.', 'wpmovielibrary' ),
								'attr'     => array( 'class' => 'half-col', 'size' => '4' ),
								'default'  => ''
							)
						)
					)
				)
			)
		);

		/**
		 * Filter archive pages managers for the grid builder.
		 * 
		 * @since    3.0
		 * 
		 * @param    array     $managers Default managers.
		 * @param    object    ArchivePages instance.
		 */
		$this->managers = apply_filters( 'wpmoly/filter/archive_pages/managers', $managers, $this );
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
?>

		<div class="misc-pub-section archive-page misc-pub-archive-page">
			<span class="wpmolicon icon-wpmoly"></span><?php _e( 'Type of Archives:', 'wpmovielibrary' ); ?> <span id="wpmoly-archive-page-type"><?php _e( 'None' ); ?></span> <a id="wpmoly-edit-archive-page" href="#"><?php _e( 'Edit' ); ?></a>
			<div id="wpmoly-edit-archive-page-type" class="hide-if-js">
				<p>
					<select id="wpmoly-archive-page-types" name="wpmoly[archive_page_type]">
						<option value=""></option>
<?php foreach ( $this->types as $type => $name ) : ?>
						<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_attr( $name ); ?></option>
<?php endforeach; ?>
					</select>
				</p>
				<p>
					<a href="#visibility" id="wpmoly-save-archive-page" class="hide-if-no-js button"><?php _e( 'OK' ); ?></a>
					<a href="#visibility" id="wpmoly-cancel-archive-page" class="hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
				</p>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready( function($) {
				var $elem = $( '#wpmoly-edit-archive-page-type'),
				 $trigger = $( '#wpmoly-edit-archive-page' ),
				  $cancel = $( '#wpmoly-cancel-archive-page' ),
				    $save = $( '#wpmoly-save-archive-page' ),
				    $text = $( '#wpmoly-archive-page-type' );

				$trigger.click( function( event ) {
					if ( $elem.is( ':hidden' ) ) {
						$elem.slideDown( 'fast' );
						$(this).hide();
					}
					event.preventDefault();
				});
				$cancel.click( function( event ) {
					if ( ! $elem.is( ':hidden' ) ) {
						$elem.slideUp( 'fast' );
					}
					event.preventDefault();
				});
				$save.click( function( event ) {
					var $selected = $( '#wpmoly-archive-page-types option:selected' )
					if ( $selected.text() != $text.text() ) {
						$text.text( $selected.text() );
					}
					if ( ! $elem.is( ':hidden' ) ) {
						$elem.slideUp( 'fast' );
						$trigger.show();
					}
					event.preventDefault();
				});
			});
		</script>
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

		$archive_type = $_POST['wpmoly']['archive_page_type'];

		$archive_pages = get_option( '_wpmoly_archive_pages' );
		if ( ! $archive_pages ) {
			$archive_pages = array_map( '__return_zero', array_keys( $this->types ) );
		}

		$archive_pages[ $archive_type ] = $post_id;

		set_option( '_wpmoly_archive_pages', $archive_pages );
	}

}
