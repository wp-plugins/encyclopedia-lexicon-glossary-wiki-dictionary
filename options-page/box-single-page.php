<table class="form-table">
<tr>
  <th><label for="related_terms"><?php Echo $this->t('Display related entries') ?></label></th>
  <td>
		<input type="radio" id="related_terms_below" disabled="disabled"> <label for="related_terms_below"><?php Echo $this->t('below the encyclopedia entry') ?></label> <span class="asterisk">*</span><br>
		<input type="radio" id="related_terms_above" checked="checked"> <label for="related_terms_above"><?php Echo $this->t('above the encyclopedia entry') ?></label><br>
		<input type="radio" id="related_terms_none" disabled="disabled"> <label for="related_terms_none"><?php Echo $this->t('do not show related terms') ?></label> <span class="asterisk">*</span><br>
	</td>
</tr>

<tr>
  <th><label><?php Echo $this->t('Number of related terms') ?></label></th>
  <td>
    <input type="number" value="10" class="short" <?php Disabled(True) ?> >
    <span class="asterisk">*</span><br>
    <small>
      <?php Echo $this->t('How many related terms should be shown for each term?') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="prefix_filter_for_singulars"><?php Echo $this->t('Prefix filter') ?></label></th>
  <td>
		<select name="prefix_filter_for_singulars" id="prefix_filter_for_singulars">
			<option value="yes" <?php Selected($this->Get_Option('prefix_filter_for_singulars'), 'yes') ?> ><?php _e('Yes') ?></option>
			<option value="no" <?php Selected($this->Get_Option('prefix_filter_for_singulars'), 'no') ?> ><?php _e('No') ?></option>
		</select><br>
		<small><?php Echo $this->t('Display the prefix filter above the encyclopedia term automatically or not.') ?></small>
	</td>
</tr>

</table>

<p>
  <span class="asterisk">*</span>
  <span class="pro-notice"><?php $this->Pro_Notice() ?></span>
</p>