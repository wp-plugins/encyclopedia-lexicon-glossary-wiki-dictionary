<?php Namespace WordPress\Plugin\Encyclopedia ?>
<p><?php Echo I18n::t('Want to unlock all settings and features? Upgrade to Encyclopedia Pro!') ?></p>
<p>
  <a href="<?php Mocking_Bird::Pro_Notice('upgrade_url') ?>" target="_blank">
    <img src="<?php Echo Core::$base_url ?>/assets/img/encyclopedia-pro.png" class="premium-banner" title="Encyclopedia Pro" style="width:100%">
  </a>
</p>
<a href="<?php Mocking_Bird::Pro_Notice('upgrade_url') ?>" target="_blank" class="button button-primary button-block button-large"><?php Echo I18n::t('Upgrade to Pro!') ?></a>