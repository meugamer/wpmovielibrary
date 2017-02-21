
					<!--<div class="node-picture term-picture actor-picture" style="background-image:url({{ data.node.get( 'thumbnail' ) }})"></div>-->
					<div class="node-name actor-name"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
					<div class="node-count actor-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>