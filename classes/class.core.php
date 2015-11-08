<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Core {
  public static
    $base_url, # url to the plugin directory
    $plugin_file, # the main plugin file
    $plugin_folder; # the path to the folder the plugin files contains

  static function Init($plugin_file){
    self::$plugin_file = $plugin_file;
    self::$plugin_folder = DirName(self::$plugin_file);
    self::loadBaseURL();

    Register_Activation_Hook(self::$plugin_file, Array(__CLASS__, 'installPlugin'));
    Add_Action('loop_start', Array(__CLASS__, 'printPrefixFilter'));
    Add_Filter('the_content', Array(__CLASS__, 'filterContent'));
    Add_Filter('the_content', Array(__CLASS__, 'linkTerms'), 99);
    Add_Filter('bbp_get_forum_content', Array(__CLASS__, 'linkTerms'), 99);
    Add_Filter('bbp_get_topic_content', Array(__CLASS__, 'linkTerms'), 99);
    Add_Filter('bbp_get_reply_content', Array(__CLASS__, 'linkTerms'), 99);
    Add_Action('wp_enqueue_scripts', Array(__CLASS__, 'enqueueScripts'));
  }

  static function loadBaseURL(){
    $absolute_plugin_folder = RealPath(self::$plugin_folder);

    If (StrPos($absolute_plugin_folder, ABSPATH) === 0)
      self::$base_url = Get_Bloginfo('wpurl').'/'.SubStr($absolute_plugin_folder, Strlen(ABSPATH));
    Else
      self::$base_url = Plugins_Url(BaseName(self::$plugin_folder));

    self::$base_url = Str_Replace("\\", '/', self::$base_url); # Windows Workaround
  }

  static function installPlugin(){
    Encyclopedia_Type::loadEncyclopediaType();
    Taxonomies::registerTaxonomies();
    Post_Type::registerPostType();
    Flush_Rewrite_Rules();
  }

  static function enqueueScripts(){
    If (Options::Get('embed_default_style'))
      WP_Enqueue_Style('encyclopedia', self::$base_url.'/assets/css/encyclopedia.css');
  }

  static function isEncyclopediaArchive($query){
		If ($query->is_post_type_archive || $query->is_tax){
      $encyclopedia_taxonomies = Get_Object_Taxonomies(Post_Type::$post_type_name);
			If ($query->Is_Post_Type_Archive(Post_Type::$post_type_name) || (!Empty($encyclopedia_taxonomies) && $query->Is_Tax($encyclopedia_taxonomies))){
				return True;
			}
		}
		return False;
	}
  
  static function isEncyclopediaSearch($query){
    If ($query->is_search){
      # Check post type
			If ($query->Get('post_type') == Post_Type::$post_type_name) return True;
      
      # Check taxonomies
      $encyclopedia_taxonomies = Get_Object_Taxonomies(Post_Type::$post_type_name);
      If (!Empty($encyclopedia_taxonomies) && $query->Is_Tax($encyclopedia_taxonomies)) return True;
    }
    return False;
  }
  
	static function filterContent($content){
		Global $post;
		If ($post->post_type == Post_Type::$post_type_name && Is_Single($post->ID)){
      If (	StrPos($content, '[encyclopedia_related_terms]') === False && # Avoid double inclusion of the ShortCode
            StrPos($content, '[encyclopedia_related_terms ') === False && # Without closing bracket to find ShortCodes with attributes
            Options::Get('related_terms') != 'none' && # User can disable the auto append feature
            !post_password_required() # The user isn't allowed to read this post
      ){
          $content .= Shortcodes::Related_Terms();
      }
		}

    return $content;
	}

  static function linkTerms($content){
    Global $post, $wp_current_filter;

    # If this is for the excerpt we bail out
    If (In_Array('get_the_excerpt', $wp_current_filter) || !Apply_Filters('encyclopedia_link_terms_in_post', True, $post)) return $content;
    
    # Start Cross Linker
    $cross_linker = New Cross_Linker();
    $cross_linker->setSkipElements(Apply_Filters('encyclopedia_cross_linking_skip_elements', Array('a', 'script', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'button', 'textarea', 'select', 'style', 'pre', 'code', 'kbd', 'tt')));
    If (!$cross_linker->loadContent($content)) return $content;

    # Build the Query
    $query_args = Array(
      'post_type' => Post_Type::$post_type_name,
      'post__not_in' => Array($post->ID),
      'ignore_filter_request' => True,
      'nopaging' => True,
      'orderby' => 'post_title_length',
      'order' => 'DESC'
    );

    # Query the terms
    $term_query = New \WP_Query($query_args);

    # Create the links
    While($term_query->Have_Posts()){
      $term = $term_query->Next_Post();
      $cross_linker->linkPhrase($term->post_title, self::getLinkTitle($term), Get_Permalink($term->ID));
    }
    
    # Overwrite the content with the parsers document which contains the links to each term
    $content = $cross_linker->getParserDocument();

    return $content;
	}

  static function getLinkTitle($term){
    If (Empty($term->post_excerpt)){
      $more = Apply_Filters('encyclopedia_link_title_more', '&hellip;');
      $more = HTML_Entity_Decode($more, ENT_QUOTES, 'UTF-8');
      $length = Apply_Filters('encyclopedia_link_title_length', Options::Get('cross_link_title_length'));
      $title = Strip_Shortcodes($term->post_content);
      $title = WP_Strip_All_Tags($title);
      $title = HTML_Entity_Decode($title, ENT_QUOTES, 'UTF-8');
      $title = WP_Trim_Words($title, $length, $more);
    }
    Else {
      $title = WP_Strip_All_Tags($term->post_excerpt, True);
      $title = HTML_Entity_Decode($title, ENT_QUOTES, 'UTF-8');
    }

    $title = Apply_Filters('encyclopedia_term_link_title', $title, $term);

    return $title;
  }

  static function printPrefixFilter($query){
    static $loop_already_started;
    If ($loop_already_started) return;
    
    # If this is feed we bail out
    If (Is_Feed()) return;
    
    # If the current query is not a post query we bail out
    If (!(GetType($query) == 'object' && Get_Class($query) == 'WP_Query')) return;
    
    Global $wp_current_filter;
    If (In_Array('wp_head', $wp_current_filter)) return;
    
    # Conditions
    If ($query->Is_Main_Query() && !$query->Get('suppress_filters')){
      $is_archive_filter = self::isEncyclopediaArchive($query) && Options::Get('prefix_filter_for_archives');
      $is_singular_filter = $query->Is_Singular(Post_Type::$post_type_name) && Options::Get('prefix_filter_for_singulars');

      # Get the Filter depth
      $filter_depth = False;
      If ($is_archive_filter) $filter_depth = Options::Get('prefix_filter_archive_depth');
      ElseIf ($is_singular_filter) $filter_depth = Options::Get('prefix_filter_singular_depth');

      If ($is_archive_filter || $is_singular_filter){
        Prefix_Filter::printFilter($filter_depth);
        $loop_already_started = True;
      }
    }
  }

  static function getTagRelatedTerms($arguments = Null){
    Global $wpdb, $post;

    $arguments = Is_Array($arguments) ? $arguments : Array();

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
    ForEach($arr_tags as $taxonomy) $arr_term_ids[] = $taxonomy->term_taxonomy_id;
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
    $query = New \WP_Query();
    $query->posts = $wpdb->Get_Results($stmt);
    $query->post_count = Count($query->posts);
    $query->Rewind_Posts();

    # return
    If ($query->post_count == 0) return False;
    Else return $query;
  }

}