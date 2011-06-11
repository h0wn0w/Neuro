<?php

// Application-level settings
define('CONFIG_APPLICATION_NAME', 'Neuro');
define('CONFIG_INVITE_CODE',      'cosmos');
define('CONFIG_HASH_ALGORITHM',   'sha256');

// Source code file structure (we can change this when we fix Nginx defaulting to generic Document Root) ($_SERVER['DOCUMENT_ROOT'])
define('CONFIG_NEURO_ROOT_DIRECTORY',          $_SERVER['DOCUMENT_ROOT']); // this doesn't include a trailing slash
define('CONFIG_NEURO_UPLOAD_DIRECTORY',        CONFIG_NEURO_ROOT_DIRECTORY . '/uploads/auto');
define('CONFIG_NEURO_MANUAL_UPLOAD_DIRECTORY', CONFIG_NEURO_ROOT_DIRECTORY . '/uploads/manual');

// Define Application-Wide Error Codes (Negatives are to allow  <= 0  response check)
define('ERROR_CODE_UPLOAD_DIRECTORY_NOT_FOUND',       -1020);

define('ERROR_CODE_UPLOAD_ARRAY_NAME_EMPTY',          -1050);
define('ERROR_CODE_UPLOAD_ARRAY_TYPE_EMPTY',          -1051);
define('ERROR_CODE_UPLOAD_ARRAY_TMP_FILE_NAME_EMPTY', -1052);
define('ERROR_CODE_UPLOAD_ARRAY_TMP_FILE_NOT_FOUND',  -1053);
define('ERROR_CODE_UPLOAD_ARRAY_TMP_FILE_IS_EMPTY',   -1054);
define('ERROR_CODE_UPLOAD_ARRAY_USER_ID_EMPTY',       -1060);

// Important: Set the include path
set_include_path(get_include_path() . PATH_SEPARATOR . CONFIG_NEURO_ROOT_DIRECTORY);
session_save_path('/www/sessions');

// Database access
define('CONFIG_DATABASE_HOST', 'localhost');
define('CONFIG_DATABASE_USER', 'neuro');
define('CONFIG_DATABASE_PASS', 'Synaptic2011');
define('CONFIG_DATABASE_NAME', 'neuro');

// Initialize parts of code needed for web visits, such as a session state
if(isset($_SERVER['HTTP_HOST'])) {
  session_start();
}
