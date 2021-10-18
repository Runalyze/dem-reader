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

class ArgumentsCheckTraitTest extends \PHPUnit\Framework\TestCase
{
    /** @var \ReflectionMethod */
    protected $CheckArgumentsMethod;

    /** @var object */
    protected $Object;

    public function setUp(): void
    {
        $this->Object = $this->getObjectForTrait('\\Runalyze\\DEM\\Interpolation\\ArgumentsCheckTrait');
        $this->CheckArgumentsMethod = new \ReflectionMethod($this->Object, 'checkArguments');
        $this->CheckArgumentsMethod->setAccessible(true);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testValidCheckArgumentsCalls()
    {
        $this->CheckArgumentsMethod->invoke($this->Object, 0.0, 0.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 0.0, 1.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 1.0, 0.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 1.0, 1.0, [42, 42, 42, 42]);
        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.3, [42, 42, 42, 42]);
    }

    public function testEmptyBoundingBox()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, []);
    }

    public function testBoundingBoxWithOnlyThreeElements()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, [1, 2, 3]);
    }

    public function testBoundingBoxWithTooManyElements()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.5, 0.5, [1, 2, 3, 4, 5]);
    }

    public function testXNegative()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, -0.1, 0.5, [1, 2, 3, 4]);
    }

    public function testXLargerThanOne()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 1.1, 0.5, [1, 2, 3, 4]);
    }

    public function testYNegative()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.1, -1.0, [1, 2, 3, 4]);
    }

    public function testYLargerThanOne()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->CheckArgumentsMethod->invoke($this->Object, 0.3, 1.001, [1, 2, 3, 4]);
    }
}
