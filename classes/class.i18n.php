<?php Namespace WordPress\Plugin\Encyclopedia;

abstract class I18n {
  private static
    $textdomain = False;

  static function loadTextDomain(){
    self::$textdomain = __NAMESPACE__;
    $locale = Apply_Filters('plugin_locale', Get_Locale(), self::$textdomain);
    Load_TextDomain (self::$textdomain, SPrintF('%s/languages/%s.mo', Core::$plugin_folder, $locale));
  }

  static function getTextDomain(){
    return self::$textdomain;
  }

  static function t ($text, $context = Null){
    # Load text domain
    If (!self::$textdomain) self::loadTextDomain();
    
    # Translates the string $text with context $context
    If (Empty($context))
      return Translate ($text, self::$textdomain);
    Else
      return Translate_With_GetText_Context ($text, $context, self::$textdomain);
  }

}