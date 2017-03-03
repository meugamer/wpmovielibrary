<?php
/**
 * Default Actor List template.
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
			$actor = $items->the_item();
?>

				<li class="node term-node actor">
					<div class="node-name actor-name"><a href="<?php echo get_term_link( $actor->term, 'actor' ); ?>"><?php $actor->the( 'name' ); ?></a></div>
					<div class="node-count actor-count"><?php printf( _n( '%d Movie', '%d Movies', $actor->term->count, 'wpmovielibrary' ), $actor->term->count ); ?></div>
				</li>
<?php
		endwhile;
	endif;
?>
			</ul>
<?php else : ?>

					<!--<div class="node-picture term-picture actor-picture" style="background-image:url({{ data.node.get( 'thumbnail' ) }})"></div>-->
					<div class="node-name actor-name"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
					<div class="node-count actor-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
<?php endif; ?>