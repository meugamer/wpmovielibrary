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
use wpmoly\Core\Metabox;

/**
 * Provide a tool to manage custom Archive Pages.
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/admin
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Archive_Pages extends Metabox {

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
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$this->post_id = (int) $_POST['post_ID'];
		}

		$types = array(
			'none'       => __( 'None' ),
			'movie'      => __( 'Movies', 'wpmovielibrary' ),
			'actor'      => __( 'Actors', 'wpmovielibrary' ),
			'collection' => __( 'Collections', 'wpmovielibrary' ),
			'genre'      => __( 'Genres', 'wpmovielibrary' ),
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
	}

	/**
	 * Define meta managers.
	 *
	 * @since    3.0
	 */
	protected function add_managers() {

		// Only register archive pages' manager when needed
		if ( ! empty( $this->pages[ $this->post_id ] ) ) {

			$this->add_manager( 'archive-page-settings', array(
				'label'     => esc_html__( 'Archive Page Settings', 'wpmovielibrary' ),
				'post_type' => 'page',
				'context'   => 'normal',
				'priority'  => 'high',
				'sections'  => array(
					'grid-settings' => array(
						'label' => esc_html__( 'Archive Grid', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-grid',
						'settings' => array(
							'grid-id' => array(
								'type'      => 'posts',
								'post_type' => 'grid',
								'section'   => 'grid-settings',
								'label'     => esc_html__( 'Grid ID', 'wpmovielibrary' ),
								// translators: link to create a new page.
								'description' => sprintf( esc_html__( 'Select a Grid to show in the page content. Or maybe %s?', 'wpmovielibrary' ), sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( admin_url( 'post-new.php?post_type=grid' ) ), __( 'add a new one', 'wpmovielibrary' ) ) ),
								'attr'      => array(
									'class' => 'half-col widefat',
								),
								'default'   => '',
							),
							'grid-position' => array(
								'type'     => 'radio',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Grid Position', 'wpmovielibrary' ),
								'description' => esc_html__( 'Where should the Grid be displayed.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
								),
								'choices'  => array(
									'top'    => esc_html__( 'Before Post content', 'wpmovielibrary' ),
									'bottom' => esc_html__( 'After Post content', 'wpmovielibrary' ),
								),
								'default'  => 'top',
							),
							'adapt-page-title' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Adapt Page Title', 'wpmovielibrary' ),
								'description' => esc_html__( 'Try to adapt the page’s title to fit the grid content changes: ordering, sorting…', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
								),
							),
							'adapt-post-title' => array(
								'type'     => 'checkbox',
								'section'  => 'grid-settings',
								'label'    => esc_html__( 'Adapt Post Title', 'wpmovielibrary' ),
								'description' => esc_html__( 'Try to adapt the post title to fit the grid content changes: ordering, sorting…', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
								),
							),
						),
					),
					'single-terms' => array(
						'label' => esc_html__( 'Single Terms', 'wpmovielibrary' ),
						'icon'  => 'wpmolicon icon-tags',
						'settings' => array(
							'single-terms' => array(
								'type'     => 'checkbox',
								'section'  => 'single-terms',
								'label'    => esc_html__( 'Use for single terms', 'wpmovielibrary' ),
								'description' => esc_html__( 'Use this page to show single terms archives too. This will replace the standard WordPress archive page with a page containing the term Headbox and a grid of related movies.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col',
								),
							),
							'headbox-theme' => array(
								'type'     => 'select',
								'section'  => 'single-terms',
								'label'    => esc_html__( 'Headbox Theme', 'wpmovielibrary' ),
								'description' => esc_html__( 'Select a theme for the term Headbox. Select "None" to hide the Headbox.', 'wpmovielibrary' ),
								'attr'     => array(
									'class' => 'half-col widefat',
								),
								'choices'  => array(
									'none'     => esc_html__( 'None', 'wpmovielibrary' ),
									'default'  => esc_html__( 'Default', 'wpmovielibrary' ),
									'extended' => esc_html__( 'Extended', 'wpmovielibrary' ),
								),
								'default'  => 'extended',
							),
							'grid-preset' => array(
								'type'    => 'radio-image',
								'section' => 'single-terms',
								'label'   => esc_html__( 'Grid preset', 'wpmovielibrary' ),
								'description' => esc_html__( 'Select a preset to apply to the grid.', 'wpmovielibrary' ),
								'attr'    => array(
									'class' => 'visible-labels half-col',
								),
								'choices' => array(
									'alphabetical-movies' => array(
										'label' => esc_html__( 'Alphabetical Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/alphabetical-movies.png',
									),
									'unalphabetical-movies' => array(
										'label' => esc_html__( 'Unalphabetical Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/unalphabetical-movies.png',
									),
									'last-added-movies' => array(
										'label' => esc_html__( 'Latest Added Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/last-added-movies.png',
									),
									'first-added-movies' => array(
										'label' => esc_html__( 'Earliest Added Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/first-added-movies.png',
									),
									'last-released-movies' => array(
										'label' => esc_html__( 'Latest Released Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/last-released-movies.png',
									),
									'first-released-movies' => array(
										'label' => esc_html__( 'Earliest Released Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/first-released-movies.png',
									),
									'most-rated-movies' => array(
										'label' => esc_html__( 'Most Rated Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/most-rated-movies.png',
									),
									'least-rated-movies' => array(
										'label' => esc_html__( 'Least Rated Movies', 'wpmovielibrary' ),
										'url'   => WPMOLY_URL . 'admin/img/least-rated-movies.png',
									),
								),
								'default'  => 'alphabetical-movies',
								'sanitize' => 'esc_attr',
							),
						),
					),
				),
			) );

		} // End if().
	}

	/**
	 * Load frameworks if needed.
	 *
	 * @since    3.0
	 */
	public function load_meta_frameworks() {

		// Bail if not our post type.
		if ( 'page' !== get_current_screen()->post_type ) {
			return;
		}

		parent::load_meta_frameworks();
	}

	/**
	 * Add a custom section to the page editor submitdiv metabox.
	 *
	 * @since    3.0
	 *
	 * @param    WP_Post    $post
	 */
	public function archive_pages_select( $post ) {

		if ( 'page' !== $post->post_type ) {
			return false;
		}

		$page_type = isset( $this->pages[ $post->ID ] ) ? $this->pages[ $post->ID ] : '';

		$json = array(
			'types' => $this->types,
			'pages' => $this->pages,
		);
?>

		<div id="wpmoly-archives-page-type" class="misc-pub-section archive-page misc-pub-archive-page">
			<span class="wpmolicon icon-wpmoly"></span><?php _e( 'Type of Archives:', 'wpmovielibrary' ); ?> <span id="wpmoly-archive-page-type"><?php echo $page_type ? $this->types[ $page_type ] : __( 'None' ); ?></span> <a id="wpmoly-edit-archive-page" href="#"><?php _e( 'Edit' ); ?></a>
			<div id="wpmoly-edit-archive-page-type" class="hide-if-js">
				<p>
					<select id="wpmoly-archive-page-types" name="wpmoly[archive_page_type]">
<?php foreach ( $this->types as $type => $name ) : ?>

						<option value="<?php echo esc_attr( $type ); ?>"<?php selected( $page_type, $type ); ?>><?php echo esc_html( $name ); ?></option>
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

		if ( 'none' == $archive ) {
			$archive = '';
		}

		// Same page, nothing to change
		if ( $oldpage == $post_id || ( empty( $archive ) && ! isset( $this->pages[ $post_id ] ) ) ) {
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
	 */
	private function set_notice() {

		Rewrite::set_notice();
	}

	/**
	 * Define metaboxes.
	 *
	 * @since    3.0
	 */
	protected function add_metaboxes() {}

}
