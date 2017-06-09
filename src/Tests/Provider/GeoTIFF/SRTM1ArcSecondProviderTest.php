<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Tests\Provider\GeoTIFF;

use Runalyze\DEM\Interpolation\BilinearInterpolation;
use Runalyze\DEM\Provider\GeoTIFF\SRTM1ArcSecondProvider;

class SRTM1ArcSecondProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    const PATH_TO_FILES = '/../../../../tests/testfiles';

    /**
     * @var SRTM1ArcSecondProvider
     */
    protected $Provider;

    public function setUp()
    {
        $this->Provider = new SRTM1ArcSecondProvider(__DIR__.self::PATH_TO_FILES);
        $this->Provider->setFilenameFormat('%s%02d_%s%03d_1arc_v3.tif');
        $this->Provider->setInterpolation(new BilinearInterpolation());
    }

    /**
     * @param string $filename
     */
    protected function checkFile($filename)
    {
        if (!$this->fileIsThere($filename)) {
            $this->markTestSkipped('Required testfile "'.$filename.'" is not available.');
        }
    }

    /**
     * @param  string $filename
     * @return bool
     */
    protected function fileIsThere($filename)
    {
        return file_exists(__DIR__.self::PATH_TO_FILES.'/'.$filename);
    }

    public function testTileBoundary()
    {
        $this->checkFile('n49_e007_1arc_v3.tif');

        if (
            !$this->fileIsThere('n49_e006_1arc_v3.tif') &&
            !$this->fileIsThere('n49_e008_1arc_v3.tif') &&
            !$this->fileIsThere('n48_e007_1arc_v3.tif') &&
            !$this->fileIsThere('n50_e007_1arc_v3.tif')
        ) {
            $this->assertTrue($this->Provider->hasDataFor([[49.00000, 7.00000]]));
            $this->assertTrue($this->Provider->hasDataFor([[49.99999, 7.00000]]));
            $this->assertTrue($this->Provider->hasDataFor([[49.99999, 7.99999]]));
            $this->assertTrue($this->Provider->hasDataFor([[49.00000, 7.99999]]));

            $this->assertFalse($this->Provider->hasDataFor([[49.00000, 6.99999]]));
            $this->assertFalse($this->Provider->hasDataFor([[49.00000, 8.00000]]));
            $this->assertFalse($this->Provider->hasDataFor([[48.99999, 7.99999]]));
        } else {
            $this->markTestSkipped('Can\'t check boundaries of tile as too many files are there.');
        }
    }

    /**
     * @group germany
     */
    public function testSingleElevationInGermany()
    {
        $this->checkFile('n49_e007_1arc_v3.tif');

        // SRTM4: 237
        $this->assertEquals(239, $this->Provider->getElevation(49.444722, 7.768889));
    }

    public function testMultipleElevationsInSydney()
    {
        $this->checkFile('s34_e151_1arc_v3.tif');

        // SRTM4: 4, 4, 3
        $this->assertEquals(
            [3, 3, 2],
            $this->Provider->getElevations(
                [-33.8706555, -33.8705667, -33.8704860],
                [151.1486918, 151.1486337, 151.1485585]
            )
        );
    }

    /**
     * @group london
     */
    public function testLocationsInLondon()
    {
        $this->checkFile('n51_w001_1arc_v3.tif');

        // SRTM4: 20, 19, 19
        $this->assertEquals(
            [18, 19, 18],
            $this->Provider->getElevations(
                [51.5073509, 51.5074509, 51.5075509],
                [-0.1277583, -0.1278583, -0.1279583]
            )
        );
    }

    public function testLocationsInWindhoek()
    {
        $this->checkFile('s23_e017_1arc_v3.tif');

        // SRTM4: 1668, 1671, 1672
        $this->assertEquals(
            [1665, 1672, 1671],
            $this->Provider->getElevations(
                [-22.5700, -22.5705, -22.5710],
                [17.0836,  17.0841,  17.0846]
            )
        );
    }

    public function testNewYork()
    {
        $this->checkFile('n40_w075_1arc_v3.tif');

        // SRTM4: 26, 29, 39
        $this->assertEquals(
            [28, 27, 46],
            $this->Provider->getElevations(
                [40.7127,  40.7132,  40.7137],
                [-74.0059, -74.0064, -74.0069]
            )
        );
    }

    public function testNewYorkWithoutInterpolation()
    {
        $this->checkFile('n40_w075_1arc_v3.tif');

        $this->Provider->removeInterpolation();

        // SRTM4: 26, 32, 32
        $this->assertEquals(
            [28, 28, 48],
            $this->Provider->getElevations(
                [40.7127,  40.7132,  40.7137],
                [-74.0059, -74.0064, -74.0069]
            )
        );
    }
}
