<?php
Namespace WordPress\Plugin\Encyclopedia;

class WPML {
  public
    $wpml_is_active = False, # Will become true if WPML is active
    $code; # Pointer to the core object

  public function __construct($core){
    $this->core = $core;

    # Define filters
    Add_Action('widgets_init', Array($this, 'Find_WPML'));
    Add_Filter('gettext_with_context', Array($this, 'Filter_Gettext_with_Context'), 1, 4);
    Add_Filter('encyclopedia_available_filters', Array($this, 'Filter_Available_Filters'));
  }

  public function Find_WPML(){
    $this->wpml_is_active = Defined('ICL_SITEPRESS_VERSION');
  }

  public function Filter_Gettext_with_Context($translation, $text, $context, $domain){
    # If you are using WPML the post type slug MUST NOT be translated! You can translate your slug in WPML
    If ($this->wpml_is_active && $context == 'URL slug' && $domain == $this->core->i18n->Get_Text_Domain())
      return $text;
    Else
      return $translation;
  }

  public function Filter_Available_Filters($arr_filter){
    If ($this->wpml_is_active && Is_Array($arr_filter)){
      ForEach($arr_filter As $index => $filter){
        # Check if there are posts behind this filter in this language
        $query = New \WP_Query(Array(
          'post_type' => $this->core->post_type,
          'post_title_like' => $filter . '%',
          'posts_per_page' => 1
        ));
        If (!$query->Have_Posts()) Unset($arr_filter[$index]);
      }
    }

    return $arr_filter;
  }

}