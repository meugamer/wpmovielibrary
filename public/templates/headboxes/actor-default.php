<?php
/**
 * Actor Headbox default template.
 * 
 * @since    3.0
 * 
 * @uses    $headbox
 * @uses    $actor
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>

	<div id="actor-headbox-<?php echo $headbox->id; ?>" class="wpmoly term-headbox actor-headbox theme-<?php echo $headbox->get_theme(); ?>">
		<div class="headbox-header">
			<div class="headbox-thumbnail">
				<img src="<?php echo $actor->get_picture(); ?>" alt="" />
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-titles">
				<div class="headbox-title">
					<div class="term-title actor-title"><a href="<?php echo get_term_link( $actor->term, 'actor' ); ?>"><?php $actor->the_name(); ?></a></div>
				</div>
				<div class="headbox-subtitle">
					<div class="term-count actor-count"><?php printf( _n( '%d Movie', '%d Movies', $actor->term->count, 'wpmovielibrary' ), $actor->term->count ); ?></div>
				</div>
			</div>
			<div class="headbox-metadata">
				<div class="headbox-description">
					<div class="term-description actor-description"><?php $actor->the_description(); ?></div>
				</div>
			</div>
		</div>
	</div>
<?php else : ?>

	<div id="actor-headbox-{{ data.node.get( 'id' ) }}" class="wpmoly term-headbox actor-headbox theme-{{ data.settings.get( 'theme' ) }}">
		<div class="headbox-header">
			<div class="headbox-thumbnail">
				<img src="{{ data.node.get( 'thumbnail' ) }}" alt="" />
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-titles">
				<div class="headbox-title">
					<div class="term-title actor-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
				</div>
				<div class="headbox-subtitle">
					<div class="term-count actor-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
				</div>
			</div>
			<div class="headbox-metadata">
				<div class="headbox-description">
					<div class="term-description actor-description">{{ data.node.get( 'description' ) }}</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>