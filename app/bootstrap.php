<?php

// get environment constants or set default
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__.DS.'..'.DS.'vendor'.DS.'autoload.php';
/**
 * Create a Default Silex application with some extra service providers
 *
 * @param string $appdir default = __DIR__ , application directory
 * @param string $env default = prod
 * @param bool $debug default = false , debug mode
 *
 * @return \Silex\Application
 */
function createDefaultSilexApp($appdir = __DIR__, $env = 'prod', $debug = false)
{
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $app = new \Silex\Application();

    if (!$fs->exists($appdir)) {
        die('the application directory doesn\'t exists !!');
    }

    /*
     * define main paths
     */
    $app['app_dir'] = realpath($appdir);
    $app['root_dir'] = realpath($appdir.DS.'..');
    $app['web_dir'] = realpath($appdir.DS.'..'.DS.'web');

    // get configs
    $config = require_once $app['app_dir'].DS.'config'.DS.'config.php';
    
    //set umask
    $umask = isset($config['umask']) && is_int($config['umask']) ? $config['umask'] : 0002;
    umask($umask);

    //create cache directories
    $app['cache_dir'] = $appdir.DS.'cache';
    if (!$fs->exists($app['cache_dir'])) {
        $rights = isset($config['cache_access']) && is_int($config['cache_access']) ? $config['cache_access'] : 0775;
        $fs->mkdir(array(
            $app['cache_dir'],
            $app['cache_dir'].DS.'http',
            $app['cache_dir'].DS.'twig',
                ), $rights);
        $fs->chmod($app['cache_dir'], $rights);
        $fs->chmod($app['cache_dir'].DS.'http', $rights);
        $fs->chmod($app['cache_dir'].DS.'twig', $rights);
    }

    $app['data_dir'] = $app['root_dir'].DS.'data';
    if (!$fs->exists($app['data_dir'])) {
        $fs->mkdir($app['data_dir'], 0777);
    }
    
    $app['uploads_dir'] = $app['web_dir'].DS.'uploads';
    if (!$fs->exists($app['uploads_dir'])) {
        $fs->mkdir($app['uploads_dir'], 0777);
    }
    /*
     * define environment variables
     */
    $app['debug'] = (bool) $debug;
    $app['env'] = $env;

    /*
     * add service providers
     */

    //add http cache
    //$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    //    'http_cache.cache_dir' => $app['cache_dir'].DS.'http',
    //));
    //add url generator
    $app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

    //add symfony2 sessions
    $app->register(new \Silex\Provider\SessionServiceProvider());

    //add symfony2 forms and validators
    $app->register(new \Silex\Provider\ValidatorServiceProvider());

    // must be registered before twig
    $app->register(new \Silex\Provider\FormServiceProvider(), array(
        'form.secret' => '4fws6dg4w6df4<qg4sh4646qfgsd4',
    ));
    
    $app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
        $extensions[] = new \Spyrit\Datalea\Form\extension\BootstrapFormExtension();
        return $extensions;
    }));

    //add symfony2 translation (needed for twig + forms)
    $app->register(new Silex\Provider\TranslationServiceProvider(), array(
        'locale_fallback' => empty($config['locale_fallback']) ? 'en' : $config['locale_fallback'],
    ));

    // add twig templating
    $app->register(new \Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/views',
//        'twig.templates' => array(),
        'twig.options' => array(
            'debug' => $app['debug'],
            'cache' => $app['cache_dir'].DS.'twig',
            'auto_reload' => $app['debug'],
        ),
        'twig.form.templates' => array(
            'form_div_layout.html.twig', // Twig SF2 original form theme
            'twitter_bootstrap_form_layout.html.twig', // Custom twitter bootstrap form theme in app/views
        ),
    ));

    //add swiftmailer with default SMTP transport
    if (!empty($config['swiftmailer'])) {
        $app->register(new \Silex\Provider\SwiftmailerServiceProvider(), array(
            'swiftmailer.options' => isset($config['swiftmailer']['options']) ? $config['swiftmailer']['options'] : array(),
        ));
        // custom swiftmailer transport
        $swiftTransport = in_array($config['swiftmailer']['transport'], array('mail', 'sendmail', 'smtp')) ? $config['swiftmailer']['transport'] : 'smtp';
        switch ($swiftTransport) {
            case 'mail':
                $app['swiftmailer.transport'] = new \Swift_MailTransport();
                break;
            case 'sendmail':
                $app['swiftmailer.transport'] = new \Swift_SendmailTransport();
                break;
            case 'smtp':
            default:
                break;
        }
    }

    //Database PDO Connection
    if (!empty($config['databases'])) {
        $app->register(new \Spyrit\Silex\Utils\Provider\PDOServiceProvider(), array(
            'pdo.dbs.options' => is_array($config['databases']) ? $config['databases'] : array(),
        ));
    }

    /*
     * mount or define controllers
     */

    // Exception Error page
    if ($app['env'] == 'prod') {
        $app->error(function(\Exception $e) use ($app) {
            if ($e instanceof Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                $content = vsprintf('<h1>%d - %s (%s)</h1>', array(
                    $e->getStatusCode(),
                    Symfony\Component\HttpFoundation\Response::$statusTexts[$e->getStatusCode()],
                    $app['request']->getRequestUri()
                ));
                $code = $e->getStatusCode();
            } elseif ($e instanceof Symfony\Component\HttpKernel\Exception\HttpException) {
                $content = '<h1>An error occured!</h1>';
                $code = $e->getStatusCode();
            } else {
                $content = '<h1>An error occured!</h1>';
                $code = 200;
            }

            return new \Symfony\Component\HttpFoundation\Response($content, $code);
        });
    }

    return $app;
}
