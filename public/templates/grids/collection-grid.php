<?php
/**
 * Actors Shortcode view Template
 * 
 * Showing a grid of collections.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $items
 */
?>

	<div id="wpmoly-grid-<?php echo $grid->id; ?>" class="wpmoly shortcode collections grid theme-<?php echo $grid->get_theme(); ?> <?php echo $grid->get_columns(); ?>-columns" data-columns="<?php echo $grid->get_columns(); ?>" data-rows="<?php echo $grid->get_rows(); ?>" data-column-width="<?php echo $grid->get_column_width(); ?>" data-row-height="<?php echo $grid->get_row_height(); ?>">
<?php if ( $grid->show_menu() ) : ?>
		<div class="grid-menu clearfix">
			<button type="button" data-action="grid-menu" class="button left"><span class="wpmolicon icon-order"></span></button>
			<button type="button" data-action="grid-settings" class="button right"><span class="wpmolicon icon-settings"></span></button>
		</div>
<?php endif; ?>
		<div class="grid-content grid clearfix">

<?php
if ( $items->has_items() ) :
	while ( $items->has_items() ) :
		$collection = $items->the_item();
?>
			<div class="node term-node collection" data-width="<?php echo $grid->get_column_width(); ?>" data-height="<?php echo $grid->get_row_height(); ?>">
				<div class="node-picture term-picture collection-picture" style="background-image:url(<?php echo $collection->get_thumbnail(); ?>)">
					<a href="<?php echo get_term_link( $collection->term, 'collection' ); ?>"></a>
				</div>
				<div class="node-name term-name collection-name"><a href="<?php echo get_term_link( $collection->term, 'collection' ); ?>"><?php $collection->the( 'name' ); ?></a></div>
				<div class="node-count term-count collection-count"><?php printf( _n( '%d Movie', '%d Movies', $collection->term->count, 'wpmovielibrary' ), $collection->term->count ); ?></div>
			</div>
<?php
	endwhile;
endif;
?>
		</div>
<?php if ( $grid->show_pagination() ) : ?>
		<div class="grid-menu pagination-menu clearfix">
<?php if ( ! $grid->is_first_page() ) : ?>
			<a href="<?php echo esc_url( $grid->get_previous_page_url() ); ?>" data-action="grid-paginate" data-value="prev" class="button left"><span class="wpmolicon icon-arrow-left"></span></a>
<?php else : ?>
			<a class="button left disabled"><span class="wpmolicon icon-arrow-left"></span></a>
<?php endif; ?>
			<div class="pagination-menu">Page <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="<?php echo esc_attr( $grid->get_current_page() ); ?>" /></span> of <span class="total-pages"><?php echo esc_attr( $grid->get_total_pages() ); ?></span></div>
<?php if ( ! $grid->is_last_page() ) : ?>
			<a href="<?php echo esc_url( $grid->get_next_page_url() ); ?>" data-action="grid-paginate" data-value="next" class="button right"><span class="wpmolicon icon-arrow-right"></span></a>
<?php else : ?>
			<a class="button right disabled"><span class="wpmolicon icon-arrow-right"></span></a>
<?php endif; ?>
		</div>
<?php endif; ?>
	</div>
