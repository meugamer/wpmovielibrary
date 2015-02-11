
<?php do_action( 'wpmoly_before_metabox_content', $metabox ); ?>

		<div id="wpmoly-meta" class="wpmoly-meta">

<?php do_action( 'wpmoly_before_metabox_menu', $metabox ); ?>

			<div id="wpmoly-meta-menu-bg"></div>
			<ul id="wpmoly-meta-menu" class="hide-if-no-js">

<?php foreach ( $tabs as $id => $tab ) : ?>

				<li id="wpmoly-meta-<?php echo $id ?>" class="tab<?php echo $tab['active'] ?>"><a class="navigate" href="#wpmoly-meta-<?php echo $id ?>-panel"><span class="<?php echo $tab['icon'] ?>"></span>&nbsp; <span class="text"><?php echo $tab['title'] ?></span><span class="label hide-if-js" title=""><span></a></li>
<?php endforeach; ?>
			</ul>

<?php do_action( 'wpmoly_after_metabox_menu', $metabox ); ?>
<?php do_action( 'wpmoly_before_metabox_panels', $metabox ); ?>

			<div id="wpmoly-meta-panels">

<?php foreach ( $panels as $id => $panel ) : ?>

				<div id="wpmoly-meta-<?php echo $id ?>-panel" class="panel<?php echo $panel['active'] ?> hide-if-js"><?php echo $panel['content'] ?></div>
<?php endforeach; ?>

			</div>

<?php do_action( 'wpmoly_after_metabox_panels', $metabox ); ?>

			<div style="clear:both"></div>

		</div>

<?php do_action( 'wpmoly_after_metabox_content', $metabox ); ?>
