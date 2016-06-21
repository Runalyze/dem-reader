<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Provider;

use Runalyze\DEM\Interpolation\InterpolationInterface;
use Runalyze\DEM\ReaderInterface;

interface ProviderInterface extends ReaderInterface
{
    /**
     * @param InterpolationInterface $interpolation
     */
    public function setInterpolation(InterpolationInterface $interpolation);

    /**
     * @param  float    $latitude
     * @param  float    $longitude
     * @return int|bool elevation [m] can be false if nothing retrieved
     */
    public function getElevation($latitude, $longitude);
}
