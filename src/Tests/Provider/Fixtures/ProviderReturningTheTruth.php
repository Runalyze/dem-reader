<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Tests\Provider\Fixtures;

use Runalyze\DEM\Interpolation\InterpolationInterface;
use Runalyze\DEM\Provider\ProviderInterface;

class ProviderReturningTheTruth implements ProviderInterface
{
    /**
     * @param  array $latitudeLongitudes array(array($lat, $lng), ...)
     * @return bool
     */
    public function hasDataFor(array $latitudeLongitudes)
    {
        return true;
    }

    /**
     * @param InterpolationInterface $interpolation
     */
    public function setInterpolation(InterpolationInterface $interpolation)
    {
    }

    /**
     * @param  float    $latitude
     * @param  float    $longitude
     * @return int|bool elevation [m] can be false if nothing retrieved
     */
    public function getElevation($latitude, $longitude)
    {
        return 42;
    }

    /**
     * @param  float[] $latitudes
     * @param  float[] $longitudes
     * @return array   elevations [m] can be false if nothing retrieved
     */
    public function getElevations(array $latitudes, array $longitudes)
    {
        return array_fill(0, count($latitudes), 42);
    }
}
