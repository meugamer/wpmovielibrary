
					<div id="wpmoly-movie-{{ data.id }}" class="wpmoly grid movie">
						<div class="movie-preview" style="<# if ( '' != data.size.height ) { #>height:{{ data.size.height - 12 }}px;<# } if ( '' != data.size.width ) { #>width:{{ data.size.width - 8 }}px<# } #>">
							<a class="wpmoly grid movie link" title="{{ data.meta.title }}" href="">
								<!--<img src="{{ data.thumbnail.medium.file }}" alt="" />-->
							</a>
						</div>
						<a class="wpmoly grid movie link" title="{{ data.meta.title }}" href="">
							<h4 class="wpmoly grid movie title">{{ data.meta.title }}</h4>
						</a>
						<span class="wpmoly grid movie genres">{{ data.meta.genres }}</span>
						<span class="wpmoly grid movie year">{{ data.meta.year }}</span>
						<span class="wpmoly grid movie runtime"><# if ( '' != data.meta.runtime ) { #>{{ data.meta.runtime }}min<# } #></span>
						<span class="wpmoly grid movie rating">{{{ data.details.stars }}}</span>
					</div>
