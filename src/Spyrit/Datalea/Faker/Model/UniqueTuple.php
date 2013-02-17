<?php

namespace Spyrit\Datalea\Faker\Model;

use Spyrit\Datalea\Faker\Model\ColumnConfig;
use Spyrit\Datalea\Faker\Model\UniqueTuple;

/**
 * UniqueTuple
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class UniqueTuple
{
    /**
     *
     * @var ColumnConfig
     */
    protected $columnConfig;
    
    /**
     *
     * @var array of \Spyrit\Datalea\Faker\Model\VariableConfig
     */
    protected $variableConfigs;
    
    /**
     *
     * @var array
     */
    protected $values = array();

    /**
     * 
     * @param ColumnConfig $columnConfig
     * @param array $variableConfigs
     */
    public function __construct(ColumnConfig $columnConfig, array $variableConfigs = array())
    {
        $this->setColumnConfig($columnConfig);
        $this->setVariableConfigs($variableConfigs);
    }
    
    /**
     * 
     * @return ColumnConfig
     */
    public function getColumnConfig()
    {
        return $this->columnConfig;
    }

    /**
     * 
     * @param ColumnConfig $columnConfig
     * @return UniqueTuple
     */
    public function setColumnConfig(ColumnConfig $columnConfig)
    {
        $this->columnConfig = $columnConfig;
        return $this;
    }

    /**
     * 
     * @return array of \Spyrit\Datalea\Faker\Model\VariableConfig
     */
    public function getVariableConfigs()
    {
        return $this->variableConfigs;
    }

    /**
     * 
     * @param array of \Spyrit\Datalea\Faker\Model\VariableConfig $variableConfigs
     * @return UniqueTuple
     */
    public function setVariableConfigs(array $variableConfigs)
    {
        $this->variableConfigs = $variableConfigs;
        return $this;
    }

    /**
     * 
     * @param array $values
     * @return boolean
     */
    public function areValuesUniques(array $values) 
    {
        $valuesToCheck = array();
        foreach ($this->variableConfigs as $variableConfig) {
            if (isset($values[$variableConfig->getName()])) {
                $valuesToCheck[] = $values[$variableConfig->getName()];
            }
        }
        
        $valuesToCheckString = implode('-', $valuesToCheck);
        if (!in_array($valuesToCheckString, $this->values)) {
            $this->values[] = $valuesToCheckString;
            return true;
        } else {
            return false;
        }
    }
}