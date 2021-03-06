<?php
require_once("core/config.php");

class Database
{
  private static $conn = NULL;
  private static $host = CONFIG_DATABASE_HOST;
  private static $user = CONFIG_DATABASE_USER;
  private static $pass = CONFIG_DATABASE_PASS;
  private static $name = CONFIG_DATABASE_NAME;

  // Stores the last error message
  private static $errorMessage = '';
  
  static function GetConnection()
  {
    self::$conn = @mysql_connect(self::$host, self::$user, self::$pass);
    if(!self::$conn) {
      self::$errorMessage = mysql_error();
      return null;
    }

    if(!@mysql_select_db(self::$name, self::$conn)) {
      self::$errorMessage = mysql_error();
      return null;
    }

    // Connection successful, let's reset the error flag
    self::$errorMessage = '';

    // Give the user their connection handle
    return self::$conn;
  }

  static function Escape($string) {
    return mysql_real_escape_string($string);
  }

  static function GetErrorMessage() {
    return self::$errorMessage;
  }
}


$DBConnection = Database::GetConnection(); 
if($DBConnection == null) {
  // We can replace this with code to show a system bootup error
  die('Core Error: ' . CONFIG_APPLICATION_NAME . ' failed to connect to database: ' . Database::GetErrorMessage());
}