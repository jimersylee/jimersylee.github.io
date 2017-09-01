<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Date.php 11046 2008-08-25 13:58:52Z thomas $
 */


/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Date extends Zend_Validate_Abstract
{
    /**
     * Validation failure message key for when the value does not follow the YYYY-MM-DD format
     */
    const NOT_YYYY_MM_DD = 'dateNotYYYY-MM-DD';

    /**
     * Validation failure message key for when the value does not appear to be a valid date
     */
    const INVALID        = 'dateInvalid';

    /**
     * Validation failure message key for when the value does not fit the given dateformat or locale
     */
    const FALSEFORMAT    = 'dateFalseFormat';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_YYYY_MM_DD => "'%value%' is not of the format YYYY-MM-DD",
        self::INVALID        => "'%value%' does not appear to be a valid date",
        self::FALSEFORMAT    => "'%value%' does not fit given date format"
    );

    /**
     * Optional format
     *
     * @var string|null
     */
    protected $_format;

    /**
     * Optional locale
     *
     * @var string|Zend_Locale|null
     */
    protected $_locale;

    /**
     * Sets validator options
     *
     * @param  string             $format OPTIONAL
     * @param  string|Zend_Locale $locale OPTIONAL
     * @return void
     */
    public function __construct($format = null, $locale = null)
    {
        $this->setFormat($format);
        $this->setLocale($locale);
    }

    /**
     * Returns the locale option
     *
     * @return string|Zend_Locale|null
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Sets the locale option
     *
     * @param  string|Zend_Locale $locale
     * @return Zend_Validate_Date provides a fluent interface
     */
    public function setLocale($locale = null)
    {
        if ($locale === null) {
            $this->_locale = null;
            return $this;
        }

        require_once 'Zend/Locale.php';
        if (!Zend_Locale::isLocale($locale, true)) {
            if (!Zend_Locale::isLocale($locale, false)) {
                require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception("The locale '$locale' is no known locale");
            }

            $locale = new Zend_Locale($locale);
        }

        $this->_locale = (string) $locale;
        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string|null
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Sets the format option
     *
     * @param  string $format
     * @return Zend_Validate_Date provides a fluent interface
     */
    public function setFormat($format = null)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if $value is a valid date of the format YYYY-MM-DD
     * If optional $format or $locale is set the date format is checked
     * according to Zend_Date, see Zend_Date::isDate()
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        if (($this->_format !== null) or ($this->_locale !== null)) {
            require_once 'Zend/Date.php';
            if (!Zend_Date::isDate($value, $this->_format, $this->_locale)) {
                if ($this->_checkFormat($value) === false) {
                    $this->_error(self::FALSEFORMAT);
                } else {
                    $this->_error(self::INVALID);
                }
                return false;
            }
        } else {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $valueString)) {
                $this->_error(self::NOT_YYYY_MM_DD);
                return false;
            }

            list($year, $month, $day) = sscanf($valueString, '%d-%d-%d');

            if (!checkdate($month, $day, $year)) {
                $this->_error(self::INVALID);
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the given date fits the given format
     *
     * @param  string $value  Date to check
     * @return boolean False when date does not fit the format
     */
    private function _checkFormat($value)
    {
        try {
            require_once 'Zend/Locale/Format.php';
            $parsed = Zend_Locale_Format::getDate($value, array(
                                                  'date_format' => $this->_format, 'format_type' => 'iso',
                                                  'fix_date' => false));
            if (isset($parsed['year']) and ((strpos(strtoupper($this->_format), 'YY') !== false) and
                (strpos(strtoupper($this->_format), 'YYYY') === false))) {
                $parsed['year'] = Zend_Date::_century($parsed['year']);
            }
        } catch (Exception $e) {
            // Date can not be parsed
            return false;
        }

        if (((strpos($this->_format, 'Y') !== false) or (strpos($this->_format, 'y') !== false)) and
            (!isset($parsed['year']))) {
            // Year expected but not found
            return false;
        }

        if ((strpos($this->_format, 'M') !== false) and (!isset($parsed['month']))) {
            // Month expected but not found
            return false;
        }

        if ((strpos($this->_format, 'd') !== false) and (!isset($parsed['day']))) {
            // Day expected but not found
            return false;
        }

        if (((strpos($this->_format, 'H') !== false) or (strpos($this->_format, 'h') !== false)) and
            (!isset($parsed['hour']))) {
            // Hour expected but not found
            return false;
        }

        if ((strpos($this->_format, 'm') !== false) and (!isset($parsed['minute']))) {
            // Minute expected but not found
            return false;
        }

        if ((strpos($this->_format, 's') !== false) and (!isset($parsed['second']))) {
            // Second expected  but not found
            return false;
        }

        // Date fits the format
        return true;
    }
}
