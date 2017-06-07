<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Provider\GeoTIFF;

use Runalyze\DEM\Exception\InvalidArgumentException;

class SRTM1ArcSecondProvider extends SRTM4Provider
{
    /** @var float */
    const DEGREES_PER_TILE = 1.0;

    /** @var string */
    protected $FilenameFormat = '%s%02d_%s%03d.tif';

    /**
     * @param  float                    $latitude
     * @param  float                    $longitude
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getFilenameFor($latitude, $longitude)
    {
        $this->loadTileReferencesFor($latitude, $longitude);

        return sprintf(
            $this->FilenameFormat,
            $this->CurrentTileVerticalReference < 0 ? 's' : 'n',
            abs($this->CurrentTileVerticalReference),
            $this->CurrentTileHorizontalReference < 0 ? 'w' : 'e',
            abs($this->CurrentTileHorizontalReference)
        );
    }

    /**
     * @param float $latitude
     * @param float $longitude
     */
    protected function loadTileReferencesFor($latitude, $longitude)
    {
        $this->CurrentTileVerticalReference = floor($latitude / static::DEGREES_PER_TILE);
        $this->CurrentTileHorizontalReference = floor($longitude / static::DEGREES_PER_TILE);

        $this->CurrentTileLatitude = $this->CurrentTileVerticalReference;
        $this->CurrentTileLongitude = $this->CurrentTileHorizontalReference;
    }

    /**
     * @param  float   $latitude
     * @param  float   $longitude
     * @return float[] array(row, col)
     */
    protected function getExactRowAndColFor($latitude, $longitude)
    {
        return $this->ResourceReader->getExactRowAndColFor(
            abs($this->CurrentTileLatitude + 1 - $latitude) / static::DEGREES_PER_TILE,
            abs($longitude - $this->CurrentTileLongitude) / static::DEGREES_PER_TILE
        );
    }
}
