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
     * @param string $name default = null
     * @param string $value default = null
     * @param string $convertMethod default = null
     */
    public function __construct($name = null, $value = null, $convertMethod = null)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setConvertMethod($convertMethod);
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
        $this->convertMethod = $convertMethod;
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
                $value = strtolower($value);
                break;
            case 'uppercase':
                $value = strtoupper($value);
                break;
            case 'capitalize':
                $value = ucfirst($value);
                break;
            case 'capitalize_words':
                $value = ucwords($value);
                break;
            case 'absolute':
                $value = abs($value);
                break;
            case 'remove_accents':
                $value = $this->removeAccents($value);
                break;
            case 'remove_accents_lowercase':
                $value = strtolower($this->removeAccents($value));
                break;
            case 'remove_accents_uppercase':
                $value = strtoupper($this->removeAccents($value));
                break;
            case 'remove_accents_capitalize':
                $value = ucfirst($this->removeAccents($value));
                break;
            case 'remove_accents_capitalize_words':
                $value = ucwords($this->removeAccents($value));
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
    
    /**
     * replace accent character by normal character
     *
     * @param string $string
     * @param string $charset default = 'utf-8'
     *
     * @return string
     */
    protected function removeAccents($string, $charset='utf-8')
    {
        $string = htmlentities($string, ENT_NOQUOTES, $charset);
        $string = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $string);
        $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string); // pour les ligatures e.g. '&oelig;'
        $string = html_entity_decode($string,ENT_NOQUOTES , $charset);

        return $string;
    }
}