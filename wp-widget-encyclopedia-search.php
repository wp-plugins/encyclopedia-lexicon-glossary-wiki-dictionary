<?php If (!Class_Exists('wp_widget_encyclopedia_search')){
Class wp_widget_encyclopedia_search Extends WP_Widget {
  var $encyclopedia;

  function __construct(){
    If (IsSet($GLOBALS['wp_plugin_encyclopedia']) && Is_Object($GLOBALS['wp_plugin_encyclopedia']))
      $this->encyclopedia = $GLOBALS['wp_plugin_encyclopedia'];
    Else
      return False;

    // Setup the Widget data
    parent::__construct (
      False,
      $this->t('Encyclopedia Search'),
      Array('description' => $this->t('Displays the encyclopedia search field.'))
    );
  }

  function t ($text, $context = ''){
    return $this->encyclopedia->t($text, $context);
  }

  function Default_Options(){
    // Default settings
    return Array(
      'title'  => ''
    );
  }

  function Load_Options($options){
    $options = (ARRAY) $options;

    // Delete empty values
    ForEach ($options AS $key => $value)
      If (!$value) Unset($options[$key]);

    // Load options
    $this->arr_option = Array_Merge ($this->Default_Options(), $options);
  }

  function Get_Option($key, $default = False){
    If (IsSet($this->arr_option[$key]) && $this->arr_option[$key])
      return $this->arr_option[$key];
    Else
      return $default;
  }

  function Set_Option($key, $value){
    $this->arr_option[$key] = $value;
  }

  function Form ($settings){
    // Load options
    $this->Load_Options ($settings); Unset ($settings);
    ?>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('title') ?>"><?php Echo $this->t('Title:') ?></label>
      <input type="text" id="<?php Echo $this->Get_Field_Id('title') ?>" name="<?php Echo $this->get_field_name('title')?>" value="<?php Echo HTMLSpecialChars($this->get_option('title')) ?>" class="widefat">
    </p>

    <?php
  }

  function Widget ($args, $settings){
    // Load options
    $this->Load_Options ($settings);
    $widget_title = Apply_Filters('widget_title', $this->Get_Option('title'), $settings, $this->id_base);

    // Display Widget
    Echo $args['before_widget'];
    If (!Empty($widget_title)) Echo $args['before_title'] . $widget_title . $args['after_title'];
    Echo $this->encyclopedia->Load_Template('encyclopedia-search-widget.php');
    Echo $args['after_widget'];
  }

  function Update ($new_settings, $old_settings){
    return $new_settings;
  }

} /* End of Class */
} /* End of If-Class-Exists-Condition */