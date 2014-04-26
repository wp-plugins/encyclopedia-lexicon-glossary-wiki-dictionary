<table class="form-table">
<tr>
  <th><label for="redirect_user_to_search_term"><?php Echo $this->t('Query terms directly') ?>:</label></th>
  <td>
		<select>
			<option <?php Disabled(True) ?> ><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?> ><?php _e('No') ?></option>
		</select><br>
		<small>
      <?php Echo $this->t('Enable this feature to redirect the user to the term if the search query matches a term title exactly.') ?>
      <span class="pro-notice"><?php $this->Pro_Notice() ?></span>
    </small>
	</td>
</tr>

<tr>
	<th><label><?php Echo $this->t('Autocomplete Min Length') ?>:</label></th>
	<td>
    <input type="number" value="2" class="short" <?php Disabled(True) ?> > <?php Echo $this->t('Characters', 'characters unit') ?><br>
    <small>
      <?php Echo $this->t('The minimum number of characters a user must type before suggestions will be shown.') ?>
      <span class="pro-notice"><?php $this->Pro_Notice('changeable') ?></span>
    </small>
  </td>
</tr>

<tr>
	<th><label><?php Echo $this->t('Autocomplete Delay') ?>:</label></th>
	<td>
    <input type="number" value="400" class="short" <?php Disabled(True) ?> ><?php Echo $this->t('ms', 'milliseconds time unit') ?><br>
    <small>
      <?php Echo $this->t('The delay in milliseconds between when a keystroke occurs and when suggestions will be shown.') ?>
      <span class="pro-notice"><?php $this->Pro_Notice('changeable') ?></span>
    </small>
  </td>
</tr>
</table>