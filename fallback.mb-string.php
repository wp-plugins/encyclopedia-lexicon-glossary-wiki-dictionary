<?php

If (!Function_Exists('MB_Convert_Encoding')){
  function MB_Convert_Encoding($str, $to_encoding, $from_encoding = Null){
    return IConv($from_encoding, $to_encoding, $str);
  }
}

If (!Function_Exists('MB_StrPos')){
  function MB_StrPos($haystack, $needle, $offset = 0){
    return StrPos($haystack, $needle, $offset);
  }
}

If (!Function_Exists('MB_SubStr')){
  function MB_SubStr($string, $start, $length = Null){
    return SubStr($string, $start, $length);
  }
}

If (!Function_Exists('MB_StrLen')){
  function MB_StrLen($string){
    return StrLen($string);
  }
}

If (!Function_Exists('MB_StrToUpper')){
  function MB_StrToUpper($string){
    return StrToUpper($string);
  }
}