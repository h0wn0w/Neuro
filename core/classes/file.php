<?php

require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/helper.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/database.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/user.php');

class File {

  // Direct database mappings
  private $FileID;
  private $UserID;   // UserID of Owner
  private $Filename;
  private $Filesize; // Bytes

  // Singletons
  private $user_object; 

  // Member variables
  private static $error_message;

  static function Factory($file_row) {
    return new File($file_row);
  }

  function __construct($file_row) {
    $this->FileID   = $file_row['FileID'];
    $this->UserID   = $file_row['UserID'];
    $this->Filename = $file_row['Filename'];
    $this->Filesize = $file_row['Filesize'];
  }

  static function GetErrorMessage() {
    return self::$error_message;
  }

  function GetFileID() {
    return $this->FileID;
  }

  function GetUserID() {
    return $this->UserID;
  }

  /*
   * Returns a User instance of the owner of this file
   */
  function GetUserObject() {
    // If the user object was already retrieved, don't go to the database again
    if($this->user_object != null) {
      return $this->user_object;
    }

    // Let's grab a User object for the owner of this file
    $this->user_object = User::FindByID($this->GetUserID());
    
    return $this->user_object;
  }

  function GetFilename() {
    return $this->Filename;
  }

  function GetFilesize() {
    return $this->Filesize;
  }

  /*
   * This is used to find a file based on a web request. We don't rely on the $id
   * parameter alone on a web request because a user could scan all files by adding
   * one to the id. A hash is also passed, which is the first 6 characters of the
   * hash of the filename
   */
  static function FindByIDAndHash($id, $hash) {
    $DB = Database::GetConnection();

    $id = (int) $id;
    $hash = Helper::ExtractAlphanumeric($hash);
    $hash = Database::Escape($hash);

    $find_query = "SELECT FileID,
                          UserID,
                          Filename,
                          Filesize 
                   FROM   Files
                   WHERE  FileID = {$id} ";
    $result = mysql_query($find_query);

    if(mysql_num_rows($result) < 1) {
      // No match
      self::$error_message = 'File Not Found';
      return null;
    }

    // We have a match. Let's return a File object
    $file_data   = mysql_fetch_assoc($result);

    // Let's analyze the hashes to see if the request is authorized
    $computed_hash = hash(CONFIG_HASH_ALGORITHM, $file_data['Filename']);
    $computed_hash = substr($computed_hash, 0, 6);

    if($hash != $computed_hash) {
      // Show the same error message as when a file doesn't exist so a malicious user can't
      // guess random ID numbers to see if a file with this ID exists.
      self::$error_message = 'File Not Found';
      return null;
    }

    $file_object = File::Factory($file_data);

    return $file_object;
  }

}

