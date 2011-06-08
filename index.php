<?php

require_once('core/config.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/superuser.php');

if(!empty($_SESSION) && isset($_SESSION['LoggedIn'])) {
  print "<h1>Hello {$_SESSION['User']['Username']}...</h1>";

  print "Session data: ";
  print_r($_SESSION);
  print "<br /><br />";
  print "<a href='/logout.php'>Logout?</a><br />";
  print "<br />";

  print "<a href='/upload.php'>Upload</a>";
  print "<br />";
  print "<h2>Who signed up:</h2>";
  
  $user_list = SuperUser::GetListOfUsers();
  foreach($user_list as $user)
    print "{$user['UserID']}: {$user['Username']}<br />";

} else {
  print "<a href='/login.php'>Login</a><br />";
  print "<br />";
  print "<a href='/register.php'>Register</a>";

}
?>

