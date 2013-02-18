<?php

namespace Spyrit\Datalea\Faker\Model;

/**
 * CsvFormat
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class CsvFormat
{
    /**
     *
     * @var string
     */
    protected $eol;

    /**
     *
     * @var string
     */
    protected $encoding;

    /**
     *
     * @var string
     */
    protected $enclosure;

    /**
     *
     * @var string
     */
    protected $escape;

    /**
     *
     * @var string
     */
    protected $delimiter;

    /**
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $encoding
     * @param string $eol
     * @param string $escape
     */
    public function __construct($delimiter = ';', $enclosure = '"', $encoding = 'WINDOWS-1252', $eol = 'windows', $escape = "\\")
    {
        $this->setDelimiter($delimiter);
        $this->setEnclosure($enclosure);
        $this->setEncoding($encoding);
        $this->setEol($eol);
        $this->setEscape($escape);
    }

    public static function getAvailableEols()
    {
        return array(
            'windows' => 'windows',
            'unix' => 'unix',
        );
    }

    public static function getAvailableEncodings()
    {
        return array(
            'WINDOWS-1252' => 'WINDOWS-1252',
            'ISO_8859-1' => 'ISO_8859-1',
            'ISO_8859-15' => 'ISO_8859-15',
            'UTF-8' => 'UTF-8',
        );
    }

    public function getEol()
    {
        return $this->eol;
    }

    public function setEol($eol)
    {
        $this->eol = in_array($eol, self::getAvailableEols()) ? $eol : 'windows' ;

        return $this;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = in_array($encoding, self::getAvailableEncodings()) ? $encoding : 'WINDOWS-1252' ;
        $this->encoding = $encoding;

        return $this;
    }

    public function getEnclosure()
    {
        return $this->enclosure;
    }

    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function getEscape()
    {
        return $this->escape;
    }

    public function setEscape($escape)
    {
        $this->escape = $escape;

        return $this;
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }
}
