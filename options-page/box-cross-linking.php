<table class="form-table">

<?php
$link_terms = Is_Array($this->Get_Option('link_terms')) ? $this->Get_Option('link_terms') : Array();
ForEach (Get_Post_Types(Array('show_ui' => True),'objects') AS $type): ?>
<tr>
  <th><?php Echo $type->label ?></th>
  <td>
    <label>
      <input type="checkbox" <?php Disabled(True); Checked(True) ?> >
      <?php PrintF($this->t('Link terms in %s'), $type->label) ?>
      <?php Echo $this->Pro_Notice('unlock') ?>
    </label><br>

    <label>
      <input type="checkbox" <?php Disabled(True) ?> >
      <?php _e('Open link in a new window/tab') ?>
      <?php Echo $this->Pro_Notice('unlock') ?>
    </label>
  </td>
</tr>
<?php EndForEach ?>

<tr>
  <th><?php Echo $this->t('Complete words') ?></th>
  <td>
    <label>
      <input type="checkbox" <?php Disabled(True) ?> >
      <?php Echo $this->t('Link complete words only.') ?><?php Echo $this->Pro_Notice('unlock') ?>
    </label>
  </td>
</tr>

<tr>
  <th><?php Echo $this->t('First match only') ?></th>
  <td>
    <label>
      <input type="checkbox" <?php Disabled(True) ?> >
      <?php Echo $this->t('Link the first match of each term only.') ?><?php Echo $this->Pro_Notice('unlock') ?>
    </label>
  </td>
</tr>

<tr>
  <th><?php Echo $this->t('Recursion') ?></th>
  <td>
    <label>
      <input type="checkbox" <?php Disabled(True) ?> >
      <?php Echo $this->t('Link the term in its own content.') ?><?php Echo $this->Pro_Notice('unlock') ?>
    </label>
  </td>
</tr>

<tr>
	<th><label for=""><?php Echo $this->t('Link title length') ?></label></th>
	<td>
		<input type="number" value="<?php Echo Esc_Attr($this->Get_Option('cross_link_title_length')) ?>" <?php Disabled(True) ?> >
    <?php Echo $this->t('words') ?><?php Echo $this->Pro_Notice('unlock') ?>
    <br>
		<small><?php Echo $this->t('The number of words of the linked term used as link title.') ?></small>
	</td>
</tr>
</table>