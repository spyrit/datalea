<?php

namespace Spyrit\Datalea\Faker\Model;

use Faker\Generator;

/**
 * UniqueTupleCollection
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class UniqueTupleCollection
{
    /**
     *
     * @var array of \Spyrit\Datalea\Faker\Model\UniqueTuple 
     */
    protected $uniqueTuples = array();
    
    public function __construct(array $uniqueTuples = array())
    {
        $this->uniqueTuples = $uniqueTuples;
    }

    public function getUniqueTuples()
    {
        return $this->uniqueTuples;
    }

    public function setUniqueTuples($uniqueTuples)
    {
        $this->uniqueTuples = $uniqueTuples;
        return $this;
    }
    
    public function addUniqueTuple(UniqueTuple $uniqueTuple)
    {
        $this->uniqueTuples[$uniqueTuple->getColumnConfig()->getName()] = $uniqueTuple;
        return $this;
    }
    
    public function hasUniqueTuple($name)
    {
        return isset($this->uniqueTuples[$name]);
    }
    
    public function getUniqueTuple($name)
    {
        return $this->hasUniqueTuple($name) ? $this->uniqueTuples[$name] : null;
    }
    
    /**
     * 
     * @param \Faker\Generator $faker
     * @param array $values
     * @param array $variableConfigs
     */
    public function unDuplicateValues(Generator $faker, array &$values, array $variableConfigs) 
    {
        $try = 0;
        do {
            $try++;
            $allUniques = true;
            
            $uniqueTuplesToRegenerate = array();
            foreach ($this->uniqueTuples as $name => $uniqueTuple) {
                if (!$uniqueTuple->areValuesUniques($values)) {
                    $uniqueTuplesToRegenerate[$name] = $uniqueTuple;
                    $allUniques = false;
                }
            }

            $variableConfigsToRegenerate = array();
            foreach ($uniqueTuplesToRegenerate as $name => $uniqueTuple) {
                foreach ($uniqueTuple->getVariableConfigs() as $name => $variableConfig) {
                    $variableConfigsToRegenerate[$name] = $variableConfig;
                }
            }

            $useIncrement = $try > 10;
            
            foreach ($variableConfigsToRegenerate as $name => $variableConfig) {
                $variableConfig->generateValue($faker, $values, $variableConfigs, true, $useIncrement, false);
            }
        } while (!$allUniques);
    }
    
}