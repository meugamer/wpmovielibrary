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
 */
?>

	<div id="wpmoly-grid-<?php echo $grid->id; ?>" class="wpmoly grid" data-grid="<?php echo $grid->id; ?>">
		<div class="grid-json"><?php echo $grid->toJSON(); ?></div>
		<div class="grid-menu settings-menu">
			<div class="grid-menu-inner">
				<button type="button" disabled="disabled" class="button left"><span class="wpmolicon icon-order"></span></button>
				<button type="button" disabled="disabled" class="button right"><span class="wpmolicon icon-settings"></span></button>
			</div>
		</div>
		<div class="grid-content clearfix">
			<noscript><?php _e( 'JavaScript seems to be disabled in your browser. You must have JavaScript enabled in your browser to use this feature.', 'wpmovielibrary' ); ?></noscript>
		</div>
		<div class="grid-menu pagination-menu">
			<div class="grid-menu-inner">
<?php if ( ! $grid->is_first_page() ) { ?>
				<button data-action="grid-navigate" data-value="prev" class="button left" title="<?php _e( 'Previous Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-left"></span></button>
<?php } else { ?>
				<button class="button left disabled"><span class="wpmolicon icon-arrow-left"></span></button>
<?php } ?>
				<div class="pagination-menu"><?php _e( 'Page', 'wpmovielibrary' ); ?> <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="<?php echo $grid->get_current_page() ; ?>" /></span> <?php _e( 'of', 'wpmovielibrary' ); ?> <span class="total-pages"><?php echo $grid->get_total_pages() ; ?></span></div>
<?php if ( ! $grid->is_last_page() ) { ?>
				<button data-action="grid-navigate" data-value="next" class="button right" title="<?php _e( 'Next Page', 'wpmovielibrary' ); ?>"><span class="wpmolicon icon-arrow-right"></span></button>
<?php } else { ?>
				<button class="button right disabled"><span class="wpmolicon icon-arrow-right"></span></button>
<?php } ?>
			</div>
		</div>
	</div>
