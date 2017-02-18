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
		<noscript><?php _e( 'JavaScript seems to be disabled in your browser. You must have JavaScript enabled in your browser to use this feature.', 'wpmovielibrary' ); ?></noscript>
	</div>
