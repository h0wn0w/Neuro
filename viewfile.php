<?php

require_once('core/config.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/file.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/helper.php');


$id   = isset($_GET['id'])  ? Helper::ExtractDigits($_GET['id'])        : '';
$hash = isset($_GET['key']) ? Helper::FilterHTTPParameter($_GET['key']) : '';

if(empty($id) || empty($hash)) {
  die("File Not Found");
}

// Compute the hash

$file = File::FindByIDAndHash($id, $hash);
if($file == null) {
  die(File::GetErrorMessage());
}

print "<b>File info...</b><br />";
print "File #" . $file->GetFileID() . "<br />";
print "Name: " . $file->GetFilename() . "<br />";
print "Size: " . $file->GetFilesize() . " bytes<br />";
print "Owner ID: " . $file->GetUserID() . "<br />";
print "Download: <a href='/getfile.php?id=" . $file->GetFileID() . "&key=" . File::ComputeFilenameHash($file->GetFilename()) . "'>Link</a><br />";
print "<br />";

$owner = $file->GetUserObject();

print "<b>About the owner of this file...</b><br />";
print "Username: " . $owner->GetUsername() . "<br />";
print "<br />";


