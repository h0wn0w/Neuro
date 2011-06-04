<?php

require_once('core/config.php');
require_once('core/classes/database.php');

if(!empty($_SESSION)) {
  print "Session data: ";
  print_r($_SESSION);
  print "<br /><br />";
  print "<a href='/logout.php'>Logout</a>";

} else {

  print "<a href='/login.php'>Login</a>";

}

?>
