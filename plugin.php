<?php

/*
Plugin Name: Encyclopedia Lite
Plugin URI: http://dennishoppe.de/en/wordpress-plugins/encyclopedia
Description: Encyclopedia enables you to create your own encyclopedia, lexicon, glossary, wiki or dictionary.
Version: 1.6.3
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
Text Domain: encyclopedia
Domain Path: /languages
*/

Include DirName(__FILE__).'/classes/fallback.mb-string.php';
Include DirName(__FILE__).'/classes/class.ajax-requests.php';
Include DirName(__FILE__).'/classes/class.core.php';
Include DirName(__FILE__).'/classes/class.cross-linker.php';
Include DirName(__FILE__).'/classes/class.encyclopedia-type.php';
Include DirName(__FILE__).'/classes/class.i18n.php';
Include DirName(__FILE__).'/classes/class.mocking-bird.php';
Include DirName(__FILE__).'/classes/class.options.php';
Include DirName(__FILE__).'/classes/class.permalinks.php';
Include DirName(__FILE__).'/classes/class.post-type.php';
Include DirName(__FILE__).'/classes/class.prefix-filter.php';
Include DirName(__FILE__).'/classes/class.shortcodes.php';
Include DirName(__FILE__).'/classes/class.taxonomies.php';
Include DirName(__FILE__).'/classes/class.taxonomy-fallbacks.php';
Include DirName(__FILE__).'/classes/class.template.php';
Include DirName(__FILE__).'/classes/class.wp-query-extensions.php';
Include DirName(__FILE__).'/classes/class.wpml.php';

Include DirName(__FILE__).'/widgets/widget.related-terms.php';
Include DirName(__FILE__).'/widgets/widget.search.php';
Include DirName(__FILE__).'/widgets/widget.taxonomies.php';
Include DirName(__FILE__).'/widgets/widget.taxonomy-cloud.php';
Include DirName(__FILE__).'/widgets/widget.terms.php';

WordPress\Plugin\Encyclopedia\Core::Init(__FILE__);