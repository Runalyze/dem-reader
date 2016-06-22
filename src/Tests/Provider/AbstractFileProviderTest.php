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

use Runalyze\DEM\Exception\InvalidArgumentException;
use Runalyze\DEM\Provider\AbstractFileProvider;

class AbstractFileProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var AbstractFileProvider */
    protected $Object;

    public function setUp()
    {
        $this->Object = $this->getMockForAbstractClass('\\Runalyze\\DEM\\Provider\\AbstractFileProvider', ['path/to/files']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgument()
    {
        $this->Object->hasDataFor(['foo', 'bar']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTooManyCoordinatesInLocationArray()
    {
        $this->Object->hasDataFor([[47.7, 47.6, 47.5]]);
    }

    public function testThatTheProviderHasNoDataAsThereAreNoFiles()
    {
        $this->assertFalse($this->Object->hasDataFor([[49.4, 7.7]]));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThatGetElevationsRequiresEqualSizedArrays()
    {
        $this->Object->getElevations([1, 2, 3], [1, 2, 3, 4]);
    }

    public function testUnknownLocation()
    {
        $this->assertFalse($this->Object->getElevation(0.0, 0.0));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidLocation()
    {
        $this->assertFalse($this->Object->getElevation(123.4, 321.0));
    }
}
