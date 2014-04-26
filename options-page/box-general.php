<table class="form-table">
<tr>
	<th><label for="encyclopedia_type"><?php Echo $this->t('Encyclopedia type:') ?></label></th>
	<td>
		<select name="encyclopedia_type" id="encyclopedia_type">
      <option value="" <?php Disabled(True) ?> ><?php Echo $this->t('Encyclopedia') ?></option>
      <option value="lexicon" <?php Selected(True) ?> ><?php Echo $this->t('Lexicon') ?></option>
      <option value="" <?php Disabled(True) ?> ><?php Echo $this->t('Wiki') ?></option>
      <option value="" <?php Disabled(True) ?> ><?php Echo $this->t('Knowledge Base') ?></option>
      <option value="" <?php Disabled(True) ?> ><?php Echo $this->t('Glossary') ?></option>
      <option value="" <?php Disabled(True) ?> ><?php Echo $this->t('Dictionary') ?></option>
    </select><br>
		<small>
      <?php Echo $this->t('Please choose the type of your encyclopedia. This option does not change the behavior of the plugin. It\'s just for the labels and captions in the backend.') ?>
      <span class="pro-notice"><?php $this->Pro_Notice('changeable') ?></span>
    </small>
	</td>
</tr>

<tr>
  <th><label for="embed_default_style"><?php Echo $this->t('Use default style') ?>:</label></th>
  <td>
		<select name="embed_default_style" id="embed_default_style">
			<option value="yes" <?php Selected($this->Get_Option('embed_default_style'), 'yes') ?> ><?php _e('Yes') ?></option>
			<option value="no" <?php Selected($this->Get_Option('embed_default_style'), 'no') ?> ><?php _e('No') ?></option>
		</select><br>
		<small>
      <?php Echo $this->t('Set this option to "No" if you want to use your own style for the encyclopedia.') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="enable_revisions"><?php Echo $this->t('Enable Revisions') ?>:</label></th>
  <td>
		<select name="" id="enable_revisions">
			<option <?php Disabled(True) ?> ><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?> ><?php _e('No') ?></option>
		</select><br>
		<small>
      <?php Echo $this->t('Enables or disables revisions for the encyclopedia terms.') ?>
      <span class="pro-notice"><?php $this->Pro_Notice() ?></span>
    </small>
	</td>
</tr>

<tr>
  <th><label for="enable_comments"><?php Echo $this->t('Enable Comments') ?>:</label></th>
  <td>
		<select name="" id="enable_comments">
			<option <?php Disabled(True) ?> ><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?> ><?php _e('No') ?></option>
		</select><br>
		<small>
      <?php Echo $this->t('Enables or disables comments and trackbacks for the encyclopedia terms.') ?>
      <span class="pro-notice"><?php $this->Pro_Notice() ?></span>
    </small>
	</td>
</tr>

<tr>
  <th><label for="enable_thumbnails"><?php Echo $this->t('Enable Thumbnails') ?>:</label></th>
  <td>
		<select name="" id="enable_thumbnails">
			<option <?php Disabled(True) ?> ><?php _e('Yes') ?></option>
			<option <?php Selected(True) ?> ><?php _e('No') ?></option>
		</select><br>
		<small>
      <?php Echo $this->t('Enables or disables the "Featured Image" for the encyclopedia terms.') ?>
      <span class="pro-notice"><?php $this->Pro_Notice() ?></span>
    </small>
	</td>
</tr>

</table>