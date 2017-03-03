<?php
/**
 * Default Genre Grid template.
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
			$genre = $items->the_item();
?>
				<div class="node term-node genre">
					<div class="node-thumbnail node-picture term-picture genre-picture" style="background-image:url(<?php echo $genre->get_thumbnail(); ?>)">
						<a href="<?php echo get_term_link( $genre->term, 'genre' ); ?>"></a>
					</div>
					<div class="node-name term-name genre-name"><a href="<?php echo get_term_link( $genre->term, 'genre' ); ?>"><?php $genre->the( 'name' ); ?></a></div>
					<div class="node-count term-count genre-count"><?php printf( _n( '%d Movie', '%d Movies', $genre->term->count, 'wpmovielibrary' ), $genre->term->count ); ?></div>
				</div>

<?php
		endwhile;
	endif;
?>
			</div>
<?php else : ?>

				<div class="node-thumbnail node-picture term-picture genre-picture" style="background-image:url({{ data.node.get( 'thumbnail' ) }})">
					<a href="{{ data.node.get( 'link' ) }}"></a>
				</div>
				<div class="node-name term-name genre-name"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
				<div class="node-count term-count genre-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
<?php endif; ?>