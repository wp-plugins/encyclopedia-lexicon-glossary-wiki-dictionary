<?php Namespace WordPress\Plugin\Encyclopedia;

class Search_Widget Extends \WP_Widget {

  function __construct(){
    parent::__construct (
      'encyclopedia_search',
      I18n::t('Encyclopedia Search'),
      Array('description' => I18n::t('Displays the encyclopedia search field.'))
    );
  }

  static function registerWidget(){
    If (Did_Action('widgets_init'))
      Register_Widget(__CLASS__);
    Else
      Add_Action('widgets_init', Array(__CLASS__, __FUNCTION__));
  }

  function getDefaultOptions(){
    # Default settings
    return Array(
      'title' => ''
    );
  }

  function loadOptions($options){
    $options = Is_Array($options) ? $options : Array();

    # Delete empty values
    $options = Array_Filter($options);

    # Load options
    $this->arr_option = Array_Merge($this->getDefaultOptions(), $options);
  }

  function getOption($key, $default = False){
    If (IsSet($this->arr_option[$key]) && $this->arr_option[$key])
      return $this->arr_option[$key];
    Else
      return $default;
  }

  function setOption($key, $value){
    $this->arr_option[$key] = $value;
  }

  function Form ($settings){
    # Load options
    $this->loadOptions($settings);
    ?>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('title') ?>"><?php Echo I18n::t('Title') ?></label>:
      <input type="text" id="<?php Echo $this->Get_Field_Id('title') ?>" name="<?php Echo $this->get_field_name('title')?>" value="<?php Echo HTMLSpecialChars($this->getOption('title')) ?>"><br>
      <small><?php Echo I18n::t('Leave blank to use the widget default title.') ?></small>
    </p>

    <?php
  }

  function Widget ($args, $settings){
    # Load options
    $this->loadOptions($settings);
    $widget_title = Apply_Filters('widget_title', $this->getOption('title'), $settings, $this->id_base);

    # Display Widget
    Echo $args['before_widget'];
    !Empty($widget_title) && Print($args['before_title'] . $widget_title . $args['after_title']);
    Echo Template::load('searchform-encyclopedia.php');
    Echo $args['after_widget'];
  }

}

Search_Widget::registerWidget();