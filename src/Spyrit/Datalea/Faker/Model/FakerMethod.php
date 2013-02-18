<?php

namespace Spyrit\Datalea\Faker\Model;

/**
 * FakerMethod
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class FakerMethod
{
    /**
     *
     * @var string
     */
    protected $provider;
    
    /**
     *
     * @var string
     */
    protected $method;
    
    /**
     *
     * @var array
     */
    protected $cultures = array();
    
    /**
     *
     * @var array
     */
    protected $examples = array();
    
    /**
     *
     * @var array
     */
    protected $arguments = array();
    
    /**
     * 
     * @param string $provider
     * @param string $method
     * @param array|string $cultures
     * @param array|string $arguments
     * @param array|string $examples
     */
    public function __construct($provider, $method, $cultures = array(), $arguments = array(), $examples = array())
    {
        $this->setProvider($provider);
        $this->setCultures($cultures);
        $this->setMethod($method);
        $this->setExamples($examples);
        $this->setArguments($arguments);
    }

    /**
     * 
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * 
     * @param string $provider
     * @return \Spyrit\Datalea\Faker\Model\FakerMethod
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getCultures()
    {
        return $this->cultures;
    }
    
    /**
     * 
     * @return array
     */
    public function getCulturesLabels()
    {
        $result = array();
        foreach ($this->cultures as $culture) {
            $result[$culture] = FakerMethodCollection::getCultureLabel($culture);
        }
        return $result;
    }

    /**
     * 
     * @param array|string $cultures
     * @return \Spyrit\Datalea\Faker\Model\FakerMethod
     */
    public function setCultures($cultures)
    {
        $cultures = empty($cultures) ? array() : $cultures;
        $this->cultures = is_array($cultures) ? $cultures : array($cultures);
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * 
     * @param string $method
     * @return \Spyrit\Datalea\Faker\Model\FakerMethod
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getExamples()
    {
        return $this->examples;
    }

    /**
     * 
     * @param array|string $cultures
     * @return \Spyrit\Datalea\Faker\Model\FakerMethod
     */
    public function setExamples($examples)
    {
        $examples = empty($examples) ? array() : $examples;
        $this->examples = is_array($examples) ? $examples : array($examples);
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * 
     * @param array|string $cultures
     * @return \Spyrit\Datalea\Faker\Model\FakerMethod
     */
    public function setArguments($arguments)
    {
        $arguments = empty($arguments) ? array() : $arguments;
        $this->arguments = is_array($arguments) ? $arguments : array($arguments);
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getMethodForSelect()
    {
        $cultures = $this->getCulturesLabels();
        
        $args = array();
        foreach ($this->getArguments() as $arg => $default) {
            $args[] = $arg.' = '.$default;
        }
        $result = $this->getMethod().
            (!empty($args) ? '('.implode(', ', $args).')' : '').
            (!empty($this->examples) ? ' // '.implode(', ', $this->examples).'' : '').
            (!empty($cultures) ? ' // ('.implode(', ', $cultures).')' : '')
            ;        
        return $result;
    }
}