
					<div id="wpmoly-movie-{{ data.post.post_id }}" class="wpmoly grid movie" style="<# if ( '' != data.size.height ) { #>height:{{ data.size.height }}px;<# } if ( '' != data.size.height ) { #>width:{{ data.size.width }}px<# } #>">
						<a class="wpmoly grid movie link" title="{{ data.meta.title }}" href="">
							<img src="{{ data.post.post_thumbnail }}" alt="" />
							<h4 class="wpmoly grid movie title">{{ data.meta.title }}</h4>
							<span class="wpmoly grid movie year">{{ data.meta.year }}</span>
							<span class="wpmoly grid movie rating">{{ data.details.star }}</span>
							<span class="wpmoly grid movie genres">{{ data.meta.genres }}</span>
							<span class="wpmoly grid movie runtime"><# if ( '' != data.meta.runtime ) { #>{{ data.meta.runtime }}min<# } #></span>
						</a>
					</div>
