<?php

namespace Spyrit\Datalea\Faker\Dump;

use \Spyrit\Datalea\Faker\Model\ColumnConfig;
use \Spyrit\Datalea\Faker\Model\Config;
use \Spyrit\Datalea\Faker\Model\CsvFormat;
use \Spyrit\Datalea\Faker\Model\VariableConfig;

/**
 * Loader
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class Loader
{
    /**
     *
     * @param  string $file filename
     * @return Config
     */
    public function loadXmlFakerConfig($file)
    {
        $root = simplexml_load_file($file, '\\Spyrit\\Datalea\\Faker\\Dump\\CdataSimpleXMLElement', LIBXML_NOCDATA);

        $config = new Config();
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

        if (isset($root->formatOptions)) {
            if (isset($root->formatOptions->csv)) {
                $config->setCsvFormat(new CsvFormat(
                    (string) $root->formatOptions->csv->delimiter,
                    (string) $root->formatOptions->csv->enclosure,
                    (string) $root->formatOptions->csv->encoding,
                    (string) $root->formatOptions->csv->eol,
                    (string) $root->formatOptions->csv->escape
                ));
            }
        }

        if (isset($root->variables->variable)) {
            foreach ($root->variables->variable as $variable) {
                $variableConfig = new VariableConfig();
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
                $columnConfig = new ColumnConfig();
                $columnConfig->setName($column['name']);
                $columnConfig->setUnique(!empty($column['unique']));
                $columnConfig->setValue((string) $column->value);
                $columnConfig->setConvertMethod((string) $column->convert);
                $config->addColumnConfig($columnConfig);
            }
        }
        return $config;
    }
}
