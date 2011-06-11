<?php

require_once('core/config.php');

if(empty($_SESSION) || !isset($_SESSION['LoggedIn'])) {
  die('Not logged in. <a href="/">Homepage.</a>');
}

?>

<h1>Upload a File:</h1>
<form name="upload" action="upload-handler.php" enctype="multipart/form-data" method="POST">
  <input type="file" name="form" size="50" /><br />
  <br />
  <input type="submit" value="Upload" />
  <br />

</form>