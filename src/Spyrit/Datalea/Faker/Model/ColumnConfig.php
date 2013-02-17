<?php

namespace Spyrit\Datalea\Faker\Model;

/**
 * ColumnConfig
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class ColumnConfig
{
    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $value;

    /**
     *
     * @var string
     */
    protected $convertMethod;

    /**
     *
     * @var bool
     */
    protected $unique;
    
    /**
     *
     * @var array
     */
    protected $usedVariables = array();
    
    /**
     * 
     * @param string $name default = null
     * @param string $value default = null
     * @param string $convertMethod default = null
     * @param bool $unique default = false
     */
    public function __construct($name = null, $value = null, $convertMethod = null, $unique = false)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setConvertMethod($convertMethod);
        $this->setUnique($unique);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = preg_replace('/[^a-zA-Z0-9\-\s_]/', '', $name);
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    public function getConvertMethod()
    {
        return $this->convertMethod;
    }

    public function setConvertMethod($convertMethod)
    {
        $this->convertMethod = (string) $convertMethod;
        $this->usedVariables = array();
        return $this;
    }

    public function getUnique()
    {
        return $this->unique;
    }

    public function setUnique($unique)
    {
        $this->unique = (bool) $unique;
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public static function getAvailableConvertMethods()
    {
        return array(
            'lowercase' => 'lowercase',
            'uppercase' => 'uppercase',
            'capitalize' => 'capitalize',
            'capitalize_words' => 'capitalize words',
            'absolute' => 'absolute',
            'as_bool' => 'as boolean',
            'as_int' => 'as integer',
            'as_float' => 'as floating number',
            'as_string' => 'as string',
            'remove_accents' => 'remove accents',
            'remove_accents_lowercase' => 'remove accents and lowercase',
            'remove_accents_uppercase' => 'remove accents and uppercase',
            'remove_accents_capitalize' => 'remove accents and capitalize',
            'remove_accents_capitalize_words' => 'remove accents and capitalize words',
        );
    }
    
    /**
     * 
     * @return array
     */
    public function getUsedVariables()
    {
        if (empty($this->usedVariables)) {
            if (preg_match_all('/%([a-zA-Z0-9_]+)%/', $this->getValue(), $matches, PREG_PATTERN_ORDER)) {
                if (isset($matches[1])) {
                    $this->usedVariables = $matches[1];
                }
            }
        }
        return $this->usedVariables;
    }
    
    /**
     * 
     * @param array $variableConfigs
     * @return \Spyrit\Datalea\Faker\Model\UniqueTuple
     */
    public function createUniqueTuple(array $variableConfigs = array())
    {
        $usedVariables = $this->getUsedVariables();
        if ($this->getUnique() && !empty($usedVariables)) {
            $usedVariableConfigs = array();
            foreach ($variableConfigs as $variableConfig) {
                if (in_array($variableConfig->getName(), $usedVariables)) {
                    $usedVariableConfigs[$variableConfig->getName()] = $variableConfig;
                }
            }
            
            if(!empty($usedVariableConfigs)) {
                return new UniqueTuple($this, $usedVariableConfigs);
            }
        }
    }
    
    /**
     * 
     * @param \Faker\Generator $faker
     * @param array $values generated value will be inserted into this array
     * @param array $variableConfigs other variable configs to be replaced in faker method arguments if used
     * @param array $uniqueColumns already generated columns which can not be generated again
     * @param array $uniqueValues already generated values which can not be generated again
     */
    public function generateValues(\Faker\Generator $faker, array &$values, array $variableConfigs = array(), array &$uniqueColumns, array &$uniqueValues)
    {
        $usedVariables = $this->getUsedVariables();
        
        if ($this->getUnique() && !empty($usedVariables)) {
            if (!isset($uniqueColumns[$this->getName()])) {
                $uniqueColumns[$this->getName()] = array();
            }

            $try = 0;
            $inc = 0;
            do {
                $columnValues = array();
                foreach ($usedVariables as $usedVariable) {
                    if (isset($variableConfigs[$usedVariable])) {
                        $variableConfigs[$usedVariable]->generateValue($faker, $values, $variableConfigs, $uniqueValues, true);
                        $columnValues[$usedVariable] = $values[$usedVariable];
                    }
                }
                
                $column = implode('-', $columnValues);
                $try++;
                if ($try > 10 )
                {
                    $inc++;
                    $column = is_numeric($column) ? $column+1 : $column.'_'.$inc;
                }
            } while (in_array($column, $uniqueColumns[$this->getName()]));

            $uniqueColumns[$this->getName()][] = $column;
        } else {
            foreach ($usedVariables as $usedVariable) {
                if (isset($variableConfigs[$usedVariable])) {
                    $variableConfigs[$usedVariable]->generateValue($faker, $values, $variableConfigs, $uniqueValues, false);
                }
            }
        }
        
        var_dump($columnValue);
    }
    
    /**
     * 
     * @param array $availableVariables
     * @return string
     */
    public function replaceVariable(array $availableVariables)
    {
        $value = preg_replace_callback('/%([a-zA-Z0-9_]+)%/',
            function($matches) use ($availableVariables) {
                return isset($availableVariables[$matches[1]]) ? $availableVariables[$matches[1]] : $matches[0];
            },
            $this->getValue()
        );
           
        switch ($this->getConvertMethod()) {
            case 'lowercase':
                $value = $this->tolower($value, 'UTF-8');
                break;
            case 'uppercase':
                $value = $this->toupper($value, 'UTF-8');
                break;
            case 'capitalize':
                $value = $this->ucfirst($value);
                break;
            case 'capitalize_words':
                $value = $this->ucwords($value);
                break;
            case 'absolute':
                $value = abs($value);
                break;
            case 'remove_accents':
                $value = $this->removeAccents($value);
                break;
            case 'remove_accents_lowercase':
                $value = $this->tolower($this->removeAccents($value), 'UTF-8');
                break;
            case 'remove_accents_uppercase':
                $value = $this->toupper($this->removeAccents($value), 'UTF-8');
                break;
            case 'remove_accents_capitalize':
                $value = $this->ucfirst($this->removeAccents($value));
                break;
            case 'remove_accents_capitalize_words':
                $value = $this->ucwords($this->removeAccents($value));
                break;
            case 'as_bool':
                $value = (bool) $value;
                break;
            case 'as_int':
                $value = (int) $value;
                break;
            case 'as_float':
                $value = (float) $value;
                break;
            case 'as_string':
                $value = (string) $value;
                break;
            default:
                break;
        }
            
        return $value;
    }
    
    protected function tolower($str)
    {
        return mb_strtolower($str, 'UTF-8');
    }
    
    protected function toupper($str)
    {
        return mb_strtoupper($str, 'UTF-8');
    }
    
    protected function ucwords($str)
    {
        return  mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    }
    
    protected function ucfirst($str)
    {
        $length = mb_strlen($str);
        if ($length > 1) {
            $first = mb_substr($str, 0, 1, 'UTF-8');
            $rest = mb_substr($str, 1, $length, 'UTF-8');
            return  mb_strtoupper($first, 'UTF-8').$rest;
        } else {
            return  mb_strtoupper($str, 'UTF-8');
        }
    }


    /**
     * replace accent character by normal character
     *
     * @param string $string
     * @param string $charset default = 'UTF-8'
     *
     * @return string
     */
    protected function removeAccents($string, $charset='UTF-8')
    {
        $string = htmlentities($string, ENT_NOQUOTES, $charset);
        $string = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $string);
        $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string); // for ligatures e.g. '&oelig;'
        $string = html_entity_decode($string,ENT_NOQUOTES , $charset);

        return $string;
    }
}