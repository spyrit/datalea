<?php

namespace Spyrit\Datalea\Faker\Dump;

/**
 * Loader
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class Loader
{
    /**
     * 
     * @param string $file filename
     * @return \Spyrit\Datalea\Faker\Model\Config
     */
    public function loadXmlFakerConfig($file)
    {
        $root = simplexml_load_file($file, '\\Spyrit\\Datalea\\Faker\\Dump\\FakerSimpleXMLElement', LIBXML_NOCDATA);
        
        $config = new \Spyrit\Datalea\Faker\Model\Config();
        if (isset($root['classname'])) {
            $config->setClassName((string) $root['classname']);
        }
        if (isset($root['fakenumber'])) {
            $config->setFakeNumber((string) $root['fakenumber']);
        }
        if (isset($root['locale'])) {
            $config->setLocale((string) $root['locale']);
        }
        if (isset($root['seed'])) {
            $config->setSeed((string) $root['seed']);
        }        
        
        if (isset($root->formats->format)) {
            $config->setFormats((array) $root->formats->format);
        }
        
        if (isset($root->variables->variable)) {
            foreach ($root->variables->variable as $variable) {
                $variableConfig = new \Spyrit\Datalea\Faker\Model\VariableConfig();
                $variableConfig->setName($variable['name']);
                $variableConfig->setFakerMethod((string) $variable->method);
                $variableConfig->setFakerMethodArg1((string) $variable->argument1);
                $variableConfig->setFakerMethodArg2((string) $variable->argument2);
                $variableConfig->setFakerMethodArg3((string) $variable->argument3);
                $config->addVariableConfig($variableConfig);
            }
        }
        
        if (isset($root->columns->column)) {
            foreach ($root->columns->column as $column) {
                $columnConfig = new \Spyrit\Datalea\Faker\Model\ColumnConfig();
                $columnConfig->setName($column['name']);
                $columnConfig->setValue((string) $column->value);
                $columnConfig->setConvertMethod((string) $column->convert);
                $config->addColumnConfig($columnConfig);
            }
        }
        
        return $config;
    }
}