<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Tests\Provider;

use Runalyze\DEM\Exception\RuntimeException;

class AbstractResourceReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param  bool                                     $resource
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function constructMock($resource = false)
    {
        return $this->getMockForAbstractClass('\\Runalyze\\DEM\\Provider\\AbstractResourceReader', [$resource]);
    }

    public function testConstructorAndDestructorWithoutResource()
    {
        $Reader = $this->constructMock(false);
        unset($Reader);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSettingInvalidResource()
    {
        $Reader = $this->constructMock(false);
        $Reader->setResource(false);
    }

    public function testReadingThisFileForSomeFun()
    {
        $Reader = $this->constructMock(fopen(__FILE__, 'r'));
        $Reader->setResource(fopen(__FILE__, 'r'));
    }
}
