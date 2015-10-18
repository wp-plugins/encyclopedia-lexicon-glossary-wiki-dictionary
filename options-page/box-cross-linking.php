<?php Namespace WordPress\Plugin\Encyclopedia ?>
<table class="form-table">

<?php
$link_terms = Is_Array(Options::Get('link_terms')) ? Options::Get('link_terms') : Array();
ForEach (Get_Post_Types(Array('show_ui' => True),'objects') AS $type): ?>
<tr>
  <th><?php Echo $type->label ?></th>
  <td>
    <label>
      <input type="checkbox" <?php Disabled(True); Checked(True) ?> >
      <?php PrintF(I18n::t('Link terms in %s'), $type->label) ?>
      <?php Echo Mocking_Bird::Pro_Notice('unlock') ?>
    </label><br>

    <label>
      <input type="checkbox" <?php Disabled(True) ?> >
      <?php _e('Open link in a new window/tab') ?>
      <?php Echo Mocking_Bird::Pro_Notice('unlock') ?>
    </label>
  </td>
</tr>
<?php EndForEach ?>

<tr>
  <th><?php Echo I18n::t('Complete words') ?></th>
  <td>
    <label>
      <input type="checkbox" <?php Disabled(True) ?> >
      <?php Echo I18n::t('Link complete words only.') ?><?php Echo Mocking_Bird::Pro_Notice('unlock') ?>
    </label>
  </td>
</tr>

<tr>
  <th><?php Echo I18n::t('First match only') ?></th>
  <td>
    <label>
      <input type="checkbox" <?php Disabled(True) ?> >
      <?php Echo I18n::t('Link the first match of each term only.') ?><?php Echo Mocking_Bird::Pro_Notice('unlock') ?>
    </label>
  </td>
</tr>

<tr>
  <th><?php Echo I18n::t('Recursion') ?></th>
  <td>
    <label>
      <input type="checkbox" <?php Disabled(True) ?> >
      <?php Echo I18n::t('Link the term in its own content.') ?><?php Echo Mocking_Bird::Pro_Notice('unlock') ?>
    </label>
  </td>
</tr>

<tr>
	<th><label for=""><?php Echo I18n::t('Link title length') ?></label></th>
	<td>
		<input type="number" value="<?php Echo Esc_Attr(Options::Get('cross_link_title_length')) ?>" <?php Disabled(True) ?> >
    <?php Echo I18n::t('words') ?><?php Echo Mocking_Bird::Pro_Notice('unlock') ?>
    <br>
		<small><?php Echo I18n::t('The number of words of the linked term used as link title.') ?></small>
	</td>
</tr>
</table>