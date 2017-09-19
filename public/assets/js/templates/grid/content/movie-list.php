<?php
/**
 * Default Movie List JavaScript template.
 *
 * @since 3.0
 */

?>

					<div class="node-thumbnail node-poster post-poster movie-poster" style="background-image:url({{ data.node.get( 'poster' ).sizes.thumbnail.url }})">
						<a href="{{ data.node.get( 'link' ) }}"></a>
					</div>
					<div class="node-title movie-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'title' ).rendered }}</a></div>
					<div class="node-meta movie-meta"><!--<span class="movie-runtime">{{ data.node.get( 'meta' ).runtime.rendered }}</span>&nbsp;−&nbsp;--><span class="movie-genres">{{ data.node.get( 'meta' ).genres.raw }}</span></div>
