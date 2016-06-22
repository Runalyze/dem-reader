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

use Runalyze\DEM\Provider\AbstractFileProvider;

abstract class AbstractGeoTIFFProvider extends AbstractFileProvider
{
    /**
     * @param  string            $filename
     * @throws \RuntimeException
     */
    protected function openResource($filename)
    {
        $resource = fopen($this->PathToFiles.DIRECTORY_SEPARATOR.$filename, 'rb');

        $this->ResourceReader->setResource($resource);
        $this->ResourceReader->readHeader();

        $this->CurrentFilename = $filename;
    }

    /**
     * @param  int      $row
     * @param  int      $col
     * @return int|bool
     */
    protected function getElevationFor($row, $col)
    {
        return $this->ResourceReader->getElevationFor($row, $col);
    }
}
