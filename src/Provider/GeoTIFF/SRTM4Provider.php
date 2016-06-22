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

class SRTM4Provider extends AbstractGeoTIFFProvider
{
    /** @var int */
    const MAX_LATITUDE = 60;

    /** @var int */
    const MAX_LONGITUDE = 180;

    /** @var float */
    const DEGREES_PER_TILE = 5.0;

    /** @var float */
    protected $CurrentTileHorizontalReference;

    /** @var float */
    protected $CurrentTileVerticalReference;

    /** @var float top left latitude */
    protected $CurrentTileLatitude;

    /** @var float top left longitude */
    protected $CurrentTileLongitude;

    /** @var string */
    protected $FilenameFormat = 'srtm_%02d_%02d.tif';

    /** @var GeoTIFFReader */
    protected $ResourceReader;

    public function initResourceReader()
    {
        $this->ResourceReader = new GeoTIFFReader();
    }

    /**
     * @param $format
     */
    public function setFilenameFormat($format)
    {
        $this->FilenameFormat = $format;
    }

    /**
     * @param  float                    $latitude
     * @param  float                    $longitude
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getFilenameFor($latitude, $longitude)
    {
        $this->loadTileReferencesFor($latitude, $longitude);

        return sprintf($this->FilenameFormat, $this->CurrentTileHorizontalReference, $this->CurrentTileVerticalReference);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     */
    protected function loadTileReferencesFor($latitude, $longitude)
    {
        if (fmod($latitude, static::DEGREES_PER_TILE) === 0.0) {
            $this->CurrentTileVerticalReference = (static::MAX_LATITUDE - $latitude) / static::DEGREES_PER_TILE + 1;
        } else {
            $this->CurrentTileVerticalReference = ceil((static::MAX_LATITUDE - $latitude) / static::DEGREES_PER_TILE);
        }

        if (fmod($longitude, static::DEGREES_PER_TILE) === 0.0) {
            $this->CurrentTileHorizontalReference = (static::MAX_LONGITUDE + $longitude) / static::DEGREES_PER_TILE + 1;
        } else {
            $this->CurrentTileHorizontalReference = ceil((static::MAX_LONGITUDE + $longitude) / static::DEGREES_PER_TILE);
        }

        $this->CurrentTileLatitude = static::MAX_LATITUDE - (($this->CurrentTileVerticalReference - 1) * static::DEGREES_PER_TILE);
        $this->CurrentTileLongitude = (($this->CurrentTileHorizontalReference - 1) * static::DEGREES_PER_TILE) - static::MAX_LONGITUDE;

        if ($latitude < 0) {
            $this->CurrentTileLatitude = -$this->CurrentTileLatitude;
        }
    }

    /**
     * @param  float   $latitude
     * @param  float   $longitude
     * @return float[] array(row, col)
     */
    protected function getExactRowAndColFor($latitude, $longitude)
    {
        return $this->ResourceReader->getExactRowAndColFor(
            abs($this->CurrentTileLatitude - abs($latitude)) / static::DEGREES_PER_TILE,
            abs($this->CurrentTileLongitude - $longitude) / static::DEGREES_PER_TILE
        );
    }
}
