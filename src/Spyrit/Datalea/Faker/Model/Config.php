<?php

namespace Spyrit\Datalea\Faker\Model;

use \Spyrit\Datalea\Faker\Dump\Dumper;
use \Spyrit\LightCsv\CsvWriter;

/**
 * Config
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class Config
{
    /**
     *
     * @var string
     */
    protected $locale;

    /**
     *
     * @var int
     */
    protected $seed;

    /**
     *
     * @var string
     */
    protected $className;

    /**
     *
     * @var array
     */
    protected $formats = array();

    /**
     *
     * @var int
     */
    protected $fakeNumber;

    /**
     *
     * @var array of ColumnConfig
     */
    protected $columnConfigs = array();

    /**
     *
     * @var array of VariableConfig
     */
    protected $variableConfigs = array();
    
    /**
     *
     * @var \Spyrit\Datalea\Faker\Model\CsvFormat
     */
    protected $csvFormat;


    public function __construct() 
    {
        $this->columnConfigs = array();
        $this->csvFormat = new CsvFormat(';', '"', 'WINDOWS-1252', "\r\n", "\\");
    }
    
    
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
    
    public static function getAvailableFakerLocale()
    {
        return array(
            'bg_BG' => 'Bulgarian - Bulgaria',
            'cs_CZ' => 'Czech - Czech Republic',
            'da_DK' => 'Danish - Denmark',
            'de_AT' => 'German - Austria',
            'de_DE' => 'German - Germany',
            'en_GB' => 'English - United Kingdom',
            'en_US' => 'English - United States',
            'es_AR' => 'Spanish - Argentina',
            'es_ES' => 'Spanish - Spain',
            'fi_FI' => 'Finnish - Finland',
            'fr_FR' => 'French - France',
            'it_IT' => 'Italian - Italy',
            'pl_PL' => 'Polish - Poland',
            'pt_BR' => 'Portuguese - Brazil',
            'ru_RU' => 'Russian - Russia',
            'sk_SK' => 'Slovak - Slovakia',
            'sr_Cyrl_RS' => 'Serbian (Cyrillic) - Serbia',
            'sr_Latn_RS' => 'Serbian (Latin) - Serbia',
            'tr_TR' => 'Turkish - Turkey',
        );
    }
    
    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function hasSeed()
    {
        return $this->seed !== null && $this->seed != '';
    }
    
    public function generateSeed()
    {
        return $this->setSeed(mt_rand(0, 50000));
    }
    
    public function getSeed()
    {
        return $this->seed;
    }

    public function setSeed($seed)
    {
        $this->seed = $seed !== null && $seed != '' ? (int) $seed : null;
        return $this;
    }

    public function getClassName($withoutSlashes = false)
    {
        return $withoutSlashes ? str_ireplace('\\', '_', $this->className) : $this->className;
    }
    
    public function getClassNameLastPart()
    {
        $res = preg_match('/([a-zA-Z0-9]+)$/', $this->className, $matches);
        if ($res) {
            return $matches[1];
        }
        return $this->getClassName(true);
    }

    public function setClassName($className)
    {
        $this->className = preg_replace('/[^a-zA-Z0-9_\\\\]/', '', $className);
        return $this;
    }

    public function addFormat($format) 
    {
        if (in_array($format, array_keys(Dumper::getAvailableFormats())) && !in_array($format, $this->formats)) {
            $this->formats[] = $format;
        }
        return $this;
    }
    
    /**
     * 
     * @param string
     * @return boolean
     */
    public function removeFormat($format) 
    {
        $key = array_search($format, $this->formats, true);

        if ($key !== false) {
            unset($this->formats[$key]);

            return true;
        }

        return false;
    }
    
    /**
     * 
     * @param string $format
     * @return bool
     */
    public function hasFormat($format)
    {
        return in_array($format, $this->formats);
    }
    
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * 
     * @param array $formats
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function setFormats(array $formats)
    {
        $this->formats = $formats;
        return $this;
    }

    public function getFakeNumber()
    {
        return $this->fakeNumber;
    }

    public function setFakeNumber($fakeNumber)
    {
        $this->fakeNumber = (int) $fakeNumber;
        return $this;
    }

    /**
     * 
     * @return \Spyrit\Datalea\Faker\Model\CsvFormat
     */
    public function getCsvFormat()
    {
        return $this->csvFormat;
    }

    /**
     * 
     * @param \Spyrit\Datalea\Faker\Model\CsvFormat $csvFormat
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function setCsvFormat($csvFormat)
    {
        $this->csvFormat = $csvFormat;
        return $this;
    }

    /**
     * create a CSV writer from CSV format options
     * 
     * @return \Spyrit\LightCsv\CsvWriter
     */
    public function createCsvWriter()
    {
        if ($this->csvFormat) {
            $csvWriter = new CsvWriter(
                $this->csvFormat->getDelimiter(),
                $this->csvFormat->getEnclosure(),
                $this->csvFormat->getEncoding(),
                $this->csvFormat->getEol(),
                $this->csvFormat->getEscape(),
                false
            );
        } else {
            $csvWriter = new CsvWriter(';', '"', 'WINDOWS-1252', "\r\n", "\\", false);
        }
        return $csvWriter;
    }

    /**
     * 
     * @param string $name
     * @return ColumnConfig
     */
    public function getColumnConfig($name)
    {
        return isset($this->columnConfigs[$name]) ? $this->columnConfigs[$name] : null ;
    }
    
    public function getColumnConfigs()
    {
        return $this->columnConfigs;
    }

    /**
     * @param array $columnConfigs
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function setColumnConfigs(array $columnConfigs)
    {
        $this->columnConfigs = $columnConfigs;
        return $this;
    }
    
    /**
     * @param \Spyrit\Datalea\Faker\Model\ColumnConfig $columnConfig
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function addColumnConfig(ColumnConfig $columnConfig)
    {
        $name = $columnConfig->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('The column config must have a name.');
        }
        
        $this->columnConfigs[$name] = $columnConfig;
        return $this;
    }
    
    /**
     * 
     * @param \Spyrit\Datalea\Faker\Model\ColumnConfig $columnConfig
     * @return boolean
     */
    public function removeColumnConfig(ColumnConfig $columnConfig) 
    {
        $key = array_search($columnConfig, $this->columnConfigs, true);

        if ($key !== false) {
            unset($this->columnConfigs[$key]);

            return true;
        }

        return false;
    }
    
    public function getVariableConfigs()
    {
        return $this->variableConfigs;
    }

    /**
     * 
     * @param string $name
     * @return VariableConfig
     */
    public function getVariableConfig($name)
    {
        return isset($this->variableConfigs[$name]) ? $this->variableConfigs[$name] : null ;
    }
    
    public function setVariableConfigs($variableConfigs)
    {
        $this->variableConfigs = $variableConfigs;
    }
    
    /**
     * @param \Spyrit\Datalea\Faker\Model\VariableConfig $variableConfig
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function addVariableConfig(VariableConfig $variableConfig)
    {
        $name = $variableConfig->getName();
        if (empty($name)) {
            throw new \InvalidArgumentException('The variable config must have a name.');
        }
        
        $this->variableConfigs[$name] = $variableConfig;
        return $this;
    }
    
    /**
     * 
     * @param \Spyrit\Datalea\Faker\Model\ColumnConfig $columnConfig
     * @return boolean
     */
    public function removeVariableConfig(VariableConfig $variableConfig) 
    {
        $key = array_search($variableConfig, $this->columnConfigs, true);

        if ($key !== false) {
            unset($this->variableConfigs[$key]);

            return true;
        }

        return false;
    }
    
    /**
     * if no column configs, generate a column config for each variable config
     */
    public function generateColumns()
    {
        if (empty($this->columnConfigs) && is_array($this->variableConfigs) && !empty($this->variableConfigs)) {
            foreach ($this->variableConfigs as $variableConfig) {
                $this->addColumnConfig(new ColumnConfig($variableConfig->getName(), $variableConfig->getVarName()));
            }
        }
    }
}
