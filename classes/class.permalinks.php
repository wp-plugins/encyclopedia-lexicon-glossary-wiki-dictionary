<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Permalinks {
  public static
    $rewrite_rules = Array(); # Array with the new additional rewrite rules
  
  static function Init(){
    Add_Filter('init', Array(__CLASS__, 'defineRewriteRules'), 99);
    Add_Filter('rewrite_rules_array', Array(__CLASS__, 'addRewriteRules'));
    Add_Action('wp_loaded', Array(__CLASS__, 'flushRewriteRules'));
  }

  static function defineRewriteRules(){
    # Add filter permalink structure for post type archive
    $post_type = Get_Post_Type_Object(Post_Type::$post_type_name);
    $archive_url_path = $post_type->rewrite['slug'];
    self::$rewrite_rules[LTrim(SPrintF('%s/filter:([^/]+)/?$', $archive_url_path), '/')] = SPrintF('index.php?post_type=%s&filter=$matches[1]', Post_Type::$post_type_name);
    self::$rewrite_rules[LTrim(SPrintF('%s/filter:([^/]+)/page/([0-9]{1,})/?$', $archive_url_path), '/')] = SPrintF('index.php?post_type=%s&filter=$matches[1]&paged=$matches[2]', Post_Type::$post_type_name);

    # Add filter permalink structure for taxonomy archives
    ForEach (Get_Taxonomies(Null, 'objects') As $taxonomy){
      $taxonomy_slug = $taxonomy->rewrite['slug'];
      If (!In_Array(Post_Type::$post_type_name, $taxonomy->object_type)) Continue;
      self::$rewrite_rules[LTrim(SPrintF('%s/([^/]+)/filter:([^/]+)/?$', $taxonomy_slug), '/')] = SPrintF('index.php?%s=$matches[1]&filter=$matches[2]', $taxonomy->name);
      self::$rewrite_rules[LTrim(SPrintF('%s/([^/]+)/filter:([^/]+)/page/([0-9]{1,})/?$', $taxonomy_slug), '/')] = SPrintF('index.php?%s=$matches[1]&filter=$matches[2]&paged=$matches[3]', $taxonomy->name);
    }
  }

  static function addRewriteRules($rules){
    If (Is_Array(self::$rewrite_rules) && Is_Array($rules))
      return Array_Merge(self::$rewrite_rules, $rules);
    Else
      return $rules;
  }

  static function flushRewriteRules(){
    $rules = Get_Option('rewrite_rules');
    ForEach (self::$rewrite_rules AS $new_rule => $redirect){
      If (!IsSet($rules[$new_rule])){
        Flush_Rewrite_Rules();
        return;
      }
    }
  }

}

Permalinks::Init();