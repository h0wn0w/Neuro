<?php

require_once('core/config.php');

if(!empty($_SESSION) && isset($_SESSION['LoggedIn'])) {
  print "Session data: ";
  print_r($_SESSION);

  print "<br /><br />";
  print "Hello {$_SESSION['User']['Username']}...<br />";
  print "<br />";
  print "<a href='/logout.php'>Logout?</a>";
} else {
  print "<a href='/login.php'>Login</a>";
}


?>