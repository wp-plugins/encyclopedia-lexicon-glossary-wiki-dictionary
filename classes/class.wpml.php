<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class WPML {

  static function Init(){
    Add_Filter('gettext_with_context', Array(__CLASS__, 'filterGettextWithContext'), 1, 4);
    Add_Filter('encyclopedia_available_prefix_filters', Array(__CLASS__, 'filterAvailablePrefixFilters'));
  }

  static function IsWPMLActive(){
    return Defined('ICL_SITEPRESS_VERSION');
  }

  static function filterGettextWithContext($translation, $text, $context, $domain){
    # If you are using WPML the post type slug MUST NOT be translated! You can translate your slug in WPML
    If (self::IsWPMLActive() && $context == 'URL slug' && $domain == I18n::getTextDomain())
      return $text;
    Else
      return $translation;
  }

  static function filterAvailablePrefixFilters($arr_filter){
    If (self::IsWPMLActive() && Is_Array($arr_filter)){
      ForEach($arr_filter As $index => $filter){
        # Check if there are posts behind this filter in this language
        $query = New \WP_Query(Array(
          'post_type' => Post_Type::$post_type_name,
          'post_title_like' => $filter . '%',
          'posts_per_page' => 1
        ));
        If (!$query->Have_Posts()) Unset($arr_filter[$index]);
      }
    }

    return $arr_filter;
  }

}

WPML::Init();