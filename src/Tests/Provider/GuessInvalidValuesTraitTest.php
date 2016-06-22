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

class GuessInvalidValuesTraitTest extends \PHPUnit_Framework_TestCase
{
    /** @var \ReflectionMethod */
    protected $GuessInvalidValuesOnBoxMethod;

    /** @var object */
    protected $Object;

    public function setUp()
    {
        $this->Object = $this->getObjectForTrait('\\Runalyze\\DEM\\Provider\\GuessInvalidValuesTrait');
        $this->GuessInvalidValuesOnBoxMethod = new \ReflectionMethod($this->Object, 'guessInvalidValuesOnBox');
        $this->GuessInvalidValuesOnBoxMethod->setAccessible(true);
    }

    public function testEmptyBoundingBox()
    {
        $box = [false, false, false, false];
        $this->GuessInvalidValuesOnBoxMethod->invokeArgs($this->Object, [&$box]);
        $this->assertEquals([false, false, false, false], $box);
    }

    public function testBoundingBoxWithAllValidEntries()
    {
        $box = [42, 42, 42, 42];
        $this->GuessInvalidValuesOnBoxMethod->invokeArgs($this->Object, [&$box]);
        $this->assertEquals([42, 42, 42, 42], $box);
    }

    public function testBoundingBoxWithInvalidEntries()
    {
        $box = [3, false, 42, false];
        $this->GuessInvalidValuesOnBoxMethod->invokeArgs($this->Object, [&$box]);
        $this->assertEquals([3, 22.5, 42, 22.5], $box);
    }
}
