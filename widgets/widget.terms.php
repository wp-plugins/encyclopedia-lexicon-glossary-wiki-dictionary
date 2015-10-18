<?php Namespace WordPress\Plugin\Encyclopedia;

class Terms_Widget Extends \WP_Widget {

  function __construct(){
    parent::__construct (
      'encyclopedia_terms',
      I18n::t('Encyclopedia Terms'),
      Array('description' => I18n::t('Displays your encyclopedia terms as list.'))
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
      'number'  => Null,
      'orderby' => 'title',
      'order'   => 'ASC',
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
    $this->loadOptions($settings);
    ?>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('title') ?>"><?php Echo I18n::t('Title') ?></label>:
      <input type="text" id="<?php Echo $this->Get_Field_Id('title') ?>" name="<?php Echo $this->get_field_name('title')?>" value="<?php Echo HTMLSpecialChars($this->getOption('title')) ?>"><br>
      <small><?php Echo I18n::t('Leave blank to use the widget default title.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('orderby') ?>"><?php Echo I18n::t('Order by') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('orderby') ?>" name="<?php Echo $this->Get_Field_Name('orderby') ?>">
      <option value="title" <?php Selected($this->getOption('orderby'), 'title') ?>><?php Echo __('Title') ?></option>
      <option value="ID" <?php Selected($this->getOption('orderby'), 'ID') ?>>ID</option>
      <option value="author" <?php Selected($this->getOption('orderby'), 'author') ?>><?php Echo I18n::t('Author') ?></option>
      <option value="date" <?php Selected($this->getOption('orderby'), 'date') ?>><?php Echo I18n::t('Date') ?></option>
      <option value="modified" <?php Selected($this->getOption('orderby'), 'modified') ?>><?php Echo I18n::t('Last modification') ?></option>
      <option value="rand" <?php Selected($this->getOption('orderby'), 'rand') ?>><?php Echo I18n::t('Random') ?></option>
      <option value="comment_count" <?php Selected($this->getOption('orderby'), 'comment_count') ?>><?php Echo I18n::t('Comment Count') ?></option>
      <option value="menu_order" <?php Selected($this->getOption('orderby'), 'menu_order') ?>><?php Echo I18n::t('Menu Order') ?></option>
      </select>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('order') ?>"><?php Echo I18n::t('Order') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('order') ?>" name="<?php Echo $this->Get_Field_Name('order') ?>">
      <option value="ASC" <?php Selected($this->getOption('order'), 'ASC') ?>><?php _e('Ascending') ?></option>
      <option value="DESC" <?php Selected($this->getOption('order'), 'DESC') ?>><?php _e('Descending') ?></option>
      </select>
    </p>

    <?php
  }

  function Widget ($args, $settings){
    # Load options
    $this->loadOptions($settings);
    $widget_title = Apply_Filters('widget_title', $this->getOption('title'), $settings, $this->id_base);

    # Load the Query
    $term_query = New \WP_Query(Array(
      'post_type' => Post_Type::$post_type_name,
      'orderby' => $this->getOption('orderby'),
      'order' => $this->getOption('order'),
      'nopaging' => True,
      'ignore_sticky_posts' => True,
      'suppress_filters' => True
    ));
    If (!$term_query->Have_Posts()) return;

    # Display Widget
    Echo $args['before_widget'];
    !Empty($widget_title) && Print($args['before_title'] . $widget_title . $args['after_title']);
    Echo Template::load('encyclopedia-terms-widget.php', Array('term_query' => $term_query));
    Echo $args['after_widget'];

    # Reset Post data
    WP_Reset_Postdata();
  }

  function Update ($new_settings, $old_settings){
    return $new_settings;
  }

}

Terms_Widget::registerWidget();