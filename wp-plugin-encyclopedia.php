<?php
/*
Plugin Name: Encyclopedia Lite
Plugin URI: http://dennishoppe.de/wordpress-plugins/encyclopedia
Description: Encyclopedia Lite enables you to create your own encyclopedia, lexicon, glossary, wiki or dictionary.
Version: 1.0.2
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

// Load Widget
Include DirName(__FILE__).'/wp-widget-encyclopedia-related-terms.php';
Include DirName(__FILE__).'/wp-widget-encyclopedia-taxonomies.php';
Include DirName(__FILE__).'/wp-widget-encyclopedia-taxonomy-cloud.php';
Include DirName(__FILE__).'/wp-widget-encyclopedia-terms.php';

// Load Plugin Kernel
If (!Class_Exists('wp_plugin_encyclopedia')){
class wp_plugin_encyclopedia {
  var $base_url; // url to the plugin directory
  var $arr_taxonomies; // All buildIn Taxonomies - also the inactive ones.
  var $post_type = 'encyclopedia'; // Name of the post type
  var $encyclopedia_type; // An object with the properties of current encyclopedia type

  Function __construct(){
    // Read base
    $this->base_url = get_bloginfo('wpurl').'/'.SubStr(RealPath(DirName(__FILE__)), Strlen(ABSPATH));

    // Option boxes
    $this->arr_option_box = Array( 'main' => Array(), 'side' => Array() );

    // Get ready to translate
    Add_Action('init', Array($this, 'Load_TextDomain'));

    // Load current Encyclopedia type
    Add_Action('init', Array($this, 'Load_Encyclopedia_Type'));

    // Set Hooks
    Add_Action('admin_menu', Array($this, 'Add_Options_Page'));
    Add_Action('init', Array($this, 'Register_Post_Type'));
    Add_Filter('post_updated_messages', Array($this, 'Updated_Messages' ));
    Add_Action('init', Array($this, 'Register_Taxonomies'));
    Add_Action('init', Array($this, 'Add_Taxonomy_Archive_Urls'), 99);
    Add_Action('loop_start', Array($this, 'Start_Loop'));
    Add_Filter('pre_get_posts', Array($this, 'Filter_Query'));
    Add_Filter('posts_where', Array($this, 'Filter_Posts_Where'), 10, 2);
    Add_Filter('the_content', Array($this, 'Filter_Content'));
    Add_Action('wp_enqueue_scripts', Array($this, 'Enqueue_Encyclopedia_Style'));
    Add_Action('admin_init', Array($this, 'User_Creates_New_Term'));
    Add_Action('untrash_post', Array($this, 'User_Untrashes_Post'));
    Add_Filter('views_edit-encyclopedia', Array($this, 'Add_Term_Count_Notice'));

    // Register Widgets
    Add_Action ('widgets_init', Array($this,'Register_Widgets'));

    // Shortcodes
    Add_Shortcode('encyclopedia_related_terms', Array($this, 'Shortcode_Related_Terms'));

    If (Is_Admin()){
			WP_Enqueue_Style('encyclopedia-icon', $this->base_url . '/post-type-icon.css');
		}

    // Add to GLOBALs
    $GLOBALS[__CLASS__] = $this;
  }

  Function Load_TextDomain(){
    $locale = Apply_Filters( 'plugin_locale', get_locale(), __CLASS__ );
    Load_TextDomain (__CLASS__, DirName(__FILE__).'/language/' . $locale . '.mo');
  }

  Function t ($text, $context = ''){
    // Translates the string $text with context $context
    If ($context == '')
      return Translate ($text, __CLASS__);
    Else
      return Translate_With_GetText_Context ($text, $context, __CLASS__);
  }

  function Register_Widgets(){
		Register_Widget('wp_widget_encyclopedia_related_terms');
		Register_Widget('wp_widget_encyclopedia_taxonomies');
		Register_Widget('wp_widget_encyclopedia_taxonomy_cloud');
		Register_Widget('wp_widget_encyclopedia_terms');
	}

  function Add_Options_Page(){
    $handle = Add_Options_Page (
      $this->t('Encyclopedia Options'),
      $this->t('Encyclopedia'),
      'manage_options',
      __CLASS__,
      Array($this, 'Print_Options_Page')
    );

    // Add JavaScript to this handle
    Add_Action ('load-' . $handle, Array($this, 'Load_Options_Page'));

    // Add option boxes
    $this->Add_Option_Box ( __('General'), DirName(__FILE__).'/options-page/box-general.php');
    $this->Add_Option_Box ( $this->t('Taxonomies'), DirName(__FILE__).'/options-page/box-taxonomies.php');
    $this->Add_Option_Box ( $this->t('Archive Page'), DirName(__FILE__).'/options-page/box-archive-page.php');
    $this->Add_Option_Box ( $this->t('Single Page'), DirName(__FILE__).'/options-page/box-single-page.php');
    $this->Add_Option_Box ( $this->t('Archive Url'), DirName(__FILE__).'/options-page/box-archive-link.php', 'side' );
  }

  function Get_Options_Page_Url($parameters = Array()){
    $url = Add_Query_Arg(Array('page' => __CLASS__), Admin_Url('options-general.php'));
    If (Is_Array($parameters) && !Empty($parameters)) $url = Add_Query_Arg($parameters, $url);
    return $url;
  }

  function Load_Options_Page(){
    // If the Request was redirected from a "Save Options"-Post
    If (IsSet($_REQUEST['options_saved']))
      $this->Flush_Rewrite_Rules();

    // If this is a Post request to save the options
    If ($this->Save_Options()) WP_Redirect( $this->Get_Options_Page_Url(Array('options_saved' => 'true')) );

    WP_Enqueue_Script('dashboard');
    WP_Enqueue_Style('dashboard');

    WP_Enqueue_Script('options-page', $this->base_url . '/options-page/options-page.js', Array('jquery') );
    WP_Enqueue_Style('options-page', $this->base_url . '/options-page/options-page.css' );

    // Remove incompatible JS Libs
    WP_Dequeue_Script('post');
  }

  function Flush_Rewrite_Rules(){
    $GLOBALS['wp_rewrite']->flush_rules();
  }

  function Print_Options_Page(){
    Include DirName(__FILE__).'/options-page/options-page.php';
  }

  function Add_Option_Box($title, $include_file, $column = 'main', $state = 'opened'){
    // Check the input
    If (!Is_File($include_file)) return False;
    If ( $title == '' ) $title = '&nbsp;';

    // Column (can be 'side' or 'main')
    If ($column != '' && $column != Null && $column != 'main')
      $column = 'side';
    Else
      $column = 'main';

    // State (can be 'opened' or 'closed')
    If ($state != '' && $state != Null && $state != 'opened')
      $state = 'closed';
    Else
      $state = 'opened';

    // Add a new box
    $this->arr_option_box[$column][] = Array('title' => $title, 'file' => $include_file, 'state' => $state);
  }

  function Get_Option($key = Null, $default = False){
    // Read Options
    $arr_option = Array_Merge (
      (Array) $this->Default_Options(),
      (Array) get_option(__CLASS__)
    );

    // Locate the option
    If ($key == Null)
      return $arr_option;
    ElseIf (IsSet($arr_option[$key]))
      return $arr_option[$key];
    Else
      return $default;
  }

  function Save_Options(){
    // Check if this is a post request
    If (Empty($_POST)) return False;

    // Clean the Post array
    $_POST = StripSlashes_Deep($_POST);
    ForEach ($_POST AS $option => $value)
      If (!$value) Unset ($_POST[$option]);

    // Save Options
    Update_Option (__CLASS__, $_POST);

    return True;
  }

  function Default_Options(){
    return Array(
      'embed_default_style' => 'yes',
			'encyclopedia_tags' => 'yes',
      'term_filter_for_archives' => 'yes'
    );
  }

  function Load_Encyclopedia_Type(){
		$this->encyclopedia_type = New StdClass;
    $this->encyclopedia_type->label = $this->t('Lexicon');
		$this->encyclopedia_type->slug = 'lexicon';
	}

  function Register_Post_Type(){
    Register_Post_Type ($this->post_type, Array(
      'labels' => Array(
        'name' => $this->encyclopedia_type->label,
        'singular_name' => $this->t('Term'),
        'add_new' => $this->t('Add Term'),
        'add_new_item' => $this->t('New Term'),
        'edit_item' => $this->t('Edit Term'),
        'view_item' => $this->t('View Term'),
        'search_items' => $this->t('Search Terms'),
        'not_found' =>  $this->t('No Terms found'),
        'not_found_in_trash' => $this->t('No Terms found in Trash'),
        'parent_item_colon' => ''
        ),
      'public' => True,
      'show_ui' => True,
      'has_archive' => True,
			'map_meta_cap' => True,
			'hierarchical' => True,
      'rewrite' => Array(
        'slug' => $this->encyclopedia_type->slug,
        'with_front' => False
      ),
      'supports' => Array( 'title', 'editor', 'author', 'excerpt', 'revisions' ),
      'menu_position' => 20, // below Pages
      'register_meta_box_cb' => Array($this, 'Add_Meta_Boxes')
    ));
  }

  function Add_Meta_Boxes(){
		// There will be added no other meta boxes
	}

  function Updated_Messages($arr_message){
    return Array_Merge ($arr_message, Array($this->post_type => Array(
      1 => SPrintF ($this->t('Term updated. (<a href="%s">View Term</a>)'), get_permalink()),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => $this->t('Term updated.'),
      5 => IsSet($_GET['revision']) ? SPrintF($this->t('Term restored to revision from %s'), WP_Post_Revision_Title( (Int) $_GET['revision'], False ) ) : False,
      6 => SPrintF($this->t('Term published. (<a href="%s">View Term</a>)'), get_permalink()),
      7 => $this->t('Term saved.'),
      8 => $this->t('Term submitted.'),
      9 => SPrintF($this->t('Term scheduled. (<a target="_blank" href="%s">View Term</a>)'), get_permalink()),
      10 => SPrintF($this->t('Draft updated. (<a target="_blank" href="%s">Preview Term</a>)'), Add_Query_Arg('preview', 'true', get_permalink()))
    )));
  }

  function Register_Taxonomies(){
    If($this->Get_Option('encyclopedia_tags') == 'yes'){
			Register_Taxonomy('encyclopedia-tag', $this->post_type, Array(
				'label' => $this->t( 'Encyclopedia Tags' ),
				'labels' => Array(
					'name' => $this->t( 'Tags' ),
					'singular_name' => $this->t( 'Tag' ),
					'search_items' =>  $this->t( 'Search Tags' ),
					'all_items' => $this->t( 'All Tags' ),
					'edit_item' => $this->t( 'Edit Tag' ),
					'update_item' => $this->t( 'Update Tag' ),
					'add_new_item' => $this->t( 'Add New Tag' ),
					'new_item_name' => $this->t( 'New Tag' )
				),
				'hierarchical' => False,
				'show_ui' => True,
				'query_var' => True,
				'rewrite' => Array(
					'with_front' => False,
					'slug' => SPrintF('%s-tag', $this->encyclopedia_type->slug)
				),
			));
    }
  }

  function Add_Taxonomy_Archive_Urls(){
    ForEach(Get_Object_Taxonomies($this->post_type) AS $taxonomy){
      Add_Action ($taxonomy.'_edit_form_fields', Array($this, 'Print_Taxonomy_Archive_Urls'), 10, 3);
    }
  }

  function Print_Taxonomy_Archive_Urls($tag, $taxonomy){
    $taxonomy = Get_Taxonomy($taxonomy);
    $archive_url = get_term_link(get_term($tag->term_id, $taxonomy->name));
    $archive_feed = get_term_feed_link($tag->term_id, $taxonomy->name);
    ?>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo $this->t('Archive Url') ?></th>
      <td>
        <code><a href="<?php Echo $archive_url ?>" target="_blank"><?php Echo $archive_url ?></a></code><br />
        <span class="description"><?php PrintF($this->t('This is the URL to the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo $this->t('Archive Feed') ?></th>
      <td>
        <code><a href="<?php Echo $archive_feed ?>" target="_blank"><?php Echo $archive_feed ?></a></code><br />
        <span class="description"><?php PrintF($this->t('This is the URL to the feed of the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <?php
  }

  function Enqueue_Encyclopedia_Style(){
    If ($this->Get_Option('embed_default_style') == 'yes')
      WP_Enqueue_Style('encyclopedia', $this->base_url.'/encyclopedia.css');
  }

  function Is_Encyclopedia_Archive($query){
		If (!Is_Admin() && ($query->is_post_type_archive || $query->is_tax)){
			If ($query->is_post_type_archive($this->post_type) || $query->is_tax(Get_Object_Taxonomies($this->post_type))){
				return True;
			}
		}
		return False;
	}

  function User_Creates_New_Term(){
    If ( BaseName($_SERVER['SCRIPT_NAME']) == 'post-new.php' && IsSet($_GET['post_type']) && $_GET['post_type'] == $this->post_type )
      $this->Check_Term_Count();
  }

  function User_Untrashes_Post($post_id){
    If (Get_Post_Type($post_id) == $this->post_type)
      $this->Check_Term_Count();
  }

  function Check_Term_Count(){
    If (Count(Get_Posts(Array('post_type' => $this->post_type, 'post_status' => 'any', 'posts_per_page' => 5))) >= 5)
      $this->Print_Term_Count_Limit();
  }

  function Print_Term_Count_Limit(){
    WP_Die(
      SPrintF(
        '<h1>%s</h1><p>%s</p><p>%s</p><p>%s</p>',
        $this->t('Sorry!'),
        $this->t('In the Lite Version you can create five terms only.'),
        $this->t('Why not switching to <a href="http://dennishoppe.de/en/wordpress-plugins/encyclopedia" target="_blank">Encyclopedia Pro</a>? :)'),
        SPrintF(
          '<a href="%s" class="button">%s</a>',
          Admin_URL('edit.php?post_type=' . $this->post_type),
          $this->t('&laquo; Back to your terms')
        )
      )
    );
  }

  function Add_Term_Count_Notice($views){
    ?><div id="message" class="error">
    <p><?php PrintF('%s %s %s',
      $this->t('Please notice:'),
      $this->t('In the Lite Version you can create five terms only.'),
      $this->t('Why not switching to <a href="http://dennishoppe.de/en/wordpress-plugins/encyclopedia" target="_blank">Encyclopedia Pro</a>? :)')
    );
    ?></p>
    </div><?php
    return $views;
  }

  function Filter_Query($query){
		If ($this->Is_Encyclopedia_Archive($query) && !$query->get('suppress_filters') ){
			$query->query_vars = Array_Merge($query->query_vars, Array(
				'orderby' => 'title',
				'order' => 'ASC',
				'post_title_like' => IsSet($_REQUEST['filter']) ? $_REQUEST['filter'] : Null
			));
		}
	}

	function Filter_Posts_Where($where, $query){
		Global $wpdb;
		$post_title_like = $query->get('post_title_like');

		If ($this->Is_Encyclopedia_Archive($query) && !Empty($post_title_like))
			return SPrintF('%s AND %s LIKE "%s%%" ', $where, $wpdb->posts.'.post_title', Esc_SQL(Like_Escape($post_title_like)));
		Else
			return $where;
	}

	function Filter_Content($content){
		Global $post;
		If ($post->post_type == $this->post_type && Is_Single($post->ID)){
    If (	StrPos($content, '[encyclopedia_related_terms]') === False && // Avoid double inclusion of the ShortCode
					StrPos($content, '[encyclopedia_related_terms ') === False && // Without closing bracket to find ShortCodes with attributes
					$this->Get_Option('related_terms') != 'none' && // User can disable the auto append feature
					!post_password_required() // The user isn't allowed to read this post
			){
        $content = $this->Shortcode_Related_Terms() . $content;
			}
		}
		return $content;
	}

  function Start_Loop($query){
		Static $loop_already_startet;
		If ($loop_already_startet) return;

    If ($this->Is_Encyclopedia_Archive($query) && !$query->get('suppress_filters') && $this->Get_Option('term_filter_for_archives') == 'yes' ){
			Echo $this->Load_Template('encyclopedia-term-filter.php', Array('filter' => $this->Generate_Term_Filters()));
			$loop_already_startet = True;
		}
  }

  function Generate_Term_Filters(){
		$arr_current_filter = (IsSet($_REQUEST['filter']) && !Empty($_REQUEST['filter'])) ? Str_Split($_REQUEST['filter']) : Array();
		Array_UnShift($arr_current_filter, '');

		$arr_filter = Array(); // This will be the function result

		ForEach ($arr_current_filter AS $filter_letter){
			$filter_part .= $filter_letter;
			$arr_available_filters = $this->Get_Available_Filters($filter_part);
			If (Count($arr_available_filters) < 2) Break;
			$active_filter_part = SubStr(Implode($arr_current_filter), 0, StrLen($filter_part)+1);

			$arr_filter_line = Array();
			ForEach ($arr_available_filters AS $available_filter){
				$filter = New StdClass;
				$filter->filter = UCFirst($available_filter);
				$filter->link = Add_Query_Arg(Array('filter' => $available_filter), Get_Post_Type_Archive_Link($this->post_type));
				$filter->active = ($active_filter_part == $available_filter);
				$arr_filter_line[] = $filter;
			}
			$arr_filter[] = $arr_filter_line;
		}

		return $arr_filter;
	}

  function Load_Template($template_name, $vars = Array()){
		Extract($vars);
		$template_path = Locate_Template($template_name);
		Ob_Start();
		If(!Empty($template_path)) Include $template_path;
		Else Include SPrintF('%s/templates/%s', DirName(__FILE__), $template_name);
		return Ob_Get_Clean();
	}

  function Get_Available_Filters($prefix = ''){
		Global $wpdb;
		$length = StrLen($prefix) + 1;
		$stmt = "
		SELECT LOWER(LEFT(post_title,{$length})) as term_part
		FROM {$wpdb->posts}
		WHERE post_type 	= 		'{$this->post_type}' AND
					post_status = 		'publish' AND
					post_title 	LIKE	'{$prefix}%'
		GROUP BY term_part
		ORDER BY term_part";
		return $wpdb->get_col($stmt);
	}

  function Get_Tag_Related_Terms($term_id = Null, $number = 10){
    Global $wpdb, $post;

    If ($term_id == Null) $term_id = $post->ID;

    // Get the Tags
    $arr_tags = WP_Get_Post_Terms($term_id, 'encyclopedia-tag');
    If(Empty($arr_tags)) return False;

    // Get term IDs
    $arr_term_ids = Array();
    ForEach( $arr_tags as $taxonomy )
      $arr_term_ids[] = $taxonomy->term_taxonomy_id;
    $str_tag_list = Implode(',', $arr_term_ids);

    // The Query to get the related posts
    $stmt = " SELECT posts.*,
                     COUNT(term_relationships.object_id) AS common_tag_count
              FROM   {$wpdb->term_relationships} AS term_relationships,
                     {$wpdb->posts} AS posts
              WHERE  term_relationships.object_id = posts.id
              AND    term_relationships.term_taxonomy_id IN({$str_tag_list})
              AND    posts.id != {$term_id}
              AND    posts.post_status = 'publish'
              GROUP  BY term_relationships.object_id
              ORDER  BY common_tag_count DESC,
                     posts.post_date_gmt DESC
              LIMIT  0, {$number}";

    // Put it in a WP_Query
    $query = New WP_Query();
    $query->posts = $wpdb->Get_Results($stmt);
    $query->post_count = Count($query->posts);
    $query->Rewind_Posts();

    // return
    If ($query->post_count == 0) return False;
    Else return $query;
  }

	function Shortcode_Related_Terms($attributes = False){
    $attributes = Array_Merge(Array(
      'max_terms' => 10
    ), (Array) $attributes);
		return $this->Load_Template('encyclopedia-related-terms.php', Array('attributes' => $attributes));
	}

  function Pro_Notice(){
    PrintF (
      $this->t('Sorry, this feature is only available in the <a href="%s" target="_blank">Pro Version of the Encyclopedia Plugin</a>.'),
      $this->t('http://dennishoppe.de/en/wordpress-plugins/encyclopedia', 'Link to the authors website')
    );
  }

} /* End of the Class */
New wp_plugin_encyclopedia;
} /* End of the If-Class-Exists-Condition */
/* End of File */