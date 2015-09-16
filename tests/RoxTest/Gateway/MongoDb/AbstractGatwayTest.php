<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Rox for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RoxTest;

use PHPUnit_Framework_TestCase;

class AbstractGatewayTest extends PHPUnit_Framework_TestCase
{

    public function testGetCollection()
    {
        $modelMock = $this->getMockForAbstractClass('Rox\Model\AbstractModel');
        $obj = $this->getMockForAbstractClass('Rox\Gateway\MongoDb\AbstractGateway',[new \MongoClient(), $modelMock]);
        $this->assertInstanceOf('\MongoDB', $obj->getCollection());
    }
}
