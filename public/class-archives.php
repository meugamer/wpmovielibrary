<?php
/**
 * Define the Archive Pages class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 */

namespace wpmoly;

/**
 *
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Archives {

	/**
	 * Single instance.
	 *
	 * @var    Frontend
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 *
	 * @since    3.0
	 *
	 * @return   Singleton
	 */
	final public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Adapt Archive Page post titles to match content.
	 *
	 * @since    3.0
	 *
	 * @param    string     $post_title Page original post title.
	 * @param    WP_Post    $post Archive page Post instance.
	 *
	 * @return   string
	 */
	public function archive_page_title( $post_title, $post ) {

		if ( is_admin() || ! is_archive_page( $post->ID ) ) {
			return $post_title;
		}

		$adapt = get_post_meta( $post->ID, '_wpmoly_adapt_page_title', true );
		if ( ! _is_bool( $adapt ) ) {
			return $post_title;
		}

		$new_title = $this->adapt_archive_title( $post_title, $post->ID, 'wp_title' );

		return $new_title;
	}

	/**
	 * Adapt Archive Page titles to match content.
	 *
	 * @since    3.0
	 *
	 * @param    string     $post_title Page original post title.
	 * @param    int        $post_id Archive page Post ID.
	 *
	 * @return   string
	 */
	public function archive_page_post_title( $post_title, $post_id ) {

		global $wp_query;

		if ( is_admin() || ! is_archive_page( $post_id ) || ! in_the_loop() ) {
			return $post_title;
		}

		$adapt = get_post_meta( $post_id, '_wpmoly_adapt_post_title', true );
		if ( ! _is_bool( $adapt ) ) {
			return $post_title;
		}

		$new_title = $this->adapt_archive_title( $post_title, $post_id, 'post_title' );

		return $new_title;
	}

	/**
	 * Adapt Archive Page titles to match content.
	 *
	 * Mostly used to feature the term name in the page and post title when
	 * showing a single term archives.
	 *
	 * @since    3.0
	 *
	 * @param    string     $title Page original post title.
	 * @param    int        $post_id Archive page Post ID.
	 * @param    string     $context Context, either 'wp_title' (page title) or 'post_title' (page post title)
	 *
	 * @return   string
	 */
	private function adapt_archive_title( $title, $post_id, $context ) {

		$type = get_archive_page_type( $post_id );
		$name = get_query_var( $type );
		if ( empty( $name ) ) {
			return $title;
		}

		$term = get_term_by( 'slug', $name, $type );
		if ( ! $term ) {
			return $title;
		}

		$title = sprintf( _x( '%1$s: %2$s', 'Archive page title', 'wpmovielibrary' ), $title, $term->name );

		/**
		 * Filter the adapted archive page/post title.
		 *
		 * @since    3.0
		 *
		 * @param    string     $title Page original post title.
		 * @param    int        $post_id Archive page Post ID.
		 * @param    WP_Term    $term Archive WP_Term instance.
		 * @param    string     $context Context, either 'wp_title' (page title) or 'post_title' (page post title)
		 */
		$title = apply_filters( "wpmoly/filter/archive_page/{$context}/title", $title, $post_id, $term, $context );

		return $title;
	}

	/**
	 * Filter post content to add grid to archive pages.
	 *
	 * Determine if we're dealing with a single item, ie. a term, or a real
	 * archive page.
	 *
	 * @since    3.0
	 *
	 * @param    string    $content Post content.
	 *
	 * @return   string
	 */
	public function archive_page_content( $content ) {

		$post_id = get_the_ID();
		if ( is_admin() || ! is_archive_page( $post_id ) ) {
			return $content;
		}

		$type = get_archive_page_type( $post_id );
		if ( ! empty( get_query_var( $type ) ) ) {
			return $this->single_page_content( $post_id, $type, $content );
		}

		return $this->real_archive_page_content( $post_id, $content );
	}

	/**
	 * Handle single item content.
	 *
	 * Mostly used to show custom pages for taxonomy terms.
	 *
	 * @since    3.0
	 *
	 * @param    int       $post_id Current Post ID
	 * @param    string    $type Archive page type.
	 * @param    string    $content Post content.
	 *
	 * @return   string
	 */
	public function single_page_content( $post_id, $type, $content ) {

		$show = get_post_meta( $post_id, '_wpmoly_single_terms', true );
		if ( ! _is_bool( $show ) ) {
			return $content;
		}

		$pre_content = '';

		$name = get_query_var( $type );
		$term = get_term_by( 'slug', $name, $type );
		if ( $term ) {

			$name = $term->name;

			$theme = get_post_meta( $post_id, '_wpmoly_headbox_theme', true );
			$headbox = get_term_headbox( $term );
			$headbox->set_theme( $theme );

			$headbox_template = get_headbox_template( $headbox );
			$pre_content = $headbox_template->render();
		}

		$archive_page_id = get_archives_page_id( 'movie' );
		if ( ! $archive_page_id ) {
			return $pre_content;
		}

		$grid_id = get_post_meta( $archive_page_id, '_wpmoly_grid_id', true );
		if ( empty( $grid_id ) ) {
			return $pre_content;
		}

		$grid = get_grid( (int) $grid_id );
		$grid->set_preset( array(
			$type => $name,
		) );

		$grid_template = get_grid_template( $grid );

		$pre_content .= $grid_template->render() . $content;

		return $pre_content;
	}

	/**
	 * Handle archive page content.
	 *
	 * @since    3.0
	 *
	 * @param    int       $post_id Current Post ID
	 * @param    string    $content Post content.
	 *
	 * @return   string
	 */
	public function real_archive_page_content( $post_id, $content ) {

		$pre_content = '';

		$grid_id = get_post_meta( $post_id, '_wpmoly_grid_id', true );
		if ( empty( $grid_id ) ) {
			return $pre_content;
		}

		$grid = get_grid( (int) $grid_id );

		$preset = get_query_var( 'preset' );
		if ( ! empty( $preset ) ) {
			$preset = prefix_meta_key( $preset, '', true );
			$grid->set_preset( array(
				$preset => get_query_var( $preset ),
			) );
		}

		$grid_template = get_grid_template( $grid );

		$position = get_post_meta( $post_id, '_wpmoly_grid_position', true );
		if ( 'top' === $position ) {
			$pre_content = $grid_template->render() . $pre_content;
		} elseif ( 'bottom' === $position ) {
			$pre_content = $pre_content . $grid_template->render();
		}

		return $pre_content;
	}

}
