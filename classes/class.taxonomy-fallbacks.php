<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Taxonomy_Fallbacks {

  static function Init(){
    Add_Filter('get_the_categories', Array(__CLASS__, 'Filter_Get_The_Categories'));
    Add_Filter('the_category', Array(__CLASS__, 'Filter_The_Category'), 10, 3);
    Add_Filter('get_the_tags', Array(__CLASS__, 'Filter_Get_The_Tags'));
    Add_Filter('the_tags', Array(__CLASS__, 'Filter_The_Tags'), 10, 5);
  }

  static function Filter_Get_The_Categories($arr_categories){
    Global $post;
    
    If (!Is_Admin()){
      $encyclopedia_taxonomy = 'encyclopedia-category';
      $taxonomy_exists = Taxonomy_Exists($encyclopedia_taxonomy);
      $is_encyclopedia_term = $post->post_type == Post_Type::$post_type_name;
      $encyclopedia_uses_post_categories = Is_Object_in_Taxonomy($post->post_type, 'category');
      $encyclopedia_uses_encyclopedia_categories = Is_Object_in_Taxonomy($post->post_type, $encyclopedia_taxonomy);
      
      If ($taxonomy_exists && $is_encyclopedia_term && !$encyclopedia_uses_post_categories && $encyclopedia_uses_encyclopedia_categories){
        $arr_categories = Get_The_Terms($post->ID, $encyclopedia_taxonomy);
        If (Is_Array($arr_categories)){
          ForEach ($arr_categories As &$category){
            _Make_Cat_Compat($category); # Compat mode for very very very old and deprecated themes...
          }
        }
      }
    }
    
    return $arr_categories;
  }

  static function Filter_The_Category($category_list, $separator = Null, $parents = Null){
    Global $post;
    
    If (!Is_Admin()){
      $encyclopedia_taxonomy = 'encyclopedia-category';
      $taxonomy_exists = Taxonomy_Exists($encyclopedia_taxonomy);
      $is_encyclopedia_term = $post->post_type == Post_Type::$post_type_name;
      $encyclopedia_uses_post_categories = Is_Object_in_Taxonomy($post->post_type, 'category');
      $encyclopedia_uses_encyclopedia_categories = Is_Object_in_Taxonomy($post->post_type, $encyclopedia_taxonomy);

      If ($taxonomy_exists && $is_encyclopedia_term && !$encyclopedia_uses_post_categories && $encyclopedia_uses_encyclopedia_categories){
        $category_list = Get_The_Term_List($post->ID, $encyclopedia_taxonomy, Null, $separator, Null);
        If (Empty($category_list)) $category_list = __('Uncategorized');
      }
    }
    
    return $category_list;
  }
  
  static function Filter_Get_The_Tags($arr_tags){
    Global $post;
    
    If (!Is_Admin()){
      $encyclopedia_taxonomy = 'encyclopedia-tag';
      $taxonomy_exists = Taxonomy_Exists($encyclopedia_taxonomy);
      $is_encyclopedia_term = $post->post_type == Post_Type::$post_type_name;
      $encyclopedia_uses_post_tags = Is_Object_in_Taxonomy($post->post_type, 'post_tag');
      $encyclopedia_uses_encyclopedia_tags = Is_Object_in_Taxonomy($post->post_type, $encyclopedia_taxonomy);
      
      If ($taxonomy_exists && $is_encyclopedia_term && !$encyclopedia_uses_post_tags && $encyclopedia_uses_encyclopedia_tags){
        $arr_tags = Get_The_Terms($post->ID, $encyclopedia_taxonomy);
      }
    }
    
    return $arr_tags;
  }

  static function Filter_The_Tags($tag_list, $before, $separator, $after, $post_id){
    $post = Get_Post($post_id);

    If (!Is_Admin()){
      $encyclopedia_taxonomy = 'encyclopedia-tag';
      $taxonomy_exists = Taxonomy_Exists($encyclopedia_taxonomy);
      $is_encyclopedia_term = $post->post_type == Post_Type::$post_type_name;
      $encyclopedia_uses_post_tags = Is_Object_in_Taxonomy($post->post_type, 'post_tag');
      $encyclopedia_uses_encyclopedia_tags = Is_Object_in_Taxonomy($post->post_type, $encyclopedia_taxonomy);

      If ($taxonomy_exists && $is_encyclopedia_term && !$encyclopedia_uses_post_tags && $encyclopedia_uses_encyclopedia_tags){
        $tag_list = Get_The_Term_List($post_id, $encyclopedia_taxonomy, $before, $separator, $after);
      }
    }
    
    return $tag_list;
  }
  
}

Taxonomy_Fallbacks::Init();