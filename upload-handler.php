<?php

require_once('core/config.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/file.php');

if(empty($_SESSION) || !isset($_SESSION['LoggedIn']))
  die('Not logged in. <a href="/">Homepage.</a>');

if(empty($_FILES))
  die('No file was uploaded.');

// We'll fill up an array that will serve as our data payload.
$file_upload_row = $_FILES['form'];

// Array contains: 
//  "name"     => raw name of the file
//  "type"     => mime type of the file (image/jpeg)
//  "size"     => size in bytes (from user's browser, untrustable)
//  "tmp_name" => path to the uploaded file on this server
//  "error"    => usually 0 unless an error occured
//
// Custom:
//  "user_id"  => we throw this in to identify which user uploaded it

$file_upload_row['user_id'] = $_SESSION['User']['UserID'];

// Send the request to add this file to this Neuro
$file_upload_response = File::AcceptUpload($file_upload_row);

if($file_upload_response->one == false)
  die('File upload failed: ' . $file_upload_response->two);

// File has been created
$file_object = $file_upload_response->two;
print "File saved.<br />";
print "<br />";
print "<a href='/viewfile.php?id=" . $file_object->GetFileID() . "&key=" . File::ComputeFilenameHash($file_object->GetFilename()) . "'>Go to file.</a>";
print "<pre>";
print_r($file_object);
print "</pre>";
