<?php
/**
 * Movies Grid 'grid' mode template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $items
 */
?>

	<script type="text/javascript">_wpmoly_grid_<?php echo $grid->id; ?> = <?php echo $grid->toJSON(); ?>;</script>
	<div id="wpmoly-grid-<?php echo $grid->id; ?>" class="wpmoly grid-<?php echo $grid->id; ?> shortcode movies grid theme-<?php echo $grid->get_theme(); ?>" data-grid="<?php echo $grid->id; ?>" data-columns="<?php echo $grid->get_columns(); ?>">
		<div class="grid-menu settings-menu"></div>
		<div class="grid-settings"></div>
		<div class="grid-customs"></div>
		<div class="grid-content grid clearfix">

<?php
if ( $items->has_items() ) :
	while ( $items->has_items() ) :
		$movie = $items->the_item();
?>
			<div class="node post-node movie">
				<div class="node-poster post-poster movie-poster" style="background-image:url(<?php $movie->get_poster()->render( 'medium' ); ?>)">
					<a href="<?php echo get_the_permalink( $movie->id ); ?>"></a>
				</div>
				<div class="node-title post-title movie-title"><a href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->the( 'title' ); ?></a></div>
				<div class="node-genres post-genres movie-genres"><?php echo apply_filters( 'wpmoly/shortcode/format/genres/value', $movie->get( 'genres' ) ); ?></div>
				<div class="node-runtime post-runtime movie-runtime"><?php echo apply_filters( 'wpmoly/shortcode/format/runtime/value', $movie->get( 'runtime' ) ); ?></div>
			</div>
<?php
	endwhile;
endif;
?>
		</div>
<?php if ( $grid->show_pagination() ) : ?>
		<div class="grid-menu pagination-menu clearfix">
			<div class="grid-menu-inner">
<?php if ( ! $grid->is_first_page() ) : ?>
				<a href="<?php echo esc_url( $grid->get_previous_page_url() ); ?>" data-action="grid-paginate" data-value="prev" class="button left"><span class="wpmolicon icon-arrow-left"></span></a>
<?php else : ?>
				<a class="button left disabled"><span class="wpmolicon icon-arrow-left"></span></a>
<?php endif; ?>
				<div class="pagination-menu">Page <span class="current-page"><?php echo esc_attr( $grid->get_current_page() ); ?></span> of <span class="total-pages"><?php echo esc_attr( $grid->get_total_pages() ); ?></span></div>
<?php if ( ! $grid->is_last_page() ) : ?>
				<a href="<?php echo esc_url( $grid->get_next_page_url() ); ?>" data-action="grid-paginate" data-value="next" class="button right"><span class="wpmolicon icon-arrow-right"></span></a>
<?php else : ?>
				<a class="button right disabled"><span class="wpmolicon icon-arrow-right"></span></a>
<?php endif; ?>
			</div>
		</div>
<?php endif; ?>
	</div>
