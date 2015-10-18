<?php Namespace WordPress\Plugin\Encyclopedia ?>
<table class="form-table">
<tr>
	<th><label for="encyclopedia_type"><?php Echo I18n::t('Encyclopedia type') ?></label></th>
	<td>
		<select name="encyclopedia_type" id="encyclopedia_type">
      <option value="" <?php Disabled(True) ?> ><?php Echo I18n::t('Encyclopedia') ?></option>
      <option value="lexicon" <?php Selected(True) ?> ><?php Echo I18n::t('Lexicon') ?></option>
      <option value="" <?php Disabled(True) ?> ><?php Echo I18n::t('Wiki') ?></option>
      <option value="" <?php Disabled(True) ?> ><?php Echo I18n::t('Knowledge Base') ?></option>
      <option value="" <?php Disabled(True) ?> ><?php Echo I18n::t('Glossary') ?></option>
      <option value="" <?php Disabled(True) ?> ><?php Echo I18n::t('Dictionary') ?></option>
    </select><?php Echo Mocking_Bird::Pro_Notice('unlock') ?><br>
		<small>
      <?php Echo I18n::t('Please choose the type of your encyclopedia. This option does not change the behavior of the plugin. It\'s just for the labels and captions in the backend.') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="embed_default_style"><?php Echo I18n::t('Use default style') ?></label></th>
  <td>
		<select name="embed_default_style" id="embed_default_style">
			<option value="1" <?php Selected(Options::Get('embed_default_style')) ?> ><?php _e('Yes') ?></option>
			<option value="0" <?php Selected(!Options::Get('embed_default_style')) ?> ><?php _e('No') ?></option>
		</select><br>
		<small>
      <?php Echo I18n::t('Set this option to "No" if you want to use your own style for the encyclopedia.') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="enable_revisions"><?php Echo I18n::t('Enable revisions') ?></label></th>
  <td>
		<select name="" id="enable_revisions" <?php Disabled(True) ?> >
			<option ><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?>><?php _e('No') ?></option>
		</select><?php Echo Mocking_Bird::Pro_Notice('unlock') ?><br>
		<small>
      <?php Echo I18n::t('Enables or disables revisions for the encyclopedia terms.') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="enable_comments"><?php Echo I18n::t('Enable comments') ?></label></th>
  <td>
		<select name="" id="enable_comments" <?php Disabled(True) ?> >
			<option <?php Disabled(True) ?> ><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?>><?php _e('No') ?></option>
		</select><?php Echo Mocking_Bird::Pro_Notice('unlock') ?><br>
		<small>
      <?php Echo I18n::t('Enables or disables comments and trackbacks for the encyclopedia terms.') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="enable_thumbnails"><?php Echo I18n::t('Enable thumbnails') ?></label></th>
  <td>
		<select name="" id="enable_thumbnails" <?php Disabled(True) ?> >
			<option <?php Disabled(True) ?> ><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?>><?php _e('No') ?></option>
		</select><?php Echo Mocking_Bird::Pro_Notice('unlock') ?><br>
		<small>
      <?php Echo I18n::t('Enables or disables the "Featured Image" for the encyclopedia terms.') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="enable_custom_fields"><?php Echo I18n::t('Enable custom fields') ?></label></th>
  <td>
		<select name="enable_custom_fields" id="enable_custom_fields">
			<option value="1" <?php Selected(Options::Get('enable_custom_fields')) ?> ><?php _e('Yes') ?></option>
			<option value="0" <?php Selected(!Options::Get('enable_custom_fields')) ?> ><?php _e('No') ?></option>
		</select><br>
		<small><?php Echo I18n::t('Enables or disables the "Custom Fields" for the encyclopedia terms.') ?></small>
	</td>
</tr>

</table>