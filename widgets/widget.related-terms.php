<?php Namespace WordPress\Plugin\Encyclopedia;

class Related_Terms_Widget Extends \WP_Widget {

  function __construct(){
    # Setup the Widget data
    parent::__construct (
      'encyclopdia_related_terms',
      I18n::t('Encyclopedia Related Terms'),
      Array('description' => I18n::t('Displays encyclopedia terms which are related to the current one as list.'))
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
      'number'  => 5,
      'exclude' => False
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
      <input type="text" id="<?php Echo $this->Get_Field_Id('title') ?>" name="<?php Echo $this->Get_Field_Name('title')?>" value="<?php Echo HTMLSpecialChars($this->getOption('title')) ?>"><br>
      <small><?php Echo I18n::t('Leave blank to use the widget default title.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('number') ?>"><?php Echo I18n::t('Number') ?></label>:
      <input type="text" id="<?php Echo $this->Get_Field_Id('number') ?>" name="<?php Echo $this->Get_Field_Name('number')?>" value="<?php Echo HTMLSpecialChars($this->getOption('number')) ?>" size="4"><br>
      <small><?php Echo I18n::t('The number of related terms you want to show.') ?></small>
    </p>

    <?php
  }

  function Widget ($args, $settings){
    # Load options
    $this->loadOptions($settings);
    $widget_title = Apply_Filters('widget_title', $this->getOption('title'), $settings, $this->id_base);

    # Load the related terms
    $related_terms = Core::getTagRelatedTerms(Array(
      'number' => $this->getOption('number')
    ));
    If (!$related_terms) return;

    # Display Widget
    Echo $args['before_widget'];
    !Empty($widget_title) && Print($args['before_title'] . $widget_title . $args['after_title']);
    Echo Template::load('encyclopedia-related-terms-widget.php', Array('related_terms' => $related_terms));
    Echo $args['after_widget'];

    # Reset Post data
    WP_Reset_Postdata();
  }

  function Update ($new_settings, $old_settings){
    return $new_settings;
  }

}

Related_Terms_Widget::registerWidget();