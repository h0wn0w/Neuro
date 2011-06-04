<?php
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/database.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/user.php');

class Account {

  // Stores the last error message
  private static $error_message = '';

  static function Login($username, $password) {
    $DB = Database::GetConnection();

    $username = Database::Escape($username);
    $password = Database::Escape($password);

    // One-way encode the password before it's saved into the database
    $password_encrypted = self::EncryptPassword($password);

    $query = "SELECT UserID, Username 
              FROM   Users
              WHERE  Username = '{$username}'
              AND    Password = '{$password_encrypted}' ";

    $result = mysql_query($query, $DB);

    if(!$result) {
      self::$error_message = mysql_error();
      return null;
    }

    if(mysql_num_rows($result) == 0) {
      self::$error_message = 'Invalid username or password.';
      return null;
    }

    $user_data   = mysql_fetch_assoc($result);
    $user_object = User::Factory($user_data);

    return $user_object;
  }

  static function EncryptPassword($string) {
    return hash(CONFIG_HASH_ALGORITHM, $string);
  }

  static function GetErrorMessage() {
    return self::$error_message;
  }

}