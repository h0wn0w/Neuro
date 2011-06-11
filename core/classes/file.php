<?php

require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/helper.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/valuepair.php');
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

  function __construct($file_row) {
    $this->FileID   = $file_row['FileID'];
    $this->UserID   = $file_row['UserID'];
    $this->Filename = $file_row['Filename'];
    $this->Filesize = $file_row['Filesize'];
  }

  static function GetErrorMessage() {
    return self::$error_message;
  }

  // ********************************************************************************
  // ** Get properties of this File
  // ********************************************************************************
  function GetFileID() {
    return $this->FileID;
  }

  function GetUserID() {
    return $this->UserID;
  }

  function GetFilename() {
    return $this->Filename;
  }

  function GetFilesize() {
    return $this->Filesize;
  }

  /*
   * Returns the absolute path to the file on disk
   */
  function GetPathOnDisk() {
    return CONFIG_NEURO_UPLOAD_DIRECTORY . '/' . $this->FileID;
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

  // ********************************************************************************
  // ** Method for creating an instance of the File class
  // ********************************************************************************

  /* 
   * Accepts a row from the Files table and returns back the proper instance
   * of the File class based on it's type
   */
  static function ModelFactory($file_row) {
    return new File($file_row);
  }

  // ********************************************************************************
  // ** New file/upload handler, validation and inserting
  // ********************************************************************************

  /*
   * This method is passed the $_FILES entry for that specific upload and it will
   * handle it's validation, add it to the database and move it to its proper directory.
   * The caller will receive a new file object if successful.
   */
  static function AcceptUpload($payload) {

    // Integrity checks / Validation
    if(!is_dir(CONFIG_NEURO_UPLOAD_DIRECTORY))
      return new ValuePair(ERROR_CODE_UPLOAD_DIRECTORY_NOT_FOUND, "Internal error: The upload directory (" . CONFIG_NEURO_UPLOAD_DIRECTORY . ") could not be found.");

    if(array_key_exists('error', $payload) && $payload['error'] != 0)
      return new ValuePair(ERROR_CODE_UPLOAD_ARRAY_NAME_EMPTY, "Upload error: The user's browser had an error while uploading.");

    if(Helper::isArrayKeyEmpty($payload, 'name'))
      return new ValuePair(ERROR_CODE_UPLOAD_ARRAY_NAME_EMPTY, "The 'name' value is empty.");

    // Check if the mime 'type' value was provided. Normal format: image/png
    // Needed to bind the file to be of that type and not be extension-dependent.
    if(Helper::isArrayKeyEmpty($payload, 'type'))
      return new ValuePair(ERROR_CODE_UPLOAD_ARRAY_TYPE_EMPTY, 
			   "The mime 'type' value is empty.");

    if(Helper::isArrayKeyEmpty($payload, 'tmp_name'))
      return new ValuePair(ERROR_CODE_UPLOAD_ARRAY_TMP_FILE_NAME_EMPTY, 
			   "The 'tmp_name' value is empty. Cannot find where the temporary upload file has been placed.");

    if(!file_exists($payload['tmp_name']))
      return new ValuePair(ERROR_CODE_UPLOAD_ARRAY_TMP_FILE_NOT_FOUND, 
			   "The temporary file '{$payload['tmp_name']}' does not exist.");

    if(filesize($payload['tmp_name']) <= 0) 
      return new ValuePair(ERROR_CODE_UPLOAD_ARRAY_TMP_FILE_IS_EMPTY,
			   "The temporary file '{$payload['tmp_name']}' is empty. (0 bytes)");

    // Check for the custom supplied 'user_id' parameter
    if(Helper::isArrayKeyEmpty($payload, 'user_id'))
      return new ValuePair(ERROR_CODE_UPLOAD_ARRAY_USER_ID_EMPTY, 
			   "The 'user_id' value is empty.");


    // Have the file inserted into the DB and given a file id, final file name and be
    // placed within a virtual directory
    $response = self::Create($payload['name'], filesize($payload['tmp_name']), $payload['user_id']);

    // Check if it was added into the database successfully
    if($response->one == false) 
      return new ValuePair(false, $response->two);

    // Let's make the file permanent by moving it to its final location
    $file_object    = $response->two;
    $save_file_path = CONFIG_NEURO_UPLOAD_DIRECTORY . '/' . $file_object->GetFileID();

    move_uploaded_file($payload['tmp_name'], $save_file_path);
    
    return new ValuePair(true, $file_object);
  }

  /*
   * This function accepts a payload array 
   */
  static function Create($filename, $filesize, $user_id) {
    $DB = Database::GetConnection();

    $filename = Database::Escape($filename);
    $filesize = Database::Escape($filesize); // Would like to force-cast to an integer will that mean it loses big-file support...?
    $user_id  = (int) $user_id;

    // TODO: Duplicate filename check

    // Insert the file into the database
    $query = "INSERT INTO Files
              SET Filename = '{$filename}',
                  Filesize = '{$filesize}',
                  UserID   = '{$user_id}',
                  DateTimeAdded = NOW() ";   // Would like to move DateTimeAdded = NOW() into MySQL
    $result = mysql_query($query, $DB);

    $insert_id = mysql_insert_id($DB);
    if($insert_id <= 0) 
      return new ValuePair(false, "Internal error: Insert ID was <= 0 (actual: {$insert_id}).");

    // From here on we cann assume the file was succesfully added. 
    // Lets get a copy back from the database and return the user
    // a full ready-made instance of the newly created File
    $find_query = "SELECT *
                   FROM   Files
                   WHERE  FileID = {$insert_id} ";
    $result = mysql_query($find_query, $DB);

    $file_data = mysql_fetch_assoc($result);
    $file_object = File::ModelFactory($file_data);

    return new ValuePair(true, $file_object);
  }

  // ********************************************************************************
  // ** Helpers
  // ********************************************************************************
  static function ComputeFilenameHash($filename) {
    $computed_hash = hash(CONFIG_HASH_ALGORITHM, $filename);
    $computed_hash = substr($computed_hash, 0, 6);
    return $computed_hash;
  }

  // ********************************************************************************
  // ** Find Files / File Searches
  // ********************************************************************************

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

    $find_query = "SELECT *
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
    $computed_hash = self::ComputeFilenameHash($file_data['Filename']);

    if($hash != $computed_hash) {
      // Show the same error message as when a file doesn't exist so a malicious user can't
      // guess random ID numbers to see if a file with this ID exists.
      self::$error_message = 'File Not Found';
      return null;
    }

    $file_object = File::ModelFactory($file_data);

    return $file_object;
  }

}

