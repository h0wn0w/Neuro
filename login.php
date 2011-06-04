<?php

require_once('core/config.php');
require_once('core/classes/account.php');

if(!empty($_SESSION) && isset($_SESSION['LoggedIn'])) {
  Header("Location: /?already-logged-in"); // temp location
  exit();
}

if(empty($_POST) || !isset($_POST['submit'])) {
?>

  <form action="" method="POST">
    <input type="hidden" name="submit" value="1" />

    Username: <br />
    <input type="text" name="username" value="" /><br />
    <br />

    Password: <br />
    <input type="password" name="password" value="" /><br />
    <br />

    <input type="submit" value="Login" />
  </form>
  
<?php
    die();
}

$username = $_POST['username'];
$password = $_POST['password'];

$user_object = Account::Login($username, $password);

if($user_object == null) {
  print "Login failed: " . Account::GetErrorMessage();
  print "<br />";
  print "<a href='/login.php'>Try again.</a>";
  exit();
}

// Initialize session data (can store the keys in a central location, Session object?)
$_SESSION['LoggedIn']         = 1;
$_SESSION['User']             = array();
$_SESSION['User']['UserID']   = $user_object->GetUserID();
$_SESSION['User']['Username'] = $user_object->GetUsername();


// Redirect to the homepage/members page
Header("Location: /"); 
exit();
