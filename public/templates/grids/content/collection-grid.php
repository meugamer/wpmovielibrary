<?php
/**
 * Default Collection Grid template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $content
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>
			<div class="grid-content-inner grid <?php echo $grid->get_columns(); ?>-columns">
<?php
	if ( $items->has_items() ) :
		while ( $items->has_items() ) :
			$collection = $items->the_item();
?>
				<div class="node term-node collection">
					<div class="node-thumbnail node-picture term-picture collection-picture" style="background-image:url(<?php echo $collection->get_thumbnail(); ?>)">
						<a href="<?php echo get_term_link( $collection->term, 'collection' ); ?>"></a>
					</div>
					<div class="node-name term-name collection-name"><a href="<?php echo get_term_link( $collection->term, 'collection' ); ?>"><?php $collection->the( 'name' ); ?></a></div>
					<div class="node-count term-count collection-count"><?php printf( _n( '%d Movie', '%d Movies', $collection->term->count, 'wpmovielibrary' ), $collection->term->count ); ?></div>
				</div>

<?php
		endwhile;
	else :
?>
				<div class="empty-nodes">
					<p><em><?php _e( 'No collections found matching you request.', 'wpmovielibrary' ); ?></em></p>
				</div>
<?php
	endif;
?>
			</div>
<?php else : ?>

				<div class="node-thumbnail node-picture term-picture collection-picture" style="background-image:url({{ data.node.get( 'thumbnail' ) }})">
					<a href="{{ data.node.get( 'link' ) }}"></a>
				</div>
				<div class="node-name term-name collection-name"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
				<div class="node-count term-count collection-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
<?php endif; ?>