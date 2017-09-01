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
 * @version    $Id: NotEmpty.php 10356 2008-07-24 15:14:56Z matthew $
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
class Zend_Validate_NotEmpty extends Zend_Validate_Abstract
{

    const IS_EMPTY = 'isEmpty';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::IS_EMPTY => "Value is empty, but a non-empty value is required"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is not an empty value.
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue((string) $value);

        if (is_string($value)
            && (('' === $value) 
                || preg_match('/^\s+$/s', $value))
        ) {
            $this->_error();
            return false;
        } elseif (!is_string($value) && empty($value)) {
            $this->_error();
            return false;
        }

        return true;
    }

}
