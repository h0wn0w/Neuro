<?php

// Application-level settings
define('CONFIG_APPLICATION_NAME', 'Neuro');
define('CONFIG_HASH_ALGORITHM', 'sha256');

// Source code file structure
define('CONFIG_NEURO_ROOT_DIRECTORY', '/www/neuro');

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
