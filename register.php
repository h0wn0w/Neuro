<?php

require_once('core/config.php');
require_once('core/classes/helper.php');
require_once('core/classes/account.php');

Account::Login('a', 'b');
if(!empty($_POST) && isset($_POST['submit'])) {
  $invite_code = $_POST['invite_code'];
  $username    = $_POST['username'];
  $password    = $_POST['password'];

  // Stack any validation errors onto this list
  $errors = array();

  if($invite_code != CONFIG_INVITE_CODE)
    $errors[] = 'Invite code not accepted. You need to be given a valid invite code by a user from this Neuro.';

  $valid_username = User::CheckValidUsername($username);
  if($valid_username->one == false)
    $errors[] = $valid_username->two;

  $valid_password = User::CheckValidPassword($password);
  if($valid_password->one == false)
    $errors[] = $valid_password->two;

  // If there were no errors, call the model and ask to create the user
  $account_created = false;
  if(empty($errors)) {
    $user_created = Account::CreateUser($username, $password);
    if($user_created == null) {
      $errors[] = Account::GetErrorMessage();
    } else {
      $account_created = true;

      // Log the user in
      $user_object = Account::Login($username, $password);

      // Set the session variables
      Account::InitializeSession($user_object);

      // Send the user off...
      Header("Location: /");
      exit();
    }
  }
}

?>

<h2>Register</h2>
<?php
if(!empty($errors)) {
  print "<b>Error during signup:</b>";
  print "<div style='color: red;'>";
  foreach($errors as $value) 
    print $value . "<br />";
  print "</div>";
  print "<br />";
}
?>
<form action="" method="POST">
  <input type="hidden" name="submit" value="1" />

    Invite code (Required):<br />
    <input type="text" name="invite_code" value="<?php echo (empty($invite_code) ? "" : Helper::FilterHTTPParameter($invite_code)); ?>" /><br />
    <br />

    Username:<br />
    <input type="text" name="username" value="<?php echo (empty($username) ? "" : Helper::FilterHTTPParameter($username)); ?>" /><br />
    <br />

    Password:<br />
    <input type="password" name="password" value="" /><br />
    <br />

    <input type="submit" value="Register" />
    <br />
    <br />
    <span style="color: #777; ">The invite code for this Neuro is <b><?php echo CONFIG_INVITE_CODE ?></b></span>
</form>

