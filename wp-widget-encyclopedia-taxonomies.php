<?php
Use WordPress\Plugin\Encyclopedia\I18n;

class wp_widget_encyclopedia_taxonomies Extends WP_Widget {
  var $encyclopedia;

  function __construct(){
    If (IsSet($GLOBALS['wp_plugin_encyclopedia']) && Is_Object($GLOBALS['wp_plugin_encyclopedia']))
      $this->encyclopedia = $GLOBALS['wp_plugin_encyclopedia'];
    Else
      return False;

    # Setup the Widget data
    parent::__construct (
      False,
      $this->t('Encyclopedia Taxonomies'),
      Array('description' => $this->t('Displays your encyclopedia taxonomies like Categories, Tags, etc.'))
    );
  }

  function t ($text, $context = Null){
    return I18n::t($text, $context);
  }

  function Default_Options(){
    # Default settings
    return Array(
      'show_count' => False,
      'number'     => Null,
      'orderby'    => 'name',
      'order'      => 'ASC',
      'exclude'    => False
    );
  }

  function Load_Options($options){
    $options = Is_Array($options) ? $options : Array();
    $options = Array_Filter($options);
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
    $this->load_options ($settings);
    ?>
    <p>
      <label for="<?php Echo $this->Get_Field_Id('title') ?>"><?php Echo $this->t('Title:') ?></label>
      <input type="text" id="<?php Echo $this->Get_Field_Id('title') ?>" name="<?php Echo $this->get_field_name('title')?>" value="<?php Echo HTMLSpecialChars($this->get_option('title')) ?>" class="widefat">
      <small><?php Echo $this->t('Leave blank to use the widget default title.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('taxonomy') ?>"><?php Echo $this->t('Taxonomy') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('taxonomy') ?>" name="<?php Echo $this->Get_Field_Name('taxonomy') ?>">
      <?php ForEach(Get_Object_Taxonomies($this->encyclopedia->post_type) AS $taxonomy) : $taxonomy = Get_Taxonomy($taxonomy); ?>
      <option value="<?php Echo $taxonomy->name ?>" <?php Selected($this->get_option('taxonomy'), $taxonomy->name) ?>><?php Echo HTMLSpecialChars($taxonomy->labels->name) ?></option>
      <?php EndForEach ?>
      </select><br>
      <small><?php Echo $this->t('Please choose the Taxonomy the widget should display.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('number') ?>"><?php Echo $this->t('Number') ?></label>:
      <input type="text" id="<?php Echo $this->Get_Field_Id('number') ?>" name="<?php Echo $this->get_field_name('number')?>" value="<?php Echo HTMLSpecialChars($this->get_option('number')) ?>" size="4"><br>
      <small><?php Echo $this->t('Leave blank to show all.') ?></small>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:' ); ?></label>
      <input type="text" value="<?php echo HTMLSpecialChars($this->get_option('exclude')); ?>" name="<?php echo $this->get_field_name('exclude') ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat">
      <small><?php Echo $this->t( 'Term IDs, separated by commas.' ); ?></small>
    </p>

    <p>
      <input type="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" <?php Checked($this->get_option('count')==True); ?> >
      <label for="<?php echo $this->get_field_id('count'); ?>"><?php Echo $this->t('Show term counts.') ?></label>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('orderby') ?>"><?php Echo $this->t('Order by') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('orderby') ?>" name="<?php Echo $this->Get_Field_Name('orderby') ?>">
      <option value="name" <?php Selected($this->get_option('orderby'), 'name') ?>><?php Echo __('Name') ?></option>
      <option value="count" <?php Selected($this->get_option('orderby'), 'count') ?>><?php Echo $this->t('Term Count') ?></option>
      <option value="ID" <?php Selected($this->get_option('orderby'), 'ID') ?>>ID</option>
      <option value="slug" <?php Selected($this->get_option('orderby'), 'slug') ?>><?php Echo $this->t('Slug') ?></option>
      </select>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('order') ?>"><?php Echo $this->t('Order') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('order') ?>" name="<?php Echo $this->Get_Field_Name('order') ?>">
      <option value="ASC" <?php Selected($this->get_option('order'), 'ASC') ?>><?php _e('Ascending') ?></option>
      <option value="DESC" <?php Selected($this->get_option('order'), 'DESC') ?>><?php _e('Descending') ?></option>
      </select>
    </p>

    <?php
  }

  function Widget ($args, $settings){
    # Load options
    $this->load_options ($settings);

    # Check if the Taxonomy is alive
    If (!Taxonomy_Exists($this->Get_Option('taxonomy'))) return False;

    # Display Widget
    Echo $args['before_widget'];

    Echo $args['before_title'] . Apply_Filters('widget_title', $this->get_option('title'), $settings, $this->id_base) . $args['after_title'];

    Echo '<ul>';
    WP_List_Categories(Array(
      'taxonomy'   => $this->Get_Option('taxonomy'),
      'show_count' => $this->Get_Option('count'),
      'number'     => $this->Get_Option('number'),
      'order'      => $this->Get_Option('order'),
      'orderby'    => $this->Get_Option('orderby'),
      'exclude'    => $this->Get_Option('exclude'),
      'title_li'   => ''
    ));
    Echo '</ul>';

    Echo $args['after_widget'];
  }

  function Update ($new_settings, $old_settings){
    return $new_settings;
  }

}