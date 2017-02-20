
				<# console.log( data.movie ) #>
				<div class="node-poster post-poster movie-poster" style="background-image:url({{ data.movie.get( 'poster' ).sizes.medium.url }})">
					<a href="{{ data.movie.get( 'link' ) }}"></a>
				</div>
				<div class="node-title post-title movie-title"><a href="{{ data.movie.get( 'link' ) }}">{{ data.movie.get( 'title' ).rendered }}</a></div>
				<div class="node-genres post-genres movie-genres"></div>
				<div class="node-runtime post-runtime movie-runtime"></div>
