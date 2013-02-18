<?php

namespace Spyrit\Datalea\Faker\Model;

/**
 * FakerMethodCollection
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class FakerMethodCollection
{
    /**
     *
     * @var array of \Spyrit\Datalea\Faker\Model\FakerMethod
     */
    protected $fakerMethods = array();
    
    /**
     * 
     * @return array of \Spyrit\Datalea\Faker\Model\FakerMethod
     */
    public function getFakerMethods()
    {
        return $this->fakerMethod;
    }

    /**
     * 
     * @param array $fakerMethods
     * @return \Spyrit\Datalea\Faker\Model\FakerMethodCollection
     */
    public function setFakerMethods($fakerMethods)
    {
        $this->fakerMethods = $fakerMethods;
        return $this;
    }
    
    /**
     * 
     * @param string $method
     * @return \Spyrit\Datalea\Faker\Model\FakerMethod
     */
    public function getFakerMethod($method)
    {
        return isset($this->fakerMethods[$method]) ? $this->fakerMethods[$method] : null;
    }
    
    /**
     * 
     * @param \Spyrit\Datalea\Faker\Model\FakerMethod $fakerMethod
     * @return \Spyrit\Datalea\Faker\Model\FakerMethodCollection
     */
    public function addFakerMethod(FakerMethod $fakerMethod)
    {
        $this->fakerMethods[$fakerMethod->getMethod()] = $fakerMethod;
        return $this;
    }

    /**
     * 
     * @param string $method
     * @return bool
     */
    public function hasFakerMethod($method)
    {
        return isset($this->fakerMethods[$method]);
    }
    
    /**
     *
     * @return array
     */
    public function getFakerMethodsForSelect()
    {
        $result = array();
        foreach ($this->fakerMethods as $fakerMethod) {
            if (!isset($result[$fakerMethod->getProvider()])) {
                $result[$fakerMethod->getProvider()] = array();
            }
            $args = '';
            
            foreach ($fakerMethod->getArguments() as $arg => $default) {
                $args .= $arg.' = '.$default;
            }
            
            $result[$fakerMethod->getProvider()][$fakerMethod->getMethod()] = $fakerMethod->getMethod().(!empty($args) ? '('.$args.')' : '');
        }
        
        return $result;
    }
    
    /**
     * 
     * @return array
     */
    public static function getAvailableFakerMethodsForSelect() 
    {
        $collection = static::createDefaultCollection();
        return $collection->getFakerMethodsForSelect();
    }
    
    
    /**
     * 
     * @return \Spyrit\Datalea\Faker\Model\FakerMethodCollection
     */
    public static function createDefaultCollection() 
    {
        $methods = array(
            //Person
            array(
                'provider' => 'Person', 
                'method' => 'prefix', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Ms.\''),
            ),
            array(
                'provider' => 'Person', 
                'method' => 'suffix', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Jr.\''),
            ),
            array(
                'provider' => 'Person', 
                'method' => 'name', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Dr. Zane Stroman\''),
            ),
            array(
                'provider' => 'Person', 
                'method' => 'firstName', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Maynard\''),
            ),
            array(
                'provider' => 'Person', 
                'method' => 'lastName', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Zulauf\''),
            ),
            array(
                'provider' => 'Person', 
                'method' => 'cpr', 
                'culture' => 'da_DK', 
                'arguments' => array(), 
                'examples'=> array('\'051280-2387\''),
            ),
            //Address
            array(
                'provider' => 'Address', 
                'method' => 'cityPrefix', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Lake\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'secondaryAddress', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Suite 961\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'state', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'NewMexico\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'stateAbbr', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'OH\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'citySuffix', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'borough\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'streetSuffix', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Keys\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'buildingNumber', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'484\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'city', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'West Judge\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'streetName', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Keegan Trail\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'streetAddress', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'439 Karley Loaf Suite 897\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'postcode', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'17916\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'address', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'8888 Cummings Vista Apt. 101, Susanbury, NY 95473\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'country', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Falkland Islands (Malvinas)\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'latitude', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'77.147489\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'longitude', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'86.211205\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'departmentName', 
                'culture' => 'fr_FR', 
                'arguments' => array(), 
                'examples'=> array('\'Haut-Rhin\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'departmentNumber', 
                'culture' => 'fr_FR', 
                'arguments' => array(), 
                'examples'=> array('\'2B\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'department', 
                'culture' => 'fr_FR', 
                'arguments' => array(), 
                'examples'=> array('array(\'18\' => \'Cher\')'),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'region', 
                'culture' => array('da_DK', 'fr_FR'), 
                'arguments' => array(), 
                'examples'=> array('\'Region Sjælland\'', '\'Saint-Pierre-et-Miquelon\''),
            ),
            array(
                'provider' => 'Address', 
                'method' => 'kommune', 
                'culture' => 'da_DK', 
                'arguments' => array(), 
                'examples'=> array('\'Frederiksberg\''),
            ),
            //PhoneNumber
            array(
                'provider' => 'PhoneNumber', 
                'method' => 'phoneNumber', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'132-149-0269x3767\''),
            ),
            //Company
            array(
                'provider' => 'Company', 
                'method' => 'catchPhrase', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Monitored regional contingency\''),
            ),
            array(
                'provider' => 'Company', 
                'method' => 'bs', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'e-enable robust architectures\''),
            ),
            array(
                'provider' => 'Company', 
                'method' => 'company', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Bogan-Treutel\''),
            ),
            array(
                'provider' => 'Company', 
                'method' => 'companySuffix', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'and Sons\''),
            ),
            array(
                'provider' => 'Company', 
                'method' => 'siren', 
                'culture' => 'fr_FR', 
                'arguments' => array(), 
                'examples'=> array('\'082 250 104\''),
            ),
            array(
                'provider' => 'Company', 'method' => 'siret', 
                'culture' => 'fr_FR', 
                'arguments' => array('sequential_digits' => 2), 
                'examples'=> array('\'347 355 708 00224\'', '\'438 472 611 01513\'' ),
            ),
            array(
                'provider' => 'Company', 
                'method' => 'cvr', 
                'culture' => 'da_DK', 
                'arguments' => array(), 
                'examples'=> array('\'32458723\''),
            ),
            array(
                'provider' => 'Company', 
                'method' => 'p', 
                'culture' => 'da_DK', 
                'arguments' => array(), 
                'examples'=> array('\'5398237590\''),
            ),
            //Lorem
            array(
                'provider' => 'Lorem', 
                'method' => 'word', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'aut\''),
            ),
            array(
                'provider' => 'Lorem', 'method' => 'words', 
                'culture' => '', 
                'arguments' => array('nb' => 3), 
                'examples'=> array('"porro\nsed\nmagni"'),
            ),
            array(
                'provider' => 'Lorem', 'method' => 'sentence', 
                'culture' => '', 
                'arguments' => array('nbWords' => 6), 
                'examples'=> array('\'Sit vitae voluptas sint non voluptates.\''),
            ),
            array(
                'provider' => 'Lorem', 'method' => 'sentences', 
                'culture' => '', 
                'arguments' => array('nb' => 3), 
                'examples'=> array('"Optio quos qui illo error.\nLaborum vero a officia id corporis.\nSaepe provident esse hic eligendi."'),
            ),
            array(
                'provider' => 'Lorem', 'method' => 'paragraph', 
                'culture' => '', 
                'arguments' => array('nbSentences' => 3), 
                'examples'=> array('\'Ut ab voluptas sed a nam. Sint autem inventore aut officia aut aut blanditiis. Ducimus eos odit amet et est ut eum.\''),
            ),
            array(
                'provider' => 'Lorem', 'method' => 'paragraphs', 
                'culture' => '', 
                'arguments' => array('nb' => 3), 
                'examples'=> array('"Quidem ut sunt et quidem est accusamus aut. Fuga est placeat rerum ut. Enim ex eveniet facere sunt.\nAut nam et eum architecto fugit repellendus illo. Qui ex esse veritatis.\nPossimus omnis aut incidunt sunt. Asperiores incidunt iure sequi cum culpa rem. Rerum exercitationem est rem."'),
            ),
            array(
                'provider' => 'Lorem', 'method' => 'text', 
                'culture' => '', 
                'arguments' => array('maxNbChars' => 200), 
                'examples'=> array('\'Fuga totam reiciendis qui architecto fugiat nemo. Consequatur recusandae qui cupiditate eos quod.\''),
            ),
            //Internet
            array(
                'provider' => 'Internet', 
                'method' => 'email', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'tkshlerin@collins.com\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'safeEmail', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'king.alford@example.org\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'freeEmail', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'bradley72@gmail.com\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'companyEmail', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'russel.durward@mcdermott.org\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'freeEmailDomain', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'yahoo.com\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'safeEmailDomain', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'example.org\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'userName', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'wade55\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'domainName', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'wolffdeckow.net\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'domainWord', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'feeney\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'tld', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'biz\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'url', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'http://www.strackeframi.com/\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'ipv4', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'109.133.32.252\''),
            ),
            array(
                'provider' => 'Internet', 
                'method' => 'ipv6', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'8e65:933d:22ee:a232:f1c1:2741:1f10:117c\''),
            ),
            //DateTime
            array(
                'provider' => 'DateTime', 
                'method' => 'unixTime', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array(58781813),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'dateTime', 
                'culture' => '', 
                'arguments' => array('format' => '\'Y-m-d H:i:s\''), 
                'examples'=> array('\'2008-04-25 08:37:17\''),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'dateTimeAD', 
                'culture' => '', 
                'arguments' => array('format' => '\'Y-m-d H:i:s\''), 
                'examples'=> array('\'1800-04-29 20:38:49\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'iso8601', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'1978-12-09T10:10:29+0000\''),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'date', 
                'culture' => '', 
                'arguments' => array('format' => '\'Y-m-d\''), 
                'examples'=> array('\'1979-06-09\''),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'time', 
                'culture' => '', 
                'arguments' => array('format' => '\'H:i:s\''), 
                'examples'=> array('\'20:49:42\''),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'dateTimeBetween', 
                'culture' => '', 
                'arguments' => array('startDate' => '\'-30 years\'', 'endDate' => '\'now\'', 'format' => '\'Y-m-d H:i:s\''), 
                'examples'=> array('\'2003-03-15 02:00:49\''),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'dateTimeThisCentury(format = \'Y-m-d H:i:s\')', 
                'culture' => '', 
                'arguments' => array('format' => '\'Y-m-d H:i:s\''), 
                'examples'=> array('\'1915-05-30 19:28:21\''),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'dateTimeThisDecade(format = \'Y-m-d H:i:s\')', 
                'culture' => '', 
                'arguments' => array('format' => '\'Y-m-d H:i:s\''), 
                'examples'=> array('\'2007-05-29 22:30:48\''),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'dateTimeThisYear(format = \'Y-m-d H:i:s\')', 
                'culture' => '', 
                'arguments' => array('format' => '\'Y-m-d H:i:s\''), 
                'examples'=> array('\'2011-02-27 20:52:14\''),
            ),
            array(
                'provider' => 'DateTime', 'method' => 'dateTimeThisMonth(format = \'Y-m-d H:i:s\')', 
                'culture' => '', 
                'arguments' => array('format' => '\'Y-m-d H:i:s\''), 
                'examples'=> array('\'2011-10-23 13:46:23\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'amPm', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'pm\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'dayOfMonth', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'04\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'dayOfWeek', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Friday\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'month', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'06\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'monthName', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'January\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'year', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'1993\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'century', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'VI\''),
            ),
            array(
                'provider' => 'DateTime', 
                'method' => 'timezone', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'Europe/Paris\''),
            ),
            //Miscellaneous
            array(
                'provider' => 'Miscellaneous', 'method' => 'boolean', 
                'culture' => '', 
                'arguments' => array('chanceOfGettingTrue' => 50), 
                'examples'=> array('true'),
            ),
            array(
                'provider' => 'Miscellaneous', 
                'method' => 'md5', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'de99a620c50f2990e87144735cd357e7\''),
            ),
            array(
                'provider' => 'Miscellaneous', 
                'method' => 'sha1', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'f08e7f04ca1a413807ebc47551a40a20a0b4de5c\''),
            ),
            array(
                'provider' => 'Miscellaneous', 
                'method' => 'sha256', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('\'0061e4c60dac5c1d82db0135a42e00c89ae3a333e7c26485321f24348c7e98a5\''),
            ),
            array(
                'provider' => 'Miscellaneous', 
                'method' => 'locale', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('en_UK'),
            ),
            array(
                'provider' => 'Miscellaneous', 
                'method' => 'countryCode', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('UK'),
            ),
            array(
                'provider' => 'Miscellaneous', 
                'method' => 'languageCode', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('en'),
            ),
            //Base
            array(
                'provider' => 'Base', 
                'method' => 'randomDigit', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array(7),
            ),
            array(
                'provider' => 'Base', 'method' => 'randomNumber', 
                'culture' => '', 
                'arguments' => array('nbDigits' => 'NULL'), 
                'examples'=> array(79907610),
            ),
            array(
                'provider' => 'Base', 
                'method' => 'randomLetter', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('b'),
            ),
            array(
                'provider' => 'Base', 'method' => 'randomElement', 
                'culture' => '', 
                'arguments' => array('array' => 'comma separated list values \'a, b,c\''), 
                'examples'=> array('b'),
            ),
            array(
                'provider' => 'Base', 'method' => 'numerify', 
                'culture' => '', 
                'arguments' => array('string' => '\'###\''), 
                'examples'=> array('609'),
            ),
            array(
                'provider' => 'Base', 'method' => 'lexify', 
                'culture' => '', 
                'arguments' => array('string' => '\'????\''), 
                'examples'=> array('wgts'),
            ),
            array(
                'provider' => 'Base', 'method' => 'bothify', 
                'culture' => '', 
                'arguments' => array('string' => '\'## ??\''), 
                'examples'=> array('42 jz'),
            ),
            //UserAgent
            array(
                'provider' => 'UserAgent', 
                'method' => 'userAgent', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('Mozilla/5.0 (Windows CE) AppleWebKit/5350 (KHTML, like Gecko) Chrome/13.0.888.0 Safari/5350'),
            ),
            array(
                'provider' => 'UserAgent', 
                'method' => 'chrome', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('Mozilla/5.0 (Macintosh; PPC Mac OS X 10_6_5) AppleWebKit/5312 (KHTML, like Gecko) Chrome/14.0.894.0 Safari/5312'),
            ),
            array(
                'provider' => 'UserAgent', 
                'method' => 'firefox', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('Mozilla/5.0 (X11; Linuxi686; rv:7.0) Gecko/20101231 Firefox/3.6'),
            ),
            array(
                'provider' => 'UserAgent', 
                'method' => 'safari', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_7_1 rv:3.0; en-US) AppleWebKit/534.11.3 (KHTML, like Gecko) Version/4.0 Safari/534.11.3'),
            ),
            array(
                'provider' => 'UserAgent', 
                'method' => 'opera', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('Opera/8.25 (Windows NT 5.1; en-US) Presto/2.9.188 Version/10.00'),
            ),
            array(
                'provider' => 'UserAgent', 
                'method' => 'internetExplorer', 
                'culture' => '', 
                'arguments' => array(), 
                'examples'=> array('Mozilla/5.0 (compatible; MSIE 7.0; Windows 98; Win 9x 4.90; Trident/3.0)'),
            ),
            //Uuid
            array(
                'provider' => 'Uuid', 
                'method' => 'uuid', 
                'culture' => '', 
                'arguments' => array(), 
                'examples' => array('\'7e57d004-2b97-0e7a-b45f-5387367791cd\''),
            ),
        );

        $collection = new self();
        foreach($methods as $method) {
            $collection->addFakerMethod(new FakerMethod($method['provider'], $method['method'], $method['culture'], $method['arguments'], $method['examples']));
        }
        return $collection;
    }
}