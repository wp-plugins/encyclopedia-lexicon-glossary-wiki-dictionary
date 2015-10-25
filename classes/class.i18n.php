<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class I18n {
  private static
    $textdomain = __NAMESPACE__,
    $textdomain_loaded = False;

  static function loadTextDomain(){
    $locale = Apply_Filters('plugin_locale', Get_Locale(), self::$textdomain);
    Load_TextDomain (self::$textdomain, SPrintF('%s/languages/%s.mo', Core::$plugin_folder, $locale));
    self::$textdomain_loaded = True;
  }

  static function getTextDomain(){
    return self::$textdomain;
  }

  static function t ($text, $context = Null){
    # Load text domain
    If (!self::$textdomain_loaded) self::loadTextDomain();
    
    # Translates the string $text with context $context
    If (Empty($context))
      return Translate ($text, self::$textdomain);
    Else
      return Translate_With_GetText_Context ($text, $context, self::$textdomain);
  }

}