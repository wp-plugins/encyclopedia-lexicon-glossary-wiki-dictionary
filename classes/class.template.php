<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Template {
  
  static function Init(){
    Add_Filter('search_template', Array(__CLASS__, 'changeSearchTemplate'));
  }

  static function changeSearchTemplate($template){
    Global $wp_query;

    If (Core::isEncyclopediaSearch($wp_query) && $search_template = Locate_Template(SPrintF('search-%s.php', Post_Type::$post_type_name)))
      return $search_template;
    Else
      return $template;
  }
  
  static function load($template_name, $vars = Array()){
		Extract($vars);
		$template_path = Locate_Template($template_name);
		Ob_Start();
		If(!Empty($template_path)) Include $template_path;
		Else Include SPrintF('%s/templates/%s', Core::$plugin_folder, $template_name);
		return Ob_Get_Clean();
  }

}

Template::Init();