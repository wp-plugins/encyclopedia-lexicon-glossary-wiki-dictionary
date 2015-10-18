<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Options {
  private static
    $arr_option_box = Array(),
    $page_slug = 'encyclopedia-options',
    $options_key = 'wp_plugin_encyclopedia';
  
  static function Init(){
    # Option boxes
    self::$arr_option_box = Array( 'main' => Array(), 'side' => Array() );
    Add_Action('admin_menu', Array(__CLASS__, 'addOptionsPage'));  
  }

  static function addOptionsPage(){
    $handle = Add_Options_Page(
      I18n::t('Encyclopedia Options'),
      I18n::t('Encyclopedia'),
      'manage_options',
      self::$page_slug,
      Array(__CLASS__, 'printOptionsPage')
    );

    # Add JavaScript to this handle
    Add_Action ('load-' . $handle, Array(__CLASS__, 'loadOptionsPage'));

    # Add option boxes
    self::addOptionBox(__('General'), Core::$plugin_folder.'/options-page/box-general.php');
    self::addOptionBox(I18n::t('Taxonomies'), Core::$plugin_folder.'/options-page/box-taxonomies.php');
    self::addOptionBox(I18n::t('Archive page'), Core::$plugin_folder.'/options-page/box-archive-page.php');
    self::addOptionBox(I18n::t('Search'), Core::$plugin_folder.'/options-page/box-search.php');
    self::addOptionBox(I18n::t('Single page'), Core::$plugin_folder.'/options-page/box-single-page.php');
    self::addOptionBox(I18n::t('Cross linking'), Core::$plugin_folder.'/options-page/box-cross-linking.php');
    self::addOptionBox(I18n::t('Archive Url'), Core::$plugin_folder.'/options-page/box-archive-link.php', 'side');
  }

  static function getOptionsPageUrl($parameters = Array()){
    $url = Add_Query_Arg(Array('page' => self::$page_slug), Admin_Url('options-general.php'));
    If (Is_Array($parameters) && !Empty($parameters)) $url = Add_Query_Arg($parameters, $url);
    return $url;
  }

  static function loadOptionsPage(){
    # If the Request was redirected from a "Save Options"-Post
    If (IsSet($_REQUEST['options_saved'])) Flush_Rewrite_Rules();

    # If this is a Post request to save the options
    If (self::saveOptions()) WP_Redirect(self::getOptionsPageUrl(Array('options_saved' => 'true')));

    WP_Enqueue_Script('dashboard');
    WP_Enqueue_Style('dashboard');

    WP_Enqueue_Style('options-page', Core::$base_url . '/options-page/options-page.css');

    # Remove incompatible JS Libs
    WP_Dequeue_Script('post');
  }

  static function printOptionsPage(){
    Include Core::$plugin_folder.'/options-page/options-page.php';
  }

  static function addOptionBox($title, $include_file, $column = 'main', $state = 'opened'){
    # Check the input
    If (!Is_File($include_file)) return False;
    If (Empty($title)) $title = '&nbsp;';

    # Column (can be 'side' or 'main')
    If ($column != '' && $column != Null && $column != 'main')
      $column = 'side';
    Else
      $column = 'main';

    # State (can be 'opened' or 'closed')
    If ($state != '' && $state != Null && $state != 'opened')
      $state = 'closed';
    Else
      $state = 'opened';

    # Add a new box
    self::$arr_option_box[$column][] = (Object) Array(
      'title' => $title,
      'file' => $include_file,
      'state' => $state
    );
  }

  static function Get($key = Null, $default = False){
    # Read Options
    $arr_option = Array_Merge (
      (Array) self::getDefaultOptions(),
      (Array) Get_Option(self::$options_key)
    );

    # Locate the option
    If ($key == Null)
      return $arr_option;
    ElseIf (IsSet($arr_option[$key]))
      return $arr_option[$key];
    Else
      return $default;
  }

  static function saveOptions(){
    # Check if this is a post request
    If (Empty($_POST)) return False;

    # Clean the Post array
    $options = StripSlashes_Deep($_POST);
    $options = Array_Filter($options, function($value){ return $value == '0' || !Empty($value); });

    # Save Options
    Update_Option (self::$options_key, $options);

    return True;
  }

  static function getDefaultOptions(){
    return Array(
      'embed_default_style' => True,
      'encyclopedia_tags' => True,
      'prefix_filter_for_archives' => True,
      'prefix_filter_archive_depth' => 3,
      'prefix_filter_for_singulars' => True,
      'prefix_filter_singular_depth' => 3,
      'cross_link_title_length' => Apply_Filters('excerpt_length', 55)
    );
  }

}

Options::Init();