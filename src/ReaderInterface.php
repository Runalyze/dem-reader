<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM;

use Runalyze\DEM\Exception\InvalidArgumentException;

interface ReaderInterface
{
    /**
     * @param  array $latitudeLongitudes array(array($lat, $lng), ...)
     * @return bool
     */
    public function hasDataFor(array $latitudeLongitudes);

    /**
     * @param  float[]                  $latitudes
     * @param  float[]                  $longitudes
     * @return array                    elevations [m] can be false if nothing retrieved
     * @throws InvalidArgumentException
     */
    public function getElevations(array $latitudes, array $longitudes);
}
