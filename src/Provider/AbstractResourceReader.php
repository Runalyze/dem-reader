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

abstract class AbstractResourceReader implements ResourceReaderInterface
{
    /** @var resource|bool */
    protected $FileResource = false;

    /**
     * @param resource|bool $resource
     */
    public function __construct($resource = false)
    {
        if (false !== $resource) {
            $this->setResource($resource);
        }
    }

    public function __destruct()
    {
        $this->closeResource();
    }

    /**
     * @param  resource          $resource
     * @throws \RuntimeException
     */
    public function setResource($resource)
    {
        $this->closeResource();

        $this->FileResource = $resource;

        if (false === $this->FileResource) {
            throw new \RuntimeException('Provider file "'.$filename.'"" can\'t be opened for reading.');
        }
    }

    protected function closeResource()
    {
        if (is_resource($this->FileResource)) {
            fclose($this->FileResource);
        }
    }

    abstract public function readHeader();

    /**
     * @param  int      $row
     * @param  int      $col
     * @return int|bool elevation [m] can be false if nothing retrieved or value is unknown
     */
    abstract public function getElevationFor($row, $col);
}
