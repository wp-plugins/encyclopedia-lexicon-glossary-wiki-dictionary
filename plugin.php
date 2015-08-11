<?php
/*
Plugin Name: Encyclopedia Lite
Plugin URI: http://dennishoppe.de/en/wordpress-plugins/encyclopedia
Description: Encyclopedia enables you to create your own encyclopedia, lexicon, glossary, wiki or dictionary.
Version: 1.5.36
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

If (Version_Compare(PHP_VERSION, '5.3.0', '<')){

  # Add PHP Version warning to the dashboard
  Add_Action('admin_notices', 'Encyclopedia_PHP53_Version_Warning');
  function Encyclopedia_PHP53_Version_Warning(){ ?>
    <div class="error">
      <p><?php PrintF('<strong>%1$s:</strong> You need at least <strong>PHP 5.3</strong> or higher to use %1$s. You are using PHP %2$s. Please ask your hoster for an upgrade.', 'Encyclopedia', PHP_VERSION) ?></p>
    </div><?php
  }

}
Else {

  # Load helper classes
  Include DirName(__FILE__).'/fallback.mb-string.php';
  Include DirName(__FILE__).'/class.cross-linker.php';
  Include DirName(__FILE__).'/class.i18n.php';
  Include DirName(__FILE__).'/class.wpml.php';

  # Load Widgets
  Include DirName(__FILE__).'/wp-widget-encyclopedia-related-terms.php';
  Include DirName(__FILE__).'/wp-widget-encyclopedia-search.php';
  Include DirName(__FILE__).'/wp-widget-encyclopedia-taxonomies.php';
  Include DirName(__FILE__).'/wp-widget-encyclopedia-taxonomy-cloud.php';
  Include DirName(__FILE__).'/wp-widget-encyclopedia-terms.php';

  # Load Core
  Include DirName(__FILE__).'/class.core.php';

  # Inititalize Plugin: Would cause a synthax error in PHP < 5.3
  Eval('New WordPress\Plugin\Encyclopedia\Core(__FILE__);');

}