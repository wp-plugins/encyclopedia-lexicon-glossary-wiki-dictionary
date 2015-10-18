<?php Namespace WordPress\Plugin\Encyclopedia;

class Taxonomy_Cloud_Widget Extends \WP_Widget {

  function __construct(){
    parent::__construct (
      'encyclopedia_taxonomy_cloud',
      I18n::t('Encyclopedia Taxonomy Cloud'),
      Array('description' => I18n::t('Displays your Encyclopedia taxonomies as cloud.'))
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
      'show_count' => False,
      'number'     => 0,
      'orderby'    => 'name',
      'order'      => 'RAND',
      'exclude'    => False
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

    <p>
      <label for="<?php Echo $this->Get_Field_Id('taxonomy') ?>"><?php Echo I18n::t('Taxonomy') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('taxonomy') ?>" name="<?php Echo $this->Get_Field_Name('taxonomy') ?>">
      <?php ForEach(Get_Object_Taxonomies(Post_Type::$post_type_name) AS $taxonomy) : $taxonomy = Get_Taxonomy($taxonomy); ?>
      <option value="<?php Echo $taxonomy->name ?>" <?php Selected($this->getOption('taxonomy'), $taxonomy->name) ?>><?php Echo HTMLSpecialChars($taxonomy->labels->name) ?></option>
      <?php EndForEach ?>
      </select><br>
      <small><?php Echo I18n::t('Please choose the Taxonomy the widget should display.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('number') ?>"><?php Echo I18n::t('Number') ?></label>:
      <input type="text" id="<?php Echo $this->Get_Field_Id('number') ?>" name="<?php Echo $this->get_field_name('number')?>" value="<?php Echo HTMLSpecialChars($this->getOption('number')) ?>" size="4"><br>
      <small><?php Echo I18n::t('Leave blank (or "0") to show all.') ?></small>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:' ); ?></label>
      <input type="text" value="<?php echo HTMLSpecialChars($this->getOption('exclude')); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" /><br />
      <small><?php Echo I18n::t( 'Taxonomy IDs, separated by commas.' ); ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('orderby') ?>"><?php Echo I18n::t('Order by') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('orderby') ?>" name="<?php Echo $this->Get_Field_Name('orderby') ?>">
      <option value="name" <?php Selected($this->getOption('orderby'), 'name') ?>><?php Echo __('Name') ?></option>
      <option value="count" <?php Selected($this->getOption('orderby'), 'count') ?>><?php Echo I18n::t('Term Count') ?></option>
      </select>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('order') ?>"><?php Echo I18n::t('Order') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('order') ?>" name="<?php Echo $this->Get_Field_Name('order') ?>">
      <option value="RAND" <?php Selected($this->getOption('order'), 'RAND') ?>><?php _e('Random') ?></option>
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

    # Check if the Taxonomy is alive
    If (!Taxonomy_Exists($this->getOption('taxonomy'))) return False;

    # Display Widget
    Echo $args['before_widget'];

    !Empty($widget_title) && Print($args['before_title'] . $widget_title . $args['after_title']);

    Echo '<ul>';
    WP_Tag_Cloud(Array(
      'taxonomy'   => $this->getOption('taxonomy'),
      'number'     => $this->getOption('number'),
      'order'      => $this->getOption('order'),
      'orderby'    => $this->getOption('orderby'),
      'exclude'    => $this->getOption('exclude')
    ));
    Echo '</ul>';

    Echo $args['after_widget'];
  }

  function Update ($new_settings, $old_settings){
    return $new_settings;
  }

}

Taxonomy_Cloud_Widget::registerWidget();