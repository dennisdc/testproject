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
 * @package    Zend_Gdata
 * @subpackage DublinCore
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Identifier.php 16971 2009-07-22 18:05:45Z mikaelkael $
 */

/**
 * @see Zend_Gdata_Extension
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * An unambiguous reference to the resource within a given context
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage DublinCore
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_DublinCore_Extension_Identifier extends Zend_Gdata_Extension
{

    protected $_rootNamespace = 'dc';
    protected $_rootElement = 'identifier';

    /**
     * Constructor for Zend_Gdata_DublinCore_Extension_Identifier which
     * An unambiguous reference to the resource within a given context
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($value = null)
    {
        $this->registerAllNamespaces(Zend_Gdata_DublinCore::$namespaces);
        parent::__construct();
        $this->_text = $value;
    }

}
