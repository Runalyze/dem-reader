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

use Runalyze\DEM\Interpolation\GuessInvalidValuesTrait;
use Runalyze\DEM\Interpolation\InterpolationInterface;

abstract class AbstractFileProvider implements ProviderInterface
{
    use GuessInvalidValuesTrait;

    /** @var int */
    const MAX_LATITUDE = 90;

    /** @var int */
    const MAX_LONGITUDE = 180;

    /** @var string */
    protected $PathToFiles;

    /** @var resource */
    protected $FileResource;

    /** @var string|bool */
    protected $CurrentFilename = false;

    /** @var \Runalyze\DEM\Interpolation\InterpolationInterface|null */
    protected $Interpolation;

    /**
     * @param string                                                  $pathToFiles
     * @param \Runalyze\DEM\Interpolation\InterpolationInterface|null $interpolation
     */
    public function __construct($pathToFiles, InterpolationInterface $interpolation = null)
    {
        $this->PathToFiles = $pathToFiles;
        $this->Interpolation = $interpolation;
    }

    /**
     * Close resource on destruction.
     */
    public function __destruct()
    {
        $this->closeResource();
    }

    /**
     * @param \Runalyze\DEM\Interpolation\InterpolationInterface $interpolation
     */
    public function setInterpolation(InterpolationInterface $interpolation)
    {
        $this->Interpolation = $interpolation;
    }

    /**
     * Close resource if it exists.
     */
    protected function closeResource()
    {
        if (is_resource($this->FileResource)) {
            fclose($this->FileResource);
        }
    }

    /**
     * @param  array $latitudeLongitudes array(array($lat, $lng), ...)
     * @return bool
     */
    public function hasDataFor(array $latitudeLongitudes)
    {
        foreach ($latitudeLongitudes as $location) {
            if (!is_array($location) || count($location) !== 2) {
                throw new \InvalidArgumentException('Locations must be arrays of two values.');
            }

            if (!$this->locationIsInBounds($location[0], $location[1])) {
                return false;
            }

            if (!file_exists($this->PathToFiles.$this->getFilenameFor($location[0], $location[1]))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  float $latitude
     * @param  float $longitude
     * @return bool
     */
    protected function locationIsInBounds($latitude, $longitude)
    {
        return
            ($latitude > -static::MAX_LATITUDE) && ($latitude <= static::MAX_LATITUDE) &&
            ($longitude > -static::MAX_LONGITUDE) && ($longitude < static::MAX_LONGITUDE)
        ;
    }

    /**
     * @return bool
     */
    public function usesInterpolation()
    {
        return null !== $this->Interpolation;
    }

    /**
     * @param  float[]                   $latitudes
     * @param  float[]                   $longitudes
     * @throws \InvalidArgumentException
     * @return array                     elevations [m] can be false if nothing retrieved
     */
    public function getElevations(array $latitudes, array $longitudes)
    {
        $numberOfPoints = count($latitudes);
        $elevations = [];

        if (count($longitudes) !== $numberOfPoints) {
            throw new \InvalidArgumentException('Arrays for latitude and longitude must be of same size.');
        }

        for ($i = 0; $i < $numberOfPoints; ++$i) {
            $elevations[] = $this->getElevation($latitudes[$i], $longitudes[$i]);
        }

        return $elevations;
    }

    /**
     * Get elevation for specified location.
     *
     * Availability of data must be checked in advance via hasDataFor($locations).
     *
     * @param  float    $latitude
     * @param  float    $longitude
     * @return int|bool elevation [m] can be false if nothing retrieved
     */
    public function getElevation($latitude, $longitude)
    {
        if (!$this->locationIsInBounds($latitude, $longitude)) {
            throw new \InvalidArgumentException(
                sprintf('Location (%f, %f) is out of bounds ([-%f, %f], [-%f, %f]).',
                    $latitude, $longitude,
                    static::MAX_LATITUDE, static::MAX_LATITUDE,
                    static::MAX_LONGITUDE, static::MAX_LONGITUDE
                )
            );
        }

        $filename = $this->getFilenameFor($latitude, $longitude);

        if ($this->CurrentFilename !== $filename) {
            $this->openResource($filename);
        }

        return $this->getElevationFromResource($latitude, $longitude);
    }

    /**
     * @param  float    $latitude
     * @param  float    $longitude
     * @return int|bool can be false if nothing retrieved
     */
    protected function getElevationFromResource($latitude, $longitude)
    {
        list($exactRowValue, $exactColValue) = $this->getExactRowAndColFor($latitude, $longitude);

        if (!$this->usesInterpolation()) {
            return $this->getElevationFor(round($exactRowValue), round($exactColValue));
        }

        return $this->getInterpolatedElevationFor($exactRowValue, $exactColValue);
    }

    /**
     * @param  float    $exactRowValue
     * @param  float    $exactColValue
     * @return int|bool can be false if nothing retrieved
     */
    protected function getInterpolatedElevationFor($exactRowValue, $exactColValue)
    {
        $row = floor($exactRowValue);
        $col = floor($exactColValue);

        $elevationOnBoundingBox = [
            $this->getElevationFor($row, $col),
            $this->getElevationFor($row, $col + 1),
            $this->getElevationFor($row + 1, $col),
            $this->getElevationFor($row + 1, $col + 1),
        ];

        $this->guessInvalidValuesOnBox($elevationOnBoundingBox);

        return $this->Interpolation->interpolate(
            ($exactRowValue - $row),
            ($exactColValue - $col),
            $elevationOnBoundingBox
        );
    }

    /**
     * @param  string $filename
     * @return bool
     */
    protected function resourceIsCurrentlyOpened($filename)
    {
        return $filename !== $this->CurrentFilename;
    }

    /**
     * @param  float  $latitude
     * @param  float  $longitude
     * @return string
     */
    abstract protected function getFilenameFor($latitude, $longitude);

    /**
     * @param string $filename
     */
    abstract protected function openResource($filename);

    /**
     * @param  int      $row
     * @param  int      $col
     * @return int|bool elevation [m] can be false if nothing retrieved
     */
    abstract protected function getElevationFor($row, $col);

    /**
     * @param  float $latitude
     * @param  float $longitude
     * @return array array(row, col)
     */
    abstract protected function getExactRowAndColFor($latitude, $longitude);
}
