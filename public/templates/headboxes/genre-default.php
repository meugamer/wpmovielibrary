<?php
/**
 * Genre Headbox default template.
 * 
 * @since    3.0
 * 
 * @uses    $headbox
 * @uses    $genre
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>
	<div id="genre-headbox-<?php echo $headbox->id; ?>" class="wpmoly term-headbox genre-headbox theme-<?php echo $headbox->get_theme(); ?>">
		<div class="headbox-header">
			<div class="headbox-thumbnail">
				<img src="<?php echo $genre->get_thumbnail(); ?>" alt="" />
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-titles">
				<div class="headbox-title">
					<div class="term-title genre-title"><a href="<?php echo get_term_link( $genre->term, 'genre' ); ?>"><?php $genre->the_name(); ?></a></div>
				</div>
				<div class="headbox-subtitle">
					<div class="term-count genre-count"><?php printf( _n( '%d Movie', '%d Movies', $genre->term->count, 'wpmovielibrary' ), $genre->term->count ); ?></div>
				</div>
			</div>
			<div class="headbox-metadata">
				<div class="headbox-description">
					<div class="term-description genre-description"><?php $genre->the_description(); ?></div>
				</div>
			</div>
		</div>
	</div>

<?php else : ?>
	<div id="genre-headbox-{{ data.node.get( 'id' ) }}" class="wpmoly term-headbox genre-headbox theme-{{ data.settings.get( 'theme' ) }}">
		<div class="headbox-header">
			<div class="headbox-thumbnail">
				<img src="{{ data.node.get( 'thumbnail' ) }}" alt="" />
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-titles">
				<div class="headbox-title">
					<div class="term-title genre-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
				</div>
				<div class="headbox-subtitle">
					<div class="term-count genre-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
				</div>
			</div>
			<div class="headbox-metadata">
				<div class="headbox-description">
					<div class="term-description genre-description">{{ data.node.get( 'description' ).rendered }}</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>