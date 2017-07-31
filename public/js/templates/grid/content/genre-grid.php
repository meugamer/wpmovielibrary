<?php
/**
 * Default Genre Grid JavaScript template.
 * 
 * @since 3.0
 */

?>

				<div class="node-thumbnail node-picture term-picture genre-picture" style="background-image:url({{ data.node.get( 'thumbnail' ) }})">
					<a href="{{ data.node.get( 'link' ) }}"></a>
				</div>
				<div class="node-name term-name genre-name"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
				<div class="node-count term-count genre-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
