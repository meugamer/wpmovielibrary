<?php
/**
 * Default Genre List template.
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
			$genre = $items->the_item();
?>

				<li class="node term-node genre">
					<div class="node-title genre-title"><a href="<?php echo get_term_link( $genre->term, 'genre' ); ?>"><?php $genre->the( 'name' ); ?></a></div>
					<div class="node-count genre-count"><?php printf( _n( '%d Movie', '%d Movies', $genre->term->count, 'wpmovielibrary' ), $genre->term->count ); ?></div>
				</li>
<?php
		endwhile;
	else :
?>
				<div class="empty-nodes">
					<p><em><?php _e( 'No genres found matching you request.', 'wpmovielibrary' ); ?></em></p>
				</div>
<?php
	endif;
?>
			</ul>
<?php else : ?>

					<div class="node-title genre-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
					<div class="node-count genre-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
<?php endif; ?>