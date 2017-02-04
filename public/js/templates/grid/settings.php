
		<div class="grid-setting-block full-col letter-setting">
<?php
$letters = str_split( '#0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' );
foreach ( $letters as $letter ) {
?>
			<label><input type="radio" name="grid-settings[letter][]" data-setting-type="letter" data-setting-value="<?php echo $letter; ?>" value="<?php echo $letter; ?>" <# if ( '<?php echo $letter; ?>' == data.query.get( 'letter' ) ) { #>checked="checked" <# } #>/><span class="letter"><?php echo $letter; ?></span></label>

<?php
}
?>
			<label><input type="radio" name="grid-settings[letter][]" data-setting-type="letter" data-setting-value="" value="" <# if ( '' == data.query.get( 'letter' ) || 'all' == data.query.get( 'letter' ) ) { #>checked="checked" <# } #>/><span class="letter"><?php _e( 'All', 'wpmovielibrary' ); ?></span></label>
		</div>

<# if ( 'movie' === data.settings.get( 'type' ) ) { #>
		<div class="grid-setting-block half-col orderby-setting">
			<span class="grid-setting-label"><?php _e( 'Order by:', 'wpmovielibrary' ); ?></span>
			<label><input type="radio" name="grid-settings[orderby][]" data-setting-type="orderby" data-setting-value="post_title" value="post_title" <# if ( 'post_title' == data.query.get( 'orderby' ) ) { #>checked="checked" <# } #>/><span class="value"><?php _e( 'Post Title', 'wpmovielibrary' ); ?></span></label>
			<label><input type="radio" name="grid-settings[orderby][]" data-setting-type="orderby" data-setting-value="post_date" value="post_title" <# if ( 'post_date' == data.query.get( 'orderby' ) ) { #>checked="checked" <# } #>/><span class="value"><?php _e( 'Post Date', 'wpmovielibrary' ); ?></span></label>
		</div>
<# } #>

		<div class="grid-setting-block half-col order-setting">
			<span class="grid-setting-label"><?php _e( 'Order:', 'wpmovielibrary' ); ?></span>
			<label><input type="radio" name="grid-settings[order][]" data-setting-type="order" data-setting-value="ASC" value="" <# if ( 'ASC' == data.query.get( 'order' ) ) { #>checked="checked" <# } #>/><span class="value"><?php _e( 'Ascendingly' ); ?></span></label>
			<label><input type="radio" name="grid-settings[order][]" data-setting-type="order" data-setting-value="DESC" value="" <# if ( 'DESC' == data.query.get( 'order' ) ) { #>checked="checked" <# } #>/><span class="value"><?php _e( 'Descendingly' ); ?></span></label>
		</div>

		<button class="grid-settings-apply" type="button" data-action="apply"><?php _e( 'Apply', 'wpmovielibrary' ); ?></button>
