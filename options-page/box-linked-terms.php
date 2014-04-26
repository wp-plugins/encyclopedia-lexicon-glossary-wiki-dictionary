<p><?php Echo $this->t('Enable automatically linked encyclopedia terms for the following content types:') ?></p>
<?php
$link_target = $this->Get_Option('link_term_target');
ForEach (Get_Post_Types(Array('show_ui' => True),'objects') AS $type): ?>
<p>
  <label>
    <input type="checkbox" <?php Disabled(True); Checked(True) ?> >
    <?php Echo $type->label ?>
  </label>
  &raquo;
  <label>
    <input type="checkbox" <?php Disabled(True) ?> >
    <?php Echo $this->t('Open link in a new window') ?>
  </label>
</p>
<?php EndForEach ?>
<p>
  <label>
    <input type="checkbox" <?php Disabled(True) ?> >
    <?php Echo $this->t('Link the first match of each term only.') ?>
  </label>
</p>
<small class="pro-notice"><?php $this->Pro_Notice('changeable') ?></small>