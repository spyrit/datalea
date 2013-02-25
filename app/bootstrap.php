<?php

use \Exception;
use \Silex\Application;
use \Silex\Provider\FormServiceProvider;
use \Silex\Provider\MonologServiceProvider;
use \Silex\Provider\ServiceControllerServiceProvider;
use \Silex\Provider\SessionServiceProvider;
use \Silex\Provider\SwiftmailerServiceProvider;
use \Silex\Provider\TranslationServiceProvider;
use \Silex\Provider\TwigServiceProvider;
use \Silex\Provider\UrlGeneratorServiceProvider;
use \Silex\Provider\ValidatorServiceProvider;
use \Silex\Provider\WebProfilerServiceProvider;
use \Spyrit\Datalea\Form\extension\BootstrapFormExtension;
use \Spyrit\Silex\Utils\Provider\Database\PDOServiceProvider;
use \Swift_MailTransport;
use \Swift_SendmailTransport;
use \Symfony\Component\ClassLoader\DebugClassLoader;
use \Symfony\Component\Filesystem\Filesystem;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\Debug\ErrorHandler;
use \Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use \Symfony\Component\HttpKernel\Exception\HttpException;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Symfony\Component\Yaml\Exception\ParseException;
use \Symfony\Component\Yaml\Yaml;
use \Symfony\Component\Translation\Loader\YamlFileLoader;

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
 * @return Application
 */
