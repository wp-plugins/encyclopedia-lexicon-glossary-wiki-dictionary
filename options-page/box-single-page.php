<table class="form-table">
<tr>
  <th><label for="related_terms"><?php Echo $this->t('Display related entries') ?></label></th>
  <td>
		<input type="radio" id="related_terms_below" <?php Checked(True) ?>> <label for="related_terms_below"><?php Echo $this->t('below the encyclopedia entry') ?></label><br>
		<input type="radio" id="related_terms_above" <?php Disabled(True) ?>> <label for="related_terms_above"><?php Echo $this->t('above the encyclopedia entry') ?></label><?php Echo $this->Pro_Notice('unlock') ?><br>
		<input type="radio" id="related_terms_none" <?php Disabled(True) ?>> <label for="related_terms_none"><?php Echo $this->t('do not show related terms') ?></label><?php Echo $this->Pro_Notice('unlock') ?><br>
	</td>
</tr>

<tr>
  <th><label><?php Echo $this->t('Number of related terms') ?></label></th>
  <td>
    <input type="number" value="10" class="short" <?php Disabled(True) ?>><?php Echo $this->Pro_Notice('unlock') ?><br>
    <small>
      <?php Echo $this->t('How many related terms should be shown for each term?') ?>
    </small>
	</td>
</tr>

<tr>
  <th><label for="prefix_filter_for_singulars"><?php Echo $this->t('Prefix filter') ?></label></th>
  <td>
		<select name="prefix_filter_for_singulars" id="prefix_filter_for_singulars">
			<option value="1" <?php Selected($this->Get_Option('prefix_filter_for_singulars')) ?> ><?php _e('Yes') ?></option>
			<option value="0" <?php Selected(!$this->Get_Option('prefix_filter_for_singulars')) ?> ><?php _e('No') ?></option>
		</select><br>
		<small><?php Echo $this->t('Display the prefix filter above the encyclopedia term automatically or not.') ?></small>
	</td>
</tr>

<tr>
	<th><label for="prefix_filter_singular_depth"><?php Echo $this->t('Prefix filter depth') ?></label></th>
	<td>
    <input type="number" name="prefix_filter_singular_depth" id="prefix_filter_singular_depth" value="<?php Echo $this->Get_Option('prefix_filter_singular_depth') ?>" class="small-text"><br>
    <small><?php Echo $this->t('The depth of the prefix filter is usually the number of lines with prefixes which are shown.') ?></small>
  </td>
</tr>

</table>