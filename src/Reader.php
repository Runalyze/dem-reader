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

use Runalyze\DEM\Provider\ProviderInterface;

class Reader
{
    /** @var \Runalyze\DEM\Provider\ProviderInterface[] */
    protected $Provider = [];

    /** @var int */
    protected $InvalidFlag = 0x8000;

    /**
     * @param \Runalyze\DEM\Provider\ProviderInterface|null $provider
     */
    public function __construct(ProviderInterface $provider = null)
    {
        if (null !== $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * @param \Runalyze\DEM\Provider\ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider)
    {
        $this->Provider[] = $provider;
    }

    /**
     * @return int
     */
    public function numberOfProviders()
    {
        return count($this->Provider);
    }

    /**
     * @return bool
     */
    public function hasProviders()
    {
        return !empty($this->Provider);
    }

    /**
     * @param  array                     $latitudes
     * @param  array                     $longitudes
     * @throws \InvalidArgumentException
     * @return array                     elevations [m] can be false if nothing retrieved
     */
    public function getElevations(array $latitudes, array $longitudes)
    {
        // TODO

        // loop through providers
        // set all 'false' elevations to $this->InvalidFlag
    }
}
