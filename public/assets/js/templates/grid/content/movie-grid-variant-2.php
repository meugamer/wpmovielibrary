<?php
/**
 * Variant-2 Movie Grid JavaScript template.
 *
 * @since 3.0
 */

?>

				<div class="node-thumbnail node-poster post-poster movie-poster" style="background-image:url({{ data.node.get( 'poster' ).sizes.medium.url }})">
					<a href="{{ data.node.get( 'link' ) }}" title="{{ data.node.get( 'title' ).raw }}"></a>
				</div>
				<div class="node-content clearfix">
					<div class="movie-title"><a href="{{ data.node.get( 'link' ) }}">{{{ data.node.get( 'title' ).rendered }}}</a></div>
					<div class="movie-year">{{{ data.node.get( 'meta' ).year.rendered }}}</div>
					<div class="movie-rating">{{{ data.node.get( 'meta' ).rating.raw }}}</div>
				</div>
