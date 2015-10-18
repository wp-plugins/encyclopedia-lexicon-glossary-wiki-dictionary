<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class WP_Query_Extensions {

  static function Init(){
    Add_Filter('query_vars', Array(__CLASS__, 'registerQueryVars'));
    Add_Filter('pre_get_posts', Array(__CLASS__, 'filterQuery'));
    Add_Filter('posts_where', Array(__CLASS__, 'filterPostsWhere'), 10, 2);
    Add_Filter('posts_fields', Array(__CLASS__, 'filterPostsFields'), 10, 2);
    Add_Filter('posts_orderby', Array(__CLASS__, 'filterPostsOrderBy'), 10, 2);
  }

  static function registerQueryVars($query_vars){
    $query_vars[] = 'filter'; # Will store the the filter of the user search
    return $query_vars;
  }

  static function filterQuery($query){
		If (!$query->Get('suppress_filters') && Core::isEncyclopediaArchive($query) || Core::isEncyclopediaSearch($query)){
      # Order the terms in the backend by title, ASC.
      If (!$query->Get('order')) $query->Set('order', 'asc');
      If (!$query->Get('orderby')) $query->Set('orderby', 'title');

      # Take an eye on the filter
      If (!$query->Get('post_title_like') && !$query->Get('ignore_filter_request') && Get_Query_Var('filter'))
        $query->Set('post_title_like', RawUrlDecode(Get_Query_Var('filter')) . '%');

      # Change the number of terms per page
      If (!Is_Admin()) $query->Set('posts_per_page', Options::Get('terms_per_page'));
		}
	}

	static function filterPostsWhere($where, $query){
		Global $wpdb;
		$post_title_like = $query->Get('post_title_like');

		If (!Empty($post_title_like))
      return SPrintF('%s AND %s LIKE "%s" ', $where, $wpdb->posts.'.post_title', Esc_SQL($post_title_like));
		Else
			return $where;
	}

  static function filterPostsFields($fields, $query){
    Global $wpdb;

    If ($query->Get('orderby') == 'post_title_length')
      $fields .= ", LENGTH({$wpdb->posts}.post_title) post_title_length";

    return $fields;
  }

  static function filterPostsOrderBy($orderby, $query){
    If ($query->Get('orderby') == 'post_title_length')
      $orderby = SPrintF('post_title_length %s', $query->Get('order'));

    return $orderby;
  }
  
}

WP_Query_Extensions::Init();