<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Mocking_Bird {
  
  static function Init(){
    Add_Action('wp_insert_post_empty_content', Array(__CLASS__, 'User_Updates_Post'), 10, 3);
    Add_Action('admin_notices', Array(__CLASS__, 'Term_Count_Notice'));
    Add_Action('admin_footer', Array(__CLASS__, 'Print_Dashboard_JS'));
    Add_Action('admin_bar_menu', Array(__CLASS__, 'Filter_Admin_Bar_Menu'), 999);
  }

  static function Pro_Notice($message = 'option', $output = True){
    $arr_message = Array(
      'upgrade' => I18n::t('Upgrade to Pro'),
      'upgrade_url' => '%s',
      'feature' => I18n::t('Available in the <a href="%s" target="_blank">premium version</a> only.'),
      'unlock' => SPrintF('<a href="%%s" title="%s" class="unlock" target="_blank"><span class="dashicons dashicons-lock"></span></a>', I18n::t('Unlock this feature')),
      'option' => I18n::t('This option is changeable in the <a href="%s" target="_blank">premium version</a> only.'),
      'custom_tax' => I18n::t('Do you need a special taxonomy for your project? No problem! Just <a href="%s" target="_blank">get in touch</a> through our support section.'),
      'count_limit' => I18n::t('In the <a href="%s" target="_blank">premium version of Encyclopedia</a> you will take advantage of unlimited terms and many more features.'),
      #'changeable' => I18n::t('Changeable in the <a href="%s" target="_blank">premium version</a> only.'),
      #'do_you_like' => I18n::t('Do you like the term management? Upgrade to the <a href="%s" target="_blank">premium version of Encyclopedia</a>!')
    );

    If (IsSet($arr_message[$message])){
      $message = SPrintF($arr_message[$message], I18n::t('http://dennishoppe.de/en/wordpress-plugins/encyclopedia', 'Link to the authors website'));
      If ($output) Echo $message;
      Else return $message;
    }
    Else
      return False;
  }

  static function Count_Terms($limit = -1){
    return Count(Get_Posts(Array('post_type' => Post_Type::$post_type_name, 'post_status' => 'any', 'numberposts' => $limit)));
  }

  static function Check_Term_Count(){
    return self::Count_Terms(12) < 12;
  }

  static function User_Updates_Post($maybe_empty, $post_data){
    If ($post_data['post_type'] == Post_Type::$post_type_name){
      $new_record = Empty($post_data['ID']);
      $untrash = !$new_record && Get_Post_Status($post_data['ID']) == 'trash';
      If (($new_record || $untrash) && !self::Check_Term_Count()){
        #WP_Die(SPrintF('<h1>%s</h1><pre>%s</pre>', __FUNCTION__, Print_R ($post_data, True)));
        self::Print_Term_Count_Limit();
      }
    }
  }

  static function Print_Term_Count_Limit(){
    WP_Die(
      SPrintF('<p>%s</p><p>%s</p>',
        self::Pro_Notice('count_limit', False),
        SPrintF('<a href="%s" class="button">%s</a>', Admin_URL('edit.php?post_type=' . Post_Type::$post_type_name), I18n::t('&laquo; Back to your terms'))
      )
    );
  }
  
  static function Term_Count_Notice(){
    If (self::Count_Terms(20) >= 20): ?>
    <div class="updated"><p>
      <?php PrintF(I18n::t('Sorry, there are to many %s terms for Encyclopedia Lite. This could result in strange behavior of the plugin. Please delete some terms.'), Encyclopedia_Type::$type->label) ?>
      <?php self::Pro_Notice('count_limit') ?>
    </p></div>
    <?php EndIf;
  }

  static function Print_Dashboard_JS(){
    If (!self::Check_Term_Count()): ?>
    <script type="text/javascript">
    (function($){
      $('a[href*="post-new.php?post_type=<?php Echo Post_Type::$post_type_name ?>"]')
        .text('<?php self::Pro_Notice('upgrade') ?>')
        .attr({
          'title': '<?php self::Pro_Notice('upgrade') ?>',
          'href': '<?php self::Pro_Notice('upgrade_url') ?>',
          'target': '_blank'
        })
        .css({
          'color': '#7ad03a',
          'font-weight': 'bold'
        });
    })(jQuery);
    </script>
    <?php EndIf;
  }

  static function Filter_Admin_Bar_Menu($admin_bar){
    If (!self::Check_Term_Count()) $admin_bar->Remove_Node(SPrintF('new-%s', Post_Type::$post_type_name));
  }

}

Mocking_Bird::Init();