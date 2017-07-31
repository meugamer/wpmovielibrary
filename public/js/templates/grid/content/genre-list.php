<?php
/**
 * Default Genre List JavaScript template.
 * 
 * @since 3.0
 */

?>

					<div class="node-title genre-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
					<div class="node-count genre-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
