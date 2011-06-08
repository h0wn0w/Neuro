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
      self::$error_message = 'Internal database error 10400.';
      // We can send the mysql_error() to admin, since its an internal message
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

  // The user of the Account::Login() method can pass the result object back into
  // this method in order to create the session variables. Console and crons would
  // not use this, only web scripts would.
  static function InitializeSession($user_object) {
    $_SESSION['LoggedIn']         = 1;
    $_SESSION['User']             = array();
    $_SESSION['User']['UserID']   = $user_object->GetUserID();
    $_SESSION['User']['Username'] = $user_object->GetUsername();
  }

  static function EncryptPassword($string) {
    return hash(CONFIG_HASH_ALGORITHM, $string);
  }

  static function GetErrorMessage() {
    return self::$error_message;
  }

}