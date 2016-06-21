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

use Runalyze\DEM\Interpolation\LambdaInterpolation;

class LambdaInterpolationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidClosure()
    {
        new LambdaInterpolation(function () {
            return 1;
        });
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThatInvalidCallsAreDetected()
    {
        $Interpolation = new LambdaInterpolation(function ($x, $y, array $elevationOnBoundingBox) {
            return 1;
        });

        $Interpolation->interpolate(0.5, 0.14, [1, 2, 3]);
    }

    public function testConstantClosure()
    {
        $Interpolation = new LambdaInterpolation(function ($x, $y, array $elevationOnBoundingBox) {
            return 42;
        });

        $this->assertEquals(42, $Interpolation->interpolate(0.314, 0.42, [100, 100, 100, 100]));
    }
}
