<?php

// get environment constants or set default
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('SILEX_ENV')) {
    define('SILEX_ENV', 'dev');
}

if (!defined('SILEX_DEBUG')) {
    define('SILEX_DEBUG', false);
}

require_once __DIR__.DS.'bootstrap.php';

$app = createDefaultSilexApp(__DIR__, SILEX_ENV, SILEX_DEBUG);

/*
 * add custom service providers
 */

/*
 * mount or define custom controllers
 */

$app->mount('/', new \Spyrit\Datalea\Controller\MainControllerProvider());

/*
 * run silex application
 */
$app->run();

//var_dump($app);
