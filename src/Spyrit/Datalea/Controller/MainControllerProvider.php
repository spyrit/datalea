<?php

namespace Spyrit\Datalea\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Spyrit\Datalea\Faker\Dump\Dumper;
use Spyrit\Datalea\Faker\Model\ColumnConfig;
use Spyrit\Datalea\Faker\Model\Config;
use Spyrit\Datalea\Faker\Model\VariableConfig;
use Spyrit\Datalea\Form\Type\FakerConfigFileType;
use Spyrit\Datalea\Form\Type\FakerConfigType;
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
        $controllers->get('/', $controller.'homeAction')->bind('datalea_homepage');
        
        // config form
        $controllers->match('/generate', $controller.'generateAction')->bind('datalea_generate'); //route name for use with url generator

        $controllers->post('/load/config', $controller.'loadConfigAction')->bind('datalea_load_config');
        
        return $controllers;
    }
    
    public function homeAction(Request $request, Application $app)
    {
        return $app['twig']->render('datalea/index.html.twig', array(
        ));
    }
    
    protected function setDefaultConfig(Config $config) 
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
    
    public function loadConfigAction(Request $request, Application $app) 
    {
        $configFileForm = $app['form.factory']->create(new FakerConfigFileType());
        
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
        
        $configForm = $app['form.factory']->create(new FakerConfigType(), $config);
        
        return $app['twig']->render('datalea/generate.html.twig', array(
            'form' => $configForm->createView(),
            'configFileForm' => $configFileForm->createView(),
        ));
    }
    
    public function generateAction(Request $request, Application $app) 
    {
        $configFileForm = $app['form.factory']->create(new FakerConfigFileType());
        
        $config = new Config();
        
        if ('GET' == $request->getMethod() && $request->get('reset', 0) != 1) {
            $this->setDefaultConfig($config);
        }
        
        $configForm = $app['form.factory']->create(new FakerConfigType(), $config);
        
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
                    'Content-Disposition' => 'attachment; filename=fakedata_'.$config->getClassNameLastPart().'_'.$date->format('Y-m-d_H-i-s').'.zip',
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
