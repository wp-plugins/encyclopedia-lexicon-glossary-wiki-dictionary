<table class="form-table">
<tr>
	<th><label for="terms_per_page"><?php Echo $this->t('Terms per page') ?></label></th>
	<td>
    <input type="number" value="<?php Echo Get_Option('posts_per_page') ?>" class="short" readonly="readonly"><span class="asterisk">*</span><br>
    <small>
      <?php Echo $this->t('This option effects all encyclopedia archive pages.') ?>
    </small>
  </td>
</tr>

<tr>
  <th><label for="prefix_filter_for_archives"><?php Echo $this->t('Prefix filter') ?></label></th>
  <td>
		<select name="prefix_filter_for_archives" id="prefix_filter_for_archives">
			<option value="1" <?php Selected($this->Get_Option('prefix_filter_for_archives')) ?> ><?php _e('Yes') ?></option>
			<option value="0" <?php Selected(!$this->Get_Option('prefix_filter_for_archives')) ?> ><?php _e('No') ?></option>
		</select><br>
		<small><?php Echo $this->t('Display a prefix filter above the encyclopedia archive automatically or not.') ?></small>
	</td>
</tr>

<tr>
	<th><label for="prefix_filter_archive_depth"><?php Echo $this->t('Prefix filter depth') ?></label></th>
	<td>
    <input type="number" name="prefix_filter_archive_depth" id="prefix_filter_archive_depth" value="<?php Echo $this->Get_Option('prefix_filter_archive_depth') ?>" class="small-text"><br>
    <small><?php Echo $this->t('The depth of the prefix filter is usually the number of lines with prefixes which are shown.') ?></small>
  </td>
</tr>

</table>

<p>
  <span class="asterisk">*</span>
  <span class="pro-notice"><?php $this->Pro_Notice() ?></span>
</p>