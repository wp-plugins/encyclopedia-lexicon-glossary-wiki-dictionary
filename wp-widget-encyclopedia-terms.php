<?php If (!Class_Exists('wp_widget_encyclopedia_terms')){
Class wp_widget_encyclopedia_terms Extends WP_Widget {
  var $encyclopedia;

  Function __construct(){
    If (IsSet($GLOBALS['wp_plugin_encyclopedia']) && Is_Object($GLOBALS['wp_plugin_encyclopedia']))
      $this->encyclopedia = $GLOBALS['wp_plugin_encyclopedia'];
    Else
      return False;

    // Setup the Widget data
    parent::__construct (
      False,
      $this->t('Encyclopedia Terms'),
      Array('description' => $this->t('Displays your encyclopedia terms as list.'))
    );
  }

  Function t ($text, $context = ''){
    return $this->encyclopedia->t($text, $context);
  }

  Function Default_Options(){
    // Default settings
    return Array(
      'number'  => Null,
      'orderby' => 'title',
      'order'   => 'ASC',
    );
  }

  Function Load_Options($options){
    $options = (ARRAY) $options;

    // Delete empty values
    ForEach ($options AS $key => $value)
      If (!$value) Unset($options[$key]);

    // Load options
    $this->arr_option = Array_Merge ($this->Default_Options(), $options);
  }

  Function Get_Option($key, $default = False){
    If (IsSet($this->arr_option[$key]) && $this->arr_option[$key])
      return $this->arr_option[$key];
    Else
      return $default;
  }

  Function Set_Option($key, $value){
    $this->arr_option[$key] = $value;
  }

  Function Form ($settings){
    // Load options
    $this->load_options ($settings); Unset ($settings);
    ?>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('title') ?>"><?php Echo $this->t('Title') ?></label>:
      <input type="text" id="<?php Echo $this->Get_Field_Id('title') ?>" name="<?php Echo $this->get_field_name('title')?>" value="<?php Echo HTMLSpecialChars($this->get_option('title')) ?>"><br>
      <small><?php Echo $this->t('Leave blank to use the widget default title.') ?></small>
    </p>

    <p>
      <label for="<?php Echo $this->Get_Field_Id('orderby') ?>"><?php Echo $this->t('Order by') ?></label>:
      <select id="<?php Echo $this->Get_Field_Id('orderby') ?>" name="<?php Echo $this->Get_Field_Name('orderby') ?>">
      <option value="title" <?php Selected($this->get_option('orderby'), 'title') ?>><?php Echo __('Title') ?></option>
      <option value="ID" <?php Selected($this->get_option('orderby'), 'ID') ?>>ID</option>
      <option value="author" <?php Selected($this->get_option('orderby'), 'author') ?>><?php Echo $this->t('Author') ?></option>
      <option value="date" <?php Selected($this->get_option('orderby'), 'date') ?>><?php Echo $this->t('Date') ?></option>
      <option value="modified" <?php Selected($this->get_option('orderby'), 'modified') ?>><?php Echo $this->t('Last modification') ?></option>
      <option value="rand" <?php Selected($this->get_option('orderby'), 'rand') ?>><?php Echo $this->t('Random') ?></option>
      <option value="comment_count" <?php Selected($this->get_option('orderby'), 'comment_count') ?>><?php Echo $this->t('Comment Count') ?></option>
      <option value="menu_order" <?php Selected($this->get_option('orderby'), 'menu_order') ?>><?php Echo $this->t('Menu Order') ?></option>
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

  Function Widget ($args, $settings){
    // Load options
    $this->load_options ($settings); Unset ($settings);

    // Load the Query
    $term_query = New WP_Query(Array(
      'post_type' => $this->encyclopedia->post_type,
      'orderby' => $this->get_option('orderby'),
      'order' => $this->get_option('order'),
      'nopaging' => True,
      'ignore_sticky_posts' => True,
      'suppress_filters' => True
    ));
    If (!$term_query->have_posts()) return;

    // Display Widget
    Echo $args['before_widget'];
    Echo $args['before_title'] . Apply_Filters('widget_title', $this->get_option('title'), $settings, $this->id_base) . $args['after_title'];
    Echo $this->encyclopedia->Load_Template('encyclopedia-terms-widget.php', Array('term_query' => $term_query));
    Echo $args['after_widget'];

    // Reset Post data
    WP_Reset_Postdata();
  }

  Function Update ($new_settings, $old_settings){
    return $new_settings;
  }

} /* End of Class */
} /* End of If-Class-Exists-Condition */
/* End of File */