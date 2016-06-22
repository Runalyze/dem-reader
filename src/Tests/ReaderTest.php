<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Tests;

use Runalyze\DEM\Exception\RuntimeException;
use Runalyze\DEM\Reader;
use Runalyze\DEM\Tests\Provider\Fixtures\ProviderForNothing;
use Runalyze\DEM\Tests\Provider\Fixtures\ProviderReturningTheTruth;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyReader()
    {
        $Reader = new Reader();

        $this->assertSame(0, $Reader->numberOfProviders());
        $this->assertFalse($Reader->hasProviders());
        $this->assertFalse($Reader->hasDataFor([[42.0, 3.14]]));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThatReaderWithoutProviderThrowsExceptionInsteadOfInvalidElevations()
    {
        (new Reader())->getElevations([42.0, 42.2], [3.14, 3.14159]);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testReaderWithNoProviderThatCanHandleTheLocations()
    {
        $Reader = new Reader(new ProviderForNothing());
        $Reader->getElevations([42.0, 42.2], [3.14, 3.14159]);
    }

    public function testThatOnlyWorkingProviderIsUsed()
    {
        $Reader = new Reader();
        $Reader->addProvider(new ProviderForNothing());
        $Reader->addProvider(new ProviderReturningTheTruth());

        $this->assertTrue($Reader->hasDataFor([[42.0, 3.14]]));
        $this->assertEquals([42, 42], $Reader->getElevations([1.0, 2.0], [3.0, 4.0]));
    }

    public function testHandlingOfInvalidLocations()
    {
        $Reader = new Reader(new ProviderReturningTheTruth());

        $this->assertTrue($Reader->hasDataFor([[0.0, 0.0], [0.0, 0.0]]));
        $this->assertTrue($Reader->hasDataFor([[0.0, 0.0], [4.2, 3.1]]));
        $this->assertEquals([42, 42, 42], $Reader->getElevations([0.0, 0.0, 0.0], [0.0, 0.0, 0.0]));
        $this->assertEquals([42, 42, 42], $Reader->getElevations([0.0, 4.2, 0.0], [0.0, 3.1, 0.0]));
    }
}
