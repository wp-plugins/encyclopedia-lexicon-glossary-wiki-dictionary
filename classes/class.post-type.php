<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Post_Type {
  public static
    $post_type_name = 'encyclopedia'; # Name of the post type
    
  static function Init(){
    Add_Filter('post_updated_messages', Array(__CLASS__, 'filterUpdatedMessages'));
    Add_Action('init', Array(__CLASS__, 'registerPostType'), 11);
  }

  static function registerPostType(){
    Register_Post_Type(self::$post_type_name, Array(
      'labels' => Array(
        'name' => Encyclopedia_Type::$type->label,
        'singular_name' => I18n::t('Term'),
        'add_new' => I18n::t('Add Term'),
        'add_new_item' => I18n::t('New Term'),
        'edit_item' => I18n::t('Edit Term'),
        'view_item' => I18n::t('View Term'),
        'search_items' => I18n::t('Search Terms'),
        'not_found' =>  I18n::t('No Terms found'),
        'not_found_in_trash' => I18n::t('No Terms found in Trash'),
        'parent_item_colon' => ''
      ),
      'public' => True,
      'show_ui' => True,
      'menu_icon' => 'dashicons-welcome-learn-more',
      'has_archive' => True,
      'map_meta_cap' => True,
			'hierarchical' => False,
      'rewrite' => Array(
        'slug' => Encyclopedia_Type::$type->slug,
        'with_front' => False
      ),
      'supports' => Array( 'title', 'editor', 'author', 'excerpt' ),
      'menu_position' => 20, # below Pages
      #'register_meta_box_cb' => Array(__CLASS__, 'addMetaBoxes')
    ));

    # Add optionally post type support
    If (Options::Get('enable_custom_fields'))
      Add_Post_Type_Support(self::$post_type_name, 'custom-fields');
  }

  static function addMetaBoxes(){
		# There will be added no other meta boxes yet
	}

  static function filterUpdatedMessages($arr_message){
    return Array_Merge ($arr_message, Array(self::$post_type_name => Array(
      1 => SPrintF (I18n::t('Term updated. (<a href="%s">View Term</a>)'), Get_Permalink()),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => I18n::t('Term updated.'),
      5 => IsSet($_GET['revision']) ? SPrintF(I18n::t('Term restored to revision from %s'), WP_Post_Revision_Title( (Int) $_GET['revision'], False ) ) : False,
      6 => SPrintF(I18n::t('Term published. (<a href="%s">View Term</a>)'), Get_Permalink()),
      7 => I18n::t('Term saved.'),
      8 => I18n::t('Term submitted.'),
      9 => SPrintF(I18n::t('Term scheduled. (<a target="_blank" href="%s">View Term</a>)'), Get_Permalink()),
      10 => SPrintF(I18n::t('Draft updated. (<a target="_blank" href="%s">Preview Term</a>)'), Add_Query_Arg('preview', 'true', Get_Permalink()))
    )));
  }

  static function getArchiveLink($filter = '', $taxonomy_term = Null){
    $permalink_structure = Get_Option('permalink_structure');

    # Get base url
    If ($taxonomy_term)
      $base_url = Get_Term_Link($taxonomy_term);
    Else
      $base_url = Get_Post_Type_Archive_Link(self::$post_type_name);

    If (!Empty($permalink_structure))
      return User_TrailingSlashIt(SPrintF('%1$s/filter:%2$s', RTrim($base_url, '/'), RawURLEncode($filter)));
    Else
      return Add_Query_Arg(Array('filter' => RawURLEncode($filter)), $base_url);
  }

}

Post_Type::Init();