
					<div id="wpmoly-movie-{{ data.post.post_id }}" class="wpmoly grid movie" style="<# if ( '' != data.size.height ) { #>height:{{ data.size.height }}px;<# } if ( '' != data.size.height ) { #>width:{{ data.size.width }}px<# } #>">
						<a class="wpmoly grid movie link" title="{{ data.meta.title }}" href="">
							<?php if ( has_post_thumbnail() ) the_post_thumbnail( $size, array( 'class' => 'wpmoly grid movie poster' ) ); ?>
<?php 	if ( $title ) : ?>
							<h4 class="wpmoly grid movie title">{{ data.meta.title }}</h4>
<?php 	endif; if ( $year ) : ?>
							<span class="wpmoly grid movie year">{{ data.meta.year }}</span>
<?php 	endif; if ( $rating ) : ?>
							<span class="wpmoly grid movie rating">{{ data.details.star }}</span>
<?php 	endif; ?>
							<span class="wpmoly grid movie genres">{{ data.meta.genres }}</span>
							<span class="wpmoly grid movie runtime"><# if ( '' != data.meta.runtime ) { #>{{ data.meta.runtime }}min<# } #></span>
						</a>
					</div>
