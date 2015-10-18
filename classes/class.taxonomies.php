<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Taxonomies {
  
  static function Init(){
    Add_Action('init', Array(__CLASS__, 'registerTaxonomies'));
    Add_Filter('nav_menu_meta_box_object', Array(__CLASS__, 'changeTaxonomyMenuLabel'));
    Add_Action('init', Array(__CLASS__, 'addTaxonomyArchiveUrls'), 99);
  }

  static function registerTaxonomies(){
    If(Options::Get('encyclopedia_tags')){
			Register_Taxonomy('encyclopedia-tag', Post_Type::$post_type_name, Array(
				'label' => I18n::t('Encyclopedia Tags'),
				'labels' => Array(
					'name' => I18n::t('Tags'),
					'singular_name' => I18n::t('Tag'),
					'search_items' =>  I18n::t('Search Tags'),
					'all_items' => I18n::t('All Tags'),
					'edit_item' => I18n::t('Edit Tag'),
					'update_item' => I18n::t('Update Tag'),
					'add_new_item' => I18n::t('Add New Tag'),
					'new_item_name' => I18n::t('New Tag')
				),
        'show_admin_column' => True,
				'hierarchical' => False,
				'show_ui' => True,
				'query_var' => True,
				'rewrite' => Array(
					'with_front' => False,
					'slug' => LTrim(SPrintF(I18n::t('%s/tag', 'URL slug'), Encyclopedia_Type::$type->slug), '/')
				)
			));
    }
  }

  static function changeTaxonomyMenuLabel($tax){
    If (IsSet($tax->object_type) && In_Array(Post_Type::$post_type_name, $tax->object_type)){
      $tax->labels->name = SPrintF('%1$s &raquo; %2$s', Encyclopedia_Type::$type->label, $tax->labels->name);
    }
    return $tax;
  }


  static function addTaxonomyArchiveUrls(){
    ForEach(Get_Object_Taxonomies(Post_Type::$post_type_name) AS $taxonomy){
      Add_Action ($taxonomy.'_edit_form_fields', Array(__CLASS__, 'printTaxonomyArchiveUrls'), 10, 3);
    }
  }

  static function printTaxonomyArchiveUrls($tag, $taxonomy){
    $taxonomy = Get_Taxonomy($taxonomy);
    $archive_url = Get_Term_Link(Get_Term($tag->term_id, $taxonomy->name));
    $archive_feed = Get_Term_Feed_Link($tag->term_id, $taxonomy->name);
    ?>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo I18n::t('Archive Url') ?></th>
      <td>
        <code><a href="<?php Echo $archive_url ?>" target="_blank"><?php Echo $archive_url ?></a></code><br>
        <span class="description"><?php PrintF(I18n::t('This is the URL to the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo I18n::t('Archive Feed') ?></th>
      <td>
        <code><a href="<?php Echo $archive_feed ?>" target="_blank"><?php Echo $archive_feed ?></a></code><br>
        <span class="description"><?php PrintF(I18n::t('This is the URL to the feed of the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <?php
  }

}

Taxonomies::Init();