<?php
/*
Plugin Name: Encyclopedia Lite
Plugin URI: http://dennishoppe.de/en/wordpress-plugins/encyclopedia
Description: Encyclopedia Lite enables you to create your own encyclopedia, lexicon, glossary, wiki or dictionary.
Version: 1.5.4
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

# Load Widget
Include DirName(__FILE__).'/wp-widget-encyclopedia-related-terms.php';
Include DirName(__FILE__).'/wp-widget-encyclopedia-search.php';
Include DirName(__FILE__).'/wp-widget-encyclopedia-taxonomies.php';
Include DirName(__FILE__).'/wp-widget-encyclopedia-taxonomy-cloud.php';
Include DirName(__FILE__).'/wp-widget-encyclopedia-terms.php';

# Load Plugin Kernel
If (!Class_Exists('wp_plugin_encyclopedia')){
class wp_plugin_encyclopedia {
  var $base_url; # url to the plugin directory
  var $arr_taxonomies; # All buildIn Taxonomies.
  var $post_type = 'encyclopedia'; # Name of the post type
  var $encyclopedia_type; # An object with the properties of current encyclopedia type

  function __construct(){
    # Read base
    $this->Load_Base_Url();

    # Option boxes
    $this->arr_option_box = Array( 'main' => Array(), 'side' => Array() );

    # Get ready to translate
    Add_Action('widgets_init', Array($this, 'Load_TextDomain'));

    # Load current Encyclopedia type
    Add_Action('init', Array($this, 'Load_Encyclopedia_Type'));

    # Set Hooks
    Register_Activation_Hook(__FILE__, Array($this, 'Plugin_Activation'));
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
    Add_Filter('the_content', Array($this, 'Link_Terms'), 99);
    Add_Filter('nav_menu_meta_box_object', Array($this, 'Change_Taxonomy_Menu_Label'));
    Add_Filter('query_vars', Array($this, 'Register_Query_Vars'));
    Add_Filter('init', Array($this, 'Define_Rewrite_Rules'));
    Add_Filter('rewrite_rules_array', Array($this, 'Add_Rewrite_Rules'));
    Add_Action('wp_loaded', Array($this, 'Optionally_Flush_Rewrite_Rules'));
    Add_Action('admin_footer', Array($this, 'Print_Dashboard_JS'));

    # Register Widgets
    Add_Action ('widgets_init', Array($this,'Register_Widgets'));

    # Shortcodes
    Add_Shortcode('encyclopedia_related_terms', Array($this, 'Shortcode_Related_Terms'));

    # Add to GLOBALs
    $GLOBALS[__CLASS__] = $this;
  }

  function Load_Base_Url(){
    $absolute_plugin_folder = RealPath(DirName(__FILE__));

    If (StrPos($absolute_plugin_folder, ABSPATH) === 0)
      $this->base_url = Get_Bloginfo('wpurl').'/'.SubStr($absolute_plugin_folder, Strlen(ABSPATH));
    Else
      $this->base_url = Plugins_Url(BaseName(DirName(__FILE__)));

    $this->base_url = Str_Replace("\\", '/', $this->base_url); # Windows Workaround
  }

  function Load_TextDomain(){
    $locale = Apply_Filters( 'plugin_locale', get_locale(), __CLASS__ );
    Load_TextDomain (__CLASS__, DirName(__FILE__).'/language/' . $locale . '.mo');
  }

  function t ($text, $context = ''){
    # Translates the string $text with context $context
    If ($context == '')
      return Translate ($text, __CLASS__);
    Else
      return Translate_With_GetText_Context ($text, $context, __CLASS__);
  }

  function Plugin_Activation(){
    $this->Load_TextDomain();
    $this->Load_Encyclopedia_Type();
    $this->Register_Post_Type();
    $this->Register_Taxonomies();
    Flush_Rewrite_Rules();
  }

  function Register_Widgets(){
		Register_Widget('wp_widget_encyclopedia_related_terms');
		Register_Widget('wp_widget_encyclopedia_search');
		Register_Widget('wp_widget_encyclopedia_taxonomies');
		Register_Widget('wp_widget_encyclopedia_taxonomy_cloud');
		Register_Widget('wp_widget_encyclopedia_terms');
	}

  function Define_Rewrite_Rules(){
    $post_type = Get_Post_Type_Object($this->post_type);
    $archive_url_path = $post_type->rewrite['slug'];
    $this->rewrite_rules[SPrintF('%s/filter:([^/]+)/?$', $archive_url_path)] = SPrintF('index.php?post_type=%s&filter=$matches[1]', $this->post_type);
    $this->rewrite_rules[SPrintF('%s/filter:([^/]+)/page/([0-9]{1,})/?$', $archive_url_path)] = SPrintF('index.php?post_type=%s&filter=$matches[1]&paged=$matches[2]', $this->post_type);
  }

  function Add_Rewrite_Rules($rules){
    If (Is_Array($this->rewrite_rules) && Is_Array($rules))
      return Array_Merge($this->rewrite_rules, $rules);
    Else
      return $rules;
  }

  function Optionally_Flush_Rewrite_Rules(){
    $rules = Get_Option('rewrite_rules');
    ForEach ($this->rewrite_rules AS $new_rule => $redirect){
      If (!IsSet($rules[$new_rule])){
        Flush_Rewrite_Rules();
        return;
      }
    }
  }

  function Register_Query_Vars($query_vars){
    $query_vars[] = 'filter'; # Will store the the filter of the user search
    return $query_vars;
  }

  function Add_Options_Page(){
    $handle = Add_Options_Page (
      $this->t('Encyclopedia Options'),
      $this->t('Encyclopedia'),
      'manage_options',
      __CLASS__,
      Array($this, 'Print_Options_Page')
    );

    # Add JavaScript to this handle
    Add_Action ('load-' . $handle, Array($this, 'Load_Options_Page'));

    # Add option boxes
    $this->Add_Option_Box(__('General'), DirName(__FILE__).'/options-page/box-general.php');
    $this->Add_Option_Box($this->t('Taxonomies'), DirName(__FILE__).'/options-page/box-taxonomies.php');
    $this->Add_Option_Box($this->t('Archive page'), DirName(__FILE__).'/options-page/box-archive-page.php');
    $this->Add_Option_Box($this->t('Search page'), DirName(__FILE__).'/options-page/box-search.php');
    $this->Add_Option_Box($this->t('Single page'), DirName(__FILE__).'/options-page/box-single-page.php');
    $this->Add_Option_Box($this->t('Linked terms in contents'), DirName(__FILE__).'/options-page/box-linked-terms.php');
    $this->Add_Option_Box($this->t('Archive Url'), DirName(__FILE__).'/options-page/box-archive-link.php', 'side' );
  }

  function Get_Options_Page_Url($parameters = Array()){
    $url = Add_Query_Arg(Array('page' => __CLASS__), Admin_Url('options-general.php'));
    If (Is_Array($parameters) && !Empty($parameters)) $url = Add_Query_Arg($parameters, $url);
    return $url;
  }

  function Load_Options_Page(){
    # If the Request was redirected from a "Save Options"-Post
    If (IsSet($_REQUEST['options_saved'])) Flush_Rewrite_Rules();

    # If this is a Post request to save the options
    If ($this->Save_Options()) WP_Redirect( $this->Get_Options_Page_Url(Array('options_saved' => 'true')));

    WP_Enqueue_Script('dashboard');
    WP_Enqueue_Style('dashboard');

    #WP_Enqueue_Script('options-page', $this->base_url . '/options-page/options-page.js', Array('jquery'), Null, True);
    WP_Enqueue_Style('options-page', $this->base_url . '/options-page/options-page.css');

    # Remove incompatible JS Libs
    WP_Dequeue_Script('post');
  }

  function Print_Options_Page(){
    Include DirName(__FILE__).'/options-page/options-page.php';
  }

  function Add_Option_Box($title, $include_file, $column = 'main', $state = 'opened'){
    # Check the input
    If (!Is_File($include_file)) return False;
    If ( $title == '' ) $title = '&nbsp;';

    # Column (can be 'side' or 'main')
    If ($column != '' && $column != Null && $column != 'main')
      $column = 'side';
    Else
      $column = 'main';

    # State (can be 'opened' or 'closed')
    If ($state != '' && $state != Null && $state != 'opened')
      $state = 'closed';
    Else
      $state = 'opened';

    # Add a new box
    $this->arr_option_box[$column][] = Array('title' => $title, 'file' => $include_file, 'state' => $state);
  }

  function Get_Option($key = Null, $default = False){
    # Read Options
    $arr_option = Array_Merge (
      (Array) $this->Default_Options(),
      (Array) get_option(__CLASS__)
    );

    # Locate the option
    If ($key == Null)
      return $arr_option;
    ElseIf (IsSet($arr_option[$key]))
      return $arr_option[$key];
    Else
      return $default;
  }

  function Save_Options(){
    # Check if this is a post request
    If (Empty($_POST)) return False;

    # Clean the Post array
    $_POST = StripSlashes_Deep($_POST);
    ForEach ($_POST AS $option => $value)
      If (!$value) Unset ($_POST[$option]);

    # Save Options
    Update_Option (__CLASS__, $_POST);

    return True;
  }

  function Default_Options(){
    return Array(
      'embed_default_style' => 'yes',
			'encyclopedia_tags' => 'yes',
      'term_filter_for_archives' => 'yes',
      'term_filter_for_singulars' => 'yes'
    );
  }

  function Load_Encyclopedia_Type(){
		$this->encyclopedia_type = (Object) Array(
      'label' => $this->t('Lexicon'),
      'slug' => $this->t('lexicon', 'URL slug')
    );
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
      'menu_icon' => 'dashicons-welcome-learn-more',
      'has_archive' => True,
			'map_meta_cap' => True,
			'hierarchical' => False,
      'rewrite' => Array(
        'slug' => $this->encyclopedia_type->slug,
        'with_front' => False
      ),
      'supports' => Array( 'title', 'editor', 'author', 'excerpt' ),
      'menu_position' => 20, # below Pages
      #'register_meta_box_cb' => Array($this, 'Add_Meta_Boxes')
    ));
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
        'show_admin_column' => True,
				'hierarchical' => False,
				'show_ui' => True,
				'query_var' => True,
				'rewrite' => Array(
					'with_front' => False,
					'slug' => SPrintF($this->t('%s-tag', 'URL slug'), $this->encyclopedia_type->slug)
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
        <code><a href="<?php Echo $archive_url ?>" target="_blank"><?php Echo $archive_url ?></a></code><br>
        <span class="description"><?php PrintF($this->t('This is the URL to the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>

    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo $this->t('Archive Feed') ?></th>
      <td>
        <code><a href="<?php Echo $archive_feed ?>" target="_blank"><?php Echo $archive_feed ?></a></code><br>
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
		If ($query->is_post_type_archive || $query->is_tax){
      $encyclopedia_taxonomies = Get_Object_Taxonomies($this->post_type);
			If ($query->Is_Post_Type_Archive($this->post_type) || (!Empty($encyclopedia_taxonomies) && $query->is_tax($encyclopedia_taxonomies))){
				return True;
			}
		}
		return False;
	}

  function Filter_Query($query){
		If ($this->Is_Encyclopedia_Archive($query) && !$query->Get('suppress_filters')){
      # Define new Query Arguments
      If (!$query->Get('order')) $query->Set('order', 'asc');
      If (!$query->Get('orderby')) $query->Set('orderby', 'title');

      # Take an eye on the filter
      If (!$query->Get('post_title_like') && !$query->Get('ignore_filter_request') && Get_Query_Var('filter'))
        $query->Set('post_title_like', RawUrlDecode(Get_Query_Var('filter')) . '%');
		}
	}

	function Filter_Posts_Where($where, $query){
		Global $wpdb;
		$post_title_like = $query->get('post_title_like');

		If (!Empty($post_title_like))
      return SPrintF('%s AND %s LIKE "%s" ', $where, $wpdb->posts.'.post_title', Esc_SQL($post_title_like));
		Else
			return $where;
	}

	function Filter_Content($content){
		Global $post;
		If ($post->post_type == $this->post_type && Is_Single($post->ID)){
      If (	StrPos($content, '[encyclopedia_related_terms]') === False && # Avoid double inclusion of the ShortCode
            StrPos($content, '[encyclopedia_related_terms ') === False && # Without closing bracket to find ShortCodes with attributes
            $this->Get_Option('related_terms') != 'none' && # User can disable the auto append feature
            !post_password_required() # The user isn't allowed to read this post
      ){
        $attributes = Array( 'max_terms' => $this->Get_Option('number_of_related_terms') );

        If ($this->Get_Option('related_terms') == 'above')
          $content = $this->Shortcode_Related_Terms($attributes) . $content;
        Else
          $content .= $this->Shortcode_Related_Terms($attributes);

      }
		}

    return $content;
	}

  function Link_Terms($content){
    Global $post;

    $arr_terms = New WP_Query(Array(
      'posts_per_page' => -1,
      'post_type' => $this->post_type,
      'post__not_in' => Array($post->ID),
      'ignore_filter_request' => True,
      'ignore_sticky_posts' => True
    ));

    ForEach($arr_terms->posts AS $term){
      $content = $this->Link_Term_in_Content($content, $term);
    }

    return $content;
	}

  function Start_Loop($query){
    Static $loop_already_started;
		If ($loop_already_started) return;

    # If the current query is not a post query we bail out
    If (!(GetType($query) == 'object' && Get_Class($query) == 'WP_Query')) return;

		Global $wp_current_filter;
    If (In_Array('wp_head', $wp_current_filter)) return;

    # Conditions
    If ($query->Is_Main_Query() && !$query->get('suppress_filters')){
      $is_archive_filter = $this->Is_Encyclopedia_Archive($query) && $this->Get_Option('term_filter_for_archives') == 'yes';
      $is_singular_filter = $query->Is_Singular($this->post_type) && $this->Get_Option('term_filter_for_singulars') == 'yes';

      If ($is_archive_filter || $is_singular_filter){
        $this->Print_Term_Filter();
        $loop_already_started = True;
      }
    }
  }

  function Print_Term_Filter(){
    Echo $this->Load_Template('encyclopedia-term-filter.php', Array('filter' => $this->Generate_Term_Filters()));
  }


  function Link_Term_in_Content($content, $term){
    Global $post;

    # Prepare search term
    $term_value = Trim($term->post_title);
    $term_value = Strip_Tags($term_value);
    $term_value = HTMLSpecialChars($term_value);

    $content = Trim($content);

    # Check if the content and term are not empty
    If (Empty($content) || Empty($term_value)) return $content;

    # Get Term Title
    If (Empty($term->post_excerpt))
      $link_title = WP_Trim_Words($term->post_content, 55, '...');
    Else
      $link_title = WP_Strip_All_Tags($term->post_excerpt, True);
    $link_title = Apply_Filters('encyclopedia_term_link_title', $link_title, $term);

    # Prepare search
    $search = SPrintF('|\b(%s)|imsuU', PReg_Quote($term_value));
    $link = SPrintF('<a href="%1$s" target="_self" title="%2$s" class="encyclopedia">$1</a>', Get_Permalink($term->ID), $link_title);

    # Load DOM
    $encoded_content = MB_Convert_Encoding($content, 'HTML-ENTITIES', 'UTF-8');
    $dom = new DOMDocument();
    @$dom->loadHTML($encoded_content); # Here we could get a Warning if the $content is not valid HTML
    $xpath = new DOMXPath($dom);

    # Go through nodes and replace
    ForEach($xpath->Query('//text()[not(ancestor::a)][not(ancestor::script)][not(ancestor::iframe)]') As $currentNode){
      $currentText = HTMLSpecialChars($currentNode->wholeText);
      $newText = PReg_Replace($search, $link, $currentText);
      If ($newText != $currentText){
        $newNode = $dom->createDocumentFragment();
        @$newNode->appendXML($newText);
        $currentNode->parentNode->replaceChild($newNode, $currentNode);
      }
    }

    $resultHTML = $dom->saveHTML();
    $body_start = MB_StrPos($resultHTML, '<body>', 0, 'UTF-8') + 6;
    $body_end = MB_StrPos($resultHTML, '</body>', $body_start, 'UTF-8');
    $resultBody = MB_SubStr($resultHTML, $body_start, $body_end - $body_start);
    return $resultBody;
  }

  function Change_Taxonomy_Menu_Label($tax){
    If (IsSet($tax->object_type) && $tax->object_type == Array($this->post_type)){
      $tax->labels->name = SPrintF('%1$s &raquo; %2$s', $this->encyclopedia_type->label, $tax->labels->name);
    }
    return $tax;
  }

  function Generate_Term_Filters(){
    # Get current Filter string
    $filter = RawUrlDecode(Get_Query_Var('filter'));
    If (!Empty($filter))
      $str_filter = $filter;
    ElseIf (Is_Singular())
      $str_filter = StrToLower(Get_The_Title());
    Else
      $str_filter = '';

    # Explode Filter string
    $arr_current_filter = (!Empty($str_filter)) ? PReg_Split('/(?<!^)(?!$)/u', $str_filter) : Array();
    Array_UnShift($arr_current_filter, '');

		$arr_filter = Array(); # This will be the function result
    $filter_part = '';

		ForEach ($arr_current_filter AS $filter_letter){
			$filter_part .= $filter_letter;
			$arr_available_filters = $this->Get_Available_Filters($filter_part);
			If (Count($arr_available_filters) < 2) Break;
			$active_filter_part = MB_SubStr(Implode($arr_current_filter), 0, MB_StrLen($filter_part) + 1);

			$arr_filter_line = Array();
			ForEach ($arr_available_filters AS $available_filter){
				$filter = New StdClass;
        $filter->filter = MB_StrToUpper(MB_SubStr($available_filter, 0, 1)) . MB_SubStr($available_filter, 1); # UCFirst Workaround for multibyte chars
				$filter->link = $this->Get_Archive_Link($available_filter);
				$filter->active = ($active_filter_part == $available_filter);
				$arr_filter_line[] = $filter;
			}
			$arr_filter[] = $arr_filter_line;
		}

		return $arr_filter;
	}

  function Get_Archive_Link($filter = ''){
    $permalink_structure = Get_Option('permalink_structure');
    If (!Empty($permalink_structure))
      return User_TrailingSlashIt(SPrintF('%1$s/filter:%2$s', RTrim(Get_Post_Type_Archive_Link($this->post_type), '/'), RawURLEncode($filter)));
    Else
      return Add_Query_Arg(Array('filter' => RawURLEncode($filter)), Get_Post_Type_Archive_Link($this->post_type));
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
    $prefix_length = MB_StrLen($prefix) + 1;

    $stmt = "SELECT   LOWER(SUBSTRING(post_title,1,{$prefix_length})) subword
             FROM     {$wpdb->posts} AS posts
             WHERE    posts.post_status = 'publish' AND
                      posts.post_type = '{$this->post_type}' AND
                      posts.post_title != '' AND
                      posts.post_title LIKE '{$prefix}%'
             GROUP BY subword
             ORDER BY subword ASC";
    $arr_filter = $wpdb->Get_Col($stmt);
    return $arr_filter;
	}

  function Get_Tag_Related_Terms($term_id = Null, $number = 10){
    Global $wpdb, $post;

    If ($term_id == Null) $term_id = $post->ID;

    # Get the Tags
    $arr_tags = WP_Get_Post_Terms($term_id, 'encyclopedia-tag');
    If(Empty($arr_tags)) return False;

    # Get term IDs
    $arr_term_ids = Array();
    ForEach( $arr_tags as $taxonomy )
      $arr_term_ids[] = $taxonomy->term_taxonomy_id;
    $str_tag_list = Implode(',', $arr_term_ids);

    # The Query to get the related posts
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

    $arr_related_term_ids = $wpdb->Get_Col($stmt);

    # Put it in a WP_Query
    $query = New WP_Query(Array(
      'post_type' => $this->post_type,
      'post__in' => $arr_related_term_ids,
      'orderby' => 'post__in',
      'ignore_sticky_posts' => True
    ));

    # return
    If ($query->post_count == 0) return False;
    Else return $query;
  }

	function Shortcode_Related_Terms($attributes = False){
    $attributes = Array_Merge(Array(
      'max_terms' => 10
    ), (Array) $attributes);
		return $this->Load_Template('encyclopedia-related-terms.php', Array('attributes' => $attributes));
	}

  function Pro_Notice($message = 'feature', $output = True){
    $arr_message = Array(
      'upgrade' => $this->t('Upgrade to Pro'),
      'upgrade_url' => '%s',
      'feature' => $this->t('Available in the <a href="%s" target="_blank">premium version</a> only.'),
      'custom_tax' => $this->t('Do you need a special taxonomy for your website? No problem! Just <a href="%s" target="_blank">get in touch</a>.'),
      'count_limit' => $this->t('In the <a href="%s" target="_blank">premium version of Encyclopedia</a> you will take advantage of unlimited terms and many more features.'),
      #'changeable' => $this->t('Changeable in the <a href="%s" target="_blank">premium version</a> only.'),
      #'do_you_like' => $this->t('Do you like the term management? Upgrade to the <a href="%s" target="_blank">premium version of Encyclopedia</a>!')
    );

    If (IsSet($arr_message[$message])){
      $message = SPrintF($arr_message[$message], $this->t('http://dennishoppe.de/en/wordpress-plugins/encyclopedia', 'Link to the authors website'));
      If ($output) Echo $message;
      Else return $message;
    }
    Else
      return False;
  }

  function Count_Terms($limit = -1){
    Static $count;
    If ($count) return $count;
    Else return $count = Count(Get_Posts(Array('post_type' => $this->post_type, 'post_status' => 'any', 'numberposts' => $limit)));
  }

  function Check_Term_Count(){
    return $this->Count_Terms(12) < 12;
  }

  function User_Creates_New_Term(){
    If ( BaseName($_SERVER['SCRIPT_NAME']) == 'post-new.php' && IsSet($_GET['post_type']) && $_GET['post_type'] == $this->post_type && !$this->Check_Term_Count() )
      $this->Print_Term_Count_Limit();
  }

  function User_Untrashes_Post($post_id){
    If (Get_Post_Type($post_id) == $this->post_type && !$this->Check_Term_Count()) $this->Print_Term_Count_Limit();
  }

  function Print_Term_Count_Limit(){
    WP_Die(
      SPrintF('<p>%s</p><p>%s</p>',
        $this->Pro_Notice('count_limit', False),
        SPrintF('<a href="%s" class="button">%s</a>', Admin_URL('edit.php?post_type=' . $this->post_type), $this->t('&laquo; Back to your terms'))
      )
    );
  }

  function Print_Dashboard_JS(){
    If (!$this->Check_Term_Count()): ?>
    <script type="text/javascript">
    (function($){
      $('a[href*="post-new.php?post_type=<?php Echo $this->post_type ?>"]')
        .text('<?php $this->Pro_Notice('upgrade') ?>')
        .attr({
          'title': '<?php $this->Pro_Notice('upgrade') ?>',
          'href': '<?php $this->Pro_Notice('upgrade_url') ?>',
          'target': '_blank'
        })
        .css({
          'color': 'green',
          'font-weight': 'bold'
        });
    })(jQuery);
    </script>

    <?php EndIf;
  }

} /* End of the Class */
New wp_plugin_encyclopedia;
} /* End of the If-Class-Exists-Condition */