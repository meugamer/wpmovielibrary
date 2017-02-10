<?php
/**
 * Actors Grid 'list' mode template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $items
 */
?>

	<script type="text/javascript">_wpmoly_grid_<?php echo $grid->id; ?> = <?php echo $grid->toJSON(); ?>;</script>
	<div id="wpmoly-grid-<?php echo $grid->id; ?>" class="wpmoly grid-<?php echo $grid->id; ?> shortcode actors grid list theme-<?php echo $grid->get_theme(); ?>" data-grid="<?php echo $grid->id; ?>" data-columns="<?php echo $grid->get_list_columns(); ?>">
		<div class="grid-menu settings-menu"></div>
		<div class="grid-settings"></div>
		<div class="grid-content list clearfix">

<?php if ( $items->has_items() ) : ?>
			<ul class="nodes-list actors-list">
<?php
	while ( $items->has_items() ) :
		$actor = $items->the_item();
?>
				<li class="node term-node actor">
					<div class="node-name actor-name"><a href="<?php echo get_term_link( $actor->term, 'actor' ); ?>"><?php $actor->the( 'name' ); ?></a></div>
					<div class="node-count actor-count"><?php printf( _n( '%d Movie', '%d Movies', $actor->term->count, 'wpmovielibrary' ), $actor->term->count ); ?></div>
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
