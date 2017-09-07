<?php
/**
 * Variant-1 Movie Grid JavaScript template.
 *
 * @since 3.0
 */

?>

				<div class="node-thumbnail node-poster post-poster movie-poster" style="background-image:url({{ data.node.get( 'poster' ).sizes.medium.url }})">
					<a href="{{ data.node.get( 'link' ) }}"></a>
				</div>
				<div class="node-content">
					<div class="node-title post-title movie-title"><a href="{{ data.node.get( 'link' ) }}">{{{ data.node.get( 'title' ).rendered }}}</a></div>
					<div class="node-genres post-genres movie-genres">{{{ data.node.get( 'meta' ).genres.rendered }}}</div>
					<div class="node-runtime post-runtime movie-runtime">{{{ data.node.get( 'meta' ).rating.rendered }}}</div>
					<div class="node-readmore"><a href="{{ data.node.get( 'link' ) }}">+</a></div>
				</div>
