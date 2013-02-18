<?php

namespace Spyrit\Datalea\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Spyrit\Datalea\Faker\Dump\Dumper;
use Spyrit\Datalea\Faker\Model\ColumnConfig;
use Spyrit\Datalea\Faker\Model\Config;
use Spyrit\Datalea\Faker\Model\VariableConfig;
use Spyrit\Datalea\Form\Type\ConfigFileType;
use Spyrit\Datalea\Form\Type\ConfigType;
use Symfony\Component\HttpFoundation\Request;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * MainControllerProvider
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class MainControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        // controller name to use with method name (instead of using function closure for controller)
        $controller = 'Spyrit\Datalea\Controller\MainControllerProvider::';

        // set as many controllers as you want
        $controllers
            ->get('/{_locale}/about', $controller.'homeAction')
            ->value('_locale', 'en')
            ->bind('datalea_about');

        $controllers
            ->get('/{_locale}', $controller.'homeAction')
            ->value('_locale', 'en')
            ->bind('datalea_homepage');

        // config form
        $controllers
            ->match('/{_locale}/generate', $controller.'generateAction')
            ->value('_locale', 'en')
            ->bind('datalea_generate'); //route name for use with url generator

        $controllers
            ->post('/{_locale}/load/config', $controller.'loadConfigAction')
            ->value('_locale', 'en')
            ->bind('datalea_load_config');

        return $controllers;
    }

    public function homeAction(Request $request, Application $app)
    {
        return $app['twig']->render('datalea/index.html.twig', array(
        ));
    }

    protected function setUserExampleConfig(Config $config)
    {
        $config->setClassname('User');
        $config->setFakeNumber(100);
        $config->setLocale('fr_FR');
        $config->setFormats(array('xml', 'yaml', 'json', 'sql', 'csv', 'php', 'perl', 'python', 'ruby'));

        $var1 = new VariableConfig('lastname', 'lastName');
        $var2 = new VariableConfig('firstname', 'firstName');
        $var3 = new VariableConfig('email_domain', 'safeEmailDomain');
        $var4 = new VariableConfig('birth_date', 'dateTimeThisCentury');

        $config->addVariableConfig($var1);
        $config->addVariableConfig($var2);
        $config->addVariableConfig($var3);
        $config->addVariableConfig($var4);
        $config->addColumnConfig(new ColumnConfig($var1->getName(), $var1->getVarName()));
        $config->addColumnConfig(new ColumnConfig($var2->getName(), $var2->getVarName()));
        $config->addColumnConfig(new ColumnConfig('username', $var1->getVarName().'.'.$var2->getVarName(), 'remove_accents_lowercase'));
        $config->addColumnConfig(new ColumnConfig('email', $var1->getVarName().'.'.$var2->getVarName().'@'.$var3->getVarName(), 'remove_accents_lowercase'));
        $config->addColumnConfig(new ColumnConfig($var4->getName(), $var4->getVarName()));
    }

    protected function setDefaultConfig(Config $config)
    {
        $config->setClassname('');
        $config->setFakeNumber(10);
        $config->setFormats(array('csv'));

        $var1 = new VariableConfig('text1', 'text');

        $config->addVariableConfig($var1);
        $config->addColumnConfig(new ColumnConfig($var1->getName(), $var1->getVarName()));
    }

    public function loadConfigAction(Request $request, Application $app)
    {
        $configFileForm = $app['form.factory']->create(new ConfigFileType());

        if ('POST' == $request->getMethod()) {
            $configFileForm->bindRequest($request);

            if ($configFileForm->isValid()) {
                $data = $configFileForm->getData();
                $loader = new \Spyrit\Datalea\Faker\Dump\Loader();
                $config = $loader->loadXmlFakerConfig($data['configFile']->getPathname());
            }
        }

        if (!isset($config) || !$config instanceof Config) {
            $config = new Config();
            $this->setDefaultConfig($config);
        }

        $configForm = $app['form.factory']->create(new ConfigType(), $config, $app['datalea']);

        return $app['twig']->render('datalea/generate.html.twig', array(
            'form' => $configForm->createView(),
            'configFileForm' => $configFileForm->createView(),
        ));
    }

    public function generateAction(Request $request, Application $app)
    {
        $configFileForm = $app['form.factory']->create(new ConfigFileType());

        $config = new Config();

        if ('GET' == $request->getMethod() && $request->get('reset', 0) != 1) {
            $this->setUserExampleConfig($config);
        } else {
            $this->setDefaultConfig($config);
        }

        $configForm = $app['form.factory']->create(new ConfigType(), $config, $app['datalea']);

        if ('POST' == $request->getMethod()) {
            $configForm->bindRequest($request);

            if ($configForm->isValid()) {
                $config = $configForm->getData();
                $config->generateColumns();
                $date = new \DateTime();

                $dumper = new Dumper($config);
                $file = $dumper->dump(realpath(sys_get_temp_dir()).DS.'Datalea', $date);
                $stream = function () use ($file) {
                    readfile($file);
                };

                return $app->stream($stream, 200, array(
                    'Content-Description' => 'File Transfer',
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'attachment; filename=datalea_'.$config->getClassNameLastPart().'_'.$date->format('Y-m-d_H-i-s').'.zip',
                    'Content-Transfer-Encoding' => 'binary',
                    'Content-Length' => filesize($file),
                ));
            }
        }

        // display the form
        return $app['twig']->render('datalea/generate.html.twig', array(
            'form' => $configForm->createView(),
            'configFileForm' => $configFileForm->createView(),
        ));
    }
}
