<?php

class Helper {
  /*
   * This method can be passed an HTTP GET/POST variable and it will 
   * escape it so it's safe to be used and saved.
   */
  static function FilterHTTPParameter($value) {
    return @eregi_replace('[^0-9A-Za-z]', '', $value);
  }

  /*
   * Returns only digits from a string
   */
  static function ExtractDigits($string) {
    return @eregi_replace('[^0-9]', '', $string);
  }

  /*
   * Returns only letters and numbers from a string
   */
  static function ExtractAlphanumeric($string) {
    return @eregi_replace('[^0-9A-Za-z]', '', $string);
  }

  static function isArrayKeyEmpty(&$array, $key) {
    if(empty($array))
      return true;

    if(!array_key_exists($key, $array))
      return true;

    if(strlen($array[$key]) == 0)
      return true;

    return false;
  }
}

