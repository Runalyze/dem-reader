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

class AbstractFileProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var AbstractFileProvider */
    protected $Object;

    public function setUp(): void
    {
        $this->Object = $this->getMockForAbstractClass('\\Runalyze\\DEM\\Provider\\AbstractFileProvider', ['path/to/files']);
    }

    public function testInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Object->hasDataFor(['foo', 'bar']);
    }

    public function testTooManyCoordinatesInLocationArray()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Object->hasDataFor([[47.7, 47.6, 47.5]]);
    }

    public function testThatTheProviderHasNoDataAsThereAreNoFiles()
    {
        $this->assertFalse($this->Object->hasDataFor([[49.4, 7.7]]));
    }

    public function testThatGetElevationsRequiresEqualSizedArrays()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Object->getElevations([1, 2, 3], [1, 2, 3, 4]);
    }

    public function testUnknownLocation()
    {
        $this->assertFalse($this->Object->getElevation(0.0, 0.0));
    }

    public function testInvalidLocation()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->assertFalse($this->Object->getElevation(123.4, 321.0));
    }
}
