<?php

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