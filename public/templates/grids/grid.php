<?php
/**
 * Default Grid template.
 * 
 * The Grid content is dynamically generate using JavaScript. You really
 * should not edit this file.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $content
 */
?>

	<div id="wpmoly-grid-<?php echo $grid->id; ?>" class="wpmoly grid theme-<?php echo $grid->get_theme(); ?>" data-grid="<?php echo $grid->id; ?>">
		<div class="grid-json"><?php echo $grid->toJSON(); ?></div>
		<div class="grid-menu settings-menu">
			<div class="grid-menu-inner">
				<button type="button" disabled="disabled" class="button left"><span class="wpmolicon icon-order"></span></button>
				<button type="button" disabled="disabled" class="button right"><span class="wpmolicon icon-settings"></span></button>
			</div>
		</div>
		<div class="grid-content clearfix">
			<noscript><?php _e( 'JavaScript seems to be disabled in your browser. You should enable it to use this feature in the best conditions.', 'wpmovielibrary' ); ?></noscript>
<?php $content->render(); ?>
		</div>
		<div class="grid-menu pagination-menu">
			<div class="grid-menu-inner">
<?php if ( ! $grid->is_first_page() ) : ?>
			<a href="<?php echo esc_url( $grid->get_previous_page_url() ); ?>" class="button left"><span class="wpmolicon icon-arrow-left"></span></a>
<?php else : ?>
			<a class="button left disabled"><span class="wpmolicon icon-arrow-left"></span></a>
<?php endif; ?>
			<div class="pagination-menu">Page <span class="current-page"><input type="text" size="1" readonly="true" value="<?php echo esc_attr( $grid->get_current_page() ); ?>" /></span> of <span class="total-pages"><?php echo esc_attr( $grid->get_total_pages() ); ?></span></div>
<?php if ( ! $grid->is_last_page() ) : ?>
			<a href="<?php echo esc_url( $grid->get_next_page_url() ); ?>" class="button right"><span class="wpmolicon icon-arrow-right"></span></a>
<?php else : ?>
			<a class="button right disabled"><span class="wpmolicon icon-arrow-right"></span></a>
<?php endif; ?>
			</div>
		</div>
	</div>
