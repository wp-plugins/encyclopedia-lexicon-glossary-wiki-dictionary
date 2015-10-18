<?php Namespace WordPress\Plugin\Encyclopedia ?>
<table class="form-table">
<tr>
  <th><label for="redirect_user_to_search_term"><?php Echo I18n::t('Query terms directly') ?></label></th>
  <td>
		<select <?php Disabled(True) ?>>
			<option <?php Disabled(True) ?>><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?>><?php _e('No') ?></option>
		</select><?php Echo Mocking_Bird::Pro_Notice('unlock') ?><br>
		<small>
      <?php Echo I18n::t('Enable this feature to redirect the user to the term if the search query matches a term title exactly.') ?>
    </small>
	</td>
</tr>

<tr>
	<th><label><?php Echo I18n::t('Autocomplete min length') ?></label></th>
	<td>
    <input type="number" value="2" class="short" <?php Disabled(True) ?>>
    <?php Echo I18n::t('characters', 'characters unit') ?><?php Echo Mocking_Bird::Pro_Notice('unlock') ?><br>
    <small>
      <?php Echo I18n::t('The minimum number of characters a user must type before suggestions will be shown.') ?>
    </small>
  </td>
</tr>

<tr>
	<th><label><?php Echo I18n::t('Autocomplete delay') ?></label></th>
	<td>
    <input type="number" value="400" class="short" <?php Disabled(True) ?>>
    <?php Echo I18n::t('ms', 'milliseconds time unit') ?><?php Echo Mocking_Bird::Pro_Notice('unlock') ?><br>
    <small>
      <?php Echo I18n::t('The delay in milliseconds between when a keystroke occurs and when suggestions will be shown.') ?>
    </small>
  </td>
</tr>
</table>