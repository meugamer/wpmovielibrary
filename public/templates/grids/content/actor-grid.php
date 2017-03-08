<?php
/**
 * Default Actor Grid template.
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
			$actor = $items->the_item();
?>
				<div class="node term-node actor">
					<div class="node-thumbnail node-picture term-picture actor-picture" style="background-image:url(<?php echo $actor->get_picture(); ?>)">
						<a href="<?php echo get_term_link( $actor->term, 'actor' ); ?>"></a>
					</div>
					<div class="node-name term-name actor-name"><a href="<?php echo get_term_link( $actor->term, 'actor' ); ?>"><?php $actor->the( 'name' ); ?></a></div>
					<div class="node-count term-count actor-count"><?php printf( _n( '%d Movie', '%d Movies', $actor->term->count, 'wpmovielibrary' ), $actor->term->count ); ?></div>
				</div>

<?php
		endwhile;
	else :
?>
				<div class="empty-nodes">
					<p><em><?php _e( 'No actors found matching you request.', 'wpmovielibrary' ); ?></em></p>
				</div>
<?php
	endif;
?>
			</div>
<?php else : ?>

				<div class="node-thumbnail node-picture term-picture actor-picture" style="background-image:url({{ data.node.get( 'thumbnail' ) }})">
					<a href="{{ data.node.get( 'link' ) }}"></a>
				</div>
				<div class="node-name term-name actor-name"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
				<div class="node-count term-count actor-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
<?php endif; ?>