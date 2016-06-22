<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Interpolation;

use Runalyze\DEM\Exception\InvalidArgumentException;

trait ArgumentsCheckTrait
{
    /**
     * @param  float                    $x
     * @param  float                    $y
     * @param  array                    $elevationOnBoundingBox elevation data on [p0, p1, p2, p3]
     * @throws InvalidArgumentException
     */
    protected function checkArguments($x, $y, array $elevationOnBoundingBox)
    {
        if (4 !== count($elevationOnBoundingBox)) {
            throw new InvalidArgumentException('Array with elevation on bounding box must have four values.');
        }

        if ($x < 0.0 || 1.0 < $x) {
            throw new InvalidArgumentException('$x must be within [0.0, 1.0]');
        }

        if ($y < 0.0 || 1.0 < $y) {
            throw new InvalidArgumentException('$y must be within [0.0, 1.0]');
        }
    }
}
