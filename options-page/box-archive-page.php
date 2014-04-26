<table class="form-table">
<tr>
	<th><label for="terms_per_page"><?php Echo $this->t('Terms per page:') ?></label></th>
	<td>
    <input type="number" value="<?php Echo Get_Option('posts_per_page') ?>" class="short disabled" <?php Disabled(True) ?> ><br>
    <small>
      <?php Echo $this->t('This option effects all encyclopedia archive pages.') ?>
      <span class="pro-notice"><?php $this->Pro_Notice('changeable') ?></span>
    </small>
  </td>
</tr>

<tr>
  <th><label for="term_filter_for_archives"><?php Echo $this->t('Term filter') ?>:</label></th>
  <td>
		<select name="term_filter_for_archives" id="term_filter_for_archives">
			<option value="yes" <?php Selected($this->Get_Option('term_filter_for_archives'), 'yes') ?> ><?php _e('Yes') ?></option>
			<option value="no" <?php Selected($this->Get_Option('term_filter_for_archives'), 'no') ?> ><?php _e('No') ?></option>
		</select><br>
		<small><?php Echo $this->t('Display a term filter above the encyclopedia archive automatically or not.') ?></small>
	</td>
</tr>

</table>