<?php
/**
 * WPMovieLibrary Movie Grid Class extension.
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Grid' ) ) :

	class WPMOLY_Grid extends WPMOLY_Movies {

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                              Movie Grid
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Generate Movie Grid
		 * 
		 * If a current letter is passed to the query use it to narrow
		 * the list of movies.
		 * 
		 * @since    2.0
		 * 
		 * @param    array       Shortcode arguments to use as parameters
		 * @param    boolean     Are we actually doing a Shortcode?
		 * 
		 * @return   string    HTML content
		 */
		public static function get_content( $args = array(), $shortcode = false ) {

			global $wp_query;

			$content  = '';
			$defaults = array(
				
				'view'       => 'grid',
				'backbone'   => wpmoly_o( 'movie-backbone-grid', $default = true ),
				'columns'    => wpmoly_o( 'movie-archives-grid-columns', $default = 4 ),
				'rows'       => wpmoly_o( 'movie-archives-grid-rows', $default = 6 ),
				'number'     => wpmoly_o( 'movie-archives-movies-limit', $default = null ),
				'paged'      => 1,
				// Taxonomies/Meta
				'category'   => null,
				'tag'        => null,
				'collection' => null,
				'actor'      => null,
				'genre'      => null,
				'meta'       => null,
				'detail'     => null,
				'value'      => null,
				// Filtering
				'letter'     => null,
				'order'      => wpmoly_o( 'movie-archives-movies-order', $default = true ),
				'orderby'    => 'post_title'
			);
			$args = wp_parse_args( $args, $defaults );

			// Allow URL params to override settings
			$_args = WPMOLY_Archives::parse_query_vars( $wp_query->query );
			$args = wp_parse_args( $_args, $args );

			// debug
			$main_args = $args;

			extract( $args, EXTR_SKIP );

			$grid_meta = (array) wpmoly_o( 'movie-archives-movies-meta', $default = true );
			$grid_meta = array_keys( $grid_meta['used'] );
			$title   = in_array( 'title', $grid_meta );
			$genre   = in_array( 'genre', $grid_meta );
			$rating  = in_array( 'rating', $grid_meta );
			$year    = in_array( 'year', $grid_meta );
			$runtime = in_array( 'runtime', $grid_meta );

			$views = array( 'grid', 'archives', 'list' );
			if ( '1' == wpmoly_o( 'rewrite-enable' ) ) {
				$views = array( 'grid' => __( 'grid', 'wpmovielibrary' ), 'archives' => __( 'archives', 'wpmovielibrary' ), 'list' => __( 'list', 'wpmovielibrary' ) );
			}

			if ( ! isset( $views[ $view ] ) ) {
				$_view = array_search( $view, $views );
				if ( false != $_view ) {
					$view = $_view;
				} else {
					$view = 'grid';
				}
			}

			$_number = (int) $columns * $rows;
			if ( ! is_null( $number ) && $_number > $number ) {
				$args['number'] = $number;
			} else {
				$args['number'] = $_number;
			}

			$movies = self::query_movies( $args );
			$args['pages'] = array(
				'total'   => $movies->max_num_pages,
				'current' => $movies->query_vars['paged'],
				'prev'    => max( $movies->query_vars['paged'] - 1, 0 ),
				'next'    => min( $movies->query_vars['paged'] + 1, $movies->max_num_pages ),
				'posts'   => $movies->found_posts
			);

			$args['show_title']   = $title;
			$args['show_genre']   = $genre;
			$args['show_rating']  = $rating;
			$args['show_year']    = $year;
			$args['show_runtime'] = $runtime;

			$movies = $movies->posts;

			if ( 'list' == $view ) {
				$movies = self::prepare_list_view( $movies );
			}

			$theme = wp_get_theme();
			if ( ! is_null( $theme->stylesheet ) ) {
				$theme = ' theme-' . $theme->stylesheet;
			} else {
				$theme = '';
			}

			// debug
			$debug = null;
			if ( current_user_can( 'manage_options' ) && '1' == wpmoly_o( 'debug-mode' ) ) {
				$debug = compact( 'main_args', 'permalinks_args' );
			}

			$attributes = compact( 'movies', 'columns', 'title', 'genre', 'year', 'rating', 'runtime', 'theme', 'debug' );

			$content = self::render_template( "movies/grid/$view-loop.php", $attributes, $require = 'always' );
			$js      = self::render_template( "movies/grid/backbone.php", $attributes = compact( 'args' ), $require = 'always' );

			$content = $js . $content;

			return $content;
		}

		/**
		 * Prepare the list view movie list
		 * 
		 * Explode the movie list by letters to show an alphabetical list
		 * 
		 * @since    2.1.1
		 * 
		 * @param    array    $movies Movies to list
		 * 
		 * @return   array    Multidimensionnal array containing prepared movies
		 */
		public static function prepare_list_view( $movies ) {

			global $post;

			$list    = array();
			$default = str_split( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
			$current = '';

			if ( empty( $movies ) )
				return $movies;

			foreach ( $movies as $post ) {

				setup_postdata( $post );

				$_current = substr( remove_accents( get_the_title() ), 0, 1 );
				if ( $_current != $current )
					$current = $_current;

				$list[ $current ][] = array( 'id' => get_the_ID(), 'url' => get_permalink(), 'title' => get_the_title() );
			}
			wp_reset_postdata();

			return $list;
		}

	}

endif;
