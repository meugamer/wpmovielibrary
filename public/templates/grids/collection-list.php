<?php
/**
 * Collections Grid 'list' mode template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $items
 */
?>

	<div id="wpmoly-grid-<?php echo $grid->id; ?>" class="wpmoly shortcode collections grid list theme-<?php echo $grid->get_theme(); ?>">
<?php if ( $grid->show_menu() ) : ?>
		<div class="grid-menu clearfix">
			<button type="button" data-action="grid-menu" class="button left"><span class="wpmolicon icon-order"></span></button>
			<button type="button" data-action="grid-settings" class="button right"><span class="wpmolicon icon-settings"></span></button>
		</div>
<?php endif; ?>
		<div class="grid-content list clearfix">

<?php if ( $items->has_items() ) : ?>
			<ul class="nodes-list collections-list">
<?php
	while ( $items->has_items() ) :
		$collection = $items->the_item();
?>
				<li class="node term-node collection">
					<div class="node-name collection-name"><a href="<?php echo get_term_link( $collection->term, 'collection' ); ?>"><?php $collection->the( 'name' ); ?></a></div>
					<div class="node-count collection-count"><?php printf( _n( '%d Movie', '%d Movies', $collection->term->count, 'wpmovielibrary' ), $collection->term->count ); ?></div>
				</li>
<?php endwhile; ?>
			</ul>
<?php endif; ?>
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
