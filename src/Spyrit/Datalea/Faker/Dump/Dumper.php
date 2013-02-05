<?php

namespace Spyrit\Datalea\Faker\Dump;

use Faker\Factory;
use InvalidArgumentException;
use Spyrit\LightCsv\CsvWriter;
use Spyrit\Datalea\Faker\Dump\Dumper;
use Spyrit\Datalea\Faker\Model\Config;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Dumper
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class Dumper
{
    /**
     *
     * @var Config
     */
    protected $config;
    
    /**
     *
     * @var array 
     */
    protected $fakeData;
    
    /**
     * 
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 
     * @return array
     */
    public static function getAvailableFormats()
    {
        return array(
            'csv' => 'CSV', 
            'yaml' => 'YAML', 
            'php' => 'PHP array', 
            'xml' => 'XML', 
            'json' => 'JSON',
            'sql' => 'SQL',
        );
    }
    
    /**
     * 
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 
     * @param Config $config
     * 
     * @return Dumper
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function saveConfigAsXML($dir)
    {
        $name = $this->config->getClassName(true).'_datalea_config';
        
        $root = new FakerSimpleXMLElement('<?xml version=\'1.0\' encoding=\'utf-8\'?><dataleaConfig/>');
        
        $root->addAttribute('classname', $this->config->getClassName());
        $root->addAttribute('locale', $this->config->getLocale());
        $root->addAttribute('seed', $this->config->getSeed());
        $root->addAttribute('fakenumber', $this->config->getFakeNumber());
        
        $formatsElt = $root->addChild('formats');
        foreach ($this->config->getFormats() as $format) {
            $formatsElt->addChild('format', $format);
        }
        
        $variablesElt = $root->addChild('variables');
        foreach ($this->config->getVariableConfigs() as $variableConfig) {
            $variableElt = $variablesElt->addChild('variable');
            $variableElt->addAttribute('name', $variableConfig->getName());
            $variableElt->addAttribute('unique', $variableConfig->getUnique());
            $variableElt->addChild('method', $variableConfig->getFakerMethod());
            $variableElt->addChildCData('argument1', $variableConfig->getFakerMethodArg1());
            $variableElt->addChildCData('argument2', $variableConfig->getFakerMethodArg2());
            $variableElt->addChildCData('argument3', $variableConfig->getFakerMethodArg3());
        }
        
        $columnsElt = $root->addChild('columns');
        foreach ($this->config->getColumnConfigs() as $columnConfig) {
            $columnElt = $columnsElt->addChild('column');
            $columnElt->addAttribute('name', $columnConfig->getName());
            $columnElt->addChildCData('value', $columnConfig->getValue());
            $columnElt->addChild('convert', $columnConfig->getConvertMethod());
        }
        
        $file = $dir.DS.$name.'.xml';
        
        $rootDom = dom_import_simplexml($root);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $rootDom = $dom->importNode($rootDom, true);
        $rootDom = $dom->appendChild($rootDom);
        
        $dom->save($file);
        return $file;
    }
    
    protected function generateFakeData()
    {
        if (empty($this->config)) {
            throw new InvalidArgumentException('A Faker configuration must be set.');
        }
        
        $uniqueValues = array();
        
        $variableConfigs = $this->config->getVariableConfigs();
        $columnConfigs = $this->config->getColumnConfigs();
        
        if (!count($columnConfigs)) {
            throw new InvalidArgumentException('the configuration must have one column configuration at least.');
        }
        
        if ($this->config->getFakeNumber() < 1) {
            throw new InvalidArgumentException('The number of fake elements to generate must be greater than 0.');
        }
        
        $faker = Factory::create($this->config->getLocale());
        if ($this->config->getSeed() !== null) {
            $faker->seed($this->config->getSeed());
        }
        
        $this->fakeData = array();
        for ($index = 1; $index <= $this->config->getFakeNumber(); $index++) {
            $values = array();
            foreach ($variableConfigs as $variableConfig) {
                $variableConfig->generateValue($faker, $values, $variableConfigs, $uniqueValues);
            }

            $data = array();
            foreach ($columnConfigs as $columnConfig) {
                $data[$columnConfig->getName()] = $columnConfig->replaceVariable($values);
            }
            $this->fakeData[] = $data;
        }
    }

    /**
     * 
     * @return array
     */
    public function getFakeData()
    {
        if (empty($this->fakeData)) {
            $this->generateFakeData();
        }
        return $this->fakeData;
    }

    /**
     * 
     * @return string
     */
    public function dumpPHPArray($dir)
    {
        $format = <<<PHPARRAY
<?php 
\$%s = %s;

PHPARRAY;
        $name = $this->config->getClassName(true);
        
        $file = $dir.DS.$name.'.php';
        file_put_contents($file, sprintf($format, $name, preg_replace('/\s*\d+\s*=>\s*/', "\n  ", var_export($this->getFakeData(), true))));
        
        return $file;
    }
    
    /**
     * 
     * @return string
     */
    public function dumpJSON($dir)
    {
        $format = <<<JSON
%s;
JSON;
        $name = $this->config->getClassName(true);
        
        $file = $dir.DS.$name.'.json';
        file_put_contents($file, json_encode($this->getFakeData(), JSON_PRETTY_PRINT));
        
        return $file;
    }
    
    /**
     * 
     * @return string
     */
    public function dumpYAML($dir)
    {
        $className = $this->config->getClassName();
        $data = array(
            $className => array(),
        );
        
        $fakeData = $this->getFakeData();
        
        $name = $this->config->getClassName(true);
        
        $itemName = $this->config->getClassNameLastPart();
        
        $i = 1 ;
        foreach ($fakeData as $items) {
            $data[$className][$itemName.'_'.$i] = $items;
            $i++;
        }
        
        $file = $dir.DS.$name.'.yml';
        file_put_contents($file, Yaml::dump($data,4));
        
        return $file;
    }
    
    /**
     * 
     * @return string
     */
    public function dumpCSV($dir)
    {
        $fakeData = $this->getFakeData();
        
        $name = $this->config->getClassName(true);
        $file = $dir.DS.$name.'.csv';
        
        $csvWriter = new CsvWriter(';', '"', 'CP1252', "\r\n", "\\", false);
        $csvWriter->setFilename($file);
                
        $csvWriter->writeRow(array_keys($fakeData[0]));
        $csvWriter->writeRows($fakeData);
        $csvWriter->close();
        return $file;
    }
    
    /**
     * 
     * @return string
     */
    public function dumpXML($dir)
    {
        $fakeData = $this->getFakeData();
        
        $name = $this->config->getClassName(true);
        
        $elementRootName = strtolower(str_ireplace('_', '', $name));
        $elementName = strtolower($this->config->getClassNameLastPart());
        
        $root = new FakerSimpleXMLElement('<?xml version=\'1.0\' encoding=\'utf-8\'?><'.$elementRootName.'s/>');
        
        foreach ($fakeData as $items) {
            $element = $root->addChild($elementName);
            foreach ($items as $column => $value) {
                $element->addChild(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $column)), $value);
            }
        }
        
        $file = $dir.DS.$name.'.xml';
        
        $rootDom = dom_import_simplexml($root);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $rootDom = $dom->importNode($rootDom, true);
        $rootDom = $dom->appendChild($rootDom);
        
        $dom->save($file);
        return $file;
    }
    
    /**
     * 
     * @return string
     */
    public function dumpSQL($dir)
    {
        $format = <<<SQL
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO %s (%s) VALUES
%s
;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
SQL;
        $fakeData = $this->getFakeData();
        
        $name = $this->config->getClassName(true);
        
        $columns = array_keys($fakeData[0]);
        
        $values = '';
        $first = true;
        foreach ($fakeData as $items) {
            if ($first) {
                $first = false;
            } else {
                $values .= ','."\n";
            }
            $values .= '(\''.implode('\', \'', $items).'\')';
        }
        
        $file = $dir.DS.$name.'.sql';
        file_put_contents($file, sprintf($format, $name, implode(', ', $columns), $values));
        
        return $file;
    }
    /**
     * 
     * @return string zip filename
     */
    
    /**
     * 
     * @param string $tmpDir
     * @param \DateTime $date
     * 
     * @return string zip filename
     * 
     * @throws \RuntimeException
     */
    public function dump($tmpDir, $date = null)
    {
        $date = $date instanceof \DateTime ? $date : new \DateTime();

        $fs = new Filesystem();
        
        $workingDir = time().'_'.uniqid();
        $workingPath = $tmpDir.DS.$workingDir;
        
        if (!$fs->exists($workingPath)) {
            $fs->mkdir($workingPath, 0777);
        }
        
        $files = array();
        
        $files[] = $this->saveConfigAsXML($workingPath);
        
        foreach ($this->config->getFormats() as $format) {
            switch ($format) {
                case 'yaml':
                    $files[] = $this->dumpYAML($workingPath);
                    break;
                case 'php':
                    $files[] = $this->dumpPHPArray($workingPath);
                    break;
                case 'csv':
                    $files[] = $this->dumpCSV($workingPath);
                    break;
                case 'xml':
                    $files[] = $this->dumpXML($workingPath);
                    break;
                case 'json':
                    $files[] = $this->dumpJSON($workingPath);
                    break;
                case 'sql':
                    $files[] = $this->dumpSQL($workingPath);
                    break;
            }
        }
        
        $zipname = $tmpDir.DS.'archive_'.$workingDir.'.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipname, \ZipArchive::CREATE)!==TRUE) {
            throw new \RuntimeException;("cannot create zip archive $filename\n");
        }
        
        foreach ($files as $file) {
            $zip->addFile($file, 'fakedata_'.$this->config->getClassNameLastPart().'_'.$date->format('Y-m-d_H-i-s').DS.basename($file));
        }
        $zip->close();
        
        return $zipname;
    }
}
