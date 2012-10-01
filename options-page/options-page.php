<div class="wrap">
<?php screen_icon(); ?>
<h2><?php Echo $this->t('Encyclopedia Options') ?></h2>

<?php If (IsSet($_GET['options_saved'])) : ?>
<div id="message" class="updated fade">
	<p><strong><?php _e('Settings saved.') ?></strong></p>
</div>
<?php EndIf; ?>

<form method="post" action="" enctype="multipart/form-data">
<div class="metabox-holder">

	<div class="postbox-container" style="width:69%;">
		<?php ForEach ($this->arr_option_box['main'] AS $box) : ?>
			<div class="postbox should-be-<?php Echo $box['state'] ?>">
				<div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div>
				<h3 class="hndle"><span><?php Echo $box['title'] ?></span></h3>
				<div class="inside"><?php Include $box['file'] ?></div>
			</div>
		<?php EndForEach ?>
	</div>

	<div class="postbox-container" style="width:29%;float:right">
		<?php ForEach ($this->arr_option_box['side'] AS $box) : ?>
			<div class="postbox should-be-<?php Echo $box['state'] ?>">
				<div class="handlediv" title="<?php _e('Click to toggle') ?>"><br /></div>
				<h3 class="hndle"><span><?php Echo $box['title'] ?></span></h3>
				<div class="inside"><?php Include $box['file'] ?></div>
			</div>
		<?php EndForEach ?>
	</div>

	<div class="clear"></div>
</div>

<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">
	<input type="reset" value="<?php _e('Reset') ?>">
</p>

</form>
</div>