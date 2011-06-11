<?php
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/valuepair.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/account.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/database.php');

class User {

  // Direct database mappings
  private $UserID;
  private $Username;

  // Member variables
  private static $error_message;

  function __construct($user_row) {
    $this->UserID   = $user_row['UserID'];
    $this->Username = $user_row['Username'];
  }

  static function GetErrorMessage() {
    return self::$error_message;
  }

  // ********************************************************************************
  // ** Get properties of this User
  // ********************************************************************************
  function GetUserID() {
    return $this->UserID;
  }

  function GetUsername() {
    return $this->Username;
  }

  // ********************************************************************************
  // ** Method for creating an instance of the File class
  // ********************************************************************************

  /* 
   * Accepts a row from the Users table and returns back the proper instance
   * of the User class based on permission/access levels
   */
  static function ModelFactory($user_row) {
    return new User($user_row);
  }

  // ********************************************************************************
  // ** New user validation and inserting
  // ********************************************************************************

  /*
   * This will handle the creation validation of a new User
   */
  static function Create($username, $password) {
    $DB = Database::GetConnection();

    $username = Database::Escape($username);
    $password = Database::Escape($password);

    // One-way encode the password before it's saved into the database
    $password_encrypted = Account::EncryptPassword($password);

    // Check to make sure this username isn't taken
    $check_user_query = "SELECT UserID
                         FROM   Users
                         WHERE  Username LIKE '{$username}'
                         LIMIT  1";
    $result = mysql_query($check_user_query, $DB);

    if(mysql_num_rows($result) > 0) {
      self::$error_message = 'This username is already taken.';
      return null;
    }

    // Now insert
    $insert_query = "INSERT INTO Users
                     SET Username = '{$username}'
                       , Password = '{$password_encrypted}'";
    $result = mysql_query($insert_query, $DB);

    if(!$result) {
      // Core Inserting Error: Later this can send an admin notification about a DB issue, to notify Neuro devs
      // Admin message: mysql_error(); 
      self::$error_message = 'Could not add you to the database. Internal error 10500.';
      return null;
    }

    if(mysql_insert_id($DB) == 0) {
      // Core 'Insert Not Registered' Error: Later this can send an admin notification about a DB issue, to notify Neuro devs
      self::$error_message = 'Could not add you to the database. Internal error 10501.';
      return null;
    }

    return true;
  }

  /*
   * This class contains static members that are used to perform the validation
   * logic.
   */
  static function CheckValidUsername($username) {
    if(empty($username))
      return new ValuePair(false, 'Empty username');

    if(strlen($username) > 16)
      return new ValuePair(false, 'Username must be 16 characters or less');

    if($username != @eregi_replace('[^0-9A-Za-z]', '', $username)) {
      return new ValuePair(false, 'Username contains invalid characters (only 0-9 and A-Z)');
    }

    return new ValuePair(true);
  }

  static function CheckValidPassword($password) {
    if(empty($password))
      return new ValuePair(false, 'Empty password');

    if(strlen($password) > 24)
      return new ValuePair(false, 'Password must be less than 24 characters');

    return new ValuePair(true);
  }

  // ********************************************************************************
  // ** Find Users / User Searches
  // ********************************************************************************
  static function FindByID($id) {
    $DB = Database::GetConnection();

    $id = (int) $id;

    $find_query = "SELECT UserID,
                          Username
                   FROM   Users
                   WHERE  UserID = {$id} ";
    $result = mysql_query($find_query, $DB);

    if(mysql_num_rows($result) < 1) {
      self::$error_message = 'User Not Found';
      return null;
    }

    $user_data   = mysql_fetch_assoc($result);
    $user_object = User::ModelFactory($user_data);

    return $user_object;
  }
}