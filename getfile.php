<?php

require_once('core/config.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/file.php');
require_once(CONFIG_NEURO_ROOT_DIRECTORY . '/core/classes/helper.php');

// ********************************************************************************
// ** Grab the parameters we need
// ********************************************************************************
$id   = isset($_GET['id'])  ? Helper::ExtractDigits($_GET['id'])        : '';
$hash = isset($_GET['key']) ? Helper::FilterHTTPParameter($_GET['key']) : '';

if(empty($id) || empty($hash)) {
  die("File Not Found");
}

// ********************************************************************************
// ** Check if the file exists
// ********************************************************************************
$file_object = File::FindByIDAndHash($id, $hash);
if($file_object == null) {
  die(File::GetErrorMessage());
}

// ********************************************************************************
// ** Define a helper method that lowers the footprint of serving a potentially large file
// ********************************************************************************
function readfile_chunked ($filename) { 
  $chunksize = 1*(1024*1024); // how many bytes per chunk 
  $buffer = ''; 
  $handle = fopen($filename, 'rb'); 
  if ($handle === false) { 
    return false; 
  } 
  while (!feof($handle)) { 
    $buffer = fread($handle, $chunksize); 
    print $buffer; 
  } 
  return fclose($handle); 
} 

// We can eventually change this to use Nginx' Sendfile
Header('Content-Type: application/octet-stream');
Header('Content-Disposition: attachment;filename="' . $file_object->GetFilename() . '"');

die('x');
readfile_chunked($file_object->GetPathOnDisk());