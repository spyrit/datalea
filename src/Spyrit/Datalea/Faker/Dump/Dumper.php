<?php

namespace Spyrit\Datalea\Faker\Dump;

use \DateTime;
use \DOMDocument;
use \Faker\Factory;
use \InvalidArgumentException;
use \RuntimeException;
use \Spyrit\Datalea\Faker\Dump\Dumper;
use \Spyrit\Datalea\Faker\Model\Config;
use \Spyrit\LightCsv\CsvWriter;
use \Symfony\Component\Filesystem\Filesystem;
use \Symfony\Component\Yaml\Yaml;
use \ZipArchive;

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
            'xml' => 'XML', 
            'json' => 'JSON',
            'sql' => 'SQL',
            'php' => 'PHP', 
            'perl' => 'Perl', 
            'ruby' => 'Ruby', 
            'python' => 'Python', 
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
            $formatElt = $formatsElt->addChild('format', $format);
        }
        
        $csvFormat = $this->config->getCsvFormat();
        if ($csvFormat && $this->config->hasFormat('csv')) {
            $formatOptionsElt = $root->addChild('formatOptions');
            $csvElt = $formatOptionsElt->addChild('csv');
            $csvElt->addChildCData('delimiter', $csvFormat->getDelimiter());
            $csvElt->addChildCData('enclosure', $csvFormat->getEnclosure());
            $csvElt->addChild('encoding', $csvFormat->getEncoding());
            $csvElt->addChild('eol', $csvFormat->getEol());
            $csvElt->addChildCData('escape', $csvFormat->getEscape());
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
        $dom = new DOMDocument('1.0', 'UTF-8');
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
    public function dumpPHP($dir)
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
    public function dumpPerl($dir)
    {
        $format = <<<DUMP
my %%%s = (
%s
);

DUMP;
        $fakeData = $this->getFakeData();
        
        $indent = 2;
        $indentChar = ' ';
        
        $values = '';
        foreach ($fakeData as $item) {
            $values .= str_repeat($indentChar, $indent).'{'."\n";
            foreach ($item as $key => $value) {
                $values .= str_repeat($indentChar, $indent*2).'"'.$key.'" => "'.$value.'",'."\n";
            }
            $values .= str_repeat($indentChar, $indent).'},'."\n";
        }
        
        $file = $dir.DS.$this->config->getClassName(true).'.pl';
        file_put_contents($file, sprintf($format, $this->config->getClassNameLastPart(), $values));
        
        return $file;
    }
    
    /**
     * 
     * @return string
     */
    public function dumpPython($dir)
    {
        $format = <<<DUMP
%s = [
%s
]

DUMP;
        $fakeData = $this->getFakeData();
        
        $indent = 2;
        $indentChar = ' ';
        
        $values = '';
        $first1 = true;
        foreach ($fakeData as $item) {
            if ($first1) {
                $first1 = false;
            } else {
                $values .= ','."\n";
            }
            $values .= str_repeat($indentChar, $indent).'{';
            
            $first2 = true;
            foreach ($item as $key => $value) {
                if ($first2) {
                    $first2 = false;
                    $values .= "\n";
                } else {
                    $values .= ','."\n";
                }
                $values .= str_repeat($indentChar, $indent*2).'\''.$key.'\': \''.$value.'\'';
            }
            
            $values .= "\n".str_repeat($indentChar, $indent).'}';
        }
        
        $file = $dir.DS.$this->config->getClassName(true).'.py';
        file_put_contents($file, sprintf($format, $this->config->getClassNameLastPart(), $values));
        
        return $file;
    }
    
    /**
     * 
     * @return string
     */
    public function dumpRuby($dir)
    {
          $format = <<<DUMP
%s = {
%s
}

DUMP;
        $fakeData = $this->getFakeData();
        
        $indent = 2;
        $indentChar = ' ';
        
        $values = '';
        $first1 = true;
        foreach ($fakeData as $item) {
            if ($first1) {
                $first1 = false;
            } else {
                $values .= ','."\n";
            }
            $values .= str_repeat($indentChar, $indent).'{';
            
            $first2 = true;
            foreach ($item as $key => $value) {
                if ($first2) {
                    $first2 = false;
                    $values .= "\n";
                } else {
                    $values .= ','."\n";
                }
                $values .= str_repeat($indentChar, $indent*2).'\''.$key.'\' => \''.$value.'\'';
            }
            
            $values .= "\n".str_repeat($indentChar, $indent).'}';
        }
        
        $file = $dir.DS.$this->config->getClassName(true).'.rb';
        file_put_contents($file, sprintf($format, $this->config->getClassNameLastPart(), $values));
        
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
        
        $csvWriter = $this->config->createCsvWriter();
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
        $dom = new DOMDocument('1.0', 'UTF-8');
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
        $format = <<<DUMP
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO %s (%s) VALUES
%s
;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
DUMP;
        $fakeData = $this->getFakeData();
        
        $name = $this->config->getClassName(true);
        
        $columns = array_keys($fakeData[0]);
        
        $values = '';
        $first = true;
        foreach ($fakeData as $item) {
            if ($first) {
                $first = false;
            } else {
                $values .= ','."\n";
            }
            $values .= '(\''.implode('\', \'', $item).'\')';
        }
        
        $file = $dir.DS.$name.'.sql';
        file_put_contents($file, sprintf($format, $name, implode(', ', $columns), $values));
        
        return $file;
    }
    
    /**
     * 
     * @param string $tmpDir
     * @param DateTime $date
     * 
     * @return string zip filename
     * 
     * @throws RuntimeException
     */
    public function dump($tmpDir, $date = null)
    {
        $date = $date instanceof DateTime ? $date : new DateTime();

        $fs = new Filesystem();
        
        $workingDir = time().'_'.uniqid();
        $workingPath = $tmpDir.DS.$workingDir;
        
        if (!$fs->exists($workingPath)) {
            $fs->mkdir($workingPath, 0777);
        }
        
        if (!$this->config->hasSeed()) {
            $this->config->generateSeed();
        }
        
        $files = array();
        
        $files[] = $this->saveConfigAsXML($workingPath);
        
        foreach ($this->config->getFormats() as $format) {
            switch ($format) {
                case 'csv':
                    $files[] = $this->dumpCSV($workingPath);
                    break;
                case 'yaml':
                    $files[] = $this->dumpYAML($workingPath);
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
                case 'php':
                    $files[] = $this->dumpPHP($workingPath);
                    break;
                case 'perl':
                    $files[] = $this->dumpPerl($workingPath);
                    break;
                case 'ruby':
                    $files[] = $this->dumpRuby($workingPath);
                    break;
                case 'python':
                    $files[] = $this->dumpPython($workingPath);
                    break;
            }
        }
        
        $zipname = $tmpDir.DS.'archive_'.$workingDir.'.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipname, ZipArchive::CREATE)!==TRUE) {
            throw new RuntimeException;("cannot create zip archive $filename\n");
        }
        
        foreach ($files as $file) {
            $zip->addFile($file, 'fakedata_'.$this->config->getClassNameLastPart().'_'.$date->format('Y-m-d_H-i-s').DS.basename($file));
        }
        $zip->close();
        
        return $zipname;
    }
}
