<?php

namespace Spyrit\Datalea\Faker\Model;

/**
 * FakerMethod
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class FakerMethod
{
    protected $provider;
    
    protected $cultures = array();
    
    protected $method;
    
    protected $examples = array();
    
    protected $arguments = array();
    
    public function __construct($provider, $method, $cultures = array(), $arguments = array(), $examples = array())
    {
        $this->setProvider($provider);
        $this->setCultures($cultures);
        $this->setMethod($method);
        $this->setExamples($examples);
        $this->setArguments($arguments);
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    public function getCultures()
    {
        return $this->cultures;
    }

    public function setCultures($cultures)
    {
        $this->cultures = is_array($cultures) ? $cultures : array($cultures);
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function getExamples()
    {
        return $this->examples;
    }

    public function setExamples($examples)
    {
        $this->examples = is_array($examples) ? $examples : array($examples);
        return $this;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setArguments($arguments)
    {
        $this->arguments = is_array($arguments) ? $arguments : array($arguments);
        return $this;
    }
}