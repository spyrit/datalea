<?php

define('DS', DIRECTORY_SEPARATOR);
define('SILEX_ENV', 'prod');
define('SILEX_DEBUG', false);

ini_set('display_errors', 'stderr');
//error_reporting(E_ERROR | E_CORE_ERROR| E_USER_ERROR);

include_once __DIR__.DS.'..'.DS.'app'.DS.'app.php';
