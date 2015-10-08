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

			$content  = '';
			$defaults = array(
				'backbone'   => wpmoly_o( 'movie-backbone-grid', $default = true ),
				'columns'    => wpmoly_o( 'movie-archives-grid-columns', $default = true ),
				'rows'       => wpmoly_o( 'movie-archives-grid-rows', $default = true ),
				'paged'      => 1,
				'category'   => null,
				'tag'        => null,
				'collection' => null,
				'actor'      => null,
				'genre'      => null,
				'meta'       => null,
				'detail'     => null,
				'value'      => null,
				'title'      => false,
				'year'       => false,
				'rating'     => false,
				'letter'     => null,
				'order'      => wpmoly_o( 'movie-archives-movies-order', $default = true ),
				'orderby'    => 'post_title',
				'view'       => 'grid'
			);
			$args = wp_parse_args( $args, $defaults );

			// debug
			$main_args = $args;

			extract( $args, EXTR_SKIP );
			$total  = 0;

			$grid_meta = (array) wpmoly_o( 'movie-archives-movies-meta', $default = true );
			$grid_meta = array_keys( $grid_meta['used'] );
			$title  = ( $title || in_array( 'title', $grid_meta ) );
			$genre  = ( $genre || in_array( 'genre', $grid_meta ) );
			$rating = ( $rating || in_array( 'rating', $grid_meta ) );
			$year   = ( $year || in_array( 'year', $grid_meta ) );

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

			$movies = self::_get_movies( $args );

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

			$attributes = compact( 'movies', 'columns', 'title', 'year', 'rating', 'theme', 'debug' );

			$content = self::render_template( "movies/grid/$view-loop.php", $attributes, $require = 'always' );
			$js      = self::render_template( "movies/grid/backbone.php", $attributes = compact( 'args' ), $require = 'always' );

			$content = $js . $content;

			return $content;
		}

		public static function _get_movies( $args ) {

			global $wpdb, $wp_query;

			extract( $args, EXTR_SKIP );

			$movies = array();
			$total  = wp_count_posts( 'movie' );
			$total  = $total->publish;

			$select = array( 'SQL_CALC_FOUND_ROWS DISTINCT ID' );

			// Limit the maximum number of terms to get
			$number = $columns * $rows;
			$limit = wpmoly_o( 'movie-archives-movies-limit', $default = true );
			if ( -1 == $number )
				$number = $limit;

			$columns = min( $columns, 8 );
			if ( 0 > $columns )
				$columns = wpmoly_o( 'movie-archives-grid-columns', $default = true );

			$rows = min( $rows, 12 );
			if ( 0 > $rows )
				$rows = wpmoly_o( 'movie-archives-grid-rows', $default = true );

			// Calculate offset
			$offset = 0;
			if ( $paged )
				$offset = max( 0, $number * ( $paged - 1 ) );

			if ( '' == $meta && '' != $detail ) {
				$meta = $detail;
				$type = 'detail';
			}
			else {
				$type = 'meta';
			}

			// Don't use LIMIT with weird values
			$limit = "LIMIT 0,$number";
			if ( $offset >= $number )
				$limit = sprintf( 'LIMIT %d,%d', $offset, $number );

			$where = array( "post_type='movie'", " AND post_status='publish'" );
			if ( '' != $letter )
				$where[] = " AND post_title LIKE '" . wpmoly_esc_like( $letter ) . "%'";

			$join = array();
			$meta_query = array( 'join' => array(), 'where' => array() );
			if ( '' != $value && '' != $meta ) {

				$meta_query = call_user_func( "WPMOLY_Search::by_$meta", $value, 'sql' );

				$join[]  = $meta_query['join'];
				$where[] = $meta_query['where'];
			}

			$tax_query = array();
			if ( ! is_null( $collection ) && ! empty( $collection ) ) {
				$tax_query = array(
					'taxonomy' => 'collection',
					'terms'    => $collection,
				);
			} elseif ( ! is_null( $genre ) && ! empty( $genre ) ) {
				$tax_query = array(
					'taxonomy' => 'genre',
					'terms'    => $genre,
				);
			} elseif ( ! is_null( $actor ) && ! empty( $actor ) ) {
				$tax_query = array(
					'taxonomy' => 'actor',
					'terms'    => $actor,
				);
			} elseif ( ! is_null( $category ) && ! empty( $category ) ) {
				$tax_query = array(
					'taxonomy' => 'category',
					'terms'    => $category,
				);
			} elseif ( ! is_null( $tag ) && ! empty( $tag ) ) {
				$tax_query = array(
					'taxonomy' => 'post_tag',
					'terms'    => $tag,
				);
			}

			if ( ! empty( $tax_query ) ) {

				$tax_query = array(
					'relation' => 'OR',
					array(
						'taxonomy' => $tax_query['taxonomy'],
						'field'    => 'slug',
						'terms'    => $tax_query['terms'],
					),
					array(
						'taxonomy' => $tax_query['taxonomy'],
						'field'    => 'name',
						'terms'    => $tax_query['terms'],
					)
				);
				$tax_query = get_tax_sql( $tax_query, $wpdb->posts, 'ID' );

				$join[]  = $tax_query['join'];
				$where[] = $tax_query['where'];
			}

			$_orderby = array(
				'year'       => 'release_date',
				'date'       => 'release_date',
				'localdate'  => 'local_release_date',
				'rating'     => 'rating'
			);

			if ( in_array( $orderby, array_keys( $_orderby ) ) ) {
				$select[] = ' pm.meta_value AS value';
				$join[]   = ' INNER JOIN wp_postmeta AS pm ON ( wp_posts.ID = pm.post_id )';
				$where[]  = ' AND pm.meta_key = "_wpmoly_movie_' . $_orderby[ $orderby ] . '"';
				$orderby = 'value';
			} elseif ( 'post_date' == $orderby ) {
				$orderby = 'post_date';
			} else {
				$orderby = 'post_title';
			}

			$where  = implode( '', $where );
			$join   = implode( '', $join );
			$select = implode( ',', $select );

			$query = "SELECT {$select} FROM {$wpdb->posts} {$join} WHERE {$where} ORDER BY {$orderby} {$order} {$limit}";

			$movies = $wpdb->get_col( $query );
			$total  = $wpdb->get_var( 'SELECT FOUND_ROWS() AS total' );
			$movies = array_map( 'get_post', $movies );

			return $movies;
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
