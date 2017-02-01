
<# if ( 1 < data.current_page ) { #>
			<button data-action="grid-navigate" data-value="prev" class="button left"><span class="wpmolicon icon-arrow-left"></span></button>
<# } else { #>
			<button class="button left disabled"><span class="wpmolicon icon-arrow-left"></span></button>
<# } #>
			<div class="pagination-menu">Page <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="{{ data.current_page }}" /></span> of <span class="total-pages">{{ data.total_page }}</span></div>
<# if ( data.current_page < data.total_page ) { #>
			<button data-action="grid-navigate" data-value="next" class="button right"><span class="wpmolicon icon-arrow-right"></span></button>
<# } else { #>
			<button class="button right disabled"><span class="wpmolicon icon-arrow-right"></span></button>
<# } #>
