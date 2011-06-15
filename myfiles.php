<?php

require_once('core/config.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/account.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/file.php');

if(Account::LoggedIn() == false)
  die('Not logged in. <a href="/login.php">Login</a>');


// Grab a list of the user's files at the path requested (root for now)
$user_id   = Account::GetUserID();
$path      = '/';
$file_list = File::GetUserFiles($user_id, $path);

// Quick makeshift header
print '<h1>My Files (' . count($file_list) . ')</h1>';
print '<a href="/">Homepage</a>, ';
print '<a href="/upload.php">Upload</a>';
print '<br /><br />';

// List out all their files
foreach($file_list as $file) {
  $access_key = File::ComputeFilenameHash($file['Filename']);
  print '<a href="viewfile.php?id=' . $file['FileID'] . '&key=' . $access_key . ' ">';
  print $file['Filename'];
  print '</a>';
  print ' ';

  print '(' . $file['Filename']  . ')';
  print '<br />';
}

?>