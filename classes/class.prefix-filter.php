<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Prefix_Filter {

  static function generate($depth = False){
    # Get current Filter string
    $filter = RawUrlDecode(Get_Query_Var('filter'));
    If (!Empty($filter))
      $str_filter = $filter;
    ElseIf (Is_Singular())
      $str_filter = StrToLower(Get_The_Title());
    Else
      $str_filter = '';

    # Explode Filter string
    $arr_current_filter = (Empty($str_filter)) ? Array() : PReg_Split('/(?<!^)(?!$)/u', $str_filter);
    Array_UnShift($arr_current_filter, '');

		$arr_filter = Array(); # This will be the function result
    $filter_part = '';

    # Check if we are inside a taxonomy archive
    $taxonomy_term = Is_Tax() ? Get_Queried_Object() : Null;

		ForEach ($arr_current_filter AS $filter_letter){
			$filter_part .= $filter_letter;
			$arr_available_filters = self::getFilters($filter_part, $taxonomy_term);
			If (Count($arr_available_filters) <= 1) Break;
			$active_filter_part = MB_SubStr(Implode($arr_current_filter), 0, MB_StrLen($filter_part) + 1);

			$arr_filter_line = Array();
			ForEach ($arr_available_filters AS $available_filter){
				$arr_filter_line[$available_filter] = (Object) Array(
          'filter' => MB_StrToUpper(MB_SubStr($available_filter, 0, 1)) . MB_SubStr($available_filter, 1), # UCFirst Workaround for multibyte chars
          'link' => Post_Type::getArchiveLink($available_filter, $taxonomy_term),
          'active' => $active_filter_part == $available_filter,
          'disabled' => False
        );
			}
			$arr_filter[] = $arr_filter_line;

      # Check filter depth limit
      If ($depth && Count($arr_filter) >= $depth) Break;
		}

    # Run a filter
    $arr_filter = Apply_Filters('encyclopedia_prefix_filter_links', $arr_filter, $depth);

		return $arr_filter;
	}

  static function getFilters($prefix = '', $taxonomy_term = Null){
    Global $wpdb;
    $prefix_length = MB_StrLen($prefix) + 1;
    $tables = Array($wpdb->posts.' AS posts');
    $where = Array(
      'posts.post_status  =     "publish"',
      'posts.post_type    =     "'.Post_Type::$post_type_name.'"',
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
    $arr_filter = Apply_Filters('encyclopedia_available_prefix_filters', $arr_filter, $prefix, $taxonomy_term);
    return $arr_filter;
	}

  static function printFilter($filter_depth = False){
    $prefix_filter = self::generate($filter_depth);

    If (!Empty($prefix_filter))
      Echo Template::load('encyclopedia-prefix-filter.php', Array('filter' => $prefix_filter));
    Else
      return False;
  }

}