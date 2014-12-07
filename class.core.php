<?php

# Load Plugin Kernel
class wp_plugin_encyclopedia {
  public
    $base_url, # url to the plugin directory
    $arr_taxonomies, # All buildIn Taxonomies - also the inactive ones.
    $post_type = 'encyclopedia', # Name of the post type
    $encyclopedia_type, # An object with the properties of current encyclopedia type
    $rewrite_rules = Array(), # Array with the new additional rewrite rules
    $i18n, # Pointer to the translation helper object
    $wpml; # Pointer to the WPML helper object

  function __construct($plugin_file){
    # Read base
    $this->Load_Base_Url();

    # Load helper objects
    $this->i18n = New WordPress\Plugin\Encyclopedia\I18n();
    $this->wpml = New WordPress\Plugin\Encyclopedia\WPML($this);

    # Option boxes
    $this->arr_option_box = Array( 'main' => Array(), 'side' => Array() );

    # Set Hooks
    Register_Activation_Hook($plugin_file, Array($this, 'Plugin_Activation'));
    Add_Action('init', Array($this, 'Load_Encyclopedia_Type'));
    Add_Action('admin_menu', Array($this, 'Add_Options_Page'));
    Add_Action('init', Array($this, 'Register_Post_Type'));
    Add_Filter('post_updated_messages', Array($this, 'Updated_Messages' ));
    Add_Action('init', Array($this, 'Register_Taxonomies'));
    Add_Action('init', Array($this, 'Add_Taxonomy_Archive_Urls'), 99);
    Add_Action('loop_start', Array($this, 'Start_Loop'));
    Add_Filter('pre_get_posts', Array($this, 'Filter_Query'));
    Add_Filter('posts_where', Array($this, 'Filter_Posts_Where'), 10, 2);
    Add_Filter('posts_fields', Array($this, 'Filter_Posts_Fields'), 10, 2);
    Add_Filter('posts_orderby', Array($this, 'Filter_Posts_OrderBy'), 10, 2);
    Add_Filter('the_content', Array($this, 'Filter_Content'));
    Add_Filter('the_content', Array($this, 'Link_Terms'), 99);
    Add_Filter('nav_menu_meta_box_object', Array($this, 'Change_Taxonomy_Menu_Label'));
    Add_Filter('query_vars', Array($this, 'Register_Query_Vars'));
    Add_Filter('init', Array($this, 'Define_Rewrite_Rules'), 99);
    Add_Filter('rewrite_rules_array', Array($this, 'Add_Rewrite_Rules'));
    Add_Action('wp_loaded', Array($this, 'Optionally_Flush_Rewrite_Rules'));
    Add_Action('wp_enqueue_scripts', Array($this, 'Enqueue_Encyclopedia_Scripts'));
    Add_Action('admin_init', Array($this, 'User_Creates_New_Term'));
    Add_Action('untrash_post', Array($this, 'User_Untrashes_Post'));
    Add_Action('admin_footer', Array($this, 'Print_Dashboard_JS'));
    Add_Action('admin_bar_menu', Array($this, 'Filter_Admin_Bar_Menu'), 999);

    # Register Widgets
    Add_Action('widgets_init', Array($this, 'Register_Widgets'));

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

  function t ($text, $context = Null){
    return $this->i18n->t($text, $context);
  }

  function Plugin_Activation(){
    $this->i18n->Load_TextDomain();
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
    # Add filter permalink structure for post type archive
    $post_type = Get_Post_Type_Object($this->post_type);
    $archive_url_path = $post_type->rewrite['slug'];
    $this->rewrite_rules[SPrintF('%s/filter:([^/]+)/?$', $archive_url_path)] = SPrintF('index.php?post_type=%s&filter=$matches[1]', $this->post_type);
    $this->rewrite_rules[SPrintF('%s/filter:([^/]+)/page/([0-9]{1,})/?$', $archive_url_path)] = SPrintF('index.php?post_type=%s&filter=$matches[1]&paged=$matches[2]', $this->post_type);

    # Add filter permalink structure for taxonomy archives
    ForEach (Get_Taxonomies(Null, 'objects') As $taxonomy){
      $taxonomy_slug = $taxonomy->rewrite['slug'];
      If (!In_Array($this->post_type, $taxonomy->object_type)) Continue;
      $this->rewrite_rules[SPrintF('%s/([^/]+)/filter:([^/]+)/?$', $taxonomy_slug)] = SPrintF('index.php?%s=$matches[1]&filter=$matches[2]', $taxonomy->name);
      $this->rewrite_rules[SPrintF('%s/([^/]+)/filter:([^/]+)/page/([0-9]{1,})/?$', $taxonomy_slug)] = SPrintF('index.php?%s=$matches[1]&filter=$matches[2]&paged=$matches[3]', $taxonomy->name);
    }
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
    $this->Add_Option_Box($this->t('Search'), DirName(__FILE__).'/options-page/box-search.php');
    $this->Add_Option_Box($this->t('Single page'), DirName(__FILE__).'/options-page/box-single-page.php');
    $this->Add_Option_Box($this->t('Linked terms in contents'), DirName(__FILE__).'/options-page/box-linked-terms.php');
    $this->Add_Option_Box($this->t('Archive Url'), DirName(__FILE__).'/options-page/box-archive-link.php', 'side');
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
    If ($this->Save_Options()) WP_Redirect($this->Get_Options_Page_Url(Array('options_saved' => 'true')));

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
    If (Empty($title)) $title = '&nbsp;';

    # Column (can be 'side' or 'main')
    If ($column != 'main')
      $column = 'side';
    Else
      $column = 'main';

    # State (can be 'opened' or 'closed')
    If ($state != 'opened')
      $state = 'closed';
    Else
      $state = 'opened';

    # Add a new box
    $this->arr_option_box[$column][] = (Object) Array(
      'title' => $title,
      'file' => $include_file,
      'state' => $state
    );
  }

  function Get_Option($key = Null, $default = False){
    # Read Options
    $arr_option = Array_Merge (
      (Array) $this->Default_Options(),
      (Array) Get_Option(__CLASS__)
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
      'prefix_filter_for_archives' => 'yes',
      'prefix_filter_archive_depth' => 3,
      'prefix_filter_for_singulars' => 'yes',
      'prefix_filter_singular_depth' => 3,
      'auto_link_title_length' => Apply_Filters('excerpt_length', 55)
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
      1 => SPrintF ($this->t('Term updated. (<a href="%s">View Term</a>)'), Get_Permalink()),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => $this->t('Term updated.'),
      5 => IsSet($_GET['revision']) ? SPrintF($this->t('Term restored to revision from %s'), WP_Post_Revision_Title( (Int) $_GET['revision'], False ) ) : False,
      6 => SPrintF($this->t('Term published. (<a href="%s">View Term</a>)'), Get_Permalink()),
      7 => $this->t('Term saved.'),
      8 => $this->t('Term submitted.'),
      9 => SPrintF($this->t('Term scheduled. (<a target="_blank" href="%s">View Term</a>)'), Get_Permalink()),
      10 => SPrintF($this->t('Draft updated. (<a target="_blank" href="%s">Preview Term</a>)'), Add_Query_Arg('preview', 'true', Get_Permalink()))
    )));
  }

  function Register_Taxonomies(){
    If($this->Get_Option('encyclopedia_tags') == 'yes'){
			Register_Taxonomy('encyclopedia-tag', $this->post_type, Array(
				'label' => $this->t('Encyclopedia Tags'),
				'labels' => Array(
					'name' => $this->t('Tags'),
					'singular_name' => $this->t('Tag'),
					'search_items' =>  $this->t('Search Tags'),
					'all_items' => $this->t('All Tags'),
					'edit_item' => $this->t('Edit Tag'),
					'update_item' => $this->t('Update Tag'),
					'add_new_item' => $this->t('Add New Tag'),
					'new_item_name' => $this->t('New Tag')
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

  function Enqueue_Encyclopedia_Scripts(){
    If ($this->Get_Option('embed_default_style') == 'yes')
      WP_Enqueue_Style('encyclopedia', $this->base_url.'/encyclopedia.css');
  }

  function Is_Encyclopedia_Archive($query){
		If ($query->is_post_type_archive || $query->is_tax){
      $encyclopedia_taxonomies = Get_Object_Taxonomies($this->post_type);
			If ($query->Is_Post_Type_Archive($this->post_type) || (!Empty($encyclopedia_taxonomies) && $query->Is_Tax($encyclopedia_taxonomies))){
				return True;
			}
		}
		return False;
	}

  function Filter_Query($query){
		If ($this->Is_Encyclopedia_Archive($query) && !$query->Get('suppress_filters')){
      # Order the terms in the backend by title, ASC.
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

  function Filter_Posts_Fields($fields, $query){
    Global $wpdb;

    If ($query->Get('orderby') == 'post_title_length')
      $fields .= ", LENGTH({$wpdb->posts}.post_title) post_title_length";

    return $fields;
  }

  function Filter_Posts_OrderBy($orderby, $query){
    If ($query->Get('orderby') == 'post_title_length')
      $orderby = SPrintF('post_title_length %s', $query->Get('order'));

    return $orderby;
  }

	function Filter_Content($content){
		Global $post;
		If ($post->post_type == $this->post_type && Is_Single($post->ID)){
      If (	StrPos($content, '[encyclopedia_related_terms]') === False && # Avoid double inclusion of the ShortCode
            StrPos($content, '[encyclopedia_related_terms ') === False && # Without closing bracket to find ShortCodes with attributes
            $this->Get_Option('related_terms') != 'none' && # User can disable the auto append feature
            !post_password_required() # The user isn't allowed to read this post
      ){
          $content .= $this->Shortcode_Related_Terms();
      }
		}

    return $content;
	}

  function Link_Terms($content){
    Global $post, $wp_current_filter;

    # If this is for the excerpt we bail out
    If (In_Array('get_the_excerpt', $wp_current_filter)) return $content;

    $terms_query = New WP_Query(Array(
      'nopaging' => True,
      'post_type' => $this->post_type,
      'post__not_in' => Array($post->ID),
      'ignore_filter_request' => True,
      'orderby' => 'post_title_length',
      'order' => 'DESC'
    ));

    ForEach($terms_query->posts AS $term){
      $content = $this->Link_Term_in_Content($content, $term);
    }

    return $content;
	}

  function Link_Term_in_Content($content, $term){
    Global $post;

    # Prepare search term
    $term_value = Trim($term->post_title);
    $term_value = WPTexturize($term_value); # This is necessary because the content runs through this filter, too
    $term_value = HTML_Entity_Decode($term_value, ENT_QUOTES, 'UTF-8');

    $content = Trim($content);

    # Check if the content and term are not empty
    If (Empty($content) || Empty($term_value)) return $content;

    # Get Term Title
    If (Empty($term->post_excerpt)){
      $link_title_more = Apply_Filters('encyclopedia_link_title_more', '&hellip;');
      $link_title_more = HTML_Entity_Decode($link_title_more, ENT_QUOTES, 'UTF-8');

      $link_title_length = Apply_Filters('excerpt_length', $this->Get_Option('auto_link_title_length'));
      $link_title_length = Apply_Filters('encyclopedia_link_title_length', $link_title_length);

      $link_title = Strip_Shortcodes($term->post_content);
      $link_title = WP_Strip_All_Tags($link_title);
      $link_title = HTML_Entity_Decode($link_title, ENT_QUOTES, 'UTF-8');
      $link_title = WP_Trim_Words($link_title, $link_title_length, $link_title_more);
    }
    Else {
      $link_title = WP_Strip_All_Tags($term->post_excerpt, True);
      $link_title = HTML_Entity_Decode($link_title, ENT_QUOTES, 'UTF-8');
    }

    # Apply link title filter
    $link_title = Apply_Filters('encyclopedia_term_link_title', $link_title, $term);

    # Prepare search
    $search = SPrintF('/(^|\W)(%s)/imsuU', PReg_Quote(HTMLSpecialChars($term_value), '/'));
    $link = SPrintF('$1<a href="%1$s" title="%2$s" class="encyclopedia">$2</a>', Get_Permalink($term->ID), Esc_Attr(HTMLSpecialChars($link_title)));

    # Load DOM
    $encoded_content = MB_Convert_Encoding($content, 'HTML-ENTITIES', 'UTF-8');
    $dom = new DOMDocument();
    If (!@$dom->loadHTML($encoded_content)) return $content; # Here we could get a Warning if the $content is not valid HTML
    $xpath = new DOMXPath($dom);

    # Go through nodes and replace
    $skip_elements = Apply_Filters('encyclopedia_auto_link_skip_elements', Array('a', 'script', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'button', 'textarea', 'style', 'pre', 'code', 'kbd', 'tt'));
    $xpath_query = '//text()';
    ForEach ($skip_elements As $skip_element) $xpath_query .= SPrintF('[not(ancestor::%s)]', $skip_element);
    ForEach($xpath->Query($xpath_query) As $original_node){
      $original_text = HTMLSpecialChars(HTML_Entity_Decode($original_node->wholeText, ENT_QUOTES, 'UTF-8'));
      $new_text = @PReg_Replace($search, $link, $original_text);
      If ($new_text != $original_text){
        $new_node = $dom->createDocumentFragment();
        If (@$new_node->appendXML($new_text)){ # If the $new_text is not valid XML this will break
          $original_node->parentNode->replaceChild($new_node, $original_node);
        }
      }
    }

    $resultHTML = $dom->saveHTML();
    $body_start = MB_StrPos($resultHTML, '<body>', 0, 'UTF-8') + 6;
    $body_end = MB_StrPos($resultHTML, '</body>', $body_start, 'UTF-8');
    $resultBody = MB_SubStr($resultHTML, $body_start, $body_end - $body_start);

    return $resultBody;
  }

  function Start_Loop($query){
    Static $loop_already_started;
		If ($loop_already_started) return;

    # If the current query is not a post query we bail out
    If (!(GetType($query) == 'object' && Get_Class($query) == 'WP_Query')) return;

		Global $wp_current_filter;
    If (In_Array('wp_head', $wp_current_filter)) return;

    # Conditions
    If ($query->Is_Main_Query() && !$query->Get('suppress_filters')){
      $is_archive_filter = $this->Is_Encyclopedia_Archive($query) && $this->Get_Option('prefix_filter_for_archives') == 'yes';
      $is_singular_filter = $query->Is_Singular($this->post_type) && $this->Get_Option('prefix_filter_for_singulars') == 'yes';

      # Get the Filter depth
      $filter_depth = False;
      If ($is_archive_filter) $filter_depth = $this->Get_Option('prefix_filter_archive_depth');
      ElseIf ($is_singular_filter) $filter_depth = $this->Get_Option('prefix_filter_singular_depth');

      If ($is_archive_filter || $is_singular_filter){
        $this->Print_Prefix_Filter($filter_depth);
        $loop_already_started = True;
      }
    }
  }

  function Change_Taxonomy_Menu_Label($tax){
    If (IsSet($tax->object_type) && In_Array($this->post_type, $tax->object_type)){
      $tax->labels->name = SPrintF('%1$s &raquo; %2$s', $this->encyclopedia_type->label, $tax->labels->name);
    }
    return $tax;
  }

  function Generate_Prefix_Filters($depth = False){
    # Get current Filter string
    $filter = RawUrlDecode(Get_Query_Var('filter'));
    If (!Empty($filter))
      $str_filter = $filter;
    ElseIf (Is_Singular())
      $str_filter = StrToLower(Get_The_Title());
    Else
      $str_filter = '';

    # Explode Filter string
    $arr_current_filter = Empty($str_filter) ? Array() : PReg_Split('/(?<!^)(?!$)/u', $str_filter);
    Array_UnShift($arr_current_filter, '');

		$arr_filter = Array(); # This will be the function result
    $filter_part = '';

    # Check if we are inside a taxonomy archive
    $taxonomy_term = Is_Tax() ? Get_Queried_Object() : Null;

		ForEach ($arr_current_filter AS $filter_letter){
			$filter_part .= $filter_letter;
			$arr_available_filters = $this->Get_Available_Filters($filter_part, $taxonomy_term);
			If (Count($arr_available_filters) < 2) Break;
			$active_filter_part = MB_SubStr(Implode($arr_current_filter), 0, MB_StrLen($filter_part) + 1);

			$arr_filter_line = Array();
			ForEach ($arr_available_filters AS $available_filter){
				$arr_filter_line[] = (Object) Array(
          'filter' => MB_StrToUpper(MB_SubStr($available_filter, 0, 1)) . MB_SubStr($available_filter, 1), # UCFirst Workaround for multibyte chars
          'link' => $this->Get_Archive_Link($available_filter, $taxonomy_term),
          'active' => $active_filter_part == $available_filter
        );
			}
			$arr_filter[] = $arr_filter_line;

      # Check filter depth limit
      If ($depth && Count($arr_filter) >= $depth) return $arr_filter;
		}

		return $arr_filter;
	}

  function Get_Archive_Link($filter = '', $taxonomy_term = Null){
    $permalink_structure = Get_Option('permalink_structure');

    # Get base url
    If ($taxonomy_term)
      $base_url = Get_Term_Link($taxonomy_term);
    Else
      $base_url = Get_Post_Type_Archive_Link($this->post_type);

    If (!Empty($permalink_structure))
      return User_TrailingSlashIt(SPrintF('%1$s/filter:%2$s', RTrim($base_url, '/'), RawURLEncode($filter)));
    Else
      return Add_Query_Arg(Array('filter' => RawURLEncode($filter)), $base_url);
  }

  function Print_Prefix_Filter($filter_depth = False){
    Echo $this->Load_Template('encyclopedia-prefix-filter.php', Array(
      'filter' => $this->Generate_Prefix_Filters($filter_depth)
    ));
  }

  function Load_Template($template_name, $vars = Array()){
		Extract($vars);
		$template_path = Locate_Template($template_name);
		Ob_Start();
		If(!Empty($template_path)) Include $template_path;
		Else Include SPrintF('%s/templates/%s', DirName(__FILE__), $template_name);
		return Ob_Get_Clean();
	}

  function Get_Available_Filters($prefix = '', $taxonomy_term = Null){
    Global $wpdb;
    $prefix_length = MB_StrLen($prefix) + 1;
    $tables = Array($wpdb->posts.' AS posts');
    $where = Array(
      'posts.post_status  =     "publish"',
      'posts.post_type    =     "'.$this->post_type.'"',
      'posts.post_title   !=    ""',
      'posts.post_title   LIKE  "'.$prefix.'%"'
    );

    If ($taxonomy_term){
      $tables[] = $wpdb->term_relationships.' AS term_relationships';
      $where[] = 'term_relationships.object_id = posts.id';
      $where[] = 'term_relationships.term_taxonomy_id = '.$taxonomy_term->term_taxonomy_id;
    }

    $stmt = 'SELECT   LOWER(SUBSTRING(posts.post_title,1,'.$prefix_length.')) subword
             FROM     '.Join($tables, ',').'
             WHERE    '.Join($where, ' AND ').'
             GROUP BY subword
             ORDER BY subword ASC';

    $arr_filter = $wpdb->Get_Col($stmt);
    $arr_filter = Apply_Filters('encyclopedia_available_filters', $arr_filter, $prefix, $taxonomy_term);
    return $arr_filter;
	}

  function Get_Tag_Related_Terms($arguments = Array()){
    Global $wpdb, $post;

    # Load default arguments
    $arguments = (Object) Array_Merge(Array(
      'term_id' => $post->ID,
      'number' => 10,
      'taxonomy' => 'encyclopedia-tag'
    ), $arguments);

    # apply filter
    $arguments = Apply_Filters('encyclopedia_tag_related_terms_arguments', $arguments);

    # Get the Tags
    $arr_tags = WP_Get_Post_Terms($arguments->term_id, $arguments->taxonomy);
    If(Empty($arr_tags)) return False;

    # Get term IDs
    $arr_term_ids = Array();
    ForEach( $arr_tags as $taxonomy ) $arr_term_ids[] = $taxonomy->term_taxonomy_id;
    $str_tag_list = Implode(',', $arr_term_ids);

    # The Query to get the related posts
    $stmt = " SELECT posts.*,
                     COUNT(term_relationships.object_id) AS common_tag_count
              FROM   {$wpdb->term_relationships} AS term_relationships,
                     {$wpdb->posts} AS posts
              WHERE  term_relationships.object_id = posts.id
              AND    term_relationships.term_taxonomy_id IN({$str_tag_list})
              AND    posts.id != {$arguments->term_id}
              AND    posts.post_status = 'publish'
              GROUP  BY term_relationships.object_id
              ORDER  BY common_tag_count DESC,
                     posts.post_date_gmt DESC
              LIMIT  0, {$arguments->number}";

    # Put it in a WP_Query
    $query = New WP_Query();
    $query->posts = $wpdb->Get_Results($stmt);
    $query->post_count = Count($query->posts);
    $query->Rewind_Posts();

    # return
    If ($query->post_count == 0) return False;
    Else return $query;
  }

	function Shortcode_Related_Terms($attributes = Array()){
    $attributes = Array_Merge(Array(
      'number' => 10
    ), (Array) $attributes);

    $related_terms = $this->Get_Tag_Related_Terms($attributes);

		return $this->Load_Template('encyclopedia-related-terms.php', Array(
      'attributes' => $attributes,
      'related_terms' => $related_terms
    ));
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

  function Filter_Admin_Bar_Menu($admin_bar){
    If (!$this->Check_Term_Count()) $admin_bar->Remove_Node(SPrintF('new-%s', $this->post_type));
  }

}