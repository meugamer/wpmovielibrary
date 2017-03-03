<?php
/**
 * Default Collection List template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $content
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>
			<ul class="grid-content-inner list nodes-list nodes-<?php echo $grid->get_list_columns(); ?>-columns">
<?php
	if ( $items->has_items() ) :
		while ( $items->has_items() ) :
			$collection = $items->the_item();
?>

				<li class="node term-node collection">
					<div class="node-name collection-name"><a href="<?php echo get_term_link( $collection->term, 'collection' ); ?>"><?php $collection->the( 'name' ); ?></a></div>
					<div class="node-count collection-count"><?php printf( _n( '%d Movie', '%d Movies', $collection->term->count, 'wpmovielibrary' ), $collection->term->count ); ?></div>
				</li>
<?php
		endwhile;
	endif;
?>
			</ul>
<?php else : ?>

					<div class="node-name collection-name"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
					<div class="node-count collection-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
<?php endif; ?>