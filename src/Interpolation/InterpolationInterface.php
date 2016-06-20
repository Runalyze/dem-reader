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

interface InterpolationInterface
{
    /**
     * Interpolate.
     *
     * p0------------p1
     * |      |
     * |      y
     * |      |
     * |--x-- Z
     * |
     * p2------------p3
     *
     * @param  float $x                      x position of Z within bounding box, required: $x in [0.0, 1.0]
     * @param  float $y                      y position of Z within bounding box, required: $y in [0.0, 1.0]
     * @param  array $elevationOnBoundingBox elevation data on [p0, p1, p2, p3]
     * @return int   estimated elevation on point Z
     */
    public function interpolate($x, $y, array $elevationOnBoundingBox);
}
