<?php
class wp_widget_encyclopedia_related_terms Extends WP_Widget {
  var $encyclopedia;

  function __construct(){
    If (IsSet($GLOBALS['wp_plugin_encyclopedia']) && Is_Object($GLOBALS['wp_plugin_encyclopedia']))
      $this->encyclopedia = $GLOBALS['wp_plugin_encyclopedia'];
    Else
      return False;

    // Setup the Widget data
    parent::__construct (
      False,
      $this->t('Encyclopedia Related Terms'),
      Array('description' => $this->t('Displays encyclopedia terms which are related to the current one as list.'))
    );
  }

  function t ($text, $context = ''){
    return $this->encyclopedia->t($text, $context);
  }

  function Default_Options(){
    # Default settings
    return Array(
      'number'  => 5,
      'exclude' => False
    );
  }

  function Load_Options($options){
    $options = (ARRAY) $options;

    # Delete empty values
    ForEach ($options AS $key => $value)
      If (!$value) Unset($options[$key]);

    # Load options
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
    # Load options
    $this->load_options ($settings); Unset ($settings);
    ?>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('title') ?>"><?php _e('Title:') ?></label>
      <input type="text" id="<?php Echo $this->Get_Field_Id('title') ?>" name="<?php Echo $this->Get_Field_Name('title')?>" value="<?php Echo HTMLSpecialChars($this->Get_Option('title')) ?>" class="widefat">
      <small><?php Echo $this->t('Leave blank to use the widget default title.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('number') ?>"><?php Echo $this->t('Number:') ?></label>
      <input type="number" id="<?php Echo $this->Get_Field_Id('number') ?>" name="<?php Echo $this->Get_Field_Name('number')?>" value="<?php Echo HTMLSpecialChars($this->Get_Option('number')) ?>" size="4"><br>
      <small><?php Echo $this->t('The number of related terms you want to show.') ?></small>
    </p>

    <?php
  }

  function Widget ($args, $settings){
    # Load options
    $this->load_options ($settings);

    # Load the related terms
    $related_terms = $this->encyclopedia->Get_Tag_Related_Terms(Array(
      'number' => $this->Get_Option('number')
    ));
    If (!$related_terms) return;

    # Display Widget
    Echo $args['before_widget'];
    Echo $args['before_title'] . Apply_Filters('widget_title', $this->Get_Option('title'), $settings, $this->id_base) . $args['after_title'];
    Echo $this->encyclopedia->Load_Template('encyclopedia-related-terms-widget.php', Array('related_terms' => $related_terms));
    Echo $args['after_widget'];

    # Reset Post data
    WP_Reset_Postdata();
  }

  function Update ($new_settings, $old_settings){
    return $new_settings;
  }

}