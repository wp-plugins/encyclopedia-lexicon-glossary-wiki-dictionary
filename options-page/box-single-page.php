<table class="form-table">
<tr>
  <th><label for="related_terms"><?php Echo $this->t('Display related entries') ?></label></th>
  <td>
		<input type="radio" id="related_terms_below" disabled="disabled"> <label for="related_terms_below"><?php Echo $this->t('below the encyclopedia entry') ?></label><br>
		<input type="radio" id="related_terms_above" checked="checked"> <label for="related_terms_above"><?php Echo $this->t('above the encyclopedia entry') ?></label><br>
		<input type="radio" id="related_terms_none" disabled="disabled"> <label for="related_terms_none"><?php Echo $this->t('do not show related terms') ?></label><br>
    <small class="pro-notice"><?php $this->Pro_Notice('changeable') ?></small>
	</td>
</tr>

<tr>
  <th><label><?php Echo $this->t('Number of related terms') ?></label></th>
  <td>
    <input type="number" value="10" class="short" <?php Disabled(True) ?> ><br>
    <small>
      <?php Echo $this->t('How many related terms should be shown for each term?') ?>
      <span class="pro-notice"><?php $this->Pro_Notice('changeable') ?></span>
    </small>
	</td>
</tr>

<tr>
  <th><label for="term_filter_for_singulars"><?php Echo $this->t('Term filter') ?></label></th>
  <td>
		<select name="term_filter_for_singulars" id="term_filter_for_singulars">
			<option value="yes" <?php Selected($this->Get_Option('term_filter_for_singulars'), 'yes') ?> ><?php _e('Yes') ?></option>
			<option value="no" <?php Selected($this->Get_Option('term_filter_for_singulars'), 'no') ?> ><?php _e('No') ?></option>
		</select><br>
		<small><?php Echo $this->t('Display a term filter above the encyclopedia term content automatically or not.') ?></small>
	</td>
</tr>

</table>