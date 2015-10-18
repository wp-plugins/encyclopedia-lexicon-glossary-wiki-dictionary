<?php Namespace WordPress\Plugin\Encyclopedia ?>
<p>
  <?php PrintF(I18n::t('The Archive link for your Encyclopedia is: <a href="%1$s" target="_blank">%1$s</a>'), Get_Post_Type_Archive_Link(Post_Type::$post_type_name)) ?>
</p>
<p>
  <?php PrintF(I18n::t('The Archive Feed for your Encyclopedia is: <a href="%1$s" target="_blank">%1$s</a>'), Get_Post_Type_Archive_Feed_Link(Post_Type::$post_type_name)) ?>
</p>