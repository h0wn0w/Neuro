<?php

class Helper {
  /*
   * This method can be passed an HTTP GET/POST variable and it will 
   * escape it so it's safe to be used and saved.
   */
  static function FilterHTTPParameter($value) {
    return @eregi_replace('[^0-9A-Za-z]', '', $value);
  }
}

