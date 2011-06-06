<?php
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/database.php');

class SuperUser {

  // Temporary location
  static function GetListOfUsers() {
    $DB = Database::GetConnection();

    $query = "SELECT UserID, Username
              FROM   Users
              ORDER BY UserID ASC ";
    $result = mysql_query($query, $DB);

    $data = array();
    while($row = mysql_fetch_assoc($result)) 
      $data[] = $row;

    return $data;
  }

}