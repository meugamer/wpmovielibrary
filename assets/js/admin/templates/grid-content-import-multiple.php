
			<div id="importer-search-form">
				<div id="importer-search-list-form">
					<div id="importer-search-list-container">
						<textarea id="importer-search-list"><?php
$draftees = WPMOLY_Import::get_draftees();
foreach ( $draftees as $i => $draftee ) {
	$draftees[ $i ] = $draftee['title'];
}
echo implode( ', ', $draftees );
?></textarea>
						<div id="importer-search-list-menu" class="">
							<a id="importer-search-list-add" href="#"><span class="wpmolicon icon-yes"></span></a><a id="importer-search-list-quit" href="#"><span class="wpmolicon icon-no-alt"></span></a>
							<div id="confirm-quit-container"><?php _e( 'Are you sure you want to do this?', 'wpmovielibrary' ); ?> <a href="#"><?php _e( 'Yes', 'wpmovielibrary' ); ?></a> &bull; <a href="#"><?php _e( 'No', 'wpmovielibrary' ); ?></a></div>
							<div id="importer-loading-bar"><div id="importer-loading"></div><div id="importer-loading-label">Creating movie 42 on 100…</div></div>
						</div>
					</div>
					<ul id="importer-search-list-draftees"></ul>
				</div>
			</div>

			<div id="importer-search-container">
				<div id="importer-search-settings"></div>
			</div>
