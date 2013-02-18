<?php

namespace Spyrit\Datalea\Faker\Model;

use Faker\Generator;

/**
 * VariableConfig
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class VariableConfig
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
    protected $fakerMethod;

    /**
     *
     * @var mixed
     */
    protected $fakerMethodArg1;

    /**
     *
     * @var mixed
     */
    protected $fakerMethodArg2;

    /**
     *
     * @var mixed
     */
    protected $fakerMethodArg3;

    /**
     *
     * @var int
     */
    protected $increment = 0;

    /**
     *
     * @return array
     */
    public static function getAvailableFakerMethods()
    {
        return array(
            'Person' => array(
                'prefix' => 'prefix',
                'suffix' => 'suffix',
                'name' => 'name',
                'firstName' => 'firstName',
                'lastName' => 'lastName',
                'cpr' => 'cpr',
            ),
            'Address' => array(
                'cityPrefix' => 'cityPrefix',
                'secondaryAddress' => 'secondaryAddress',
                'state' => 'state',
                'stateAbbr' => 'stateAbbr',
                'citySuffix' => 'citySuffix',
                'streetSuffix' => 'streetSuffix',
                'buildingNumber' => 'buildingNumber',
                'city' => 'city',
                'streetName' => 'streetName',
                'streetAddress' => 'streetAddress',
                'postcode' => 'postcode',
                'address' => 'address',
                'country' => 'country',
                'latitude' => 'latitude',
                'longitude' => 'longitude',
                'departmentName' => 'departmentName',
                'departmentNumber' => 'departmentNumber',
                'department' => 'department',
                'region' => 'region',
                'kommune' => 'kommune',
            ),
            'PhoneNumber' => array(
                'phoneNumber' => 'phoneNumber',
            ),
            'Company' => array(
                'catchPhrase' => 'catchPhrase',
                'bs' => 'bs',
                'company' => 'company',
                'companySuffix' => 'companySuffix',
                'siren' => 'siren',
                'siret' => 'siret(sequential_digits = 2)',
                'cvr' => 'cvr',
                'p' => 'p',
            ),
            'Lorem' => array(
                'word' => 'word',
                'words' => 'words($nb = 3)',
                'sentence' => 'sentence(nbWords = 6)',
                'sentences' => 'sentences(nb = 3)',
                'paragraph' => 'paragraph(nbSentences = 3)',
                'paragraphs' => 'paragraphs(nb = 3)',
                'text' => 'text($maxNbChars = 200)',
            ),
            'Internet' => array(
                'email' => 'email',
                'safeEmail' => 'safeEmail',
                'freeEmail' => 'freeEmail',
                'companyEmail' => 'companyEmail',
                'freeEmailDomain' => 'freeEmailDomain',
                'safeEmailDomain' => 'safeEmailDomain',
                'userName' => 'userName',
                'domainName' => 'domainName',
                'domainWord' => 'domainWord',
                'tld' => 'tld',
                'url' => 'url',
                'ipv4' => 'ipv4',
                'ipv6' => 'ipv6',
            ),
            'DateTime' => array(
                'unixTime' => 'unixTime',
                'dateTime' => 'dateTime(format = \'Y-m-d H:i:s\')',
                'dateTimeAD' => 'dateTimeAD(format = \'Y-m-d H:i:s\')',
                'iso8601' => 'iso8601',
                'date' => 'date(format = \'Y-m-d\')',
                'time' => 'time(format = \'H:i:s\')',
                'dateTimeBetween' => 'dateTimeBetween(startDate = \'-30 years\', endDate = \'now\', format = \'Y-m-d H:i:s\')',
                'dateTimeThisCentury' => 'dateTimeThisCentury(format = \'Y-m-d H:i:s\')',
                'dateTimeThisDecade' => 'dateTimeThisDecade(format = \'Y-m-d H:i:s\')',
                'dateTimeThisYear' => 'dateTimeThisYear(format = \'Y-m-d H:i:s\')',
                'dateTimeThisMonth' => 'dateTimeThisMonth(format = \'Y-m-d H:i:s\')',
                'amPm' => 'amPm',
                'dayOfMonth' => 'dayOfMonth',
                'dayOfWeek' => 'dayOfWeek',
                'month' => 'month',
                'monthName' => 'monthName',
                'year' => 'year',
                'century' => 'century',
                'timezone' => 'timezone',
            ),
            'Miscellaneous' => array(
                'boolean' => 'boolean(chanceOfGettingTrue = 50)',
                'md5' => 'md5',
                'sha1' => 'sha1',
                'sha256' => 'sha256',
                'locale' => 'locale',
                'countryCode' => 'countryCode',
                'languageCode' => 'languageCode',
            ),
            'Base' => array(
                'randomDigit' => 'randomDigit',
                'randomNumber' => 'randomNumber(nbDigits = NULL)',
                'randomLetter' => 'randomLetter',
                'randomElement' => 'randomElement(array = comma separated list values \'a, b,c\')',
                'numerify' => 'numerify(string = \'###\')',
                'lexify' => 'lexify(string = \'????\')',
                'bothify' => 'bothify(string = \'## ??\')',
            ),
            'UserAgent' => array(
                'userAgent' => 'userAgent',
                'chrome' => 'chrome',
                'firefox' => 'firefox',
                'safari' => 'safari',
                'opera' => 'opera',
                'internetExplorer' => 'internetExplorer',
            ),
            'Uuid' => array(
                'uuid' => 'uuid',
            ),
        );
    }

    /**
     *
     * @param string $name            default = null
     * @param string $fakerMethod     default = null
     * @param string $fakerMethodArg1 default = null
     * @param string $fakerMethodArg2 default = null
     */
    public function __construct($name = null, $fakerMethod = null, $fakerMethodArg1 = null, $fakerMethodArg2 = null)
    {
        $this->setName($name);
        $this->setFakerMethod($fakerMethod);
        $this->setFakerMethodArg1($fakerMethodArg1);
        $this->setFakerMethodArg2($fakerMethodArg2);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVarName()
    {
        return '%'.$this->name.'%';
    }

    public function setName($name)
    {
        $this->name = preg_replace('/[^a-zA-Z0-9\-\s_]/', '', $name);

        return $this;
    }

    public function getFakerMethod()
    {
        return $this->fakerMethod;
    }

    public function setFakerMethod($fakerMethod)
    {
        $this->fakerMethod = $fakerMethod;

        return $this;
    }

    public function hasFakerMethodArg1()
    {
        return $this->getFakerMethodArg1() !== null && $this->getFakerMethodArg1() !== '';
    }

    public function getFakerMethodArg1()
    {
        return $this->fakerMethodArg1;
    }

    public function setFakerMethodArg1($fakerMethodArg)
    {
        $this->fakerMethodArg1 = $fakerMethodArg;

        return $this;
    }

    public function hasFakerMethodArg2()
    {
        return $this->getFakerMethodArg2() !== null && $this->getFakerMethodArg2() !== '';
    }

    public function getFakerMethodArg2()
    {
        return $this->fakerMethodArg2;
    }

    public function setFakerMethodArg2($fakerMethodArg)
    {
        $this->fakerMethodArg2 = $fakerMethodArg;

        return $this;
    }

    public function hasFakerMethodArg3()
    {
        return $this->getFakerMethodArg3() !== null && $this->getFakerMethodArg3() !== '';
    }

    public function getFakerMethodArg3()
    {
        return $this->fakerMethodArg3;
    }

    public function setFakerMethodArg3($fakerMethodArg)
    {
        $this->fakerMethodArg3 = $fakerMethodArg;

        return $this;
    }

    /**
     *
     * @param \Faker\Generator $faker
     * @param array            $values          generated value will be inserted into this array
     * @param array            $variableConfigs other variable configs to be replaced in faker method arguments if used
     * @param bool             $force           force generating value even if it already exists
     * @param bool             $useIncrement    use increment suffix or add increment
     * @param bool             $resetIncrement  reset current variable increment
     */
    public function generateValue(Generator $faker, array &$values, array $variableConfigs = array(), $force = false, $useIncrement = false, $resetIncrement = false)
    {
        if ($resetIncrement) {
            $this->increment = 0;
        }

        if (!isset($values[$this->getName()]) || $force) {
            $value = $this->generate($faker, $values, $variableConfigs);
            if ($useIncrement) {
                $this->increment++;
                $value = is_numeric($value) ? $value+1 : $value.'_'.$this->increment;
            }
            $values[$this->getName()] = $value;
        }
    }

    /**
     *
     * @param  \Faker\Generator $faker
     * @param  array            $values          generated value will be inserted into this array
     * @param  array            $variableConfigs other variable configs to be replaced in faker method arguments if used
     * @return string
     */
    protected function generate(Generator $faker, array &$values, array $variableConfigs = array())
    {
        $method = $this->getFakerMethod();

        $args = array();

        switch ($method) {
            case 'randomElement':
                if ($this->hasFakerMethodArg1()) {
                    $args[] = array_map(
                        'trim',
                        explode(
                            ',',
                            $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs)
                        )
                    );

                    if ($this->hasFakerMethodArg2()) {
                        $args[] = $this->replaceVariables($this->getFakerMethodArg2(), $faker, $values, $variableConfigs);
                        if ($this->hasFakerMethodArg3()) {
                            $args[] = $this->replaceVariables($this->getFakerMethodArg3(), $faker, $values, $variableConfigs);
                        }
                    }
                }

                $value = call_user_func_array(array($faker, $method), $args);
                break;

            case 'dateTime':
            case 'dateTimeAD':
            case 'dateTimeThisCentury':
            case 'dateTimeThisDecade':
            case 'dateTimeThisYear':
            case 'dateTimeThisMonth':
                if ($this->hasFakerMethodArg1()) {
                    $format = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                }

                $format = empty($format) ? 'Y-m-d H:i:s' : $format;
                $datetime = call_user_func_array(array($faker, $method), $args);
                $value = $datetime->format($format);
                break;

            case 'dateTimeBetween':
                if ($this->hasFakerMethodArg1()) {
                    $args[] = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                    if ($this->hasFakerMethodArg2()) {
                        $args[] = $this->replaceVariables($this->getFakerMethodArg2(), $faker, $values, $variableConfigs);
                        if ($this->hasFakerMethodArg3()) {
                            $format = $this->replaceVariables($this->getFakerMethodArg3(), $faker, $values, $variableConfigs);
                        }
                    }
                }

                $format = empty($format) ? 'Y-m-d H:i:s' : $format;
                $datetime = call_user_func_array(array($faker, $method), $args);
                $value = $datetime->format($format);
                break;

            case 'words':
                if ($this->hasFakerMethodArg1()) {
                    $args[] = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                }

                $value = implode(' ', call_user_func_array(array($faker, $method), $args));
                break;
            case 'sentences':
            case 'paragraphs':
                if ($this->hasFakerMethodArg1()) {
                    $args[] = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                }

                $value = implode("\n", call_user_func_array(array($faker, $method), $args));
                break;

            default:
                if ($this->hasFakerMethodArg1()) {
                    $args[] = $this->replaceVariables($this->getFakerMethodArg1(), $faker, $values, $variableConfigs);
                    if ($this->hasFakerMethodArg2()) {
                        $args[] = $this->replaceVariables($this->getFakerMethodArg2(), $faker, $values, $variableConfigs);
                        if ($this->hasFakerMethodArg3()) {
                            $args[] = $this->replaceVariables($this->getFakerMethodArg3(), $faker, $values, $variableConfigs);
                        }
                    }
                }

                try {
                    $value = call_user_func_array(array($faker, $method), $args);
                } catch (\InvalidArgumentException $e) {
                    // if the method doesn't exists in Faker set an empty string as value
                    $value = '';
                }
                break;
        }

        return $value;
    }

    /**
     * replace variable in faker method arguments
     *
     * @param  string           $str
     * @param  \Faker\Generator $faker
     * @param  array            $values
     * @param  array            $variableConfigs
     * @return string
     */
    protected function replaceVariables($str, Generator $faker, array &$values, array $variableConfigs = array())
    {
        return preg_replace_callback('/%([a-zA-Z0-9_]+)%/',
            function($matches) use (&$values) {
                if (!isset($values[$matches[1]]) && isset($variableConfigs[$matches[1]])) {
                    $variableConfigs[$matches[1]]->generateValue($faker, $values, $variableConfigs);
                }

                return isset($values[$matches[1]]) ? $values[$matches[1]] : $matches[0];
            },
            $str
        );
    }
}
