<?php Namespace WordPress\Plugin\Encyclopedia ?>
<table class="form-table">
<tr>
  <th><label for="encyclopedia_categories"><?php Echo I18n::t('Activate categories') ?></label></th>
  <td>
		<select id="encyclopedia_categories" <?php Disabled(True) ?>>
			<option <?php Disabled(True) ?> ><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?> ><?php _e('No') ?></option>
		</select><?php Echo Mocking_Bird::Pro_Notice('unlock') ?><br>
		<small>
      <?php Echo I18n::t('Categories can help you create an awesome knowledge base.') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="encyclopedia_tags"><?php Echo I18n::t('Activate tags') ?></label></th>
  <td>
		<select name="encyclopedia_tags" id="encyclopedia_tags">
			<option value="1" <?php Selected(Options::Get('encyclopedia_tags')) ?> ><?php _e('Yes') ?></option>
			<option value="0" <?php Selected(!Options::Get('encyclopedia_tags')) ?> ><?php _e('No') ?></option>
		</select><br>
		<small>
      <?php Echo I18n::t('Tags are necessary if you want to display relevant entries automatically.') ?>
    </small>
	</td>
</tr>
</table>

<p><?php Echo Mocking_Bird::Pro_Notice('custom_tax') ?></p>