function createDefaultSilexApp($appdir = __DIR__, $env = 'prod', $debug = false)
{
    $fs = new Filesystem();
    if (!$fs->exists($appdir)) {
        die('the application directory doesn\'t exists !!');
    }

    $app = new Application();

    /*
     * define environment variables
     */
    $app['debug'] = (bool) $debug;
    $app['env'] = $env;

    if ($app['debug']) {
        DebugClassLoader::enable();
        ErrorHandler::register();
        if ('cli' !== php_sapi_name()) {
            ExceptionHandler::register();
        }
    }

    /*
     * define main paths
     */
    $app['app_dir'] = realpath($appdir);
    $app['root_dir'] = realpath($appdir.DS.'..');
    $app['web_dir'] = realpath($appdir.DS.'..'.DS.'web');

    // get configs
    if (file_exists($app['app_dir'].DS.'config'.DS.'config.php')) {
        $config = require_once $app['app_dir'].DS.'config'.DS.'config.php';
    } elseif (file_exists($app['app_dir'].DS.'config'.DS.'config.yml')) {
        try {
            $config = Yaml::parse($app['app_dir'].DS.'config'.DS.'config.yml');
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s in config file", $e->getMessage());
            exit();
        }
    } else {
        printf("no config file config file");
        exit();
    }

    //set umask
    $umask = isset($config['umask']) && is_int($config['umask']) ? $config['umask'] : 0002;
    umask($umask);

    //create cache and log directories
    $app['cache_dir'] = $appdir.DS.'cache';
    $app['log_dir'] = $appdir.DS.'logs';

    if (!$fs->exists($app['cache_dir'])) {
        $rights = isset($config['cache_access']) && is_int($config['cache_access']) ? $config['cache_access'] : 0775;
        $fs->mkdir(array(
            $app['cache_dir'],
            $app['cache_dir'].DS.'http',
            $app['cache_dir'].DS.'twig',
            $app['cache_dir'].DS.'profiler',
                ), $rights);

        $fs->chmod($app['cache_dir'], $rights);
        $fs->chmod($app['cache_dir'].DS.'http', $rights);
        $fs->chmod($app['cache_dir'].DS.'twig', $rights);
        $fs->chmod($app['cache_dir'].DS.'profiler', $rights);
    }

    if (!$fs->exists($app['log_dir'])) {
        $fs->mkdir($app['log_dir'], 0775);
        $fs->chmod($app['log_dir'], 0775);
    }

    $app['data_dir'] = $app['root_dir'].DS.'data';
    if (!$fs->exists($app['data_dir'])) {
        $fs->mkdir($app['data_dir'], 0777);
    }

    $app['uploads_dir'] = $app['web_dir'].DS.'uploads';
    if (!$fs->exists($app['uploads_dir'])) {
        $fs->mkdir($app['uploads_dir'], 0777);
    }

    // Application specific parameters
    $app['datalea'] = array(
        'max_variables' => isset($config['max_variables']) ? (int) $config['max_variables'] : 100,
        'max_columns' => isset($config['max_columns']) ? (int) $config['max_columns'] : 80,
        'max_rows' => isset($config['max_rows']) ? (int) $config['max_rows'] : 2000,
        'google_analytics_account' => isset($config['google_analytics_account']) ? $config['google_analytics_account'] : null,
    );

    /*
     * add service providers
     */

    //add http cache
    //$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    //    'http_cache.cache_dir' => $app['cache_dir'].DS.'http',
    //));
    //add url generator
    $app->register(new UrlGeneratorServiceProvider());

    //add symfony2 sessions
    $app->register(new SessionServiceProvider());

    //add symfony2 forms and validators
    $app->register(new ValidatorServiceProvider());

    $app->register(new ServiceControllerServiceProvider());

    // must be registered before twig
    $app->register(new FormServiceProvider(), array(
        'form.secret' => '4fws6dg4w6df4<qg4sh4646qfgsd4',
    ));

    $app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
        $extensions[] = new BootstrapFormExtension();

        return $extensions;
    }));

    //add symfony2 translation (needed for twig + forms)
    $app->register(new TranslationServiceProvider(), array(
        'locale_fallback' => empty($config['locale_fallback']) ? 'en' : $config['locale_fallback'],
    ));

    $app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
        $translator->addLoader('yaml', new YamlFileLoader());
        $languages = array('en', 'fr');
        $domains = array('messages', 'validators');
        foreach ($languages as $lang) {
            foreach ($domains as $domain) {
                $translator->addResource('yaml', $app['app_dir'].DS.'translations'.DS.$domain.'.'.$lang.'.yml', $lang);
            }
        }

        return $translator;
    }));

     $app->before(function () use ($app) {
        if ($locale = $app['request']->get('_locale')) {
            $app['locale'] = $locale;
            $app['translator']->setLocale($app['locale']);
        }
    });

    // add twig templating
    $app->register(new TwigServiceProvider(), array(
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

    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
        // add custom globals, filters, tags, ...
        $twig->addGlobal('google_analytics_account', $app['datalea']['google_analytics_account']);

        return $twig;
    }));

    // Web Profiler and Monolog
    if ($app['debug']) {
        $app->register(new MonologServiceProvider(), array(
            'monolog.logfile' => $app['log_dir'].DS.'silex.log',
        ));

        $app->register($p = new WebProfilerServiceProvider(), array(
            'profiler.cache_dir' => $app['cache_dir'].DS.'profiler',
        ));

        $app->mount('/_profiler', $p);
    }

    //add swiftmailer with default SMTP transport
    if (!empty($config['swiftmailer'])) {
        $app->register(new SwiftmailerServiceProvider(), array(
            'swiftmailer.options' => isset($config['swiftmailer']['options']) ? $config['swiftmailer']['options'] : array(),
        ));
        // custom swiftmailer transport
        $swiftTransport = in_array($config['swiftmailer']['transport'], array('mail', 'sendmail', 'smtp')) ? $config['swiftmailer']['transport'] : 'smtp';
        switch ($swiftTransport) {
            case 'mail':
                $app['swiftmailer.transport'] = new Swift_MailTransport();
                break;
            case 'sendmail':
                $app['swiftmailer.transport'] = new Swift_SendmailTransport();
                break;
            case 'smtp':
            default:
                break;
        }
    }

    //Database PDO Connection
    if (!empty($config['databases'])) {
        $app->register(new PDOServiceProvider(), array(
            'pdo.dbs.options' => is_array($config['databases']) ? $config['databases'] : array(),
        ));
    }

    /*
     * mount or define controllers
     */

    // Exception Error page
    if ($app['env'] == 'prod') {
        $app->error(function(Exception $e) use ($app) {
            if ($e instanceof NotFoundHttpException) {
                $content = vsprintf('<h1>%d - %s (%s)</h1>', array(
                    $e->getStatusCode(),
                Response::$statusTexts[$e->getStatusCode()],
                    $app['request']->getRequestUri()
                ));
                $code = $e->getStatusCode();
            } elseif ($e instanceof HttpException) {
                $content = '<h1>An error occured!</h1>';
                $code = $e->getStatusCode();
            } else {
                $content = '<h1>An error occured!</h1>';
                $code = 200;
            }

            return new Response($content, $code);
        });
    }

    return $app;
}
