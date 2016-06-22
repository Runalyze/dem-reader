<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Tests\Interpolation;

use Runalyze\DEM\Exception\InvalidArgumentException;

class ArgumentsCheckTraitTest extends \PHPUnit_Framework_TestCase
{
    /** @var \ReflectionMethod */
    protected $CheckArgumentsMethod;

    /** @var object */
    protected $Object;

    public function setUp()
    {
        $this->Object = $this->getObjectForTrait('\\Runalyze\\DEM\\Interpolation\\ArgumentsCheckTrait');
        $this->CheckArgumentsMethod = new \ReflectionMethod($this->Object, 'checkArguments');
        $this->CheckArgumentsMethod->setAccessible(true);
    }

    public function testValidCheckArgumentsCalls()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 0.0, 0.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 0.0, 1.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 1.0, 0.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 1.0, 1.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.3, [42, 42, 42, 42]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEmptyBoundingBox()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, []);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoundingBoxWithOnlyThreeElements()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, [1, 2, 3]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBoundingBoxWithTooManyElements()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, [1, 2, 3, 4, 5]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testXNegative()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, -0.1, 0.5, [1, 2, 3, 4]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testXLargerThanOne()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 1.1, 0.5, [1, 2, 3, 4]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testYNegative()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 0.1, -1.0, [1, 2, 3, 4]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testYLargerThanOne()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 0.3, 1.001, [1, 2, 3, 4]);
    }
}
