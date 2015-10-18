<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class Encyclopedia_Type {
  public static
    $type = '';
  
  static function Init(){
    Add_Action('init', Array(__CLASS__, 'loadEncyclopediaType'));
  }    

  static function loadEncyclopediaType(){
		$type = Options::Get('encyclopedia_type');
    $arr_types = self::getEncyclopediaTypes();

		If (IsSet($arr_types[$type]))
      self::$type = $arr_types[$type];
    Else
      self::$type = Reset($arr_types);
	}

  static function getEncyclopediaTypes(){
		# Type definition
		$arr_types = Array(
			'lexicon' => (Object) Array(
				'label' => I18n::t('Lexicon'),
				'slug' => I18n::t('lexicon', 'URL slug')
			)
		);

		# Run filter
		$arr_types = Apply_Filters('encyclopedia_types', $arr_types);

		return $arr_types;
	}

}

Encyclopedia_Type::Init();