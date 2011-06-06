<?php
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/valuepair.php');

class User {

  private $UserID;
  private $Username;

  /* 
   * Accepts a row from the Users table and returns back the proper instance
   * of the User class based on permission/access levels
   */
  static function Factory($user_row) {
    return new User($user_row);
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

  function __construct($user_row) {
    $this->UserID   = $user_row['UserID'];
    $this->Username = $user_row['Username'];
  }

  function GetUserID() {
    return $this->UserID;
  }

  function GetUsername() {
    return $this->Username;
  }
}